<?php

namespace app\controllers;

use Yii;
use app\models\Items;
use app\models\search\ItemsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;


/**
 * ItemsController implements the CRUD actions for Items model.
 */
class ItemsController extends DefaultController
{
    public $modelClass = 'app\models\Items';    
    public $modelSearchClass = 'app\models\search\ItemsSearch';   


    public function actionAddStockValidate()
    {
        $model = new \app\models\ItemStock();
        $request = \Yii::$app->getRequest();
        if ( $request->isPost && $model->load( $request->post() ) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }


    public function actionAddStock($id)
    {
        $model = new \app\models\ItemStock;
        $warehouses = \app\models\Warehouses::find()
        ->asArray()
        ->all();
        $itemModel = \app\models\Items::find()
        ->where(['id'=>$id])
        ->one();

        if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {
            $warehouseModel = \app\models\Warehouses::find()->where(['id'=>Yii::$app->request->post('ItemStock')['warehouse_id']])->asArray()->one();
            $model->sku = strtoupper( $warehouseModel['name'] )."P".Yii::$app->request->post('ItemStock')['price'];
            if ($model->save ( false) ) {
                \app\models\ItemStock::calcTotalStock($id);
            }

            return $this->redirect("index");
        }
        return $this->renderIsAjax('add-stock', [
            'model' => $model,
            'warehouses' => $warehouses,
            'itemModel' => $itemModel
        ]);
    }   



    public function actionUseItemValidate()
    {
        $model = new \app\models\ItemUsage();
        $model->scenario = \app\models\ItemUsage::SCENARIO_USE_ITEM;
        $request = \Yii::$app->getRequest();
        if ( $request->isPost && $model->load( $request->post() ) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionUseItem($id){
        $model = new \app\models\ItemUsage; 
        $model->scenario = \app\models\ItemUsage::SCENARIO_USE_ITEM;
        $itemModel = \app\models\Items::find()->where(['id'=>$id])->one();

        $itemStock = \app\models\ItemStock::find()->where(['item_id'=>$id])->andWhere(['!=','quantity',0])->asArray()->all();

        if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {
            if (preg_match('/^(?P<day>\d+)[-\/](?P<month>\d+)[-\/](?P<year>\d+)$/', Yii::$app->request->post('ItemUsage')['created_at'], $matches)) {
                $timestamp = mktime(0,0, 0, ($matches['month']), $matches['day'], $matches['year']);
            }
            $model->created_at  = $timestamp;
            if ( $model->save(false) ) {
                $itemStockModel = \app\models\ItemStock::find()
                ->where(['id'=>$model->item_stock_id])
                ->asArray()
                ->one();

                $amount = $itemStockModel['price'] * $model->quantity;
                if ( Yii::$app->request->post('ItemUsage')['personals'] != "" ) {
                    foreach ( Yii::$app->request->post('ItemUsage')['personals'] as $personalKey => $personalData ) {
                        $itemUsagePersonal = new \app\models\ItemUsagePersonal;
                        $itemUsagePersonal->item_usage_id = $model->id;
                        $itemUsagePersonal->personal_id = $personalData;
                        $itemUsagePersonal->save(false);
                    }
                }
                \app\models\ItemStock::updateStock( 
                    $model->item_stock_id, 
                    $itemModel['id'], 
                    Yii::$app->request->post('ItemUsage')['quantity'] 
                );

                \app\models\UserBalance::BalanceOut(
                     null, 
                     $amount, 
                     time(), 
                     1, 
                     3, 
                     null, 
                     null,  
                     false, 
                     $model->id 
                );
            }   
            return $this->redirect("index");
        }
        return $this->renderIsAjax('use-item', [
            'model' => $model,
            'itemModel' => $itemModel,
            'itemStock' => $itemStock
        ]);
    }




    public function actionPersonalList($q = null, $id = null)
    {
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $query = new \yii\db\Query;
            $query->select('id, fullname AS text')
                ->from('members')
                ->where(['like', 'fullname', $q])
                ->andWhere(['personal'=>'1'])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id) {
            if (is_array($id)) {
                $ids = array_values($id);
                $query = new \yii\db\Query;
                $query->select('id, fullname AS text')
                    ->from('members')
                    ->where(['id' => $id])
                    ->andWhere(['personal'=>'1'])
                    ->limit(20);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out = $data;
            }
        }
        return $out;
    }
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}

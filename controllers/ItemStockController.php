<?php

namespace app\controllers;

use Yii;
use app\models\ItemStock;
use app\models\search\ItemStockSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * ItemStockController implements the CRUD actions for ItemStock model.
 */
class ItemStockController extends DefaultController
{
    public $modelClass = 'app\models\ItemStock';    
    public $modelSearchClass = 'app\models\search\ItemStockSearch';       


    public function actionCreate(){
        echo Yii::t("app","If you want add a stock for item,Please do it Items section");
        die;
    }



    public function actionUpdateValidate($id)
    {
        $model = \app\models\ItemStock::findOne($id);
        $request = \Yii::$app->getRequest();
        if ( $request->isPost && $model->load( $request->post() ) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionUpdate($id){
        $model = $this->findModel($id);
        $warehouses = \app\models\Warehouses::find()->asArray()->all();
        if ($model->load(Yii::$app->request->post()) && $model->validate() ) {

            $warehouseModel = \app\models\Warehouses::find()->where(['id'=>Yii::$app->request->post('ItemStock')['warehouse_id']])->asArray()->one();
            $model->sku = strtoupper( $warehouseModel['name'] )."P".Yii::$app->request->post('ItemStock')['price'];
            if ( $model->save() ) {
                \app\models\ItemStock::calcTotalStock( $model->item_id );   
            }

            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
            'warehouses' => $warehouses,
        ]);
    }

    public function actionDelete($id)
    {
       $model =  $this->findModel($id);
       $itemId = $model->item_id;
       if ( $model->delete() ) {
           \app\models\ItemStock::calcTotalStock( $model->item_id );
       }
        return $this->redirect(['index']);
    }

}

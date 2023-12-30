<?php

namespace app\controllers;

use Yii;
use app\models\ItemUsage;
use app\models\search\ItemUsageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;


/**
 * ItemUsageController implements the CRUD actions for ItemUsage model.
 */
class ItemUsageController extends DefaultController
{
    public $modelClass = 'app\models\ItemUsage';    
    public $modelSearchClass = 'app\models\search\ItemUsageSearch';     

    /**
     * Creates a new ItemUsage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        die;
        // $model = new ItemUsage();

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // }

        // return $this->render('create', [
        //     'model' => $model,
        // ]);
    }

    /**
     * Updates an existing ItemUsage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        die;
        // $model = $this->findModel($id);

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // }

        // return $this->render('update', [
        //     'model' => $model,
        // ]);
    }    

    public function actionDelete($id)
    {
       $model =  $this->findModel($id);
       $itemStockId = $model->item_stock_id;
       $quantity = $model->quantity;
       if ( $model->delete() ) {
           \app\models\ItemUsage::calcDeletedStock( $itemStockId, $quantity );
       }
        return $this->redirect(['index']);
    }


}

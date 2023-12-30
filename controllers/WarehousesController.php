<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * WarehousesController implements the CRUD actions for Warehouses model.
 */
class WarehousesController extends DefaultController
{
    public $modelClass = 'app\models\Warehouses';
    public $modelSearchClass = 'app\models\search\WarehousesSearch';    




    public function actionCreate(){
        $model = new $this->modelClass;
        $siteConfig = \app\models\SiteConfig::find()->one();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'siteConfig' => $siteConfig
        ]);
    }


    public function actionUpdate($id){

        $model = $this->findModel($id);
        $siteConfig = \app\models\SiteConfig::find()->one();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
            'siteConfig' => $siteConfig
        ]);
    }


    public function actionDelete($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);

        $model->delete();

        return ['status' => 'success'];
    }

}

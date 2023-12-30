<?php

namespace app\controllers;

use app\components\DefaultController;
use app\models\Cities;
use app\models\search\CitiesSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Logs;


/**
 * CitiesController implements the CRUD actions for Cities model.
 */
class CitiesController extends DefaultController{

    public function actionIndex(){
        $searchModel = new CitiesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(){
        $model = new Cities();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $logMessage = "{$model->city_name} city was created (id:{$model->id})";
            Logs::writeLog(
                Yii::$app->user->username, 
                null, 
                $logMessage, 
                time()
            );
            return $this->redirect(['index']);
        }
        return $this->renderIsAjax('create', [
            'model' => $model,
        ]);
    }


    public function actionUpdate($id){
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $logMessage = "{$model->city_name} city was updated (id:{$model->id})";
            Logs::writeLog(
                Yii::$app->user->username, 
                null, 
                $logMessage, 
                time()
            );
            return $this->redirect(['index']);
        }
        return $this->renderIsAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Cities model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id){
        $model = $this->findModel($id);
        $logMessage = "{$model->city_name} city was deleted (id:{$model->id})";
        Logs::writeLog(
            Yii::$app->user->username, 
            null, 
            $logMessage, 
            time()
        );
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Cities model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cities the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id){
        if (($model = Cities::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

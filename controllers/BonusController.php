<?php

namespace app\controllers;

use Yii;
use app\models\Bonus;
use app\models\search\BonusSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;
use app\models\Logs;

/**
 * BonusController implements the CRUD actions for Bonus model.
 */
class BonusController extends DefaultController
{
    public $modelClass = 'app\models\Bonus';
    public $modelSearchClass = 'app\models\search\BonusSearch';

    public function actionCreate(){
        $model = new Bonus();
        if ( Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap4\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ( is_array( Yii::$app->request->post('Bonus')['packets'] ) && !empty( Yii::$app->request->post('Bonus')['packets'] ) ) {
                foreach ( Yii::$app->request->post('Bonus')['packets'] as $pK => $packet) {
                   $BonusExceptPacketsModel = new \app\models\BonusExceptPackets;
                   $BonusExceptPacketsModel->bonus_id = $model->id;
                   $BonusExceptPacketsModel->packet_id = $packet;
                   $BonusExceptPacketsModel->save(false);
                }
            }
            $logMessage = "{$model->name} bonus rule was created";
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
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
             return \yii\bootstrap4\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \app\models\BonusExceptPackets::deleteAll(['bonus_id'=>$id]);
            if ( is_array( Yii::$app->request->post('Bonus')['packets'] ) && !empty( Yii::$app->request->post('Bonus')['packets'] ) ) {
                foreach ( Yii::$app->request->post('Bonus')['packets'] as $pK => $packet) {
                   $BonusExceptPacketsModel = new \app\models\BonusExceptPackets;
                   $BonusExceptPacketsModel->bonus_id = $model->id;
                   $BonusExceptPacketsModel->packet_id = $packet;
                   $BonusExceptPacketsModel->save(false);
                }
            }

            $logMessage = "{$model->name} bonus rule was updated";
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

    public function actionDelete($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);
        if ($model->delete()) {
            $logMessage = "{$model->name} bonus rule was deleted";
            Logs::writeLog(
                Yii::$app->user->username, 
                null, 
                $logMessage, 
                time()
            );
            return ['status'=>'success'];
        }
    }


}

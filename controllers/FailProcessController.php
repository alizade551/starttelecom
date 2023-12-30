<?php

namespace app\controllers;

use Yii;
use app\models\FailProcess;
use app\models\FailProcessSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * FailProcessController implements the CRUD actions for FailProcess model.
 */
class FailProcessController extends DefaultController{
    
    public $modelClass = 'app\models\FailProcess';
    public $modelSearchClass = 'app\models\search\FailProcessSearch';

    public function actionView($id){
        $model = \app\models\FailProcess::find()
        ->select('fail_process.*,members.fullname as member_fullaname')
        ->leftJoin('members','members.id=fail_process.member_id')
        ->where(['fail_process.id'=>$id])
        ->asArray()
        ->one();
        return $this->renderIsAjax('view', [
            'model' => $model,
        ]);
    }  
    public function actionDelete($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $model->delete();
        return ['status' => 'success'];
    }
}

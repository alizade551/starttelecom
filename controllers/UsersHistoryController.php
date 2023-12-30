<?php

namespace app\controllers;

use Yii;
use app\models\UsersHistory;
use app\models\search\UsersHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * UsersHistoryController implements the CRUD actions for UsersHistory model.
 */
class UsersHistoryController extends DefaultController
{
    public $modelClass = 'app\models\UsersHistory';
    public $modelSearchClass = 'app\models\search\UsersHistorySearch';

    public function actionView($id)
    {
        return $this->renderIsAjax('view', [
            'model' => $this->findModel($id),
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

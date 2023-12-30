<?php

namespace app\controllers;

use Yii;
use app\models\UsersNote;
use app\models\search\UsersNoteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * UsersNoteController implements the CRUD actions for UsersNote model.
 */
class UsersNoteController extends DefaultController
{
    public $modelClass = 'app\models\UsersNote';
    public $modelSearchClass = 'app\models\search\UsersNoteSearch';

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

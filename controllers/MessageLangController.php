<?php

namespace app\controllers;

use Yii;
use app\models\MessageLang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * MessageLangController implements the CRUD actions for MessageLang model.
 */
class MessageLangController extends DefaultController
{
    public $modelClass = 'app\models\MessageLang';
    
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionIndex()
    {
        if ($this->modelSearchClass) {
            $searchModel = new $this->modelSearchClass;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }else{
            $searchModel = null;
             $dataProvider = new ActiveDataProvider([
                'query' => $this->modelClass::find(),
                'pagination' => [
                    'pageSize' => \Yii::$app->request->cookies->getValue( '_grid_page_size_message_lang', 20),
                ]
            ]);
        }     

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    
}

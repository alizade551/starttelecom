<?php

namespace app\controllers;

use app\components\DefaultController;
use app\models\MessageTemplate;
use app\models\search\MessageTemplateSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Logs;
/**
 * MessageTemplateController implements the CRUD actions for MessageTemplate model.
 */
class MessageTemplateController extends DefaultController
{

    public function actionIndex()
    {
        $searchModel = new MessageTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAddMessageTemplateValidate(){
        $model = new \app\models\MessageTemplate();
        $model->scenario = \app\models\MessageTemplate::SCENARIO_CREATE;
        $request = \Yii::$app->getRequest();
        if ($request->isAjax && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }


    public function actionUpdateMessageTemplateValidate($id){
        $model = \app\models\MessageTemplate::findOne($id);
        $model->scenario = \app\models\MessageTemplate::SCENARIO_UPDATE;
        $request = \Yii::$app->getRequest();
        if ($request->isAjax && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }


    /**
     * Creates a new MessageTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MessageTemplate();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            preg_match_all( '/{(.*?)}/', $model->sms_text, $matches );
            $model->params = implode(",", $matches[0]);
            $model->save(false);
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MessageTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            preg_match_all( '/{(.*?)}/', $model->sms_text, $matches );
   
            $model->params = implode(",", $matches[0]);
            $model->save(false);

            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MessageTemplate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MessageTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MessageTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MessageTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

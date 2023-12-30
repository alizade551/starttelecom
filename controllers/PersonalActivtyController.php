<?php

namespace app\controllers;

use Yii;
use app\models\PersonalActivty;
use app\models\search\PersonalActivtySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * PersonalActivtyController implements the CRUD actions for PersonalActivty model.
 */
class PersonalActivtyController extends DefaultController
{
    public $modelClass = 'app\models\PersonalActivty';
    public $modelSearchClass = 'app\models\search\PersonalActivtySearch';

    public function actionCreate(){
        die;
    }


    public function actionUpdate($id){
        $model = $this->findModel($id);
        $modelPersonalUserActivty = \app\models\PersonalUserActivty::find()
        ->where(['activty_id'=>$id])
        ->asArray()
        ->all();
        $defaultPersonal = [];
        foreach ($modelPersonalUserActivty as $key => $value){
            $defaultPersonal[$key] = $value['member_id'];
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
           if (Yii::$app->request->post('PersonalActivty')['members'] != null) {
               \app\models\PersonalUserActivty::deleteAll(['activty_id' => $id]);
               foreach (Yii::$app->request->post('PersonalActivty')['members'] as $key => $member) {
                   $PersonalUserActivty  = new \app\models\PersonalUserActivty;
                   $PersonalUserActivty->activty_id  = $model->id;
                   $PersonalUserActivty->member_id  = $member;
                   $PersonalUserActivty->save(false);
               }
            return $this->redirect(['index']);
           }
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
            'defaultPersonal' => $defaultPersonal,
        ]);
    }
   
    public function actionPersonalList($q = null, $id = null){
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $query = new \yii\db\Query;
            $query->select('id, fullname AS text')
                ->from('members')
                ->where(['like', 'fullname', $q])
                ->andWhere(['personal' => '1'])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id) {
            if (is_array($id)) {
                $ids = array_values($id);
                $query = new \yii\db\Query;
                $query->select('id, fullname AS text')
                    ->from('members')
                    ->where(['id' => $id])
                    ->andWhere(['personal' => '1'])
                    ->limit(20);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out = $data;
            }
        }
        return $out;
    }

    public function actionDelete($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);

        $model->delete();

        return ['status' => 'success'];
    }
    
}

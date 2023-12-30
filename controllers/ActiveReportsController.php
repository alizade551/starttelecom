<?php

namespace app\controllers;

use Yii;
use app\models\UserDamages;
use app\models\search\UserDamagesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;

/**
 * ActiveReportsController implements the CRUD actions for UserDamages model.
 */
class ActiveReportsController extends DefaultController
{

    public $modelClass = 'app\models\UserDamages';
    public $modelSearchClass = 'app\models\search\UserDamagesActiveSearch';


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = UserDamages::UPDATE_DAMAG;
        $allPersonal = \webvimark\modules\UserManagement\models\User::find()
        ->where(['personal'=>'1'])
        ->orderBy(['fullname'=>SORT_ASC])
        ->asArray()
        ->all();

        if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
            \app\models\DamagePersonal::deleteAll(['damage_id'=>$model->id]);
            \app\models\PersonalActivty::createActivty( $model->user_id,$model->id, Yii::$app->request->post('UserDamages')['personals'] );

            foreach ( Yii::$app->request->post('UserDamages')['personals'] as $pk => $personal) {
                $damagePersonalModel = new \app\models\DamagePersonal;
                $damagePersonalModel->damage_id = $model->id;
                $damagePersonalModel->personal_id = $personal;
                $damagePersonalModel->save(false);
            }
            $damageStatus = ( $model->status == "0" ) ? "1" : "0";
      
            $userModel = \app\models\Users::find()
            ->where(['id'=>$model->user_id])
            ->one();
            $userModel->damage_status = $damageStatus;
            $userModel->save(false);

            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
            'allPersonal' => $allPersonal,
        ]);
    }


    public function actionSyncPersonalList(){
        $model = \app\models\UserDamages::find()
        ->asArray()
        ->all();
        foreach ( $model as $dk => $damage ) {
            if ( $damage['personal'] != null ) {
               foreach (explode(",",$damage['personal']) as $p => $personal) {
                   $damagePersonalModel = new \app\models\DamagePersonal;
                   $damagePersonalModel->personal_id =  $personal;
                   $damagePersonalModel->damage_id = $damage['id'];
                   $damagePersonalModel->save(false);
               }
            }
        }
    }


    public function actionCreate(){
        
      return $this->redirect(['index']);
    }


    public function actionDelete($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);

        $model->delete();

        return ['status' => 'success'];
    }


}

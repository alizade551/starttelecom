<?php

namespace app\controllers;

use Yii;
use app\models\radius\Radacct;
use app\models\search\RadacctAllSearch;
use app\components\DefaultController;

/**
 * RadacctAllController implements the CRUD actions for Radacct model.
 */
class OnlineUsersController extends DefaultController
{
    public $modelClass = 'app\models\radius\Radacct';
    public $modelSearchClass = 'app\models\search\RadacctSearch';


    public function actionPacketAjaxStatus(){
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $status = Yii::$app->request->post('checked');
            $user_id = Yii::$app->request->post('_user_id');

            $u_s_p_i = Yii::$app->request->post('_usp_id');

            $model = \app\models\UsersServicesPackets::find()
            ->where(['user_id' => $user_id,   'id' => $u_s_p_i])
            ->one();


            if (intval($status) == 1) {
                $logMessage = $model->packet->packet_name . ' packet status was enabled';
            } else {
                $logMessage = $model->packet->packet_name . ' packet status was disabled';
            }
            \app\models\Logs::writeLog(
                Yii::$app->user->username, 
                intval($user_id), 
                $logMessage, time()
            );

            $model->status = $status;
   
            if ($model->service->service_alias == "internet") {
       

                $inet_model = \app\models\UsersInet::find()
                ->where(['user_id' => $user_id, 'u_s_p_i' => $u_s_p_i])
                ->one();

                $inet_model->status = $status;
                if ($inet_model->save(false)) {
                    if (intval($status) == 1) {
                        \app\models\radius\Radgroupreply::unblock($inet_model->login,$model->packet->packet_name);
                        \app\components\COA::disconnect( $inet_model->login );
                    } else {
                       \app\models\radius\Radgroupreply::block( $inet_model->login );
                       \app\components\COA::disconnect( $inet_model->login );
                    }
                }
            }
            if ($model->service->service_alias == "tv") {
                $tv_model = \app\models\UsersTv::find()
                ->where(['user_id' => $user_id, 'u_s_p_i' => $u_s_p_i])
                ->one();
                $tv_model->status = $status;
                $tv_model->save(false);
                // api
            }
            if ($model->service->service_alias == "wifi") {
                $wifi_model = \app\models\UsersWifi::find()
                ->where(['user_id' => $user_id, 'u_s_p_i' => $u_s_p_i])
                ->one();
                $wifi_model->status = $status;
                $wifi_model->save(false);

                // UsersWifi get login password and disabled or enabled
            }

            $model->save(false);

            return ['code' => 'successful'];
        }
    }
}

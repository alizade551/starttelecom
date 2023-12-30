<?php

namespace app\controllers;

use Yii;
use app\components\DefaultController;
use app\models\Logs;
use app\models\RequestOrder;
use app\models\search\RequestOrderSearch;
use app\models\Users;
use app\models\UsersHistory;
use app\models\UsersServicesPackets;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use \app\constants\RadiusAttributes;
/**
 * RequestOrderController implements the CRUD actions for RequestOrder model.
 */
class RequestOrderController extends DefaultController
{
    public function actionIndex(){
        $searchModel = new RequestOrderSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPhotoUpload(){
        $uploadHandler = new \app\components\UploadHandler(['accept_file_types' => '/\.(gif|jpe?g|png)$/i']);
    }

    public function actionServicePacketDetail($id){
        $model = \app\models\UsersServicesPackets::find()
        ->where(['id' => $id])
        ->one();
        return $this->renderIsAjax('detail-service-packet', ['model' => $model]);
    }

    public function actionChangePacketValidate(){
        $model = new \app\models\UsersServicesPackets();
        $model->scenario = \app\models\UsersServicesPackets::CHANGE_PACKET;
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionChangePacket($id){
        $model = UsersServicesPackets::find()
        ->where(['id' =>$id])
        ->one();
        $model->scenario = UsersServicesPackets::CHANGE_PACKET;

        $packetsModel = \app\models\Packets::find()
        ->where(['service_id'=>$model->service_id])
        ->orderBy(['packet_name'=>SORT_ASC])
        ->all();

        $userModel = \app\models\Users::find()->where(['id'=>$model['user_id']])->asArray()->one();

        if ($model->service->service_alias == "internet") {

            $staticIpModel = \app\models\IpAdresses::find()
            ->where(['id'=>$model->usersInet->static_ip])
            ->andWhere(['type'=>'1'])
            ->orWhere(['status'=>'0'])
            ->asArray()
            ->all();

            $variables = [
                'model'=>$model,
                'packetsModel'=>$packetsModel,
                'staticIpModel'=>$staticIpModel
            ];
        }


        if ($model->service->service_alias == "tv") {
            $variables = [
                'model'=>$model,
                'packetsModel'=>$packetsModel,
            ];
        }

        if ($model->service->service_alias == "wifi") {
   
            $variables = [
                'model'=>$model,
                'packetsModel'=>$packetsModel,
                
            ];
        }

        if ($model->service->service_alias == "voip") {
   
            $variables = [
                'model'=>$model,
                'packetsModel'=>$packetsModel,
                
            ];
        }


          if ( $model->load(Yii::$app->request->post()) && $model->validate() ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $packetId = Yii::$app->request->post('UsersServicesPackets')['packet_id'];
            $staticIP = isset(Yii::$app->request->post('UsersServicesPackets')['static_ip_address']) ? Yii::$app->request->post('UsersServicesPackets')['static_ip_address'] : null;

            if ($model == null) {
                return ['status'=>'error','message'=>Yii::t('app','Model not found,Please contact web developer :)')];
            }

            $userModel = Users::find()
            ->where(['id' => $model->user_id])
            ->one();

            if ($userModel == null) {
                 return ['status'=>'error','message'=>Yii::t('app','userModel not found,Please contact web developer :)')];
            }

            $newPacketQuery = \app\models\Packets::find()
            ->where(['id' => $packetId])
            ->asArray()
            ->one();

            $memberUsername = Yii::$app->user->username;

            if ($newPacketQuery == null) {
                 return ['status'=>'error','message'=>Yii::t('app','New packet not found,Please contact web developer :)')];
            }


            if ( $model->service->service_alias == "internet" ) {
                $routerModel = \app\models\Routers::find()
                ->where(['id' => $userModel->district->router_id])
                ->asArray()
                ->one();

                if ($routerModel == null) {
                    return ['status'=>'error','message'=>Yii::t('app','routerModel not found,Please contact web developer :)')];
                }


                $cgnModel = \app\models\CgnIpAddress::find()
                ->where(['router_id'=>$routerModel['id']])
                ->andWhere(['is', 'inet_login', new \yii\db\Expression('null')])
                ->orderBy(['internal_ip'=>SORT_ASC])
                ->one();

                if ( $cgnModel == null ) {
                    return [
                        'status'=>'error',
                        'message'=>Yii::t(
                            'app',
                            'Nat was not defined for {router} router',
                            [
                                'router'=>$routerModel['name'],
                            ]
                        )
                    ];
                }



                $inetModel = \app\models\UsersInet::find()
                ->where(['u_s_p_i' => $model->id])
                ->one();

                if ($inetModel == null) {
                    return ['status'=>'error','message'=>Yii::t('app','inetModel not found,Please contact web developer :)')];
                }
                if (empty($staticIP)) {
                    if ($inetModel->static_ip != "") {
                        $staticIpModel = \app\models\IpAdresses::find()
                            ->where(['id' => $inetModel->static_ip])
                            ->one();
                        if ($staticIpModel == null) {
                              return ['status'=>'error','message'=>Yii::t('app','Static ip to ordinary changes staticIpModel not found,Packet was not changed.')];
                        }

                    }
                }else{
                    $findStaticIp = \app\models\IpAdresses::find()
                    ->where(['id'=>$staticIP])
                    ->asArray()
                    ->one();

                    if ( $findStaticIp == null ) {
                          return ['status'=>'error','message'=>Yii::t('app','Static ip not found,Packet was not changed.')];
                    }
                }
            }


            $logMessage = "{$model->packet->packet_name} packet has been changed to {$newPacketQuery['packet_name']} by {$memberUsername}";
            Logs::writeLog(Yii::$app->user->username, intval($model->user_id), $logMessage, time());
            $model->packet_id = $packetId;
            if ($model->save(false)) {
                    
    
                

                $userModel->tariff = \app\models\UserBalance::CalcUserTariffDaily($userModel->id)['per_total_tariff'];
                $userModel->save(false);
                ///
                if ($model->service->service_alias == "internet") {
                    if ( empty( $staticIP ) ) {
                        if ( $inetModel->static_ip != "" ) {
                            $staticIpModel->status = '0';
                            if ($staticIpModel->save(false)) {
                                $inetModel->static_ip = '';
                            }
                            $internalIp = $cgnModel['internal_ip'];

                        }else{

                            $findNat = \app\models\CgnIpAddress::find()
                            ->where(['inet_login'=>$inetModel->login])
                            ->one();

                            if ( $findNat != null ) {
                                $internalIp = $findNat['internal_ip'];
                            }else{
                                $internalIp = $cgnModel['internal_ip'];
                            }

                        }
                        $inetModel->packet_id = $newPacketQuery['id'];
                        if ($inetModel->save(false)) {
                            $cgnModel->inet_login = $inetModel->login;
                            if ( $cgnModel->save(false) ) {
                                \app\components\MikrotikQueries::dhcpSetMac(
                                    $inetModel->login,
                                    $newPacketQuery['download']."k"."/".$newPacketQuery['upload']."k",
                                    $internalIp,
                                    $routerModel['nas'],
                                    $routerModel['username'],
                                    $routerModel['password'],
                                    "dhcpSetMac",
                                    [
                                        'login'=>$inetModel->login,
                                        'rateLimit'=>$newPacketQuery['download']."k"."/".$newPacketQuery['upload']."k",
                                        'ipAddress'=>$cgnModel['internal_ip'],
                                        'nas'=>$routerModel['nas'],
                                        'router_username'=>$routerModel['username'],
                                        'router_password'=>$routerModel['password'],
                                    ]
                                );
                            }
                            return [
                                'status' => 'success',
                                 'url' => \yii\helpers\Url::to(['index?RequestOrderSearch[fullname]='.rawurlencode($userModel->fullname)], true)
                             ];
                        }
                    } else {
                        if ( $inetModel->static_ip != "" ) {
                            $findNat = \app\models\CgnIpAddress::find()
                            ->where(['inet_login'=>$inetModel->login])
                            ->one();

                            if ( $findNat != null ) {
                                $findNat->inet_login = null;
                                $findNat->save( false );
                            }

                            $staticIpQuery = \app\models\IpAdresses::find()
                            ->where(['id' => $inetModel->static_ip])
                            ->one();
                            $staticIpQuery->status = 0;
                            $staticIpQuery->save(false);
                            
                        }

                        $staticIpNewQuery = \app\models\IpAdresses::find()
                        ->where(['public_ip' => $findStaticIp['public_ip']])
                        ->one();
                        $staticIpNewQuery->status = '1';
                        if ($staticIpNewQuery->save(false)) {
                            $inetModel->packet_id = $newPacketQuery['id'];
                            $inetModel->static_ip = $findStaticIp['id'];
                            if ($inetModel->save(false)) {

                                \app\components\MikrotikQueries::dhcpSetMac(
                                    $inetModel->login,
                                    $newPacketQuery['download']."k"."/".$newPacketQuery['upload']."k",
                                    $findStaticIp['public_ip'],
                                    $routerModel['nas'],
                                    $routerModel['username'],
                                    $routerModel['password'],
                                    "dhcpSetMac",
                                    [
                                        'login'=>$inetModel->login,
                                        'rateLimit'=>$newPacketQuery['download']."k"."/".$newPacketQuery['upload']."k",
                                        'ipAddress'=>$findStaticIp['public_ip'],
                                        'nas'=>$routerModel['nas'],
                                        'router_username'=>$routerModel['username'],
                                        'router_password'=>$routerModel['password'],
                                    ]
                                );

                                return [
                                    'status' => 'success', 
                                    'url' => \yii\helpers\Url::to(['index?RequestOrderSearch[fullname]='.rawurlencode($userModel->fullname)], true)
                                ];
                            }
                        }

                    }

                }

                if ($model->service->service_alias == "tv") {
                    $tvModel = \app\models\UsersTv::find()
                    ->where(['u_s_p_i' => $model->id])
                    ->one();
                    $tvModel->packet_id = $newPacketQuery['id'];
                    if ( $tvModel->save(false)) {
                       // Tv api here 
                        return [
                            'status' => 'success', 
                            'url' => \yii\helpers\Url::to(['index?RequestOrderSearch[fullname]='.rawurlencode($userModel->fullname)], true)
                        ];
                    }
                   }  

                if ($model->service->service_alias == "voip") {
                    $voipModel = \app\models\UsersVoip::find()
                    ->where(['u_s_p_i' => $model->id])
                    ->one();
                    $voipModel->phone_number = $model->phone_number;
                    $voipModel->save(false);
                    return [
                        'status' => 'success', 
                        'url' => \yii\helpers\Url::to(['index?RequestOrderSearch[fullname]='.rawurlencode($userModel->fullname)], true)
                    ];
                } 

            }
        }




        return $this->renderIsAjax(
            'change-packet',$variables
        );
    }


    public function actionUpdateValidate($id){
        $model = \app\models\RequestOrder::findOne($id);
        $model->scenario = \app\models\RequestOrder::SCENARIO_UPDATE;
        $request = \Yii::$app->getRequest();
        if ($request->isAjax && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }


    public function actionUpdate($id){
        $model = $this->findModel($id);
        $model->scenario = \app\models\RequestOrder::SCENARIO_UPDATE;
        $passwordIsEmpty = empty( $model->password );
        $siteConfig = \app\models\SiteConfig::find()->asArray()->one();

        if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
            $post_photos = Yii::$app->request->post('RequestOrder')['photos'];

            if (Yii::$app->request->post('RequestOrder')['selected_service_form'] != "") {
                $services = "";
                foreach (Yii::$app->request->post('RequestOrder')['selected_service_form'] as $key => $value) {
                    $services .= $value . ",";
                }
                $services = substr($services, 0, -1);
                $model->selected_services = $services;
            }
            if ( $passwordIsEmpty == true ) {
                $model->password = password_hash( $model->password, PASSWORD_DEFAULT );
            }


            if ( $model->save(false) ) {
                   $post_photos = Yii::$app->request->post('RequestOrder')['photos'];
                    if ($post_photos != "") {
                      $photos_array = explode("@", $post_photos);
                      \app\models\UserPhotos::deleteAll(['user_id'=>$model->id]);
                        foreach ($photos_array as $key_phts => $value_phts) {
                          $userPhotoModel = new \app\models\UserPhotos;
                          $userPhotoModel->user_id = $model->id;
                          $userPhotoModel->position = $key_phts;
                          $userPhotoModel->photo_url = $value_phts;
                          $userPhotoModel->save(false);
                        }
                    }
                $logMessage = 'Order was updated';
                Logs::writeLog(Yii::$app->user->username, intval($model->id), $logMessage, time());
                return $this->redirect(['index']);
            }
        }
        return $this->renderIsAjax('update', [
            'model' => $model,
            'siteConfig' => $siteConfig
        ]);
    }



    public function actionView($id){
        $model = \app\models\Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();
        return $this->renderAjax('view', [
            'model' => $model,
        ]);
    }


    public function actionCreateValidate(){
        $model = new RequestOrder();
        $model->scenario = \app\models\RequestOrder::SCENARIO_CREATE;
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }




    public function actionCreate(){

        $model = new RequestOrder();
        $siteConfig = \app\models\SiteConfig::find()->asArray()->one();

        $services = "";
        if ( $model->load(Yii::$app->request->post()) && $model->validate() ) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            }

            if (Yii::$app->request->post('RequestOrder')['selected_service_form'] != "") {
                foreach (Yii::$app->request->post('RequestOrder')['selected_service_form'] as $key => $value) {
                    $services .= $value . ",";
                }
                $services = substr($services, 0, -1);
                $model->selected_services = $services;
                $model->phone = Yii::$app->request->post('RequestOrder')['phone'];
                $model->extra_phone = Yii::$app->request->post('RequestOrder')['extra_phone'];
                $model->password = password_hash($model->password, PASSWORD_DEFAULT);
                if ($model->save()) {
                    if ( Yii::$app->request->post('RequestOrder')['photos'] != "") {
                        $post_photos = Yii::$app->request->post('RequestOrder')['photos'];
                        $explode_photos = explode('@', $post_photos);
                        $postion_photo = 0;
                        foreach ($explode_photos as $key_ex_photo => $value_ex_photo) {
                            $postion_photo++;
                            $product_photos_model = new \app\models\UserPhotos;
                            $product_photos_model->user_id = $model->id;
                            $product_photos_model->photo_url = $value_ex_photo;
                            $product_photos_model->position = $postion_photo;
                            $product_photos_model->save(false);
                        }
                    }

                    $userHistoryText = "User added with pending status";
                    UsersHistory::AddHistory( intval($model->id),Yii::$app->user->username, $userHistoryText, time() );
                  
                    Logs::writeLog(Yii::$app->user->username, intval($model->id), $userHistoryText, time());
                    return ['status' => 'success', 'url' => \yii\helpers\Url::to(['request-order/index'], true)];
                }
            } else {
                return $this->renderIsAjax('create', [
                    'model' => $model,
                    'siteConfig' => $siteConfig
                ]);
            }

        }
        return $this->renderIsAjax('create', [
            'model' => $model,
            'siteConfig' => $siteConfig
        ]);
    }


    public function actionAddingPacketValidate(){
        $model = new \app\models\UsersServicesPackets();
        $model->scenario = UsersServicesPackets::ADDING_PACKET;
        
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap4\ActiveForm::validate($model);
        }
    }

    public function actionAddingPacket($id){
        $model = new UsersServicesPackets();
        $model->scenario = UsersServicesPackets::ADDING_PACKET;
        $userModel = Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();

        $routerModel = \app\models\Routers::find()
        ->where(['id' => $userModel->district->router_id])
        ->asArray()
        ->one();

        $siteConfig = \app\models\SiteConfig::find()->asArray()->one();

        $memberUsername = Yii::$app->user->username;

        if ($userModel != null) {
            $UsersServicesPacketsModel = \app\models\UsersServicesPackets::find()
                ->joinWith(['user','service','packet'])
                ->where(['user_id'=>$userModel->id])
                ->all();
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');

        }

        if ( $model->load(Yii::$app->request->post()) && $model->validate() ) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            }
            if ($model->validate()) {
                $serviceModel = \app\models\Services::find()
                    ->where(['id' => Yii::$app->request->post('UsersServicesPackets')['service_id']])
                    ->one();

                $checkService = \app\models\UsersSevices::find()
                ->where([
                    'user_id' => $userModel->id,
                    'service_id' => $serviceModel->id
                ])
                ->all();

                if ($checkService == null) {
                    $userServiceModel = new \app\models\UsersSevices;
                    $userServiceModel->service_id = $serviceModel->id;
                    $userServiceModel->user_id = $userModel->id;
                    $userServiceModel->user_id = $userModel->id;
                    $userServiceModel->save(false);
                }
             
                    if ($serviceModel->service_alias == "internet") {

                        if ( $routerModel == null ) {
                            return [
                                'status'=>'error',
                                'message'=>Yii::t(
                                    'app',
                                    '{router} router has not been selected for {district} district',
                                    [
                                        'district'=>$userModel->district->district_name,
                                        'router'=>$routerModel['name'],
                                    ]
                                )
                            ];
                        }


                        $cgnModel = \app\models\CgnIpAddress::find()
                        ->where(['router_id'=>$routerModel['id']])
                        ->andWhere(['is', 'inet_login', new \yii\db\Expression('null')])
                        ->orderBy(['internal_ip'=>SORT_ASC])
                        ->one();

                        if ( $cgnModel == null ) {
                            return [
                                'status'=>'error',
                                'message'=>Yii::t(
                                    'app',
                                    'Nat was not defined for {router} router',
                                    [
                                        'router'=>$routerModel['name'],
                                    ]
                                )
                            ];
                        }
          
          
                        if ( isset(Yii::$app->request->post('UsersServicesPackets')['port_type']) ) {

                            if ( Yii::$app->request->post('UsersServicesPackets')['port_type'] == "switch" ) {
                                if ( isset( Yii::$app->request->post('UsersServicesPackets')['devices']  ) ) {
                                    if ( Yii::$app->request->post('UsersServicesPackets')['devices'] != "" ) {
                                        if ( isset( Yii::$app->request->post('UsersServicesPackets')['switch_port'] ) ) {

                                            $checkSwitchPort = \app\models\SwitchPorts::find()
                                            ->where(['device_id'=>Yii::$app->request->post('UsersServicesPackets')['devices']])
                                            ->andWhere(['id'=> Yii::$app->request->post('UsersServicesPackets')['switch_port'] ])
                                            ->andWhere(['status'=>'0'])
                                            ->andWhere(['is', 'u_s_p_i', new \yii\db\Expression('null')])
                                            ->one();
                                          

                                            if ($checkSwitchPort == null) {
                                                return [
                                                    'status'=>'error',
                                                    'message'=>Yii::t(
                                                        'app',
                                                        'Port unused or something went wrong',
                                                    )
                                                ];
                                            }
                                        }
                                    }
                                }
                            }elseif ( Yii::$app->request->post('UsersServicesPackets')['port_type'] == "epon" || Yii::$app->request->post('UsersServicesPackets')['port_type'] == "gpon" || Yii::$app->request->post('UsersServicesPackets')['port_type'] == "xpon") {
                                if (  isset( Yii::$app->request->post('UsersServicesPackets')['devices'] ) ) {

                                    if ( Yii::$app->request->post('UsersServicesPackets')['devices'] != "" ) {
                                        if ( isset( Yii::$app->request->post('UsersServicesPackets')['box'] ) ) {
                                            if ( Yii::$app->request->post('UsersServicesPackets')['box'] != "" ) {
                                                 if ( isset( Yii::$app->request->post('UsersServicesPackets')['box_port'] ) ) {
                                                     if ( Yii::$app->request->post('UsersServicesPackets')['box_port'] != "" ) {
                                                        $checkBoxPort = \app\models\EgonBoxPorts::find()
                                                        ->andWhere(['id'=> Yii::$app->request->post('UsersServicesPackets')['box_port'] ])
                                                        ->andWhere(['status'=>'0'])
                                                        ->andWhere(['is', 'u_s_p_i', new \yii\db\Expression('null')])
                                                        ->one();

                                                        if ( $checkBoxPort == null ) {
                                                            return [
                                                                'status'=>'error',
                                                                'message'=>Yii::t(
                                                                    'app',
                                                                    'Port unused or something went wrong',
                                                                )
                                                            ];
                                                        }


                                                     }
                                                 }
                                            }
                                        }
                                    }
                                }
                            }

                        }

                        $model->packet_id = Yii::$app->request->post('UsersServicesPackets')['packet_tags'];
                        if ($model->save(false)) {
                        if ( isset(Yii::$app->request->post('UsersServicesPackets')['port_type']) ) {
                            if ( Yii::$app->request->post('UsersServicesPackets')['port_type'] == "switch" ) {
                                if ( isset( Yii::$app->request->post('UsersServicesPackets')['devices']  ) ) {
                                    if ( Yii::$app->request->post('UsersServicesPackets')['devices'] != "" ) {
                                        if ( isset( Yii::$app->request->post('UsersServicesPackets')['switch_port'] ) ) {
                                            $switchPortsModel = \app\models\SwitchPorts::find()
                                            ->where(['device_id'=>Yii::$app->request->post('UsersServicesPackets')['devices']])
                                            ->andWhere(['id'=> Yii::$app->request->post('UsersServicesPackets')['switch_port'] ])
                                            ->andWhere(['status'=>'0'])
                                            ->andWhere(['is', 'u_s_p_i', new \yii\db\Expression('null')])
                                            ->one();
                                            $switchPortsModel->u_s_p_i = $model->id;
                                            $switchPortsModel->status = '1';
                                            $switchPortsModel->save(false);
                                        }
                                    }
                                }
                            }elseif ( Yii::$app->request->post('UsersServicesPackets')['port_type'] == "epon" || Yii::$app->request->post('UsersServicesPackets')['port_type'] == "gpon" || Yii::$app->request->post('UsersServicesPackets')['port_type'] == "xpon"  ) {
                                if (  isset( Yii::$app->request->post('UsersServicesPackets')['devices'] ) ) {

                                    if ( Yii::$app->request->post('UsersServicesPackets')['devices'] != "" ) {
                                        if ( isset( Yii::$app->request->post('UsersServicesPackets')['box'] ) ) {
                                            if ( Yii::$app->request->post('UsersServicesPackets')['box'] != "" ) {
                                                 if ( isset( Yii::$app->request->post('UsersServicesPackets')['box_port'] ) ) {
                                                     if ( Yii::$app->request->post('UsersServicesPackets')['box_port'] != "" ) {
                                                        $boxPortModel = \app\models\EgonBoxPorts::find()
                                                        ->andWhere(['id'=> Yii::$app->request->post('UsersServicesPackets')['box_port'] ])
                                                        ->andWhere(['status'=>'0'])
                                                        ->andWhere(['is', 'u_s_p_i', new \yii\db\Expression('null')])
                                                        ->one();

                                                        $boxPortModel->u_s_p_i = $model->id;
                                                        $boxPortModel->status = '1';
                                                        $boxPortModel->save(false);
                                                     }
                                                 }
                                            }
                                        }
                                    }
                                }
                            }

                        }

                            $inetModel = new \app\models\UsersInet;
                            $inetModel->user_id = $userModel->id;
                            $inetModel->router_id = $routerModel['id'];
                            $inetModel->u_s_p_i = $model->id;
                            $inetModel->packet_id = Yii::$app->request->post('UsersServicesPackets')['packet_tags'];
                            $inetModel->mac_address = Yii::$app->request->post('UsersServicesPackets')['mac_address'];

                            if (isset( Yii::$app->request->post("UsersServicesPackets")['static_ip_address'] )) {
                                if (Yii::$app->request->post("UsersServicesPackets")['static_ip_address'] != "") {
                                    $inetModel->static_ip = Yii::$app->request->post("UsersServicesPackets")['static_ip_address'];
                                }
                            }

                            $inetModel->status = 1;
                            $inetModel->created_at = time();
                            if ( $model->packet_password != "" ) {
                                $inetModel->password = $model->packet_password;
                            }else{
                                $inetModel->password = $model->randomString;
                            }

                            if ( $inetModel->save(false) ) {
                         
                                
                                    $inetModel->login = Yii::$app->request->post("UsersServicesPackets")['mac_address'];
                                    if ( $inetModel->save(false) ) {
                                        $packetModel = \app\models\Packets::find()
                                        ->where(['id' => Yii::$app->request->post('UsersServicesPackets')['packet_tags']])
                                        ->one();

                                        if (!empty(Yii::$app->request->post("UsersServicesPackets")['static_ip_address'])) {
                                            $staticIpModel = \app\models\IpAdresses::find()
                                            ->where(['id' => Yii::$app->request->post("UsersServicesPackets")['static_ip_address']])
                                            ->one();
                                     
                                            $staticIpModel->status = "1";
                                            if ( $staticIpModel->save(false) ) {
                                                $logMessage = "Inet service {$packetModel->packet_name} packet with {$staticIpModel['public_ip']} ip was added";
                                                Logs::writeLog( Yii::$app->user->username, intval( $userModel->id ), $logMessage, time() );

                                                 \app\components\MikrotikQueries::dhcpAddMac(
                                                    $inetModel->login, 
                                                    $packetModel['download']."k"."/".$packetModel['upload']."k",
                                                    $staticIpModel->public_ip,
                                                    "iNet_yes",
                                                    $routerModel['nas'], 
                                                    $routerModel['username'], 
                                                    $routerModel['password'],
                                                    "dhcpAddMac",
                                                    [
                                                        'login'=> $inetModel->login,
                                                        'rateLimit'=> $packetModel['download']."k"."/".$packetModel['upload']."k",
                                                        'ipAddress'=> $staticIpModel->public_ip,
                                                        'addressList'=>"iNet_yes",
                                                        'nas'=>$routerModel['nas'],
                                                        'router_username'=>$routerModel['username'],
                                                        'router_password'=>$routerModel['password'],
                                                    ]
                                                 );
                                            }

                                        } else {
                                            $logMessage = "Inet service {$packetModel->packet_name} packet was added";
                                                Logs::writeLog(Yii::$app->user->username, intval($userModel->id), $logMessage, time() );
                                                    
                                                $cgnModel->inet_login = $inetModel->login;
                                                if ( $cgnModel->save( false ) ) {
                                                    \app\components\MikrotikQueries::dhcpAddMac(
                                                        $inetModel->login, 
                                                        $packetModel['download']."k"."/".$packetModel['upload']."k",
                                                        $cgnModel['internal_ip'],
                                                        "iNet_yes",
                                                        $routerModel['nas'], 
                                                        $routerModel['username'], 
                                                        $routerModel['password'],
                                                        "dhcpAddMac",
                                                        [
                                                            'login'=> $inetModel->login,
                                                            'rateLimit'=> $packetModel['download']."k"."/".$packetModel['upload']."k",
                                                            'ipAddress'=> $cgnModel['internal_ip'],
                                                            'addressList'=>"iNet_yes",
                                                            'nas'=>$routerModel['nas'],
                                                            'router_username'=>$routerModel['username'],
                                                            'router_password'=>$routerModel['password'],
                                                        ]
                                                     );
                                                }

                                        }
                                    }
                                }
                        }
                    }
                    if ($serviceModel->service_alias == "wifi") {
                        $packetModel = \app\models\Packets::find()
                        ->where(['id' => Yii::$app->request->post('UsersServicesPackets')['packet_tags']])
                        ->one();
                        $model->packet_id = Yii::$app->request->post('UsersServicesPackets')['packet_tags'];

                
                        if ($model->save(false)) {
                            $wifiModel = new \app\models\UsersWifi;
                            $wifiModel->password = $model->randomString;
                            $wifiModel->user_id = $userModel->id;
                            $wifiModel->u_s_p_i = $model->id;
                            $wifiModel->packet_id = Yii::$app->request->post("UsersServicesPackets")['packet_tags'];
                            $wifiModel->status = 1;
                            $wifiModel->created_at = time();
                            if ($wifiModel->save(false)) {
                                 $wifiModel->login = $siteConfig['wifi_ppoe_login_start'] . sprintf("%06d", $wifiModel->id);
                                if ($wifiModel->save(false)) {
                                    $packetModel = \app\models\Packets::find()
                                    ->where(['id' => Yii::$app->request->post('UsersServicesPackets')['packet_tags']])
                                    ->one();   

                                    $logMessage = "{$packetModel->service->service_name} service {$packetModel->packet_name} packet was added";
                                    Logs::writeLog(Yii::$app->user->username, intval($userModel->id), $logMessage, time());
                                }
                            }
                        }
                    }

                    if ($serviceModel->service_alias == "tv") {
                        $usersServicesPacketsModel = new UsersServicesPackets();
                        $usersServicesPacketsModel->user_id = $userModel->id;
                        $usersServicesPacketsModel->service_id = $serviceModel->id;
                        $usersServicesPacketsModel->packet_id = Yii::$app->request->post('UsersServicesPackets')['packet_tags'];
                        $usersServicesPacketsModel->status = 0;
                        $usersServicesPacketsModel->created_at = time();
                        if ( $usersServicesPacketsModel->save(false)) {
                            $tvModel = new \app\models\UsersTv;
                            $tvModel->user_id = $userModel->id;
                            $tvModel->u_s_p_i = $usersServicesPacketsModel->id;
                            $tvModel->packet_id = Yii::$app->request->post('UsersServicesPackets')['packet_tags'];
                            $tvModel->card_number = Yii::$app->request->post("UsersServicesPackets")["property"]['card_number'];
                            $tvModel->status = 1;
                            $tvModel->created_at = time();
                            $tvModel->save(false);
                        }
                        $logMessage = "{$usersServicesPacketsModel->service->service_name} service {$usersServicesPacketsModel->packet->packet_name} packet was added";
                        Logs::writeLog(Yii::$app->user->username, intval($userModel->id), $logMessage, time());
                        

                        $userModel->tariff = \app\models\UserBalance::CalcUserTariffDaily($userModel->id)['per_total_tariff'];
                        $userModel->save(false);

                        return [
                            'status' => 'success',
                            'url' => \yii\helpers\Url::to(['index?RequestOrderSearch[fullname]='.rawurlencode($userModel->fullname)], true)
                        ];
                    }

                    if ($serviceModel->service_alias == "voip") {
                        $packetModel = \app\models\Packets::find()
                        ->where(['id' => Yii::$app->request->post('UsersServicesPackets')['packet_tags']])
                        ->one();
                        $model->packet_id = Yii::$app->request->post('UsersServicesPackets')['packet_tags'];
                        if ( $model->save(false)) {

                            $voIpModel = new \app\models\UsersVoip;
                            $voIpModel->user_id = $userModel->id;
                            $voIpModel->u_s_p_i = $model->id;
                            $voIpModel->packet_id = Yii::$app->request->post('UsersServicesPackets')['packet_tags'];
                            $voIpModel->phone_number = Yii::$app->request->post("UsersServicesPackets")['phone_number'];
                            $voIpModel->status = 1;
                            $voIpModel->created_at = time();
                            $voIpModel->save(false);

                            $logMessage = "{$packetModel->service->service_name} service {$packetModel->packet_name} packet was added";
                            Logs::writeLog(Yii::$app->user->username, intval($userModel->id), $logMessage, time());
           
                        }
                        
                        $userModel->tariff = \app\models\UserBalance::CalcUserTariffDaily($userModel->id)['per_total_tariff'];
                        $userModel->save(false);

                        return [
                            'status' => 'success',
                            'url' => \yii\helpers\Url::to(['index?RequestOrderSearch[fullname]='.rawurlencode($userModel->fullname)], true)
                        ];
                    }
                    $userModel->tariff = \app\models\UserBalance::CalcUserTariffDaily($userModel->id)['per_total_tariff'];
                    $userModel->save(false);
                    return [
                        'status' => 'success',
                        'url' => \yii\helpers\Url::to(['index?RequestOrderSearch[fullname]='.rawurlencode($userModel->fullname)], true)
                    ];
            }
        }
        return $this->renderIsAjax('adding-packet', [
            'model' => $model,
            'model_user' => $userModel,
            'routerModel' => $routerModel,
            'UsersServicesPacketsModel' => $UsersServicesPacketsModel,
        ]);
    }





    public function actionCancelOrder($id){
        $model = RequestOrder::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();

        $services_user_paccket = UsersServicesPackets::find()
        ->where(['user_id' => $model->id])
        ->all();

        $router_model = \app\models\Routers::find()
        ->where(['id' => $model->district->router_id])
        ->asArray()
        ->one();

        if ($model->second_status == '4') {
            $model->status = 3;
            $model->second_status = null;
            $model->save(false);

            $userHistoryText = "User status was changed reconnect to Archive";
            UsersHistory::AddHistory( intval( $model->id ), Yii::$app->user->username, $userHistoryText, time() );
            Logs::writeLog( Yii::$app->user->username , intval( $model->id ), $userHistoryText, time());

            return $this->redirect(['index']);
        }

        if ($model->second_status == '5') {

            //deleting added new services
            $model_user_services_new = \app\models\UsersServicesPackets::find()
            ->where(['status' => 0])
            ->andWhere(['user_id' => $model->id])
            ->all();
            foreach ($model_user_services_new as $key => $value_model_user_services_new) {
                if ($value_model_user_services_new->service->service_alias == "internet") {
                    $inet_model = \app\models\UsersInet::find()
                    ->where(['user_id' => $model->id, 'u_s_p_i' => $value_model_user_services_new->id])
                    ->one();

                    \app\models\MikrotikQueries::dhcpRemoveMac(
                        $inet_model->login, 
                        $router_model['nas'], 
                        $router_model['username'], 
                        $router_model['password'],
                        "dhcpRemoveMac",
                        [
                            'login'=>$inet_model->login,
                            'nas'=> $router_model['nas'],
                            'router_username'=>$router_model['username'],
                            'router_password'=>$router_model['password'],
                        ]
                    );


                    $cNatModel = \app\models\CgnIpAddress::find()
                    ->where(['inet_login'=>$inet_model->login])
                    ->one();

                    if ( $cNatModel != null ) {
                        $cNatModel->inet_login = null;
                        $cNatModel->save(false);
                    }


                    if ($inet_model->static_ip) {
                        $staticIpModel = \app\models\IpAdresses::find()
                        ->where(['id' => $inet_model->static_ip])
                        ->one();
                        $staticIpModel->status = "0";
                        $staticIpModel->save(false);
                    }
                    $inet_model->delete();
                }

                if ($value_model_user_services_new->service->service_alias == "tv") {
                    $tv_model = \app\models\UsersTv::find()
                    ->where(['user_id' => $model->id, 'u_s_p_i' => $value_model_user_services_new->id])
                    ->one()
                    ->delete();
                    // api goes to here

                }
                if ($value_model_user_services_new->service->service_alias == "wifi") {
                    $wifi_model = \app\models\UsersWifi::find()
                    ->where(['user_id' => $model->id, 'u_s_p_i' => $value_model_user_services_new->id])
                    ->one()
                    ->delete();
                    // api goes to here
                }
                $value_model_user_services_new->delete();
            }

            $model->status = 1;
            $model->second_status = '0';
            $model->tariff = \app\models\UserBalance::CalcUserTariffDaily($model->id)['per_total_tariff'];
            $model->save(false);

            $userHistoryText =  'New service was canceled';
            UsersHistory::AddHistory( intval( $model->id ),Yii::$app->user->username, $userHistoryText, time() );
            Logs::writeLog( Yii::$app->user->username, intval($model->id), $userHistoryText, time() );
            return $this->redirect(['index']);
        }

        if ( $model->status == 0 ) {
            foreach ($services_user_paccket as $key => $value_ser) {
                if ($value_ser->service->service_alias == "internet") {
                    $internet_service = \app\models\UsersInet::find()
                    ->where(['user_id' => $model->id])
                    ->all();
                    foreach ($internet_service as $key => $serv) {

                        \app\components\MikrotikQueries::dhcpRemoveMac(
                            $serv->login, 
                            $router_model['nas'], 
                            $router_model['username'], 
                            $router_model['password'],
                            "dhcpRemoveMac",
                            [
                                'login'=>$serv->login,
                                'nas'=> $router_model['nas'],
                                'router_username'=>$router_model['username'],
                                'router_password'=>$router_model['password'],
                            ]
                        );

                        $cNatModel = \app\models\CgnIpAddress::find()
                        ->where(['inet_login'=>$serv->login])
                        ->one();

                        if ( $cNatModel != null ) {
                            $cNatModel->inet_login = null;
                            $cNatModel->save(false);
                        }




                        if ($serv->static_ip) {
                            $staticIpModel = \app\models\IpAdresses::find()
                            ->where(['id' => $serv->static_ip])
                            ->one();
                            $staticIpModel->status = "0";
                            $staticIpModel->save(false);
                        }
                        $serv->delete();
                    }

                }
                if ($value_ser->service->service_alias == "tv") {
                    $tv_service = \app\models\UsersTv::deleteAll(['user_id' => $model->id]);
                    //users tv packets delete all
                }

                if ($value_ser->service->service_alias == "wifi") {
                    $wifi_service = \app\models\UsersTv::deleteAll(['user_id' => $model->id]);
                    // users wifi packets delete all
                }
                
                if ($value_model_user_services_new->service->service_alias == "voip") {
                    $voip_model = \app\models\UsersVoip::find()
                    ->where(['user_id' => $model->id, 'u_s_p_i' => $value_model_user_services_new->id])
                    ->one()
                    ->delete();
                    // api goes to here
                }


            }
            // all counting table data deleting
            UsersServicesPackets::deleteAll(['user_id' => $model->id]);
            \app\models\UserPhotos::deleteAll(['user_id' => $model->id]);
            $model->delete();
            return $this->redirect(['index']);

        }
    }



    public function actionAddingCordinate($id){
        $model = \app\models\Users::find()
        ->where(['id'=>$id])
        ->one();
        $model->scenario = \app\models\Users::CORDINATE_UPDATE;
        if ( $model == null ) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $siteConfig = \app\models\SiteConfig::find()->asArray()->one();

        $districtModel = \app\models\District::find()
        ->where(['id'=>$model->district_id])
        ->one();

        if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('adding-cordinate', [
            'model' => $model,
            'districtModel' => $districtModel,
            'siteConfig' => $siteConfig,

        ]);
    }

    public function actionAddCordinateValidate( $id ){
        $model = \app\models\Users::find()->where(['id' => $id])->one();
        $model->scenario = \app\models\Users::CORDINATE_UPDATE;
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }




    public function actionAcceptOrderPrice(){
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return \app\components\Utils::nextUpdateAtWhenRequested( 
                Yii::$app->request->post('id'), 
                Yii::$app->request->post('balance_in'), 
                Yii::$app->request->post('request_type'),  
                Yii::$app->request->post('temporary_day')  
            );
    
        }
    }


    public function actionAcceptOrderValidate(){
        $model = new \app\models\RequestOrder();
        $model->scenario = \app\models\RequestOrder::SCENARIO_ACCEPT_ORDER;
        $request = \Yii::$app->getRequest();

        if ( $request->isPost && $model->load( $request->post() ) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionAcceptOrder($id){
        $model = \app\models\RequestOrder::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();


        $model->scenario = \app\models\RequestOrder::SCENARIO_ACCEPT_ORDER;
        $model_services = \app\models\UsersSevices::find()
        ->where(['user_id' => $id])
        ->all();

        $router_model = \app\models\Routers::find()
        ->where(['id' => $model->district->router_id])
        ->asArray()
        ->one();

  
        $siteConfig = \app\models\SiteConfig::find()->asArray()->one();

        $personal_data = \yii\helpers\ArrayHelper::map(
            \webvimark\modules\UserManagement\models\User::find()
            ->where(['personal' => '1'])
            ->all(), 
            'id', 
            'fullname'
        );

        $itemUsageCount = \app\models\ItemUsage::find()->where(['user_id'=>$model->id])->count();
        $selectedService = explode( ",", $model->selected_services );

     
        if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            foreach ( $selectedService as $key => $service ) {
                $model_req_or_ser = UsersServicesPackets::find()->where(['user_id' => $model->id, 'service_id' => $service])->all();
                if ( count( $model_req_or_ser ) == 0 ) {
                    return [
                        'status' => 'error',
                        'message'=>Yii::t("app","Please add a packets for each service to {customer} customer",['customer'=>$model->fullname])
                    ];
                }
            }

            if (  $itemUsageCount == 0  ) {
                return [
                    'status' => 'error',
                    'message'=>Yii::t("app","Please add an item for {customer} customer",['customer'=>$model->fullname])
                ];
            }

            $requestType = Yii::$app->request->post("RequestOrder")['request_type'];
            $balanceIn = Yii::$app->request->post("RequestOrder")['balance_in'];
            $contractNumber = Yii::$app->request->post("RequestOrder")['contract_number'];
            $personals = Yii::$app->request->post("RequestOrder")['personals'];
            $temporaryDday = isset( Yii::$app->request->post("RequestOrder")['temporary_day'] ) ? Yii::$app->request->post("RequestOrder")['temporary_day'] : "";
            $created_at = time();

          


            if ( $model->status == 0 || $model->status == 3 ) {

                if ( $requestType == "0" && $model->status == "0" ) {
                    $userHistoryAndLogText = 'Pending status was changed to Active with request type "With use price"';
                }elseif( $requestType == "1" && $model->status == "0" ){
                    $userHistoryAndLogText = 'Pending status was changed to Active with request type "Only this month is free"';
                }elseif ( $requestType == "2" && $model->status == "0" ) {
                    $userHistoryAndLogText = 'Pending status was changed to VIP';
                }elseif ( $requestType == "3" && $model->status == "0" ) {
                    $userHistoryAndLogText = 'Pending status was changed to Active with request type "Temporary usage permission" - '.$temporaryDday." hours";
                }elseif ( $requestType == "0" && $model->status == "3" ) {
                    $userHistoryAndLogText = 'Archive status was changed to Active with request type "With use price"';
                }elseif ( $requestType == "1" && $model->status == "3" ) {
                    $userHistoryAndLogText = 'Archive status was changed to Active with request type "Only this month is free"';
                }elseif ( $requestType == "2" && $model->status == "3" ) {
                    $userHistoryAndLogText = 'Archive status was changed to VIP';
                }elseif ( $requestType == "3" && $model->status == "3" ) {
                    $userHistoryAndLogText = 'Archive status was changed to Active with request type "Temporary usage permission" - '.$temporaryDday." hours";
                }

                if ( $model->status == 0 ) {
                    $model->contract_number = $contractNumber;
                }

                $daily_calc =  true;
                $half_month =  false;

                $tariffAndServiceArray = \app\models\UserBalance::CalcUserTariffDaily(
                    $model->id, 
                    $daily_calc, 
                    $half_month
                );

                if ( $model->paid_time_type == "0" ) {
                    $caclNextUpdateAtForUser = Users::caclNextUpdateAtForUser( 
                        $model->id,
                        $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                        round( $balanceIn + $model->balance, 2 ),
                        ['untilToMonthTariff'=>$tariffAndServiceArray['services_total_tariff'],'credit_tariff'=>$tariffAndServiceArray['credit_tariff'],'total_tariff'=>$model->tariff]
                    );
                }

                if ( $model->paid_time_type == "1" ) {
                    $caclNextUpdateAtForUser = Users::caclNextUpdateAtForUser( 
                        $model->id,
                        $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                        round( $balanceIn + $model->balance, 2 ),
                    );
                }
                    // request type equal to "This month is free"
                if ( $requestType == "1" ) {
                    $nextUpdateAtWhenRequested = \app\components\Utils::nextUpdateAtWhenRequested( $model->id, $model->tariff, "1");
                }



                foreach ( $tariffAndServiceArray['service_tariff_array'] as $tariffAndServiceKey => $tariffAndService ) {
                   foreach ( $tariffAndService as $key => $service ) {
                         if ( $tariffAndServiceKey == "internet" ) {
                            $userServicePacketModel = \app\models\UsersServicesPackets::find()
                            ->where(['id'=>$service['u_s_p_i']])
                            ->one();
                             $userServicePacketModel->status = 1;
                             if ( $userServicePacketModel->save( false ) ) {
                                $inetModel = \app\models\UsersInet::find()
                                ->where(['u_s_p_i' => $service['u_s_p_i']])
                                ->one();
                                $inetModel->status = 1;

                                if ( $inetModel->save(false) ) {
                                    if ( $model->second_status == 4  ) {
                                        if ( $inetModel->static_ip != null ) {
                                            $staticIpModel = \app\models\IpAdresses::find()
                                            ->where(['id' => $inetModel->static_ip])
                                            ->one();
                         
                                             \app\components\MikrotikQueries::dhcpAddMacFromArchive(
                                                $inetModel->login, 
                                                $inetModel->packet->download."k"."/".$inetModel->packet->upload."k",
                                                $staticIpModel->public_ip,
                                                "iNet_yes",
                                                $router_model['nas'], 
                                                $router_model['username'], 
                                                $router_model['password'],
                                                "dhcpAddMacFromArchive",
                                                [
                                                    'login'=> $inetModel->login,
                                                    'rateLimit'=>$inetModel->packet->download."k"."/".$inetModel->packet->upload."k",
                                                    'ipAddress'=> $staticIpModel->public_ip,
                                                    'addressList'=>"iNet_yes",
                                                    'nas'=>$router_model['nas'],
                                                    'router_username'=>$router_model['username'],
                                                    'router_password'=>$router_model['password'],
                                                ]
                                             );

                                             $staticIpModel = \app\models\IpAdresses::find()
                                             ->where(['id'=>$inetModel->static_ip])
                                             ->one();
                                             $staticIpModel->status = '1';
                                             $staticIpModel->save( false );

                                        }else{

                                            $cgnModel = \app\models\CgnIpAddress::find()
                                            ->where(['inet_login'=>$inetModel->login])
                                            ->one();

                                            \app\components\MikrotikQueries::dhcpAddMacFromArchive(
                                                $inetModel->login, 
                                                $inetModel->packet->download."k"."/".$inetModel->packet->upload."k",
                                                $cgnModel['internal_ip'],
                                                "iNet_yes",
                                                $router_model['nas'], 
                                                $router_model['username'], 
                                                $router_model['password'],
                                                "dhcpAddMacFromArchive",
                                                [
                                                    'login'=> $inetModel->login,
                                                    'rateLimit'=>$inetModel->packet->download."k"."/".$inetModel->packet->upload."k",
                                                    'ipAddress'=> $cgnModel['internal_ip'],
                                                    'addressList'=>"iNet_yes",
                                                    'nas'=>$router_model['nas'],
                                                    'router_username'=>$router_model['username'],
                                                    'router_password'=>$router_model['password'],
                                                ]
                                             );

                                        }

                                    }
                                }

                             }
                         }
                         if ( $tariffAndServiceKey == "tv" ) {
                            $userServicePacketModel = \app\models\UsersServicesPackets::find()
                            ->where(['id'=> $service['u_s_p_i']])
                            ->one();
                            $userServicePacketModel->status = 1;
                            if ( $userServicePacketModel->save( false ) ) {
                                \app\models\UsersTv::turnOnTvAccess(
                                    $service['user_id'], 
                                    $service['u_s_p_i']
                                );
                            }
                         }

                         if ( $tariffAndServiceKey == "wifi" ) {
                            $userServicePacketModel = \app\models\UsersServicesPackets::find()
                            ->where(['id'=>$service['u_s_p_i']])
                            ->one();
                            $userServicePacketModel->status = 1;
                            if ( $userServicePacketModel->save( false ) ) {
                                \app\models\UsersWifi::turnOnWifiAccess(
                                    $service['user_id'], 
                                    $service['u_s_p_i']
                                );
                            }
                         }

                         if ( $tariffAndServiceKey == "voip" ) {
                            $userServicePacketModel = \app\models\UsersServicesPackets::find()
                            ->where(['id'=>$service['u_s_p_i']])
                            ->one();
                            $userServicePacketModel->status = 1;
                            if ( $userServicePacketModel->save( false ) ) {
                                $voIpModel = \app\models\UsersVoip::find()
                                ->where(['u_s_p_i'=>$service['u_s_p_i']])
                                ->one();
                                $voIpModel->status = 1;
                                $voIpModel->save( false );
                            }
                         }
                   }
                }

                if ( $model->second_status == 4  ) {
                    $model->second_status = 0;
                }
             
                if ( $requestType == "0" ) {
                    if ( $balanceIn > 0 ) {
                        \app\models\UserBalance::BalanceAdd( 
                            $model->id, 
                            $balanceIn,
                            $created_at, 
                            0, 
                            $receipt_id = false 
                        );
                    }
                    if ( $caclNextUpdateAtForUser['monthCount'] > 0 ) {
                        for ( $i=0; $i < $caclNextUpdateAtForUser['monthCount']; $i++ ) { 
                            if (  $i == 0 && $model->paid_time_type == 0  ) {
                                $tariffAndServiceArray = \app\models\UserBalance::CalcUserTariffDaily(
                                    $model->id, 
                                    $daily_calc, 
                                    $half_month
                                );
                            }else{
                                $daily_calc = false; 
                                $half_month =  false;

                                $tariffAndServiceArray = \app\models\UserBalance::CalcUserTariffDaily(
                                    $model->id, 
                                    $daily_calc, 
                                    $half_month
                                );
                            }

                            foreach ( $tariffAndServiceArray['service_tariff_array'] as $tariffAndServiceKey => $tariffAndService ) {
                               foreach ( $tariffAndService as $key => $service ) {
                                     if ( $tariffAndServiceKey == "internet" ) {
                                        $pay_for = 0;
                                        \app\models\UserBalance::BalanceOut(
                                            $service['user_id'], 
                                            $service['packet_price'], 
                                            $created_at + 1, 
                                            0, 
                                            $pay_for, 
                                            0, 
                                            false,
                                            $service['packet_id']
                                        );
                                     }
                                     if ( $tariffAndServiceKey == "tv" ) {
                                        $pay_for = 1;
                                        \app\models\UserBalance::BalanceOut(
                                            $service['user_id'], 
                                            $service['packet_price'], 
                                            $created_at + 1, 
                                            0, 
                                            $pay_for, 
                                            0, 
                                            false,
                                            $service['packet_id']
                                        );
                                     }

                                     if ( $tariffAndServiceKey == "wifi" ) {
                                        $pay_for = 2;
                                        \app\models\UserBalance::BalanceOut(
                                            $service['user_id'], 
                                            $service['packet_price'], 
                                            $created_at + 1, 
                                            0, 
                                            $pay_for, 
                                            0, 
                                            false,
                                            $service['packet_id']
                                        );
                                     }

                                     if ( $tariffAndServiceKey == "voip" ) {
                                        $pay_for = 4;
                                        \app\models\UserBalance::BalanceOut(
                                            $service['user_id'], 
                                            $service['packet_price'],
                                            $created_at + 1,  
                                            0, 
                                            $pay_for, 
                                            0, 
                                            false,
                                            $service['packet_id']
                                        );
                                     }
                               }
                            }
                            \app\models\UsersGifts::checkAndAddGiftHistory( $model->id );
                            \app\models\UsersCredit::CheckAndAddCreditHistory( $model->id, 0, false );
                            $model->balance = \app\models\UserBalance::CalcUserTotalBalance( $model->id );
                            $model->paid_day = $caclNextUpdateAtForUser['paidDay'];
                            $model->updated_at = $caclNextUpdateAtForUser['updateAt'];
                        }
                    }
                }

                if ( $requestType == "1" ) {
                    $model->paid_day =  $nextUpdateAtWhenRequested['paidDay'];
                    $model->updated_at = $nextUpdateAtWhenRequested['updatedAt'];
                }

                if ( $requestType == "2" ) {
                    $model->updated_at = time();
                }

                if ( $requestType == "3" ) {
                    if ( $temporaryDday != "" ) {
                       $model->updated_at = time() + ( $temporaryDday * 3600 );
                    }
                }

                if ( $requestType != "2" ) {
                     $model->status = 1;
                }else{
                    $model->status = 7;
                }
                   
                
                if ( $model->save(false) ) {
                   \app\models\PersonalActivty::deleteAll(['user_id' => $model->id,'type'=>'0']);
                    $personalActivtyModel =  new \app\models\PersonalActivty;
                    $personalActivtyModel->user_id = $model->id;
                    $personalActivtyModel->type = "0";
                    $personalActivtyModel->created_at = time();
                    if ($personalActivtyModel->save(false)) {
                        foreach ( $personals  as $key => $personalId ) {
                            $personalUserActivty = new \app\models\PersonalUserActivty;
                            $personalUserActivty->activty_id = $personalActivtyModel->id; 
                            $personalUserActivty->member_id = $personalId; 
                            $personalUserActivty->save(false);  
                        }
                    }
        
                    UsersHistory::AddHistory( intval($model->id), Yii::$app->user->username, $userHistoryAndLogText, time() );
                    Logs::writeLog(Yii::$app->user->username, intval($model->id), $userHistoryAndLogText, time());

                     return $this->redirect(['index']);
                }
                
            }

            if ( $model->status == 1 || $model->second_status == 5 ) {
                   
                    $userServicesPacketsModel = \app\models\UsersServicesPackets::find()
                    ->where(['user_id'=>$model->id])
                    ->andWhere(['status'=>0])
                    ->all();

                     $model->status = 1;

                    if ( count( $userServicesPacketsModel ) == 0 ) {
                        return [
                            'status' => 'error',
                            'message'=>Yii::t("app","Please add a new packet for New service to {customer} customer",['customer'=>$model->fullname])
                        ];
                    }

                   
                    if ( $requestType == "0" ) {
                        $userHistoryAndLogText = 'New service was added with request type "With use price"';
                        if ( $balanceIn > 0 ) {
                            \app\models\UserBalance::BalanceAdd( 
                                $model->id, 
                                $balanceIn,
                                $created_at, 
                                0, 
                                $receipt_id = false 
                            );
                        }
                        $current_day = date("d");
                        $month_day = date("t");
                        $diff = ( $month_day - date("d") ) + 1;
                        $service_tariff = 0;
                        foreach ($userServicesPacketsModel as $key => $packet_one) {
                            if ( $model->paid_time_type == "0" ) {
                                if ( $packet_one->price != null || $packet_one->price != 0 ) {
                                    $service_tariff = round( ( $packet_one->price   / $month_day ) * $diff , 1);
                                }else{
                                    $service_tariff = round(  ( $packet_one->packet->packet_price  / $month_day ) * $diff , 1 );
                                }
                            }

                            if ( $model->paid_time_type == "1" ) {
                                if ( $packet_one->price != null || $packet_one->price != 0 ) {
                                    $service_tariff = round( $packet_one->price , 1 );
                                }else{
                                    $service_tariff = round( $packet_one->packet->packet_price , 1 );                       
                                }
                            }

                            if ( $packet_one->service->service_alias == "internet" ) {
                                $pay_for = 0;
                            }elseif ( $packet_one->service->service_alias == "tv" ) {
                                $pay_for = 1;
                            }elseif ( $packet_one->service->service_alias == "wifi" ) {
                                $pay_for = 2;
                            }elseif ( $packet_one->service->service_alias == "voip" ) {
                                $pay_for = 4;
                            }

                            \app\models\UserBalance::BalanceOut(
                                $packet_one->user_id, 
                                $service_tariff,
                                $created_at + 1, 
                                0, 
                                $pay_for, 
                                0, 
                                false,
                                $packet_one->packet_id
                            );
                        }
                        $model->balance = \app\models\UserBalance::CalcUserTotalBalance( $model->id );
                    }

                    if ( $requestType == "1" ) {
                       $userHistoryAndLogText = 'New service was added with request type "Only this month is free"';
                    }

                    if ( $requestType == "2" ) {
                        $model->status = 7;
                        $userHistoryAndLogText = 'New service was added and Active status was changed to VIP';
                    }

                    foreach ( $userServicesPacketsModel as $serviceKey => $servicePacket ) {
                        $servicePacket->status = 1;
                        $servicePacket->save(false);
                    }

                    $model->second_status = 0;
                    if ( $model->save(false) ) {
                        $personalActivtyModel =  new \app\models\PersonalActivty;
                        $personalActivtyModel->user_id = $model->id;
                        $personalActivtyModel->type = "4";
                        $personalActivtyModel->created_at = time();
                        if ($personalActivtyModel->save(false)) {
                            foreach ( $personals  as $key => $personalId ) {
                                $personalUserActivty = new \app\models\PersonalUserActivty;
                                $personalUserActivty->activty_id = $personalActivtyModel->id; 
                                $personalUserActivty->member_id = $personalId; 
                                $personalUserActivty->save(false);  
                            }
                        }
                        
                        UsersHistory::AddHistory( intval( $model->id ), Yii::$app->user->username , $userHistoryAndLogText, time() );
                        Logs::writeLog( Yii::$app->user->username, intval($model->id), $userHistoryAndLogText, time() );
                    }
                return $this->redirect(['index']);
            }
        }
        return $this->renderAjax('accept_user', [
            'tarif' => $model->tariff, 
            'personal_data' => $personal_data, 
            'model_services' => $model_services,
            'model' => $model,
            'siteConfig' => $siteConfig,
        ]);
    }



    /* Item action  start */

    public function actionAddItemToUserValidate(){
        $model = new \app\models\ItemUsage();
        $model->scenario = \app\models\ItemUsage::SCENARIO_USE_ITEM_TO_USER;
        $request = \Yii::$app->getRequest();
        if ( $request->isPost && $model->load( $request->post() ) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionAddItemToUser($id){
        $siteConfig = \app\models\SiteConfig::find()->asArray()->one();
        $model = new \app\models\ItemUsage;
        $model->scenario = \app\models\ItemUsage::SCENARIO_USE_ITEM_TO_USER;
        $userModel = \app\models\Users::find()
        ->withByLocation()
        ->where(['id' => $id])
        ->one();
   
        $itemUsage = \app\models\ItemUsage::find()
        ->select('item_usage.*,items.name as item_name,users.status as user_status,item_stock.price as price')
        ->leftJoin('items', 'items.id=item_usage.item_id')
        ->leftJoin('users', 'users.id=item_usage.user_id')
        ->leftJoin('item_stock', 'item_stock.id=item_usage.item_stock_id')
        ->where(['user_id' => $id])
        ->asArray()
        ->all();


        $first_connection = \yii\helpers\ArrayHelper::map(
            \app\models\PersonalUserActivty::find()
            ->select('personal_user_activty.*,personal_activty.type as activty_type')
            ->leftJoin('personal_activty','personal_activty.id=personal_user_activty.activty_id')
            ->where(['personal_activty.user_id'=>$id])
            ->andWhere(['personal_activty.type'=>'0'])
            ->asArray()
            ->all(), 
            'member_id', 
            'member_id'
        );
        $personal_data = \yii\helpers\ArrayHelper::map(
            \webvimark\modules\UserManagement\models\User::find()
            ->where(['personal' => '1'])
            ->all(), 
            'id', 
            'fullname'
        );
        if ( $model->load(Yii::$app->request->post()) && $model->validate() ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $post_data = Yii::$app->request->post('ItemUsage');

            if (preg_match('/^(?P<day>\d+)[-\/](?P<month>\d+)[-\/](?P<year>\d+)$/', Yii::$app->request->post('ItemUsage')['created_at'], $matches)) {
                $timestamp = mktime(0,0, 0, ($matches['month']), $matches['day'], $matches['year']);
            }
            $model->user_id = $id;
            $logTime = time();
            $model->created_at = $timestamp;
            $created_at = time();
            if ($model->save(false)) {

                $itemStock = \app\models\ItemStock::find()->where(['id'=>$post_data['item_stock_id']])->one();

                if ( $model->status == 6 ) {

                    $model->credit = 1;
                    $creditTariff = ceil( ( $itemStock['price'] * $model->quantity ) / intval( $model->month ) );
                    \app\models\ItemUsage::getCalcUserTariff( $id, $creditTariff );
                    $logText = "Added {$model->quantity} {$itemStock->item->name} item with price {$itemStock->price} ( credit )";

                } elseif ( $model->status == 0 || $model->status == 1) {

                    $model_receipt = \app\models\Receipt::find()->orderBy(['id' => SORT_ASC])->where(['status' => '0'])->asArray()->one();
                    $pay_for = 3;
                    $recipetId =  ( $model->status == 1 ) ? $model_receipt['id'] : false;
                    $itemPrice = $itemStock['price'] * $model->quantity;

                    \app\models\UserBalance::BalanceAdd( 
                        $id, 
                        $itemPrice, 
                        $created_at,
                        0, 
                        $recipetId 
                    );
                    \app\models\UserBalance::BalanceOut( 
                        $id, 
                        $itemPrice, 
                        $created_at + 1,
                        0, 
                        $pay_for, 
                        0, 
                        $recipetId, 
                        false, 
                        $model->id 
                    );

                    $logText = ( $model->status == 0 ) ? "Added {$model->quantity} {$itemStock->item->name} item with price {$itemStock->price} ( Paid - used for initial Setup )" : "Added {$model->quantity} {$itemStock->item->name} item with price {$itemStock->price} ( Paid )";

                } elseif ( $model->status == 4 ) {
                    $model->credit = 2;
                     $logText = "Added {$model->quantity} {$itemStock->item->name} item with price {$itemStock->price} ( Gift )";
                } else {
                    $model->credit = null;
                    $model->month = null;
                    $pay_for = 3;
                    $itemPrice = $itemStock['price'] * $model->quantity;

                    \app\models\UserBalance::BalanceOut( 
                        $id, 
                        $itemPrice,
                        $created_at + 1, 
                        1, 
                        $pay_for, 
                        0, 
                        null, 
                        false, 
                        $model->id 
                    );

                    $logText = "Added {$model->quantity} {$itemStock->item->name}  item with price {$itemStock->price}";
                }

    
                if ($model->save(false)) {
                    if ($post_data['personals'] != "") {
                        foreach ($post_data['personals'] as $key => $personal_id) {
                            $itemUsagePersonal = new \app\models\ItemUsagePersonal;
                            $itemUsagePersonal->item_usage_id = $model->id;
                            $itemUsagePersonal->personal_id = $personal_id;
                            $itemUsagePersonal->save(false);
                        }
                        $personal_activty =  new \app\models\PersonalActivty;
                        $personal_activty->user_id = $userModel->id;
                        $personal_activty->item_count_id = $model->id;
                        $personal_activty->type = '2';
                        $personal_activty->created_at = $logTime;
                        if ( $personal_activty->save(false) ) {
                            foreach ($post_data['personals'] as $key => $personal_id) {
                                $personal_user_activty = new \app\models\PersonalUserActivty;
                                $personal_user_activty->activty_id  = $personal_activty->id;
                                $personal_user_activty->member_id  = $personal_id;
                                $personal_user_activty->save(false);
                            }
                        }

                    }
               
                    \app\models\ItemStock::updateStock( $model->item_stock_id, $model->item_id, $model->quantity );
                    Logs::writeLog(
                        Yii::$app->user->username,
                        intval($id),
                        $logText,
                        $logTime
                    );
                }

            }
            return [
                'status' => 'success',
                'url' => \yii\helpers\Url::to(['index?RequestOrderSearch[fullname]='.rawurlencode($userModel->fullname)], true),
                'item_status' => $post_data['status'],
            ];
        }
        return $this->renderIsAjax('item_user_count', [
            'model' => $model,
            'itemUsage' => $itemUsage,
            'siteConfig' => $siteConfig,
            'userModel' => $userModel,
            'first_connection' => $first_connection,
            'personal_data' => $personal_data,
        ]);
    }

    public function actionItemList($q = null, $id = null){
       if ( Yii::$app->request->isAjax ) {
            if (!is_null($q)) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $query = new \yii\db\Query;
                $query->select('id, name AS text')
                    ->from('items')
                    ->where(['like', 'name', $q])->limit(20);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);
            } elseif ($id) {
                if (is_array($id)) {
                    $ids = array_values($id);
                    $query = new \yii\db\Query;
                    $query->select('id, name AS text')
                        ->from('items')
                        ->where(['id' => $id])
                        ->limit(20);
                    $command = $query->createCommand();
                    $data = $command->queryAll();
                    $out = $data;
                }
            }
            return $out;
       }
    }

    public function actionGetItemStockPrice(){
       if ( Yii::$app->request->isAjax && Yii::$app->request->isPost ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model = \app\models\ItemStock::findOne( Yii::$app->request->post("id") );
            return [
                'status'=>'success',
                'price'=> $model['price']
            ];
       }
    }

    public function actionPersonalList($q = null, $id = null){
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $query = new \yii\db\Query;
            $query->select('id, fullname AS text')
                ->from('members')
                ->where(['personal'=>'1'])
                ->andWhere(['like', 'fullname', $q])
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
                    ->andWhere(['personal'=>'1'])
                    ->limit(20);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out = $data;
            }
        }
        return $out;
    }

    public function actionUserItemDelete(){
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $post = Yii::$app->request->post();

            $model = \app\models\ItemUsage::find()
            ->where(['id' => $post['item_usage_id']])
            ->andWhere(['user_id' => $post['user_id']])
            ->one();

           $userModel = Users::find()
            ->where(['id' => $post['user_id']])
            ->withByLocation()
            ->one();

            if ( $userModel == null || $model == null ) {
                return [
                    'status' => 'error',
                    "message" => Yii::t('app','Something went wrong,Please reload page and try again')
                ];
            }

            $itemStockId = $model->item_stock_id;
            $quantity = $model->quantity;
            $itemName = $model->item->name;

            if ( $model->delete() ) {
                \app\models\UsersGifts::deleteAll(['user_id' => $post['user_id'], 'item_usage_id' => $post['item_usage_id']]);
                \app\models\UsersCredit::deleteAll(['user_id' => $post['user_id'], 'item_usage_id' => $post['item_usage_id']]);
                \app\models\UserBalance::deleteAll(['user_id' => $post['user_id'], 'item_usage_id' => $post['item_usage_id']]);
                \app\models\ItemUsage::calcDeletedStock( $itemStockId, $quantity );

                $userModel->tariff = \app\models\UserBalance::CalcUserTariffDaily( $userModel->id )['per_total_tariff'];
                if ($userModel->save(false)) {
                    $logMessage = "{$itemName} item deleted form {$userModel['fullname']} inventory.Item stock was updated";
                    Logs::writeLog(
                        Yii::$app->user->username, 
                        intval( $userModel->id ), 
                        $logMessage, 
                        time()
                    );
                    return [
                        'status' => 'success',
                        'url' => \yii\helpers\Url::to(['index?RequestOrderSearch[fullname]='.rawurlencode($userModel->fullname)], true),
                        "message" => Yii::t('app','{itemName} item deleted form {userFullname} item list.{itemName} item stock was updated',['itemName'=>$itemName,'userFullname'=>$userModel['fullname']])
                    ];
                }
            }
        }
    }

    protected function findModel($id){
        if (($model = RequestOrder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

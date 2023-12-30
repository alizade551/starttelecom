<?php

namespace app\controllers;

use Yii;
use app\models\Devices;
use app\models\search\DevicesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\DefaultController;
use app\models\Logs;

/**
 * DevicesController implements the CRUD actions for Devices model.
 */
class DevicesController extends DefaultController
{
    public $modelClass = 'app\models\Devices';
    public $modelSearchClass = 'app\models\search\DevicesSearch';

    public function actionCreate(){
        $model = new Devices();
        $siteConfig = \app\models\SiteConfig::find()->one();

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $post = Yii::$app->request->post('Devices');

            $model->type        =  $post['type'];
            $model->description =  $post['description'];
            $model->created_at  =  $post['created_at'];
            $model->vendor_name =  $post['vendor_name'];
            $model->ip_address  =  $post['ip_address'];

            if ($post['type'] == "switch") {
                $model->name = null;
                $model->pon_port_count =  null;
                $model->port_count =  $post['port_count'];
                if ($model->save(false)) {
                    for ($i=1; $i <= $model->port_count ; $i++) { 
                       $switchPortsModel = new \app\models\SwitchPorts;
                       $switchPortsModel->device_id   = $model->id;
                       $switchPortsModel->port_number =$i;
                       $switchPortsModel->save(false);
                    }

                    $logMessage = "{$model->name} device ( switch ) was created (id:{$model->id})";
                    Logs::writeLog(
                        Yii::$app->user->username, 
                        null, 
                        $logMessage, 
                        time()
                    );

                    return [
                        'status' => 'success',
                        'url' => \yii\helpers\Url::to(['index?DevicesSearch[name]='.rawurlencode( $model->name )], true)
                    ];
                }
            }

            if ( $post['type'] == "epon" || $post['type'] == "gpon" || $post['type'] == "xpon" ) {

              $model->pon_port_count =  $post['pon_port_count'];
              $model->port_count =  null;
              if ($model->save(false)) {

                $deviceQuery = \app\models\Devices::find();
                $eponCount = $deviceQuery->where(['type'=>'epon'])
                ->count();
                $gponCount = $deviceQuery->where(['type'=>'gpon'])
                ->count();
                $xponCount = $deviceQuery->where(['type'=>'xpon'])
                ->count();


                    if ($model->type == "epon") {
                        $model->name  =  "OLT_E".sprintf("%02d", $eponCount);
                    }

                    if ($model->type == "gpon") {
                        $model->name  =  "OLT_G".sprintf("%02d", $gponCount);
                    }

                    if ($model->type == "xpon") {
                        $model->name  =  "OLT_X".sprintf("%02d", $xponCount);
                    }

                    if ($model->save(false)) {
                        for ($i=1; $i <= $model->pon_port_count ; $i++) { 
                           $egponPonPortModel = new \app\models\EgponPonPort;
                           $egponPonPortModel->device_id       = $model->id;
                           $egponPonPortModel->pon_port_number = $i;
                           $egponPonPortModel->save(false);
                        }
                        $logMessage = "{$model->name} device ( {$post['type']} ) was created (id:{$model->id})";
                        Logs::writeLog(
                            Yii::$app->user->username, 
                            null, 
                            $logMessage, 
                            time()
                        );
                    }

                    return [
                        'status' => 'success',
                        'url' => \yii\helpers\Url::to(['index?DevicesSearch[name]='.rawurlencode($model->name)], true)
                    ];
              }


            }
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'siteConfig'=>$siteConfig
        ]);
    }



    public function actionUpdate($id){
        $model =  Devices::findOne($id);

        if ( Yii::$app->request->isAjax && Yii::$app->request->isPost ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $post = Yii::$app->request->post('Devices');

            $checkSwitchPortCount = \app\models\SwitchPorts::find()
            ->where(['device_id'=>$model->id])
            ->andWhere(['not', ['u_s_p_i' => null]])
            ->count();


           $checkEgponBoxPortsCount = \app\models\EgonBoxPorts::find()
            ->leftJoin('egpon_box','egpon_box.id=egon_box_ports.egon_box_id')
            ->where(['egpon_box.device_id'=>$model->id])
            ->andWhere(['NOT', ['u_s_p_i' => null]])
            ->count();




            if ( $checkSwitchPortCount != 0 ) {
                return [
                    'status'=>'error',
                    'message'=>Yii::t(
                        'app',
                        'You have {count} active ports in {device} ,Please free all ports and Try Configure again!',
                        [
                            'count'=>$checkSwitchPortCount,
                            'device'=>$model->name,
                        ]
                )
                ];
            }


            if ( $checkEgponBoxPortsCount != 0 ) {
                return [
                    'status'=>'error',
                    'message'=>Yii::t(
                        'app',
                        'You have {count} active ports in {device} ,Please free all ports and Try Configure again!',
                        [
                            'count'=>$checkEgponBoxPortsCount,
                            'device'=>$model->name,
                        ]
                )
                ];
            }

            $model->description =  $post['description'];
            $model->vendor_name =  $post['vendor_name'];
            $model->ip_address  =  $post['ip_address'];
            if ($post['type'] == "switch" ) {

                if ($model->type == "epon" || $model->type == "gpon" ) {
                    \app\models\DeviceLocations::deleteAll('device_id = :device_id', array(':device_id' => $model->id));
                    \app\models\EgponPonPort::deleteAll('device_id = :device_id', array(':device_id' => $model->id));
                    \app\models\EgponBox::deleteAll('device_id = :device_id', array(':device_id' => $model->id));
                }
                $model->type       =  $post['type'];
                $model->port_count =  $post['port_count'];
                $model->pon_port_count =  null;

                if ($model->save(false)) {
                    \app\models\SwitchPorts::deleteAll('device_id = :device_id', array(':device_id' => $model->id));

                    for ($i=1; $i <= $model->port_count ; $i++) { 
                       $switchPortsModel = new \app\models\SwitchPorts;
                       $switchPortsModel->device_id   = $model->id;
                       $switchPortsModel->port_number =$i;
                       $switchPortsModel->save(false);
                    }

                    $logMessage = "{$model->name} device ( {$post['type']} ) was updated (id:{$model->id})";
                    Logs::writeLog(
                        Yii::$app->user->username, 
                        null, 
                        $logMessage, 
                        time()
                    );

                    return $this->redirect('/devices/index?DevicesSearch[name]='.rawurlencode($model->name));
                }
            }

            if ( $post['type'] == "epon" || $post['type'] == "gpon" || $post['type'] == "xpon" ) {
                if ($model->type == "switch") {
                    \app\models\DeviceLocations::deleteAll('device_id = :device_id', array(':device_id' => $model->id));
                    \app\models\SwitchPorts::deleteAll('device_id = :device_id', array(':device_id' => $model->id));
                }

                if ( $post['type'] == "epon" || $post['type'] == "gpon" || $post['type'] == "xpon" ) {
                    \app\models\DeviceLocations::deleteAll('device_id = :device_id', array(':device_id' => $model->id));
                    \app\models\EgponPonPort::deleteAll('device_id = :device_id', array(':device_id' => $model->id));
                    \app\models\EgponBox::deleteAll('device_id = :device_id', array(':device_id' => $model->id));
                }

                
              $model->type = $post['type'];
              $model->pon_port_count =  $post['pon_port_count'];
              $model->port_count =  null;
              if ($model->save(false)) {

                $deviceQuery = \app\models\Devices::find();
                $eponCount = $deviceQuery->where(['type'=>'epon'])
                ->count();
                $gponCount = $deviceQuery->where(['type'=>'gpon'])
                ->count();
                $xponCount = $deviceQuery->where(['type'=>'gpon'])
                ->count();
                    if ($model->type == "epon") {
                        $model->name  =  "OLT_E".sprintf("%02d", $eponCount);
                    }

                    if ($model->type == "gpon") {
                        $model->name  =  "OLT_G".sprintf("%02d", $gponCount);
                    }

                    if ($model->type == "xpon") {
                        $model->name  =  "OLT_X".sprintf("%02d", $xponCount);
                    }
                    if ($model->save(false)) {
                        for ( $i = 1; $i <= $model->pon_port_count ; $i++ ) { 
                           $egponPonPortModel = new \app\models\EgponPonPort;
                           $egponPonPortModel->device_id       = $model->id;
                           $egponPonPortModel->pon_port_number = $i;
                           $egponPonPortModel->save(false);
                        }
                        $logMessage = "{$model->name} device ( {$post['type']} ) was updated (id:{$model->id})";
                        Logs::writeLog(
                            Yii::$app->user->username, 
                            null, 
                            $logMessage, 
                            time()
                        );
                    }

                    return $this->redirect('/devices/index?DevicesSearch[name]='.rawurlencode($model->name));
              }


            }
        }

        return $this->renderIsAjax('update', [
            'model' => $model,
        ]);
    }


    public function actionBoxPortClear($id){
        if ( Yii::$app->request->isAjax && Yii::$app->request->isPost ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $model = \app\models\EgonBoxPorts::find()
            ->where(['id'=>$id])
            ->one();

            if ( $model == null ) {
                 return ['status'=>'error','message'=>Yii::t('app','Something went wrong')];
            }
            $logMessage = "{$model->egonBox->device->name} device {$model->egonBox->box_name} box {$model->port_number} port number inet_login was cleared and status was changed to free";
   
            $model->status = 0;
            $model->u_s_p_i = null;
            if ( $model->save(false) ) {

                Logs::writeLog(
                    Yii::$app->user->username, 
                    null, 
                    $logMessage, 
                    time()
                );

                return ['status'=>'success','message'=>Yii::t(
                    'app','{device_name} device {box_name} box {port_number} port inet_login was cleared and status was changed to free',
                    [
                        'device_name'=>$model->egonBox->device->name,
                        'box_name'=>$model->egonBox->box_name,
                        'port_number'=>$model->port_number,
                    ]
                )];
            }
        }
    }



    public function actionAddDeviceValidate(){
        $model = new \app\models\Devices();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }


    public function actionUpdateCordinateValidate(){
        $model = new \app\models\Devices();
        $model->scenario = \app\models\Devices::SCENARIO_CORDINATE;
        $request = \Yii::$app->getRequest();
        if ( $request->isPost && $model->load( $request->post() ) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionUpdateCordinate($id){
        $model =  Devices::findOne($id);
        $model->scenario = \app\models\Devices::SCENARIO_CORDINATE;
        $siteConfig = \app\models\SiteConfig::find()->one();
        if ( Yii::$app->request->isPost ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $post = Yii::$app->request->post('Devices');
            $model->city_id     =  $post['city_id'];
            $model->district_id =  $post['district_id'];
            $model->location_id =  $post['location_id'];
            $model->cordinate =  $post['cordinate'];
            if ( $model->save(false) ) {
                return $this->redirect('/devices/index?DevicesSearch[name]='.rawurlencode($model->name));
            }
        }

        return $this->renderIsAjax('update-cordinate', [
            'model' => $model,
            'siteConfig'=>$siteConfig
        ]);

    }



    public function actionListPorts($id){
        $model = \app\models\SwitchPorts::find()
        ->select('switch_ports.*,users_inet.login as inet_login,users_inet.user_id as port_user_id')
        ->leftJoin('users_inet','users_inet.u_s_p_i=switch_ports.u_s_p_i')
        ->where(['device_id'=>$id])
        ->asArray()
        ->all();

        $deviceModel = \app\models\Devices::find()
        ->where(['id'=>$id])
        ->asArray()
        ->one();

        return $this->renderIsAjax('switch-ports',['model'=>$model,'deviceModel'=>$deviceModel]);
    }

    public function actionBoxPortSettingValidate(){
        $model = new \app\models\EgponBox();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionBoxPortSetting($id){
        $model = \app\models\EgponBox::find()
        ->where(['id'=>$id])
        ->one();

        $coverageDistrictsModel = \app\models\DeviceLocations::find()
        ->where(['device_id'=>$model->egponPonPort->device_id])
        ->asArray()
        ->all();

        $coverageDistrict = [];
        foreach ($coverageDistrictsModel as $cvgDisKey => $covargeDistrict) {
             $coverageDistrict[] = $covargeDistrict['district_id'];
        }


        $coverageLocations = \app\models\Locations::find()
        ->where(['district_id'=>$coverageDistrict])
        ->asArray()
        ->all();
        


        if ( Yii::$app->request->post() && Yii::$app->request->isAjax ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                $checkEgponBoxPonPort = \app\models\EgonBoxPorts::find()
                ->where(['egon_box_id'=>$model->id])
                ->andWhere(['not', ['egon_box_ports.u_s_p_i' => null]])
                ->asArray()
                ->count();

                if ($checkEgponBoxPonPort != 0) {
                    return [
                        'status'=>'error',
                        'message'=>Yii::t('app','You have {count} ports in {box} box  on device {device} ,Please free all ports and Try Configure again!',
                            [
                                'count'=>$checkEgponBoxPonPort,
                                'device'=>$model->device->name,
                                'box'=>$model->box_name,
                            ]
                    )
                    ];
                }

            $model->location_id = Yii::$app->request->post('EgponBox')['location_id'];
            if ( $model->save(false) ) {
                $logMessage = "{$model->device->name} device {$model->box_name} box  was updated to location: {$model->location->name}";
                Logs::writeLog(
                    Yii::$app->user->username, 
                    null, 
                    $logMessage, 
                    time()
                );
            }

             return $this->redirect('/devices/index?DevicesSearch[name]='.rawurlencode($model->device->name));

        }



        return $this->renderIsAjax('box-port-setting',['model'=>$model,'coverageLocations'=>$coverageLocations]);
    }

    public function actionUseBoxPort($id){
        $model = \app\models\EgonBoxPorts::find()
        ->select('egon_box_ports.*,users_inet.login as inet_login,users_inet.user_id as port_user_id')
        ->leftJoin('users_inet','users_inet.u_s_p_i=egon_box_ports.u_s_p_i')
        ->where(['egon_box_id'=>$id])
        ->orderBy(['port_number'=>SORT_ASC])
        ->asArray()
        ->all();

        $egonBoxPortsModel = \app\models\EgonBoxPorts::find()
        ->where(['egon_box_id'=>$id])
        ->one();

        return $this->renderIsAjax(
            'use-box-port',
            [
                'model'=>$model,
                'egonBoxPortsModel'=>$egonBoxPortsModel
            ]
        );
    }


    public function actionBoxOnMapValidate(){
        $model = new \app\models\EgponBox();
        $model->scenario = \app\models\EgponBox::CHANGE_BOX_CORDINATE;
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }


   public function actionBoxOnMap($id){

        $model = \app\models\EgponBox::find()
        ->where(['id'=>$id])
        ->one();

        $siteConfig = \app\models\SiteConfig::find()->one();

        if ( Yii::$app->request->isAjax ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ( $model->load( Yii::$app->request->post() ) && $model->save() ) {
                return $this->redirect('/devices/index?DevicesSearch[name]='.rawurlencode($model->device->name));
            }


        }

        return $this->renderIsAjax('box-on-map',['model'=>$model,'siteConfig'=>$siteConfig]);
    }



    public function actionTagInetLoginToBoxPortValidate(){
        $model = new \app\models\EgonBoxPorts();
        $model->scenario = \app\models\EgonBoxPorts::TAG_USER_INET;
        
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionTagInetLoginToBoxPort($id){
        $model = \app\models\EgonBoxPorts::find()
        ->where(['id'=>$id])
        ->one();

        $covargeLocationModel = \app\models\EgponBox::find()
        ->where(['id'=>$model->egon_box_id])
        ->asArray()
        ->all();
        $supportedLocationId = [];
        foreach ($covargeLocationModel as $key => $box) {
            $supportedLocationId[] = $box['location_id'];
        }

        $userServicePacketModel = \app\models\UsersServicesPackets::find()
        ->select('users_services_packets.*,users_inet.login as inet_login')
        ->leftJoin('users','users_services_packets.user_id=users.id')
        ->leftJoin('services','users_services_packets.service_id=services.id')
        ->leftJoin('users_inet','users_services_packets.id=users_inet.u_s_p_i')
        ->leftJoin('egon_box_ports','users_services_packets.id=egon_box_ports.u_s_p_i')
        ->where(['services.service_alias'=>'internet'])
        ->andWhere(['users.location_id'=>$supportedLocationId])
        ->andWhere(['egon_box_ports.u_s_p_i'=>null])
        ->asArray()
        ->all();


        if ( Yii::$app->request->isAjax && Yii::$app->request->post() ) {
             Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $model->u_s_p_i = null;
            if (Yii::$app->request->post('EgonBoxPorts')['status'] == "0" || Yii::$app->request->post('EgonBoxPorts')['status'] == "2") {
                $model->status = Yii::$app->request->post('EgonBoxPorts')['status'];
            }
            if (Yii::$app->request->post('EgonBoxPorts')['status'] == "1") {
                $model->u_s_p_i = Yii::$app->request->post('EgonBoxPorts')['u_s_p_i'];
                $model->status = Yii::$app->request->post('EgonBoxPorts')['status'];
            }

            if ( $model->save(false) ) {
                $userInet = \app\models\UsersInet::find()
                ->where(['u_s_p_i'=>Yii::$app->request->post('EgonBoxPorts')['u_s_p_i']])
                ->asArray()
                ->one();
                
                $logMessage = "{$model->egonBox->device->name} device {$model->egonBox->box_name} box  $model->port_number port number was updated to : {$userInet['login']}";
                Logs::writeLog(
                    Yii::$app->user->username, 
                    null, 
                    $logMessage, 
                    time()
                );

                return $this->redirect('/devices/index?DevicesSearch[name]='.rawurlencode($model->egonBox->device->name));

            }

        }


        return $this->renderIsAjax('tag-inet-login-to-box-port',['model'=>$model,'userServicePacketModel'=>$userServicePacketModel]);
    }

    public function actionUsePort($id){
        $model = \app\models\SwitchPorts::find()
        ->where(['id'=>$id])
        ->one();

        $deviceLocationModel = \app\models\DeviceLocations::find()
        ->where(['device_id'=>$model->device_id])
        ->andWhere(['not', ['device_id' => null]])
        ->asArray()
        ->all();

        $supportedLocationId = [];
        foreach ($deviceLocationModel as $deviceLocKey => $deviceLocation) {
             $supportedLocationId[] = $deviceLocation['location_id'];
        }
        $userServicePacketModel = \app\models\UsersServicesPackets::find()
        ->select('users_services_packets.*,users_inet.login as inet_login')
        ->leftJoin('users','users_services_packets.user_id=users.id')
        ->leftJoin('services','users_services_packets.service_id=services.id')
        ->leftJoin('users_inet','users_services_packets.id=users_inet.u_s_p_i')
        ->leftJoin('switch_ports','users_services_packets.id=switch_ports.u_s_p_i')
        ->where(['services.service_alias'=>'internet'])
        ->andWhere(['users.location_id'=>$supportedLocationId])
        ->andWhere(['switch_ports.u_s_p_i'=>null])
        ->asArray()
        ->all();

        if ( Yii::$app->request->post() ) {

            if (Yii::$app->request->post('SwitchPorts')['status'] == "0" || Yii::$app->request->post('SwitchPorts')['status'] == "2") {
                $model->u_s_p_i = null;
                $model->status = Yii::$app->request->post('SwitchPorts')['status'];
                $model->save(false);
            }
            if (Yii::$app->request->post('SwitchPorts')['status'] == "1") {
                $model->u_s_p_i = Yii::$app->request->post('SwitchPorts')['u_s_p_i'];
                $model->status = Yii::$app->request->post('SwitchPorts')['status'];
                if (  $model->save(false) ) {

                    $userInet = \app\models\UsersInet::find()
                    ->where(['u_s_p_i'=>Yii::$app->request->post('SwitchPorts')['u_s_p_i']])
                    ->asArray()
                    ->one();

                    $logMessage = "{$model->device->name} device {$model->port_number} port updated to : {$userInet['login']}";
                    Logs::writeLog(
                        Yii::$app->user->username, 
                        null, 
                        $logMessage, 
                        time()
                    );
                }
            }
    
             return $this->redirect('/devices/index?DevicesSearch[name]='.rawurlencode($model->device->name));
        }


        return $this->renderIsAjax('switch-port',['model'=>$model,'userServicePacketModel'=>$userServicePacketModel]);
    }

    public function actionListPonPort($id){
        $model = \app\models\EgponPonPort::find()
        ->where(['device_id'=>$id])
        ->asArray()
        ->all();

        $deviceModel = \app\models\Devices::find()
        ->where(['id'=>$id])
        ->asArray()
        ->one();

        $checkLocation = \app\models\DeviceLocations::find()->where(['device_id'=>$deviceModel['id']])
        ->asArray()
        ->all();

        return $this->renderIsAjax('egpon-pon-ports',['model'=>$model,'deviceModel'=>$deviceModel,'checkLocation'=>$checkLocation]);
    }

    public function actionSplitPonPort( $id ){
        $model = \app\models\EgponPonPort::find()
        ->where(['id'=>$id])
        ->one();

        $ponPortBoxes = \app\models\EgponBox::find()
        ->where(['egpon_pon_port_id'=>$model->id])
        ->all();

        $siteConfig = \app\models\SiteConfig::find()->one();

        if ( Yii::$app->request->post() && Yii::$app->request->isAjax ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                $checkEgponBoxPonPort = \app\models\EgonBoxPorts::find()
                ->leftJoin('egpon_box','egpon_box.id=egon_box_ports.egon_box_id')
                ->leftJoin('egpon_pon_port','egpon_pon_port.id=egpon_box.egpon_pon_port_id')
                ->where(['egpon_pon_port.id'=>$model->id])
                ->andWhere(['not', ['egon_box_ports.u_s_p_i' => null]])
                ->asArray()
                ->count();
               
                if ($checkEgponBoxPonPort != 0) {
                    return [
                        'status'=>'error',
                        'message'=>Yii::t('app','You have {count} ports in {ponPort} pon-port on device {device} ,Please remove all ports and Try Configure again!',
                            [
                                'count'=>$checkEgponBoxPonPort,
                                'device'=>$model->device->name,
                                'ponPort'=>$model->pon_port_number
                            ]
                    )
                    ];
                }
                
            $model->splitting = Yii::$app->request->post('EgponPonPort')['splitting'];
            $model->status = Yii::$app->request->post('EgponPonPort')['status'];

            if ($model->save(false)) {
                \app\models\EgponBox::deleteAll('egpon_pon_port_id = :egpon_pon_port_id', array(':egpon_pon_port_id' => $model->id));
                if ($model->device->type == "epon") {
                   $ponPortSize = 64;
                }elseif($model->device->type == "gpon"){
                   $ponPortSize = 128;
                }elseif($model->device->type == "xpon"){
                    $ponPortSize = 256;
                }
                $boxesCount = intval($model->splitting);
                $perBoxPortCount = $ponPortSize / $boxesCount;

           

                for ($box = 1; $box <= $boxesCount; $box++) { 
                    $egponBoxModel = new \app\models\EgponBox;
                    $egponBoxModel->device_id  = $model->device_id;
                    $egponBoxModel->egpon_pon_port_id  = $model->id;
                    $egponBoxModel->box_name  ="P".(sprintf("%02d", $model->pon_port_number))."B".sprintf("%02d", $box);
                    $egponBoxModel->location_id  = null;
                    $egponBoxModel->pon_port_number = $model->pon_port_number;
                    $egponBoxModel->box_number = $box;
                    if ($egponBoxModel->save(false)) {
                       for ($port=1; $port <= $perBoxPortCount ; $port++) { 
                           $egonBoxPortsModel = new \app\models\EgonBoxPorts;
                           $egonBoxPortsModel->egon_box_id =  $egponBoxModel->id;
                           $egonBoxPortsModel->port_number =  $port;
                           $egonBoxPortsModel->u_s_p_i =  null;
                           $egonBoxPortsModel->status =  '0';
                           $egonBoxPortsModel->save(false);
                       }
                    }

                }
                $ponPortNumber = "P".(sprintf("%02d", $model->pon_port_number));
                $logMessage = "{$ponPortNumber} pon port splitted  {$boxesCount} box on {$model->device->name}";
                Logs::writeLog(
                    Yii::$app->user->username, 
                    null, 
                    $logMessage, 
                    time()
                );
                return $this->redirect('/devices/index?DevicesSearch[name]='.rawurlencode($model->device->name));
            }
        }

        return $this->renderIsAjax('egpon-pon-port',['model'=>$model,'ponPortBoxes'=>$ponPortBoxes,'siteConfig'=>$siteConfig]);
    }

    public function actionAddLocation($id){
        $model = new \app\models\DeviceLocations;

        $deviceModel = \app\models\Devices::find()
        ->where(['id'=>$id])
        ->asArray()
        ->one();

        $deviceLocationsModel = \app\models\DeviceLocations::find()
        ->select('device_locations.*,devices.name as device_name,devices.type as device_type,address_cities.city_name as device_city,address_district.district_name as device_district,address_locations.name as device_location')
        ->leftJoin('devices','devices.id=device_locations.device_id')
        ->leftJoin('address_cities','address_cities.id=device_locations.city_id')
        ->leftJoin('address_district','address_district.id=device_locations.district_id')
        ->leftJoin('address_locations','address_locations.id=device_locations.location_id')
        ->where(['device_locations.device_id'=>$id])
        ->asArray()
        ->all();

        if ( $model->load(Yii::$app->request->post()) && $model->validate() ) {

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ( isset(Yii::$app->request->post('DeviceLocations')['location_id']) ) {
   
                    $checkExistLocation = \app\models\DeviceLocations::find()
                    ->where(['device_id'=>$deviceModel['id']])
                    ->andWhere(['city_id'=>Yii::$app->request->post('DeviceLocations')['city_id']])
                    ->andWhere(['district_id'=>Yii::$app->request->post('DeviceLocations')['district_id']])
                    ->andWhere(['location_id'=>Yii::$app->request->post('DeviceLocations')['location_id']])
                    ->asArray()
                    ->one();
                    if ($checkExistLocation != null) {
                        return [
                            'status'=>'error',
                            'message'=>Yii::t('app','You must add location before for this switch.Check swith support location list')
                        ];
                    }else{
                        $deviceLocations =  new \app\models\DeviceLocations;
                        $deviceLocations->device_id = $deviceModel['id'];
                        $deviceLocations->city_id  = Yii::$app->request->post('DeviceLocations')['city_id'];
                        $deviceLocations->district_id  = Yii::$app->request->post('DeviceLocations')['district_id'];
                        $deviceLocations->location_id   = Yii::$app->request->post('DeviceLocations')['location_id'];
                        if ( $deviceLocations->save(false) ) {
                            // re-name switch (deviceName)
                            #1.Find district name 
                            #2.Check count added location switch count + 1 (combine device table to device_locations)
                            #3.Re-name device which it is switch

                            $districtModel = \app\models\District::find()
                            ->where(['id'=>Yii::$app->request->post('DeviceLocations')['district_id']])
                            ->asArray()
                            ->one();

                            #1
                            $districtName = $districtModel['district_name'];
                            #2 
                            $countSwitchQueryWithDistrict = \app\models\DeviceLocations::find()
                            // ->where(['city_id'=>Yii::$app->request->post('DeviceLocations')['city_id']])
                            ->andWhere(['district_id'=>Yii::$app->request->post('DeviceLocations')['district_id']])
                            ->count();
                            #3
                            $deivceModelQuery = \app\models\Devices::find()
                            ->where(['id'=>$deviceModel['id']])
                            ->one();
                            $deivceModelQuery->name = $districtName."-SW".$countSwitchQueryWithDistrict;
                            $deivceModelQuery->save(false);

                             return $this->redirect('index?DevicesSearch[name]='.rawurlencode($districtName."-SW".$countSwitchQueryWithDistrict));
                        }
                    }

                
            }else{
                // $checkDeviceLocationsCount = \app\models\DeviceLocations::find()
                // ->andWhere(['device_id'=>$deviceModel['id']])
                // ->asArray()
                // ->count();

                // if (  $checkDeviceLocationsCount != 0  ) {
                //     return [
                //         'status'=>'error',
                //         'message'=>Yii::t('app','Location limit,You can oly one location for this device')
                //     ];
                // }


                $checkExistLocation = \app\models\DeviceLocations::find()
                ->where(['city_id'=>Yii::$app->request->post('DeviceLocations')['city_id']])
                ->andWhere(['district_id'=>Yii::$app->request->post('DeviceLocations')['district_id']])
                ->andWhere(['device_id'=>$deviceModel['id']])
                ->asArray()
                ->one();

                if ($checkExistLocation != null) {
                    return [
                        'status'=>'error',
                        'message'=>Yii::t('app','This location was added before.Please check location list')
                    ];
                }

                $model->location_id = null;

               if ( $model->save() ) {
                   return $this->redirect('index?DevicesSearch[name]='.rawurlencode($deviceModel['name']));
               }
            }
      
        }

        return $this->renderIsAjax('add-location',[
            'model'=>$model,
            'deviceModel'=>$deviceModel,
            'deviceLocationsModel'=>$deviceLocationsModel
        ]);
    }

    public function actionAddLocationValidate(){
        $model = new \app\models\DeviceLocations();
        $model->scenario = \app\models\DeviceLocations::ADD_LOCATION;
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionUpdateDeviceLocation($id){
        $model = \app\models\DeviceLocations::find()
        ->where(['id'=>$id])
        ->one();

        if (Yii::$app->request->post() && Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;



            if ( $model->device->type == "epon" ||  $model->device->type == "gpon" ) {
                $checkEgponBoxPorts = \app\models\EgonBoxPorts::find()
                ->leftJoin('egpon_box','egpon_box.id=egon_box_ports.egon_box_id')
                ->where(['egpon_box.device_id'=>$model->device_id])
                ->andWhere(['NOT', ['u_s_p_i' => null]])
                ->count();
                if ($checkEgponBoxPorts != 0) {
                    return [
                        'status'=>'error',
                        'message'=>Yii::t(
                            'app',
                            'You have {count} ports at {district} district,Please remove all then Try change',
                            [
                                'count'=>$checkEgponBoxPorts,
                                'district'=>$model->district->district_name,
                            ]
                        )
                    ];
                }
            }

            if ( $model->device->type == "switch" ) {

                $checkSwitchPortCount = \app\models\SwitchPorts::find()
                ->where(['device_id'=>$model->device_id])
                ->andWhere(['not', ['u_s_p_i' => null]])
                ->count();



                if ($checkSwitchPortCount != 0) {
                    return [
                        'status'=>'error',
                        'message'=>Yii::t(
                            'app',
                            'You have {count} ports at {location} location,Please remove all then Try change',
                            [
                                'count'=>$checkSwitchPortCount,
                                'location'=>$model->location->name
                            ]
                        )
                    ];
                }
            }

            $post = Yii::$app->request->post('DeviceLocations');
            $cityId = $post['city_id'];
            $districtId = $post['district_id'];

            $model->city_id = $cityId;
            $model->district_id = $districtId;

            if (isset($post['location_id'])) {
                $locationId = $post['location_id'];
                $model->location_id = $locationId;
            }
            if ($model->save(false)) {
               return [
                'status'=>'success',
                'url'=>\yii\helpers\Url::to(['index?DevicesSearch[name]='.rawurlencode($model->device->name)], true)
                ];
            }
        }

        return $this->renderIsAjax('update-device-location',['model'=>$model]);
    }

    public function actionDelete($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);
        if ($model->type == "epon" || $model->type == "gpon") {

            $checkEgponBoxPortsCount = \app\models\EgonBoxPorts::find()
            ->leftJoin('egpon_box','egpon_box.id=egon_box_ports.egon_box_id')
            ->where(['egpon_box.device_id'=>$model->id])
            ->andWhere(['NOT', ['u_s_p_i' => null]])
            ->count();
            if ($checkEgponBoxPortsCount != 0) {
                return [
                    'status'=>'error',
                    'message'=>Yii::t(
                        'app',
                        'You have {activePortCount} ports on {deviceName} device,Please remove all then delete device',
                        [
                            'activePortCount'=>$checkEgponBoxPortsCount,
                            'deviceName'=>$model->name,
                        ]
                    )
                ];
            }
        }


        if ($model->type == "switch") {

            $checkSwitchPortCount = \app\models\SwitchPorts::find()
            ->where(['device_id'=>$model->id])
            ->andWhere(['not', ['u_s_p_i' => null]])
            ->count();

            if ($checkSwitchPortCount != 0 ) {
                return [
                    'status'=>'error',
                    'message'=>Yii::t(
                            'app',
                            'You have {activePortCount} ports on {deviceName} device,Please remove all then delete device',
                            [
                                'activePortCount'=>$checkSwitchPortCount,
                                'deviceName'=>$model->name,
                            ]
                        )
                    ];
            }
        }

        $deviceName = $model['name'];
        if ($model->delete()) {
            $memberUsername = Yii::$app->user->username;
            $logMessage = "{$deviceName} was deleted from devices by {$memberUsername}";
            \app\models\Logs::writeLog(Yii::$app->user->username,null, $logMessage, time());
            return [
                'status' => 'success',
                'message'=>Yii::t(
                    'app',
                    '{deviceName} was deleted from devices by {memberUsername}',
                    [
                        'memberUsername'=>$memberUsername,
                        'deviceName'=>$deviceName,
                    ]
                )
            ];
        }
    }


    public function actionDeleteDeviceFromLocation($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = \app\models\DeviceLocations::find()
        ->where(['id'=>$id])
        ->one();


        if ($model->device->type == "epon" || $model->device->type == "gpon") {
            $locationOfDistrtict = \app\models\Locations::find()
            ->where(['district_id'=>$model->district_id])
            ->asArray()
            ->all();

            $supportedLocations = [];
            foreach ($locationOfDistrtict as $locKey => $loc) {
                 $supportedLocations[] = $loc['id'];
            }

            $checkEgponBoxPortsCount = \app\models\EgonBoxPorts::find()
            ->leftJoin('egpon_box','egpon_box.id=egon_box_ports.egon_box_id')
            ->where(['egpon_box.device_id'=>$model->device_id])
            ->andWhere(['egpon_box.location_id'=>$supportedLocations])
            ->andWhere(['NOT', ['u_s_p_i' => null]])
            ->count();

            if ($checkEgponBoxPortsCount != 0) {
                return [
                    'status'=>'error',
                    'message'=>Yii::t(
                        'app',
                        'You have {activePortCount} ports on {deviceName} device at {districtName} district ,Please remove all active ports then delete district',
                        [
                            'activePortCount'=>$checkEgponBoxPortsCount,
                            'deviceName'=>$model->device->name,
                            'districtName'=>$model->district->district_name,
                        ]
                    )
                ];
            }
        }



        if ($model->device->type  == "switch") {

            $checkSwitchPortCount = \app\models\SwitchPorts::find()
            ->leftJoin('devices','devices.id=switch_ports.device_id')
            ->where(['device_id'=>$model->device_id])
            ->where(['devices.location_id'=>$model->location_id])
            ->andWhere(['not', ['u_s_p_i' => null]])
            ->count();

            if ($checkSwitchPortCount != 0 ) {
                return [
                    'status'=>'error',
                    'message'=>Yii::t(
                            'app',
                            'You have {activePortCount} ports on {deviceName} device at {districtName} district {locationName} location,Please remove all active ports then delete location',
                            [
                                'activePortCount'=>$checkSwitchPortCount,
                                'deviceName'=>$model->device->name ,
                                'districtName'=>$model->district->district_name ,
                                'locationName'=>$model->location->name ,
                            ]
                        )
                    ];
            }
        }




        $city = $model->city->city_name;
        $district = $model->district->district_name;

        if ($model->location_id != null) {
            $location = $model->location->name;
        }else{
            $location = '';
        }

        $memberUsername = Yii::$app->user->username;

        if ($model->delete()) {
            $logMessage = "{$city} {$district} {$location} located device  was deleted from device coverage";
            \app\models\Logs::writeLog(Yii::$app->user->username,null, $logMessage, time());
            return [
                'status' => 'success',
                'message'=>Yii::t(
                    'app',
                    '{city} {district} {location} located device  was deleted from devices by {memberUsername}',
                    [
                      'city'=>$city,  
                      'district'=>$district,  
                      'location'=>$location,  
                      'memberUsername'=>$memberUsername,  
                    ]
                )
            ];
        }
    }




}

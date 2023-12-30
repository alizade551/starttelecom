<?php

namespace app\controllers;

use Yii;
use app\components\DefaultController;
use app\models\Routers;
use yii\web\Controller;
use app\components\RouterosApi;

/**
 * RoutersController implements the CRUD actions for Routers model.
 */

class RoutersController extends DefaultController
{

    public $modelClass = 'app\models\Routers';
    public $modelSearchClass = 'app\models\search\RouterSearch';
    // public $modelSearchClass = 'app\smodels\RoutersSearch';

    public function actionIntegrateValidate()
    {
        $model = new \app\models\Users();
        $model->scenario = \app\models\Users::INTEGRATE_USER;
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }


    public function actionActiveDeactive($id)
    {
        $model = Routers::find()->where(['id'=>$id])->asArray()->one();

        return $this->renderIsAjax('router-active-deactive',['model'=>$model]);
    }




  

   public function actionActiveUsers($id)
    {
        $model = Routers::find()->where(['id'=>$id])->asArray()->one();

        $searchModel = new \app\models\search\OnlineUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('active-users', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model
        ]);
    }




    public function actionGetActiveDeactive( $nas,$username,$password ){
        if ( Yii::$app->request->isAjax ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $routerModel = \app\models\Routers::find()
            ->where(['nas'=>$nas])
            ->asArray()
            ->one();

            $API = new RouterosAPI();
            $API->debug = false;
            if ( $API->connect($nas , $username, $password) ) {
                $data = $API->comm("/ppp/secret/print");
            }

            $API->disconnect();

            $activeCount = 0;
            $deactiveCount = 0;
            $checkRouterInet = \app\models\UsersInet::find()
            ->where(['status'=>'2'])
            ->andWhere(['router_id'=>$routerModel['id']])
            ->asArray()
            ->all();

            $unLegalLogins = [];
            foreach ( $data as $secretKey => $secret ) {
                if ( $secret['disabled'] == "false" ) {
                    foreach ( $checkRouterInet as $key => $inet ) {
                        if ( $secret['name'] == $inet['login'] ) {
                            $unLegalLogins[] = "<div style='display:inline-block'><a href='users/view?id=".$inet['user_id']."'>".$inet['login']."</a></div>";
                        }
                    }
                     $activeCount ++;
                }
                if ( $secret['disabled'] == "true" ) {
                     $deactiveCount ++;
                }
            }
            
           $result = [
                'activeCount'=>$activeCount,
                'deactiveCount'=>$deactiveCount,
                'unLegalLogins'=>$unLegalLogins,
            ];

            return $result;
        }
    }



    // public function actionIntegrate($id){

    //     $routerModel = \app\models\Routers::find()
    //     ->where(['id'=>$id])
    //     ->asArray()
    //     ->one();

    //     if ( $routerModel == null) {
    //         return ['status'=>'error','message'=>Yii::t('app','Router not found')];
    //     }

    //     $supportedDistrictsQuery = \app\models\District::find()
    //     ->where(['router_id'=>$routerModel['id']]);

    //     $supportedCity = \app\models\Cities::find()
    //     ->select('address_cities.city_name,address_cities.id as city_id')
    //     ->leftJoin('address_district','address_district.city_id=address_cities.id')
    //     ->where(['address_district.router_id'=>$routerModel['id']])
    //     ->asArray()
    //     ->one();


    //     $model = new \app\models\Users;
    //     $model->scenario = \app\models\Users::INTEGRATE_USER;
  
    //     if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {

    //         $profilesPrint = \app\components\MikrotikQueries::pppProfilePrint( $routerModel['nas'], $routerModel['username'], $routerModel['password'] );
    //         $filtredProfilesPrint = array_filter($profilesPrint,function($profile){
    //             return $profile['name'] != "default" && $profile['name'] != "default-encryption";
    //         },ARRAY_FILTER_USE_BOTH);


    //         $profilesPrintArr = [];
    //         foreach ($filtredProfilesPrint as $key => $profil) {
    //             $profilesPrintArr[$key]['name'] = $profil['name'];
    //             if ( isset( $profil['local-address'] ) ) {
    //                 $profilesPrintArr[$key]['local-address'] = $profil['local-address'];
    //             }
    //             if ( isset( $profil['remote-address'] ) ) {
    //                 $profilesPrintArr[$key]['remote-address'] = $profil['remote-address'];
    //             }
    //             $profilesPrintArr[$key]['bridge-learning'] = $profil['bridge-learning'];
    //             $profilesPrintArr[$key]['use-mpls'] = $profil['use-mpls'];
    //             $profilesPrintArr[$key]['use-compression'] = $profil['use-compression'];
    //             $profilesPrintArr[$key]['use-encryption'] = $profil['use-encryption'];
    //             $profilesPrintArr[$key]['only-one'] = $profil['only-one'];
    //             $profilesPrintArr[$key]['change-tcp-mss'] = $profil['change-tcp-mss'];
    //             $profilesPrintArr[$key]['use-upnp'] = $profil['use-upnp'];
    //             $profilesPrintArr[$key]['rate-limit'] = $profil['rate-limit'];
    //         }

    //         $inetPackets = \app\models\Packets::find()
    //         ->leftJoin('services','services.id=service_packets.service_id')
    //         ->where(['services.service_alias'=>'internet'])
    //         ->asArray()
    //         ->all();

    //         $dbPacketArr = [];
    //         foreach ( $inetPackets as $key => $packet ) {
               
    //                 $dbPacketArr[$key]['name'] = $packet['packet_name'];
    //                 if ( $packet['type'] == "0" ) {
    //                     $dbPacketArr[$key]['local-address'] = "172.16.10.10";
    //                     $dbPacketArr[$key]['remote-address'] = $routerModel['ip_pool_var'];
    //                 }
    //                 $dbPacketArr[$key]['bridge-learning'] = "default";
    //                 $dbPacketArr[$key]['use-mpls'] = $packet['use_mpls'];
    //                 $dbPacketArr[$key]['use-compression'] = $packet['use_compression'];
    //                 $dbPacketArr[$key]['use-encryption'] = $packet['use_encryption'];
    //                 $dbPacketArr[$key]['only-one'] = $packet['only_one'];
    //                 $dbPacketArr[$key]['change-tcp-mss'] = $packet['change_tcp_mss'];
    //                 $dbPacketArr[$key]['use-upnp'] = $packet['use_upnp'];
    //                 $dbPacketArr[$key]['rate-limit'] = $packet['download']."k"."/".$packet['upload']."k";
                
    //         }

    //         $compareProfiles = \app\components\Utils::arrayDifference($dbPacketArr,$profilesPrintArr);
    //         $service = \app\models\Services::find()->where(['service_alias'=>'internet'])->asArray()->one();

    //         if ( count( $compareProfiles['insertions'] ) >  0 ) {
    //             // save to database 
    //             foreach ( $compareProfiles['insertions'] as $insertKey => $insert ) {
    //                 $type = ( isset( $insert['remote-address'] )  ) ? "0" : "1";
    //                 $packetModel = new \app\models\Packets;
    //                 $packetModel->service_id = $service['id'];
    //                 $packetModel->packet_name = $insert['name'];
    //                 $packetModel->download = intval(explode("/",$insert['rate-limit'])[0]);
    //                 $packetModel->upload = intval(explode("/",$insert['rate-limit'])[1]);
    //                 $packetModel->change_tcp_mss = $insert['change-tcp-mss'];
    //                 $packetModel->use_upnp = $insert['use-upnp'];
    //                 $packetModel->use_mpls = $insert['use-mpls'];
    //                 $packetModel->use_compression = $insert['use-compression'];
    //                 $packetModel->use_encryption = $insert['use-encryption'];
    //                 $packetModel->only_one = $insert['only-one'];
    //                 $packetModel->type = $type;
    //                 $packetModel->packet_price = 0;
    //                 $packetModel->position = 9;
    //                 $packetModel->created_at = time();
    //                 if ( $packetModel->save( false ) ) {

    //                     $routers = \app\models\Routers::find()
    //                     ->asArray()
    //                     ->all();

    //                     foreach ($routers as $router_key => $routerOne) {
    //                          $routersServicePacketsModel =  new \app\models\RoutersServicePackets;
    //                          $routersServicePacketsModel->router_id = $routerOne['id'];
    //                          $routersServicePacketsModel->packet_id = $packetModel->id;
    //                          $routersServicePacketsModel->status = "1";
    //                          if ( $routersServicePacketsModel->save(false) ) {
    //                              if ( $routerOne['id'] != $routerModel['id'] ) {
    //                                     if ( $type == "1" ) {
    //                                         \app\components\MikrotikQueries::pppProfileAddStatic(
    //                                             $insert['name'],
    //                                             intval(explode("/",$insert['rate-limit'])[0]),
    //                                             intval(explode("/",$insert['rate-limit'])[1]),
    //                                             $insert['change-tcp-mss'],
    //                                             $insert['use-upnp'],
    //                                             $insert['use-mpls'],
    //                                             $insert['use-compression'],
    //                                             $insert['use-encryption'],
    //                                             $insert['only-one'],
    //                                             $routerOne['nas'],
    //                                             $routerOne['username'],
    //                                             $routerOne['password'],
    //                                             "pppProfileAddStatic",
    //                                             [
    //                                                 $insert['name'],
    //                                                 intval(explode("/",$insert['rate-limit'])[0]),
    //                                                 intval(explode("/",$insert['rate-limit'])[1]),
    //                                                 $insert['change-tcp-mss'],
    //                                                 $insert['use-upnp'],
    //                                                 $insert['use-mpls'],
    //                                                 $insert['use-compression'],
    //                                                 $insert['use-encryption'],
    //                                                 $insert['only-one'],
    //                                                 $routerOne['nas'],
    //                                                 $routerOne['username'],
    //                                                 $routerOne['password']
    //                                             ]
    //                                         );
    //                                     }else{
                         
    //                                         \app\components\MikrotikQueries::pppProfileAdd(
    //                                             $insert['name'],
    //                                             $routerOne['ip_pool_var'],
    //                                             "172.16.10.10",
    //                                             intval(explode("/",$insert['rate-limit'])[0]),
    //                                             intval(explode("/",$insert['rate-limit'])[1]),
    //                                             $insert['change-tcp-mss'],
    //                                             $insert['use-upnp'],
    //                                             $insert['use-mpls'],
    //                                             $insert['use-compression'],
    //                                             $insert['use-encryption'],
    //                                             $insert['only-one'],
    //                                             $routerOne['nas'],
    //                                             $routerOne['username'],
    //                                             $routerOne['password'],
    //                                             "pppProfileAdd",
    //                                             [
    //                                                 $insert['name'],
    //                                                 $routerOne['ip_pool_var'],
    //                                                 intval(explode("/",$insert['rate-limit'])[0]),
    //                                                 intval(explode("/",$insert['rate-limit'])[1]),
    //                                                 $insert['change-tcp-mss'],
    //                                                 $insert['use-upnp'],
    //                                                 $insert['use-mpls'],
    //                                                 $insert['use-compression'],
    //                                                 $insert['use-encryption'],
    //                                                 $insert['only-one'],
    //                                                 $routerOne['nas'],
    //                                                 $routerOne['username'],
    //                                                 $routerOne['password']
    //                                             ]
    //                                         );
    //                                     }
    //                              }
    //                          }


    //                     }

    //                 }



    //             }
    //         }


    //         $secretsPrint = \app\components\MikrotikQueries::pppSecretPrint( $routerModel['nas'], $routerModel['username'], $routerModel['password'] );



    //         $supportedDistrictId = array_reduce( $supportedDistrictsQuery->asArray()->all() , function( $accumulator, $item) {
    //               $accumulator[] = $item['id'];
    //               return $accumulator;
    //         },[]);

    //         $secretCount = 0;
    //         foreach ($secretsPrint as $secretKey => $secret ) {
    //             $userServicePacketModel = \app\models\UsersServicesPackets::find()
    //             ->select('users_inet.login as name,users_inet.password as password,users_inet.static_ip as remote_address,users_inet.status as secret_status')
    //             ->leftJoin('users_inet','users_inet.u_s_p_i=users_services_packets.id')
    //             ->leftJoin('users','users.id=users_services_packets.user_id')
    //             ->where(['users.district_id'=>$supportedDistrictId])
    //             ->andWhere(['service_id'=>$service['id']])
    //             ->andWhere(['users_inet.login'=>$secret['name']])
    //             ->asArray()
    //             ->one();

    //             if ( $userServicePacketModel == null ) {
    //                     $findPacket = \app\models\Packets::find()
    //                     ->where(['service_id'=>$service['id']])
    //                     ->andWhere(['packet_name'=>$secret['profile']])
    //                     ->asArray()
    //                     ->one();

    //                     if ( $findPacket != null ) {
    //                         $secretCount++;
    //                         // adding to request order 
    //                         $intergrateUser = new \app\models\Users;
    //                         $intergrateUser->fullname = "intergrated_".$secret['name'];
    //                         $intergrateUser->password = null;
    //                         $intergrateUser->phone = null;
    //                         $intergrateUser->city_id =  $supportedCity['city_id'];
    //                         $intergrateUser->district_id = $model->district_id;
    //                         $intergrateUser->location_id = $model->location_id;
    //                         $intergrateUser->room = null;
    //                         $intergrateUser->cordinate = null;
    //                         $intergrateUser->selected_services = '1';
    //                         $intergrateUser->tariff = $findPacket['packet_price'];
    //                         $intergrateUser->balance = 0;
    //                         $intergrateUser->bonus = 0;
    //                         $intergrateUser->status = 0;
    //                         $intergrateUser->paid_time_type = '0';
    //                         $intergrateUser->request_at = time();
    //                         $intergrateUser->created_at = time();
    //                         if ( $intergrateUser->save(false) ) {

    //                            $intergrateUserServicePacket = new \app\models\UsersServicesPackets;
    //                            $intergrateUserServicePacket->user_id =  $intergrateUser->id;
    //                            $intergrateUserServicePacket->service_id =  $service['id'];
    //                            $intergrateUserServicePacket->packet_id =  $findPacket['id'];
    //                            if ( $secret['disabled'] == "false" ) {
    //                                $intergrateUserServicePacket->status = "1";
    //                            }else{
    //                                $intergrateUserServicePacket->status = "2";
    //                            }
    //                            $intergrateUserServicePacket->created_at = time();
    //                            if ( $intergrateUserServicePacket->save( false ) ) {
    //                                $intergrateUserInet = new \app\models\UsersInet;
    //                                $intergrateUserInet->user_id = $intergrateUser->id;
    //                                $intergrateUserInet->router_id = $routerModel['id'];
    //                                $intergrateUserInet->packet_id = $findPacket['id'];
    //                                $intergrateUserInet->u_s_p_i = $intergrateUserServicePacket->id;
    //                                $intergrateUserInet->login = $secret['name'];
    //                                $intergrateUserInet->password = $secret['password'];

    //                                if ( isset( $secret['remote-address'] ) ) {
    //                                     $statIpModel = new \app\models\IpAdresses;
    //                                     $statIpModel->public_ip = $secret['remote-address'];
    //                                     $statIpModel->router_id = $routerModel['id'];
    //                                     $statIpModel->type = '1';
    //                                     $statIpModel->status = '1';
    //                                     $statIpModel->created_at = time();
    //                                     if ( $statIpModel->save(false) ) {
    //                                        $intergrateUserInet->static_ip = $secret['remote-address'];
    //                                     }
    //                                }
 
    //                                if ( $secret['disabled'] == "false" ) {
    //                                    $intergrateUserInet->status = "1";
    //                                }else{
    //                                    $intergrateUserInet->status = "2";
    //                                }

    //                                $intergrateUserInet->created_at = time();
    //                                $intergrateUserInet->save(false);
    //                            }


    //                         }
                            
    //                     }
    //             }
    //         }
            
    //         return [
    //             'status'=>'success',
    //             'message'=>Yii::t('app','Intgrated profil count: {profileCount} , intergrated secret count: {secretCount}',['secretCount'=>$secretCount,'profileCount'=> count( $compareProfiles['insertions'] ) ])
    //         ];
            
    //     }
    //     return $this->renderIsAjax('integrate', [
    //         'model' => $model,
    //         'routerModel' => $routerModel,
    //         'supportedCity' => $supportedCity,
    //         'supportedDistrictsQuery' => $supportedDistrictsQuery,

    //     ]);
    // }

    public function actionCreate(){
        $model = new Routers();
        $siteConfig = \app\models\SiteConfig::find()->one();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('create', [
            'model' => $model,
            'siteConfig' => $siteConfig
        ]);
    }

    public function actionReboot($id){
        $model = Routers::find()->where(['id'=>$id])->asArray()->one();

        if ( Yii::$app->request->isAjax && Yii::$app->request->isPost ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

           $routerIsReboot =  \app\components\MikrotikQueries::reboot( 
                $model['nas'],
                $model['username'],
                $model['password'],
                "reboot",
                [
                    'nas'=>$model['nas'],
                    'router_username'=>$model['username'],
                    'router_password'=>$model['password'],
                ]
             );

           if ( $routerIsReboot ) {
             return ['status'=>'success','message'=>Yii::t('app','Router was rebooted')];
           }else{
            return ['status'=>'error','message'=>Yii::t('app','Router was not rebooted.Please contact network adminstrator')];
           }

        }
  
        return $this->renderIsAjax('reboot',['model'=>$model]);
    }

    public function actionRouterLog($id){
        $model = Routers::find()->where(['id'=>$id])->asArray()->one();
            $API = new RouterosAPI();
            $API->debug = false;
            if ( $API->connect($model['nas'] , $model['username'], $model['password']) ) {
                $log = $API->comm("/log/print");
                $countLog = count($log);
                $API->disconnect();
            }
        return $this->renderIsAjax('router-log',['model'=>$model,'log'=>$log,'countLog'=>$countLog]);
    }

    public function actionRouterTotalTraffic($id){
          if ( Yii::$app->request->isAjax ) {
            $routerModel = \app\models\Routers::find()
            ->where(['id'=>$id])
            ->asArray()
            ->one();


            $data = \app\components\MikrotikQueries::interfacePrintName(
                $routerModel['interface'], 
                $routerModel['nas'], 
                $routerModel['username'], 
                $routerModel['password']
            );

           $tx_byte =  \app\components\Utils::formatBytes( $data['tx-byte'],'G',2)." GB";
           $rx_byte =  \app\components\Utils::formatBytes( $data['rx-byte'],'G',2)." GB";
           $mac_address = $data['mac-address'];
           $type = $data['type'];
           $mtu = ( isset( $data['mtu'] ) ) ? $data['mtu'] : "-";
           $actual_mtu = ( isset( $data['actual-mtu'] ) ) ? $data['actual-mtu'] : "-";
           $l2mtu = ( isset( $data['l2mtu'] ) ) ? $data['l2mtu'] : "-";
           $max_l2mtu = ( isset( $data['max-l2mtu'] ) ) ? $data['max-l2mtu'] : "-";
           $last_link_up_time = ( isset( $data['last-link-up-time'] ) ) ? $data['last-link-up-time'] : "-";
           $link_downs = ( isset( $data['link-downs'] ) ) ? $data['link-downs'] : "-";
           $rx_packet =  \app\components\Utils::formatBytes( $data['rx-packet'],'G',2)." GB";
           $tx_packet =  \app\components\Utils::formatBytes( $data['tx-packet'],'G',2)." GB";
           $rx_drop = ( isset( $data['rx-drop'] ) ) ? $data['rx-drop'] : "-"; 
           $tx_drop = ( isset( $data['tx-drop'] ) ) ? $data['tx-drop'] : "-";
           $fp_rx_byte =  \app\components\Utils::formatBytes( $data['fp-rx-byte'],'G',2)." GB";
           $fp_tx_byte =  \app\components\Utils::formatBytes( $data['fp-tx-byte'],'G',2)." GB";
           $fp_rx_packet =  \app\components\Utils::formatBytes( $data['fp-rx-packet'],'G',2)." GB";
           $fp_tx_packet =  \app\components\Utils::formatBytes( $data['fp-tx-packet'],'G',2)." GB";
           $tx_queue_drop =  ( isset( $data['tx-queue-drop'] ) ) ? $data['tx-queue-drop'] : "-";

            return [
                "tx_byte"=>$tx_byte,
                "rx_byte"=>$rx_byte,
                "mac_address"=>$mac_address,
                "type"=>$type,
                "mtu"=>$mtu,
                "actual_mtu"=>$actual_mtu,
                "l2mtu"=>$l2mtu,
                "max_l2mtu"=>$max_l2mtu,
                "last_link_up_time"=>$last_link_up_time,
                "link_downs"=>$link_downs,
                "rx_packet"=>$rx_packet,
                "tx_packet"=>$tx_packet,
                "rx_drop"=>$rx_drop,
                "tx_drop"=>$tx_drop,
                "fp_rx_byte"=>$fp_rx_byte,
                "fp_tx_byte"=>$fp_tx_byte,
                "fp_rx_packet"=>$fp_rx_packet,
                "fp_tx_packet"=>$fp_tx_packet,
                "tx_queue_drop"=>$tx_queue_drop,

            ];
        }
        
    }

    public function actionRouterInfo($id){
        $model = Routers::find()->where(['id'=>$id])->asArray()->one();

        return $this->renderIsAjax('router-info',['model'=>$model]);
    }

    public function actionGetRouterUsage($nas,$username,$password){
        if ( Yii::$app->request->isAjax ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $API = new RouterosAPI();
            $API->debug = false;
            if ( $API->connect($nas , $username, $password) ) {
                $data = $API->comm("/system/resource/print");
            }

            $API->disconnect();
            
            return json_decode(json_encode($data[0]),true);
        }
    }

    public function actionRouter($id){
        $model = \app\models\Routers::find()->where(['id'=>$id])->asArray()->one();
        $query = \app\models\UsersInet::find()
        ->select('users_inet.*,users.status as user_status,routers.nas as router_nas,routers.name as router_name,routers.username as router_username,routers.password as router_password,service_packets.packet_name as packet_name')
        ->leftJoin('users','users.id=users_inet.user_id')
        ->leftJoin('users_services_packets','users_services_packets.user_id=users.id')
        ->leftJoin('services','services.id=users_services_packets.service_id')
        ->leftJoin('service_packets','service_packets.id=users_services_packets.packet_id')
        ->leftJoin('address_district','address_district.id=users.district_id')
        ->leftJoin('routers','routers.id=address_district.router_id');
    


        $vip_user_packet_count = $query->where(['users.status'=>7])->andWhere(['routers.id'=>$id])->andWhere(['routers.id'=>$id])->asArray()->all();
        $deactive_user_packet_count = $query->where(['users_inet.status'=>2,'users.status'=>2])->andWhere(['routers.id'=>$id])->asArray()->all();
        $active_user_packet_count = $query->where(['users.status'=>1])->andWhere(['routers.id'=>$id])->asArray()->all();
        $pending_user_packet_count = $query->where(['users.status'=>0])->andWhere(['routers.id'=>$id])->asArray()->all();
        $archive_user_packet_count = $query->where(['users.status'=>3])->andWhere(['routers.id'=>$id])->asArray()->all();


        $packets = $query->where(['routers.id'=>$id])
        ->andWhere(['!=', 'users.status', 3])
        ->asArray()->all();
 
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            // foreach ($packets as $key => $packet) {
   
            //        if ($packet['static_ip'] != "") {
            //           \app\components\MikrotikQueries::createStaticInternetPacketCommandPpo(
            //             $packet['login'],
            //             $packet['password'],
            //             $packet['packet_name'],
            //             "pppoe",
            //             "172.16.10.10",
            //             $packet['static_ip'],
            //             $packet['router_nas'],
            //             $packet['router_username'],
            //             $packet['router_password']
            //         );
            //        }else{
            //             \app\components\MikrotikQueries::createInternetPacketCommandPppo(
            //                 $packet['login'], 
            //                 $packet['password'], 
            //                 $packet['packet_name'],
            //                 "pppoe", 
            //                 $packet['router_nas'], 
            //                 $packet['router_username'], 
            //                 $packet['router_password']
            //             );
            //        }

            //         if ($packet['user_status'] == 2) {
            //             $API = new RouterosAPI;
            //             if ( $API->connect( $packet['router_nas'],  $packet['router_username'],  $packet['router_password'] ) ) {
            //                  $API->comm("/ppp/secret/disable",[
            //                   "numbers"=>$packet['login']]);
            //                  $API->comm("/interface/pppoe-server/remove",[
            //                   "numbers"=> "<pppoe-".$packet['login'].">"]);
            //                  $API->disconnect();
            //               }
            //         }
            // }
                 sleep(2);

           return ['status'=>'success','message'=>Yii::t('app','Router data was restored.')];
        }


        return $this->renderIsAjax('router.php',[
            'model'=>$model,
            'vip_user_packet_count'=>count($vip_user_packet_count),
            'deactive_user_packet_count'=>count($deactive_user_packet_count),
            'active_user_packet_count'=>count($active_user_packet_count),
            'pending_user_packet_count'=>count($pending_user_packet_count),
            'archive_user_packet_count'=>count($archive_user_packet_count),
        ]);
    }

    public function actionRouterChart($id){
        $model = \app\models\Routers::find()->where(['id' => $id])->one();

        return $this->renderIsAjax('rx-tx', ['model' => $model]);

    }

    public function actionGetRxTx($id){
        if (Yii::$app->request->isAjax ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $router_model = \app\models\Routers::find()
            ->where(['id' => $id])
            ->one();

            $data = \app\components\MikrotikQueries::checkRxTxRouter( 
                $router_model['nas'], 
                $router_model['username'], 
                $router_model['password'],
                $router_model['interface']
            );
            return $data;
        }
    }


}

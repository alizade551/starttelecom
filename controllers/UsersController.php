<?php

namespace app\controllers;

use app\components\DefaultController;
use app\models\Logs;
use app\models\search\UsersArchiveSearch;
use app\models\search\UsersSearch;
use app\models\Users;
use app\models\UsersHistory;
use app\models\UsersInet;
use app\models\UsersServicesPackets;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends DefaultController
{
    public function actionIndex(){
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStatistc(){
        $users_status = \app\models\Users::find()
        ->select('sum( case  when users.status = 1 then 1 else 0 end ) as active,sum( case  when users.status = 2 then 1 else 0 end ) as deactive,sum( case  when users.status = 3 then 1 else 0 end ) as archive,sum( case  when users.status = 0 then 1 else 0 end ) as pending,sum( case  when users.status = 6 then 1 else 0 end ) as black,sum( case  when users.status = 7 then 1 else 0 end ) as free_user,sum( case  when users.second_status = 5 then 1 else 0 end ) as new_service_user_count,sum( case  when users.damage_status = "1" then 1 else 0 end ) as damage_count')
        ->withByLocation()
        ->asArray()
        ->one();


        $usersStatusHistoryGraphich = \app\models\UsersStatusHistory::find()
        ->where(['DATE_FORMAT(FROM_UNIXTIME(users_status_history.created_at), "%Y")' => date('Y')])
        ->asArray()
        ->all();


        $active = [];
        $deactive = [];
        $archive = [];
        $pending = [];
        $vip = [];
        $black_list = [];
        $damage = [];
        $new_service = [];
        $stockedAxisCategories = [];

        foreach ($usersStatusHistoryGraphich as $graphicKey => $userStatusGroup ) {
            $stockedAxisCategories[] = date("M",$userStatusGroup['created_at']);
            $active[] = $userStatusGroup['active_count'];
            $deactive[] = $userStatusGroup['deactive_count'];
            $archive[] = $userStatusGroup['archive_count'];
            $pending[] = $userStatusGroup['pending_count'];
            $vip[] = $userStatusGroup['vip_count'];
            $damage[] = $userStatusGroup['damage_count'];
            $black_list[] = $userStatusGroup['black_list_count'];
            $new_service[] = $userStatusGroup['new_service'];
           
        }

        $graphicData = [
            [
                "name"=>Yii::t("app","Active"),
                "data"=>$active
            ],
            [
                "name"=>Yii::t("app","Deactive"),
                "data"=>$deactive
            ],
            [
                "name"=>Yii::t("app","Archive"),
                "data"=>$archive
            ],

            [
                "name"=>Yii::t("app","Pending"),
                "data"=>$pending
            ],
            [
                "name"=>Yii::t("app","VIP"),
                "data"=>$vip
            ],

            [
                "name"=>Yii::t("app","Black"),
                "data"=>$black_list
            ],

            [
                "name"=>Yii::t("app","Damage"),
                "data"=>$damage
            ],

            [
                "name"=>Yii::t("app","New service"),
                "data"=>$new_service
            ],

        ];

        $all_data = [
            'users_status' => $users_status,
            'pending_users_count' => $users_status['pending'],
            'damage_users_count' => $users_status['damage_count'],
            'graphicData'=>$graphicData,
            'stockedAxisCategories'=>$stockedAxisCategories,
        ];

        return $this->render('statistic', $all_data);
    }

    public function actionTagPortUserPacketValidate(){
        $model = new \app\models\UsersServicesPackets();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionTagPortUserPacket($id){
        $model = \app\models\UsersServicesPackets::find()
        ->where(['id'=>$id])
        ->one();

        $memberUsername = Yii::$app->user->username;
        $login = $model->usersInet->login;


        if ( Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if ( Yii::$app->request->post('UsersServicesPackets')['port_type'] == "epon" || Yii::$app->request->post('UsersServicesPackets')['port_type'] == "gpon" ) {

                $boxPortModel = \app\models\EgonBoxPorts::find()
                ->where(['id'=> Yii::$app->request->post('UsersServicesPackets')['box_port'] ])
                ->andWhere(['egon_box_id'=> Yii::$app->request->post('UsersServicesPackets')['box'] ])
                ->andWhere(['status'=>'0'])
                ->one();

                if ($boxPortModel == null) {
                   return [
                        'status'=>'error',
                        'message'=>Yii::t("app","Port busy or something went wrong!")
                    ];
                }

                $portNumber =  $boxPortModel->port_number;
                $boxName =  $boxPortModel->egonBox->box_name;
                $deviceName =  $boxPortModel->egonBox->device->name;


                $boxPortModel->u_s_p_i = $model->id;
                $boxPortModel->status = '1';
                if ($boxPortModel->save(false)) {
                    $logMessage = "{$deviceName} device {$boxName} box {$portNumber} port has been added to {$login}";
                    Logs::writeLog(
                        $memberUsername, 
                        intval($model->user_id), 
                        $logMessage, 
                        time()
                    );
                   return [
                        'status'=>'success',
                        'message'=>Yii::t(
                            "app","{deviceName} device {boxName} box {portNumber} port has been added to {login} by {memberUsername}",
                            [
                                'memberUsername'=>$memberUsername,
                                'login'=>$login,
                                'portNumber'=>$portNumber,
                                'deviceName'=>$boxName,
                                'boxName'=>$deviceName,
                            ]
                        )
                    ];
                }
            }


            if ( Yii::$app->request->post('UsersServicesPackets')['port_type'] == "switch" ) {
                $switchPortsModel = \app\models\SwitchPorts::find()
                ->where(['id'=>Yii::$app->request->post('UsersServicesPackets')['switch_port']])
                ->andWhere(['device_id'=>Yii::$app->request->post('UsersServicesPackets')['devices']])
                ->one();


                if ($switchPortsModel == null) {
                   return [
                        'status'=>'error',
                        'message'=>Yii::t("app","Port busy or something went wrong!")
                    ];
                }

                $switchPortsModel->u_s_p_i = $model->id;
                $switchPortsModel->status = '1';
                if ( $switchPortsModel->save(false) ) {
                    $logMessage = "{$switchPortsModel->device->name} device  {$switchPortsModel->port_number} port has been added to {$login}";
                    Logs::writeLog(
                        $memberUsername, 
                        intval($model->user_id), 
                        $logMessage, 
                        time()
                    );
                   return [
                        'status'=>'success',
                        'message'=>Yii::t(
                            "app",
                            "{deviceName} device {portNumber} port has been added to {login} by {memberUsername}",
                            [
                                'memberUsername'=>$memberUsername,
                                'login'=>$login,
                                'portNumber'=>$switchPortsModel->port_number,
                                'deviceName'=>$switchPortsModel->device->name
                            ]
                        )
                    ];
                }

            }
        }

        $checkEgponPort = \app\models\EgonBoxPorts::find()
        ->where(['u_s_p_i'=>$model->id])
        ->one();

        $checkSwitchPort = \app\models\SwitchPorts::find()
        ->where(['u_s_p_i'=>$model->id])
        ->one();

        if ($checkEgponPort != null) {
            $egponBoxPorts = \app\models\EgonBoxPorts::find()
            ->where(['egon_box_id'=>$checkEgponPort['egon_box_id']])
            ->orderBy(['port_number'=>SORT_ASC])
            ->asArray()
            ->all();

           return $this->renderIsAjax('tag-port-user-packet-egpon', ['checkEgponPort' => $checkEgponPort,'model'=>$model,'egponBoxPorts' => $egponBoxPorts]);
        }

        if ($checkSwitchPort != null) {

            $switchPorts = \app\models\SwitchPorts::find()
            ->where(['device_id'=>$checkSwitchPort['device_id']])
            ->orderBy(['port_number'=>SORT_ASC])
            ->asArray()
            ->all();

           return $this->renderIsAjax('tag-port-user-packet-switch', ['switchPorts' => $switchPorts,'checkSwitchPort'=>$checkSwitchPort,'model'=>$model]);
        }


        return $this->renderIsAjax('tag-port-user-packet', ['model' => $model]);
    }

    public function actionRemovePort(){
        if (  Yii::$app->request->isAjax && Yii::$app->request->isPost ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $memberUsername = Yii::$app->user->username;

            $post = Yii::$app->request->post();
            if ( !isset($post['deviceType']) || !isset($post['userPacketId']) || !isset($post['portId']) || !isset($post['inetLogin'])  ) {
                return ['status'=>'error','message'=>Yii::t('app','Something went wrong!')];
            }
            /* switch port remove start */
            if ( $post['deviceType'] == "switch" ) {
                $userServicePacketModel = \app\models\UsersServicesPackets::find()
                ->where(['id'=>$post['userPacketId']])
                ->asArray()
                ->one();

                $switchPortsModel = \app\models\SwitchPorts::find()
                ->where(['id'=>$post['portId']])
                ->andWhere(['u_s_p_i'=>$post['userPacketId'] ])
                ->one();

                $portNumber =  $switchPortsModel->port_number;

                $switchPortsModel->u_s_p_i = null;
                $switchPortsModel->status = '0';
                if ($switchPortsModel->save(false)) {
                    $logMessage = " {$switchPortsModel->device->name} device {$portNumber} port was removed from {$post['inetLogin']}";
                    Logs::writeLog(
                        $memberUsername,
                        intval( $userServicePacketModel['user_id'] ),
                        $logMessage, 
                        time()
                    );
                    return [
                        'status'=>'success',
                        'message'=>Yii::t(
                            'app',
                            '{deviceName} device {portNumber} port was removed from {inetLogin} by {memberUsername}',
                            [
                                'memberUsername'=>$memberUsername,
                                'inetLogin'=>$post['inetLogin'],
                                'portNumber'=>$portNumber,
                                'deviceName'=>$switchPortsModel->device->name,
                            ]
                         )
                    ];
                }
            }
            /* switch port remove end */

            if ( $post['deviceType'] == "epon" || $post['deviceType'] == "gpon" ) {

                $userServicePacketModel = \app\models\UsersServicesPackets::find()
                ->where(['id'=>$post['userPacketId']])
                ->asArray()
                ->one();


                $boxPortModel = \app\models\EgonBoxPorts::find()
                ->where(['id'=> $post['portId'] ])
                ->andWhere(['u_s_p_i'=>$post['userPacketId'] ])
                ->one();

                $portNumber =  $boxPortModel->port_number;
                $boxName =  $boxPortModel->egonBox->box_name;
                $deviceName =  $boxPortModel->egonBox->device->name;

                $boxPortModel->u_s_p_i = null;
                $boxPortModel->status = '0';
                if ( $boxPortModel->save(false) ) {
                    $logMessage = "{$deviceName} device {$boxName} box {$portNumber} port was removed from {$post['inetLogin']}";
                    Logs::writeLog(
                        $memberUsername,
                        intval($userServicePacketModel['user_id']),
                        $logMessage,
                        time()
                    );
                    return [
                        'status'=>'success',
                        'message'=>Yii::t(
                            'app',
                            '{deviceName} device {boxName} box {portNumber} port was removed from {inetLogin} by {memberUsername}',
                            [
                                'memberUsername'=>$memberUsername,
                                'inetLogin'=>$post['inetLogin'],
                                'portNumber'=>$portNumber,
                                'boxName'=>$boxName,
                                'deviceName'=>$deviceName,
                            ]
                         )
                    ];
                }
            }


        }
    }

    public function actionUpdatePhonesValidate($id){
        $model = \app\models\Users::findOne($id);
        $model->scenario = \app\models\Users::PHONES_UPDATE;
        $request = \Yii::$app->getRequest();
        if ($request->isAjax && $model->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionUpdatePhones($id){
        $model = \app\models\Users::find()->where(['id'=>$id])->one();
        $model->scenario = \app\models\Users::PHONES_UPDATE;

       if ( $model->load( Yii::$app->request->post() ) && $model->save() ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return $this->redirect(['view?id='.$model['id']]);
            

        }
        return $this->renderIsAjax('update-phones', [
            'model' => $model
        ]);
    }

    public function actionSendPacketDetail($id){
        $model = new \app\models\UsersMessage;
        $model->scenario = \app\models\UsersMessage::SCENARIO_SEND_PACKET_DETAIL;

        $packetModel = \app\models\UsersServicesPackets::find()
        ->where(['id' => $id])
        ->one();

        $userModel = \app\models\Users::find()
        ->where(['id'=>$packetModel->user_id])
        ->asArray()
        ->one();

        if ( $userModel == null ) {
            return ['status'=>'error','message'=>Yii::t('app','userModel not found')];
        }

        $languages = \yii\helpers\ArrayHelper::map(\app\models\MessageTemplate::find()->where(['name'=>'packet_info'])->groupBy('lang')->asArray()->all(),'lang','lang');
        $phones = ( $userModel['extra_phone'] == "") ? [$userModel['phone'] ] :  [ $userModel['phone'],$userModel['extra_phone'] ];


        if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $messageTemplateModel = \app\models\MessageTemplate::find()
            ->where(['name'=>'packet_info'])
            ->andWhere(['lang'=>Yii::$app->request->post('UsersMessage')['lang']])
            ->asArray()
            ->one();

            $filtredNumber = str_replace( "+", "", $phones[Yii::$app->request->post('UsersMessage')['user_phone']] );
            if ( Yii::$app->request->post('UsersMessage')['type'] == "sms" ) {
                $params = json_encode(
                    [
                        '{fullname}'=>$userModel['fullname'],
                        '{packet_name}'=>$packetModel->packet->packet_name,
                        '{login}'=>$packetModel->usersInet->login,
                        '{password}'=>$packetModel->usersInet->password,
                    ]
                );

                $templateSmsAsText = \app\components\Utils::createText( 
                    $messageTemplateModel['sms_text'],
                    [
                        '{fullname}'=>$userModel['fullname'],
                        '{packet_name}'=>$packetModel->packet->packet_name,
                        '{login}'=>$packetModel->usersInet->login,
                        '{password}'=>$packetModel->usersInet->password,
                    ] 
                );

                $checkSmsWasSent = \app\models\UsersMessage::sendSms( 
                    $userModel['id'] , 
                    Yii::$app->user->username, 
                    $filtredNumber, 
                    $templateSmsAsText,
                    $params
                );

                if ( $checkSmsWasSent == true ) {
                   return $this->redirect(['view?id='.$userModel['id']]);
                }else{
                    return [ 
                        'status'=>'error',
                        'message'=>Yii::t('app','Maybe your sms balance is over or something went wrong.')
                    ];
                }
            }


            if ( Yii::$app->request->post('UsersMessage')['type'] == "whatsapp" ) {

                $params = json_encode(
                    [
                        '{{1}}'=>$userModel['fullname'],
                        '{{2}}'=>$packetModel->packet->packet_name,
                        '{{3}}'=>$packetModel->usersInet->login,
                        '{{4}}'=>$packetModel->usersInet->password
                    ]
                );

                $template = [
                  'name'=>'packet_info',
                  'language'=>['code'=>Yii::$app->request->post('UsersMessage')['lang']],
                  "components"=>[
                    ['type'=>'header'],
                    [
                        'type'=>'body',
                        'parameters'=>[
                          [ 'type'=>'text','text'=>$userModel['fullname'] ],
                          [ 'type'=>'text','text'=>$packetModel->packet->packet_name ],
                          [ 'type'=>'text','text'=>$packetModel->usersInet->login ],
                          [ 'type'=>'text','text'=>$packetModel->usersInet->password ],
                        ]
                    ],
                  ]
                ];

                $templateWhatsappAsText = \app\components\Utils::createText( 
                    $messageTemplateModel['whatsapp_body_text'],
                    [
                        '{{1}}'=>$userModel['fullname'],
                        '{{2}}'=>$packetModel->packet->packet_name,
                        '{{3}}'=>$packetModel->usersInet->login,
                        '{{4}}'=>$packetModel->usersInet->password
                    ],
       
                );

                $checkWhatsappMessage = \app\components\Utils::sendWhatsappMessage( 
                    $template, $userModel['id'], 
                    Yii::$app->user->username, 
                    $filtredNumber, 
                    $templateWhatsappAsText,
                    $params
                );

                if ( $checkWhatsappMessage == true ) {
                   return $this->redirect(['view?id='.$userModel['id']]);
                }else{
                    return [ 
                        'status'=>'error',
                        'message'=>Yii::t('app','Maybe your whatsapp balance is over or something went wrong.')
                    ];
                }
            }
        }
        return $this->renderIsAjax('send-packet-detail', [
            'model' => $model,
            'userModel' => $userModel,
            'phones'=>$phones,
            'packetModel'=>$packetModel,
            'languages'=>$languages
        ]);
    }

    public function actionSendContractNumber($id){
        $model = \app\models\Users::find()->where(['id'=>$id])->one();
        $model->scenario = \app\models\Users::SCENARIO_SEND_CONTRACT_NUMBER;

        if ( $model == null ) {
            return ['status'=>'error','message'=>Yii::t('app','Model not found')];
        }

        $languages = \yii\helpers\ArrayHelper::map(\app\models\MessageTemplate::find()->where(['name'=>'packet_info'])->groupBy('lang')->asArray()->all(),'lang','lang');

        $phones = ( $model->extra_phone == "") ? [$model->phone ] :  [$model->phone,$model->extra_phone];

        if ( $model->load(Yii::$app->request->post()) && $model->validate() ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $filtredNumber = str_replace( "+", "", $phones[Yii::$app->request->post('Users')['phone']] );

            $messageTemplateModel = \app\models\MessageTemplate::find()
            ->where(['name'=>'contract_info'])
            ->andWhere(['lang'=>Yii::$app->request->post('Users')['lang']])
            ->asArray()
            ->one();


            if ( Yii::$app->request->post('Users')['type'] == "sms" ) {

                $params = json_encode(
                    [
                        '{fullname}'=>$model->fullname,
                        '{contract_number}'=>$model->contract_number
                    ]
                );

                $templateSmsAsText = \app\components\Utils::createText( 
                    $messageTemplateModel['sms_text'],
                    [
                        '{fullname}'=>$model->fullname,
                        '{contract_number}'=>$model->contract_number
                    ],

                );

                $checkSmsWasSent = \app\models\UsersMessage::sendSms( 
                    $model->id, 
                    Yii::$app->user->username, 
                    $filtredNumber, 
                    $templateSmsAsText,
                    $params
                );

                if ( $checkSmsWasSent == true ) {
                   return $this->redirect([ 'view?id='.$model->id ]);
                }else{
                    return [ 
                        'status'=>'error',
                        'message'=>Yii::t('app','Maybe your sms balance is over or something went wrong.')
                    ];
                }
            }


            if ( Yii::$app->request->post('Users')['type'] == "whatsapp" ) {

                $params = json_encode(
                    [
                        '{{1}}'=>$model->fullname,
                        '{{2}}'=>$model->contract_number
                    ]
                );
                
                $template = [
                  'name'=>$messageTemplateModel['name'],
                  'language'=>['code'=>Yii::$app->request->post('Users')['lang']],
                  "components"=>[
                    ['type'=>'header'],
                    [
                        'type'=>'body',
                        'parameters'=>[
                          [ 'type'=>'text','text'=>$model->fullname ],
                          [ 'type'=>'text','text'=>$model->contract_number ],
                        ]
                    ],
                  ]
                ];

                $templateWhatsappAsText = \app\components\Utils::createText( 
                    $messageTemplateModel['whatsapp_body_text'],
                    [
                        '{{1}}'=>$model->fullname,
                        '{{2}}'=>$model->contract_number
                    ],
       
                );
        
                $checkWhatsappMessage = \app\components\Utils::sendWhatsappMessage( 
                    $template, 
                    $model->id, 
                    Yii::$app->user->username, 
                    $filtredNumber, 
                    $templateWhatsappAsText,
                    $params,
                );

                if ( $checkWhatsappMessage == true ) {
                   return $this->redirect(['view?id='.$model->id]);
                }else{
                    return [ 
                        'status'=>'error',
                        'message'=>Yii::t('app','Maybe your whatsapp balance is over or something went wrong.')
                    ];
                }
            }
        }
        return $this->renderIsAjax('send-contract-number', [
            'model' => $model,
            'phones'=>$phones,
            'languages'=>$languages
        ]);
    }

    public function actionAddDebit( $id ){
        $model = new \app\models\UserBalance;
        $model->scenario = \app\models\UserBalance::SCENARIO_ADD_DEBIT;

        $userModel = \app\models\Users::find()
        ->where(['id'=>$id])
        ->one();

        if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
            $userModel->balance = \app\models\UserBalance::CalcUserTotalBalance($id);
            if ( $userModel->save( false ) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $logMessage = "Debit was added to {$userModel['fullname']}'s balance";
                Logs::writeLog(
                    Yii::$app->user->username,
                    intval( $userModel['id'] ),
                    $logMessage,
                    time()
                );
                return ['status'=>'success','message'=>Yii::t('app','Debit was added to {customer}\'s balance',['customer'=>$userModel['fullname']])];
            }
        }
        return $this->renderIsAjax('add-debit', [
            'model' => $model,
            'userModel'=>$userModel
        ]);
    }

    public function actionAddNote($id){
        $model = new \app\models\UsersNote;

        $userModel = \app\models\Users::find()
        ->where(['id'=>$id])
        ->asArray()
        ->one();

        if ( $model->load(Yii::$app->request->post()) && $model->save() ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $logMessage = "Note was added to {$userModel['fullname']}'s note list";
                Logs::writeLog(
                    Yii::$app->user->username,
                    intval( $userModel['id'] ),
                    $logMessage,
                    time()
                );
            return ['status'=>'success','message'=>Yii::t('app','Note was added to {customer}\'s note list',['customer'=>$userModel['fullname']])];
        }
        return $this->renderIsAjax('add-note', [
            'model' => $model,
            'userModel'=>$userModel
        ]);
    }

    public function actionAddCordinate($id){
        $model = \app\models\Users::find()->where(['id' => $id])->one();
        $model->scenario = \app\models\Users::CORDINATE_UPDATE;

        $siteConfig = \app\models\SiteConfig::find()->one();
        $districtModel = \app\models\District::find()->where(['id'=>$model->district_id])->one();

        if ( $model->load(Yii::$app->request->post()) && $model->save()) {
            if ( $model->save(false) ) {
                $logMessage = "{$model->fullname}'s location coordinates was  updated";
                Logs::writeLog( 
                    Yii::$app->user->username,
                    intval($id),
                    $logMessage,
                    time() 
                );
                return $this->redirect(['view?id='.$model->id]);
            }
        }
        return $this->renderIsAjax('add-cordinate', [
            'model' => $model,
            'districtModel'=> $districtModel,
            'siteConfig'=>$siteConfig
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

    public function actionDisableAllTemporaryServices(){
        $model = \app\models\UsersServicesPackets::find()
        ->select('users_services_packets.*,services.service_alias as service_alias_name,users.fullname as user_fullname,users.status as users_status,users.credit_status as users_credit_status,users.balance as user_balance,users.tariff as user_tariff,users.id as user_t_id,users_inet.id as user_inet_id,users_inet.id as user_inet_id,users_tv.id as users_tv_id,users_wifi.id as users_wifi_id,users_inet.login as user_inet_login,users_inet.password as user_inet_password,users_inet.static_ip as u_p_static_ip,service_packets.packet_price as user_packet_price,service_packets.packet_name as user_packet_name,address_district.router_id as user_router_id,routers.name as routers,routers.nas as nas_server,routers.password as nas_password,max(user_balance.created_at) as last_user_payment')
        ->leftJoin('services', 'services.id=users_services_packets.service_id')
        ->leftJoin('users', 'users.id=users_services_packets.user_id')
        ->leftJoin('user_balance', 'user_balance.user_id=users_services_packets.user_id')
        ->leftJoin('address_district', 'address_district.id=users.district_id')
        ->leftJoin('routers', 'routers.id=address_district.router_id')
        ->leftJoin('users_inet', 'users_inet.u_s_p_i=users_services_packets.id')
        ->leftJoin('users_tv', 'users_tv.u_s_p_i=users_services_packets.id')
        ->leftJoin('users_wifi', 'users_wifi.u_s_p_i=users_services_packets.id')
        ->leftJoin('service_packets', 'service_packets.id=users_services_packets.packet_id')
        ->where(['users.status' => '2'])
        ->andWhere(['users_services_packets.status' => '1'])
        ->andWhere(['users.credit_status' => '0'])
        ->withByLocation()
        ->groupBy('users_services_packets.id')
        ->asArray()
        ->all();

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $temp_user = [];
            $temp_inet_users = [];
            $temp_tv_users = [];
            $temp_wifi_users = [];

            foreach ($model as $key => $packet_one) {
                $temp_user[] = $packet_one['user_id'];
                if ($packet_one['service_alias_name'] == "internet") {
                     \app\models\radius\Radgroupreply::block( $packet_one['user_inet_login']);
                      \app\components\COA::disconnect( $packet_one['user_inet_login'] );
                }
                if ($packet_one['service_alias_name'] == "tv") {
                    $temp_tv_users[] = $packet_one['user_id'];
                }

                if ($packet_one['service_alias_name'] == "wifi") {
                    $temp_wifi_users[] = $packet_one['user_id'];
                }
            }  

            if (count($temp_user) > 0) {
                /*multipe update user status*/
                \app\models\Users::updateAll(['status' => 2], ['id' => array_unique($temp_user)]);
                /*multipe update user_services_packet  status*/
                \app\models\UsersServicesPackets::updateAll(['status' => 2], ['user_id' => $temp_user]);
            }

            if (count($temp_inet_users) > 0) {
                /*multipe update user_inet status*/
                \app\models\UsersInet::updateAll(['status' => 2], ['user_id' => $temp_inet_users]);
            }

            if (count($temp_tv_users) > 0) {
                /*multipe update user_tv status*/
                \app\models\UsersTv::updateAll(['status' => 2], ['user_id' => $temp_tv_users]);
            }

            if (count($temp_wifi_users) > 0) {
                /*multipe update wifi status*/
                \app\models\UsersWifi::updateAll(['status' => 2], ['user_id' => $temp_wifi_users]);
            }

                return ['code'=>'success','message'=>Yii::t('app','All packets disabled')];
        }


        return $this->renderIsAjax('disable-all-temporary-services', ['model' => $model]);
    }

    public function actionPhotoUpload(){
        $uploadHandler = new \app\components\UploadHandler(['accept_file_types' => '/\.(gif|jpe?g|png)$/i']);
    }

    public function actionUploadForm($id){
     $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $post_photos = Yii::$app->request->post('Users')['photos'];
            if ($model->save(false)) {
                if ($post_photos != "") {
                    $photos_array = explode("@", $post_photos);
                    \app\models\UserPhotos::deleteAll(['user_id' => $model->id]);
                    foreach ($photos_array as $key_phts => $value_phts) {
                        $user_photo = new \app\models\UserPhotos;
                        $user_photo->user_id = $model->id;
                        $user_photo->position = $key_phts;
                        $user_photo->photo_url = $value_phts;
                        $user_photo->save();
                    }
                }
            }

            $logMessage = 'Photo was uploaded form user\'s profile';
            Logs::writeLog(
                Yii::$app->user->username,
                intval($model->id),
                $logMessage,
                time()
            );
            return $this->redirect(['index']);
        }
        return $this->render('upload-form', [
            'model' => $model,
        ]);
    }

    public function actionAddBlackList($user_id){
       $user_model = \app\models\Users::find()
       ->where(['id' => $user_id])
       ->one();
       $model_note = new \app\models\UsersNote;
       $services_user_paccket = UsersServicesPackets::find()
            ->where(['user_id' => $user_model->id])
            ->all();

       $router_model = \app\models\Routers::find()
            ->where(['id' => $user_model->district->router_id])
            ->asArray()
            ->one();

        if (Yii::$app->request->isAjax) {
            if ($model_note->load(Yii::$app->request->post()) && $model_note->save()) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if (\yii\widgets\ActiveForm::validate($model_note)) {
                    return \yii\widgets\ActiveForm::validate($model_note);
                }
                $model_note->time = time();

                if ($model_note->save(false)) {
                    foreach ($services_user_paccket as $key => $value_ser) {
                        if ($value_ser->service->service_alias == "internet") {

                            $internet_service = \app\models\UsersInet::find()
                            ->where(['user_id' => $user_model->id])
                            ->all();

                            foreach ( $internet_service as $key => $serv ) {
                                $cgnModel = \app\models\CgnIpAddress::find()->where(['inet_login'=>$serv->login])->one();
                                if ( $cgnModel != null ) {
                                    $cgnModel->inet_login = null;
                                    $cgnModel->save( false );
                                }

                                \app\models\MikrotikQueries::dhcpRemoveMac(
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
                            $tv_service = \app\models\UsersTv::deleteAll(['user_id' => $user_model->id]);
                            //users tv packets delete all
                        }

                        if ($value_ser->service->service_alias == "wifi") {
                            $wifi_service = \app\models\UsersTv::deleteAll(['user_id' => $user_model->id]);
                            // users wifi packets delete all
                        }
                    }
                    // all counting table data deleting
                    UsersServicesPackets::deleteAll(['user_id' => $user_model->id]);
                    $user_model->status = 6;
                    if ( $user_model->save(false) ) {
                        $logMessage = "{$user_model->fullname} was added to black list";
                        Logs::writeLog(
                            Yii::$app->user->username,
                            intval($user_model->id),
                            $logMessage,
                            time()
                        );
                        return $this->redirect(['view?id='.$user_model->id]);;
                    }
                }

            }
        }

        return $this->renderIsAjax('black-list', ['model_note' => $model_note, 'user_id' => $user_model->id]);
    }

    public function actionDeleteUser($user_id){
       $model = \app\models\Users::find()
       ->where(['id' => $user_id])
       ->one();

       $userFullname = $model->fullname;
        if ( Yii::$app->request->isAjax && Yii::$app->request->isPost ) {
            \app\models\UserBalance::deleteAll(['user_id' => $user_id]);
            \app\models\UserDamages::deleteAll(['user_id' => $user_id]);
            \app\models\UsersHistory::deleteAll(['user_id' => $user_id]);
            \app\models\UsersNote::deleteAll(['user_id' => $user_id]);
            \app\models\UsersMessage::deleteAll(['user_id' => $user_id]);
            \app\models\ItemUsage::deleteAll(['user_id' => $user_id]);
            \app\models\UsersCredit::deleteAll(['user_id' => $user_id]);
            \app\models\UsersGifts::deleteAll(['user_id' => $user_id]);
            \app\models\UserPhotos::deleteAll(['user_id' => $user_id]);
            
            if ( $model->delete() ) {
                $logMessage = "{$userFullname} was deleted from users list";
                Logs::writeLog(
                    Yii::$app->user->username,
                    null,
                    $logMessage,
                    time()
                );

                 return $this->redirect(['/users/index']);
            }
        }

        return $this->renderIsAjax('delete-user', ['model' => $model]);
    }

    public function actionChangePaidDay( $id ){
        $model = \app\models\Users::find()
        ->where(['id' => $id])
        ->one();

        $model->scenario = \app\models\Users::PAID_UPDATE;
        $currentTimestamp = time(); 
        $currentStatus = $model->status;

        if ( $model->load(Yii::$app->request->post()) && $model->validate()  ) {
            if ( Yii::$app->request->post('Users')['paid_time_type'] == "1" ) {
                $timestamp = strtotime( Yii::$app->request->post('Users')['updatedAt'] ." 01:00" );
                $day =  date("d",$timestamp);
                $paid_type =  "1";
                $model->updated_at = $timestamp;
            }else{
                $day =  "01";
                $paid_type =  "0";

                $explode_updated_at = explode( "-", Yii::$app->request->post('Users')['updatedAt'] );
                $timestamp = strtotime(  $day."-".$explode_updated_at[1]."-".$explode_updated_at['2'] ." 01:00" );
                $model->updated_at = $timestamp;
            }

            $model->paid_time_type = $paid_type;
            $model->paid_day = $day;



            if ( $timestamp > $currentTimestamp  && $currentStatus == "2" ) {
                $model->status = "1";
            }

            $model->credit_status = 0;
               if ( $model->save(false) ) {

                   if (  $timestamp > $currentTimestamp  && $currentStatus == "2" ) {
                        $userServicePacketModel = \app\models\UsersServicesPackets::find()
                        ->where(['user_id'=>$model->id])
                        ->all();

                        foreach ($userServicePacketModel as $key => $userService) {
                          $userService->status = '1';
                          if( $userService->save(false) ){
                            if ( $userService->service->service_alias == "internet"  ) {
                                $inetModel = \app\models\UsersInet::find()->where(['u_s_p_i'=>$userService->id])->one();
                                $inetModel->status = '1';
                                if ( $inetModel->save(false) ) {

                                 \app\components\MikrotikQueries::dhcpUnBlockMac(
                                        $inetModel->login, 
                                        $model->district->router->nas, 
                                        $model->district->router->username, 
                                        $model->district->router->password,
                                        "dhcpUnBlockMac",
                                        [
                                            'login'=>$inetModel->login,
                                            'nas'=> $model->district->router->nas,
                                            'router_username'=>$model->district->router->username,
                                            'router_password'=>$model->district->router->password,
                                        ]
                                    );
                                }
                            }

                            if ( $userService->service->service_alias == "tv" ) {
                              \app\models\UsersTv::turnOnTvAccess(
                                    $model->id, 
                                    $userService->id
                                );
                            }

                            if ( $userService->service->service_alias == "wifi" ) {
                                \app\models\UsersWifi::turnOnWifiAccess(
                                    $model->id, 
                                    $userService->id
                                );
                            }
                          }
                        }
                        $logMessage = "{$model['fullname']}'s paid day was updated and services was activated until  ".Yii::$app->request->post('Users')['updatedAt'] ."01:00";
                    }else{
                        $logMessage = "{$model['fullname']}'s paid day was updated";
                    }



               

                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    
                    Logs::writeLog(
                        Yii::$app->user->username,
                        intval( $model['id'] ),
                        $logMessage,
                        time()
                    );
                    return ['status'=>'success','message'=>Yii::t('app','{customer}\'s paid day was updated',['customer'=>$model['fullname']])];
               }
        }


        return $this->renderIsAjax('change-paid-day', ['model' => $model]);
    }

    public function actionCreditHistory( $user_id, $item_usage_id ){
        $credit_model = \app\models\UsersCredit::find()
        ->where(['item_usage_id' => $item_usage_id])
        ->andWhere(['user_id' => $user_id])
        ->asArray()
        ->all();
        return $this->renderIsAjax('credit_list', ['credit_model' => $credit_model]);
    }

    public function actionGetRxTx($login){
        if (Yii::$app->request->isAjax ) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $user_inet_model = \app\models\UsersInet::find()
            ->where(['login' => $login])
            ->one();

            $router_model = \app\models\Routers::find()
            ->where(['id' => $user_inet_model->user->district->router_id])
            ->one();

            $data = \app\components\MikrotikQueries::checkRxTxDHCP(
                 $login,
                 $router_model['nas'], 
                 $router_model['username'], 
                 $router_model['password']
             );
            return $data;
        }
    }

    public function actionRxTx($login){
        $user_inet_model = \app\models\UsersInet::find()
        ->where(['login' => $login])
        ->one();

        $router_model = \app\models\Routers::find()
        ->where(['id' => $user_inet_model->user->district->router_id])
        ->one();

        return $this->renderIsAjax('rx-tx', ['router_model' => $router_model,'login'=>$login,'user_inet_model'=>$user_inet_model]);
    }

    public function actionCheckUserInternet($login){
        $user_inet_model = \app\models\UsersInet::find()
        ->where(['login' => $login])
        ->one();

        $router_model = \app\models\Routers::find()
        ->where(['id' => $user_inet_model->user->district->router_id])
        ->one();

        $data = \app\components\MikrotikQueries::dhcpPrint(
            $login, 
            $router_model['nas'], 
            $router_model['username'], 
            $router_model['password']
        )[0];
 
        if ($data != null) {

            return $this->renderIsAjax('check-internet', ['data' => $data]);
        } else {

            return $this->renderIsAjax('check-internet', ['data' => []]);
        }
    }

    public function actionGiftHistory($user_id, $item_id){
        $gift_model = \app\models\UsersGifts::find()
        ->where(['user_id' => $user_id, 'item_count_id' => $item_id])
        ->asArray()
        ->all();
        return $this->renderIsAjax('gift_list', ['gift_model' => $gift_model]);
    }

    public function actionCheckFreeStatus(){
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $user_id = Yii::$app->request->post('user_id');
            $checked = Yii::$app->request->post('checked');
            $model_user = Users::find()->where(['id' => $user_id])->one();
            if ($checked == 1) {
                if ($model_user->status == 7) {

                    $userHistoryText = 'VIP status was changed to Active';

                    UsersHistory::AddHistory(
                        intval($model_user->id), 
                        Yii::$app->user->username, 
                        $userHistoryText, 
                        time()
                    );

                    Logs::writeLog(
                        Yii::$app->user->username, 
                        intval($model_user->id), 
                        $userHistoryText, 
                        time()
                    );

                    $model_user->tariff = \app\models\UserBalance::CalcUserTariffDaily($user_id)['per_total_tariff'];
                    $model_user->status = 1;
                    $model_user->save(false);
                    return ['status'=>'success','message' => Yii::t('app','VIP changed to Active')];
                } else {
                    return ['status'=>'error','message' => Yii::t('app','User must be VIP ! You dont change status to active!!!')];
                }
            } elseif ($checked == 7) {
                if ($model_user->status == 1) {
                    $model_user->status = 7;
                    $model_user->tariff = 0;
                    $model_user->save(false);

                   $userHistoryText = 'Active status was changed to VIP';
                    UsersHistory::AddHistory(
                        intval($model_user->id), 
                        Yii::$app->user->username, 
                        $userHistoryText, 
                        time()
                    );

                    Logs::writeLog(
                        Yii::$app->user->username, 
                        intval($model_user->id), 
                        $userHistoryText, 
                        time()
                    );
                    return ['status'=>'success','message' =>Yii::t('app', 'Active changed to VIP')];
                } else {
                    return ['status'=>'error','message' =>Yii::t('app','User must be active!You dont change status to VIP!!!') ];
                }
            }
        }
    }

    public function actionCheckBankStatus(){
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $user_id = Yii::$app->request->post('user_id');
            $checked = Yii::$app->request->post('checked');
            $model_user = Users::find()
            ->where(['id' => $user_id])
            ->one();

            if ($checked == 0) {
                $logText = "User's bank status was canceled";
                $model_user->bank_status = 0;
                if ( $model_user->save(false) ) {
                    Logs::writeLog(
                        Yii::$app->user->username, 
                        intval($model_user->id), 
                        $logText, 
                        time()
                    );
                    return ['code' => 'Bank changed to Normal'];
                }

            } elseif ($checked == 1) {
                $model_user->bank_status = 1;
                if ( $model_user->save(false)  ) {
                    $logText = "User's bank status was actived";
                    Logs::writeLog(
                        Yii::$app->user->username, 
                        intval($model_user->id), 
                        $log_text, 
                        time()
                    );
                    return ['code' => 'Normal changed to Bank'];
                }
            }
        }
    }

    public function actionView($id){
        $model = Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one(); 

        $siteConfig = \app\models\SiteConfig::find()->one();

        if ( !$model ) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model_note = new \app\models\UsersNote;


        $notes = \app\models\UsersNote::find()
        ->where(['user_id' => $id])
        ->orderBy(['time' => SORT_DESC])
        ->asArray()
        ->all();

        $messages = \app\models\UsersMessage::find()
        ->where(['user_id' => $id])
        ->orderBy(['message_time' => SORT_DESC])
        ->asArray()
        ->all();

        $itemUsage = \app\models\ItemUsage::find()
        ->select('item_usage.*,items.name as item_name,users.status as user_status,item_stock.price as price')
        ->leftJoin('items', 'items.id=item_usage.item_id')
        ->leftJoin('users', 'users.id=item_usage.user_id')
        ->leftJoin('item_stock', 'item_stock.id=item_usage.item_stock_id')
        ->where(['user_id' => $id])
        ->asArray()
        ->all();

        $damages = \app\models\UserDamages::find()
        ->select('user_damages.*,members.username as member_name')
        ->leftJoin('members', 'members.id=user_damages.member_id')
        ->orderBy(['created_at' => SORT_ASC])
        ->where(['user_id' => $id])
        ->asArray()
        ->all();

        $transactions = \app\models\UserBalance::find()
        ->select('user_balance.*,receipt.code as receipt_name')
        ->leftJoin("receipt", "receipt.id=user_balance.receipt_id")
        ->orderBy([
            'user_balance.created_at'=>SORT_ASC,
        ])
        ->where(['user_id' => $id])
        ->asArray()
        ->all();


        $user_history = \app\models\UsersHistory::find()
        ->orderBy(['time' => SORT_ASC])
        ->where(['user_id' => $id])
        ->all();

        $user_logs = \app\models\Logs::find()
        ->orderBy(['time' => SORT_ASC])
        ->where(['user_id' => $id])
        ->all();

        return $this->render('view', [
            'model' => $model,
            'damages' => $damages,
            'transactions' => $transactions,
            'user_history' => $user_history,
            'model_note' => $model_note,
            'notes' => $notes,
            'messages' => $messages,
            'itemUsage' => $itemUsage,
            'user_logs' => $user_logs,
            'siteConfig'=> $siteConfig
            
        ]);
    }

    public function actionPacketAjaxStatus(){
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $status = Yii::$app->request->post('checked');
            $user_id = Yii::$app->request->post('_user_id');
            $packet_id = Yii::$app->request->post('_packet_id');
            $service_id = Yii::$app->request->post('_service_id');
            $u_s_p_i = Yii::$app->request->post('_usp_id');

            $model = UsersServicesPackets::find()
            ->where(['user_id' => $user_id, 'packet_id' => $packet_id, 'service_id' => $service_id, 'id' => $u_s_p_i])
            ->one();

            if (intval($status) == 1) {
                $logMessage = $model->packet->packet_name . ' packet status was enabled';
            } else {
                $logMessage = $model->packet->packet_name . ' packet status was disabled';
            }
            Logs::writeLog(
                Yii::$app->user->username, 
                intval($user_id), 
                $logMessage, time()
            );

            $model->status = $status;
   
            if ($model->service->service_alias == "internet") {
       
                $router_model = \app\models\Routers::find()
                ->where(['id' => $model->user->district->router_id])
                ->one();

                $inet_model = \app\models\UsersInet::find()
                ->where(['user_id' => $user_id, 'u_s_p_i' => $u_s_p_i])
                ->one();

                $inet_model->status = $status;
                if ($inet_model->save(false)) {
                    if (intval($status) == 1) {

                        \app\components\MikrotikQueries::dhcpUnBlockMac(
                            $inet_model->login, 
                            $router_model['nas'], 
                            $router_model['username'], 
                            $router_model['password'],
                            "dhcpUnBlockMac",
                            [
                                'login'=>$inet_model->login,
                                'nas'=> $router_model['nas'],
                                'router_username'=>$router_model['username'],
                                'router_password'=>$router_model['password'],
                            ]
                        );

                    } else {

                        \app\components\MikrotikQueries::dhcpBlockMac(
                            $inet_model->login, 
                            $router_model['nas'], 
                            $router_model['username'], 
                            $router_model['password'],
                            "dhcpBlockMac",
                            [
                                'login'=>$inet_model->login,
                                'nas'=> $router_model['nas'],
                                'router_username'=>$router_model['username'],
                                'router_password'=>$router_model['password'],
                            ]
                        );
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
                'url' => \yii\helpers\Url::to(['view?id='.rawurlencode($userModel->id)], true),
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

    /* Item action  end */

    public function actionContract($id){
        $model = \app\models\Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();

        $oldContractNumber = $model->contract_number;
        $model->scenario = \app\models\Users::CONTRACT_UPDATE;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\bootstrap4\ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $logMessage = "{$oldContractNumber} contract number updated to {$model->contract_number}";
            Logs::writeLog(
                Yii::$app->user->username, 
                intval( $model->id ), 
                $logMessage, 
                time()
            );
            return $this->redirect(['view?id='.$model->id]);
        }

        return $this->renderIsAjax('contract.php', ['model' => $model]);
    }

    public function actionSendToArchive($id){
        $model = \app\models\Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();
        $model->scenario = \app\models\Users::SCENARIO_SEND_ARCHIVE;
        $archive_reasons = \app\models\Users::getArchiveReason();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ( $model->status == 2 || $model->status == 1 ) {
                foreach ( $model->usersInets as $key => $value_inet ) {
                    if ( $value_inet->static_ip != "") {
                        $static_model = \app\models\IpAdresses::find()
                        ->where(['id' => $value_inet->static_ip])
                        ->one();
                        $static_model->status = "0";
                        $static_model->save(false);
                        $value_inet->static_ip = "";
                    }
                    $value_inet->status = 3;
                    if ($value_inet->save(false)) {
                        $router_model = \app\models\Routers::find()
                            ->where(['id' => $model->district->router_id])
                            ->one();

                        \app\components\MikrotikQueries::dhcpRemoveMac(
                            $value_inet->login, 
                            $router_model['nas'], 
                            $router_model['username'], 
                            $router_model['password'],
                            "dhcpRemoveMac",
                            [
                                'login'=>$value_inet->login,
                                'nas'=> $router_model['nas'],
                                'router_username'=>$router_model['username'],
                                'router_password'=>$router_model['password'],
                            ]
                        );





                    }
                }

                foreach ($model->usersTvs as $key => $value_tv) {
                    $value_tv->status = 3;
                    $value_tv->save(false);
                }

                foreach ($model->usersWifis as $key => $value_wifi) {
                    $value_wifi->status = 3;
                    $value_wifi->save(false);
                }
                foreach ($model->usersServicesPackets as $key => $value_usp) {
                    $value_usp->status = 3;
                    $value_usp->save(false);
                }

                if ($model->status == 2) {
                    $userHistoryText = 'Deactive status was changed to Archive';
                } elseif ($model->status == 1) {
                    $userHistoryText = 'Active status was changed to Archive'; 
                }
                UsersHistory::AddHistory(
                    intval($model->id), 
                    Yii::$app->user->username, 
                    $userHistoryText, 
                    time()
                );
                Logs::writeLog(
                    Yii::$app->user->username, 
                    intval($model->id), 
                    $userHistoryText, 
                    time()
                );
                $model->second_status = '0';
                $model->status = 3;
                $model->note = $model->archive_reason;
                $model->save(false);

              return $this->redirect(['view?id='.$model->id]);;
            }
        }
        return $this->renderIsAjax('send-to-archive.php', ['model' => $model,'archive_reasons'=>$archive_reasons]);
    }


    public function actionAddReportValidate(){
        $model = new \app\models\UserDamages();
        $model->scenario = \app\models\UserDamages::ADD_REPORT;
        $request = \Yii::$app->getRequest();

        if ( $request->isPost && $model->load( $request->post() ) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }


    public function actionAddReport( $id ){
        $model = new \app\models\UserDamages;
        $model->scenario = \app\models\UserDamages::ADD_REPORT;

       if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->user_id = $id;
            $model->member_id = Yii::$app->user->id;
            $model->created_at = time();
            $model->save( false );
            return $this->redirect(['view?id='.$id]);
        }
     
        $damage_reasons = \app\models\UserDamages::getDamageReason();
        return $this->renderIsAjax('add-report.php', ['model' => $model,'damage_reasons'=>$damage_reasons]);
    }


    public function actionReConnect($id){
        $model = \app\models\Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $user_id = Yii::$app->request->post('user_id');
            $model_user = Users::find()
            ->where(['id' => $user_id])
             ->withByLocation()
            ->one();
            if ($model_user !== null) {
                if ($model_user->status == 3 || $model_user->status == 6) {

                    if ($model_user->status == 3) {
                        $userHistoryText = 'Archive status was changed to Reconnect';
                    } else {
                         $userHistoryText = 'Cancelled status was changed to Reconnect';  
                    }
                    UsersHistory::AddHistory(
                        intval( $model_user->id ), 
                        Yii::$app->user->username, 
                        $userHistoryText, 
                        time()
                    );
                    Logs::writeLog(
                        Yii::$app->user->username, 
                        intval($model_user->id), 
                        $userHistoryText, 
                        time()
                    );
                    $model_user->second_status = '4';
                    $model_user->request_at = time();
                    $model_user->save(false);

                    return ['code' => 'success', 'url' => \yii\helpers\Url::to(['request-order/index'], true)];
                }
            }
        }

        return $this->renderIsAjax('re-connect.php', ['model' => $model]);
    }

    public function actionServiceDelete(){
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $user_id = Yii::$app->request->post('user_id');
            $service_id = Yii::$app->request->post('service_id');
            $packet_id = Yii::$app->request->post('packet_id');
            $usrp = Yii::$app->request->post('id_usrp');

            $model_user_ser_packets = \app\models\UsersServicesPackets::find()
            ->where(['id' => $usrp, 'user_id' => $user_id, 'service_id' => $service_id, 'packet_id' => $packet_id])
            ->one();



            $packetName = $model_user_ser_packets->packet->packet_name;


            $switchPortsModel = \app\models\SwitchPorts::find()
            ->where(['u_s_p_i'=>$usrp])
            ->one();

            $egponBoxPortsModel = \app\models\EgonBoxPorts::find()
            ->where(['u_s_p_i'=>$usrp])
            ->one();


            $model_user = Users::find()
            ->where(['id' => $user_id])
            ->withByLocation()
            ->one();

            if ( $model_user_ser_packets->service->service_alias == "internet" ) {
                $router_model = \app\models\Routers::find()
                ->where(['id' => $model_user->district->router_id])
                ->asArray()
                ->one();

                $inet_model = \app\models\UsersInet::find()
                ->where(['user_id' => $user_id, 'packet_id' => $packet_id, 'u_s_p_i' => $usrp])
                ->one();
          
                if ( $inet_model ) {
                   
                        if ( $switchPortsModel != null ) {
                             $switchPortsModel->status = 0;
                             $switchPortsModel->u_s_p_i = null;

                             if ( $switchPortsModel->save(false) ) {
                                 $logMessage = "{$packetName} was deleted.{$switchPortsModel->device->name} device {$switchPortsModel->port_number} port number status was free and tagged packet was removed";
                             }
                         }

                         if ( $egponBoxPortsModel != null ) {
                             $egponBoxPortsModel->status = 0;
                             $egponBoxPortsModel->u_s_p_i = null;
                             
                             if ( $egponBoxPortsModel->save(false) ) {
                                 $logMessage = "{$packetName} was deleted.{$egponBoxPortsModel->egonBox->device->name} device {$egponBoxPortsModel->egonBox->box_name} box name {$egponBoxPortsModel->port_number} port status was free and tagged packet was removed ";
                             }
                         }
                        
                         if ( $egponBoxPortsModel == null  && $switchPortsModel == null ) {
                              $logMessage = "{$packetName} was deleted";
                         }


                        $packetModel = \app\models\Packets::find()->where(['id' => $inet_model->packet_id])->one();

                        \app\components\MikrotikQueries::dhcpRemoveMac(
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

                        $cNatModel = \app\models\CgnIpAddress::find()->where(['inet_login'=>$inet_model->login])->one();
                        if ( $cNatModel != null ) {
                            $cNatModel->inet_login = null;
                            $cNatModel->save(false);
                        }

                
                        if ( $inet_model->static_ip != "" ) {
                            $static_ip_M = \app\models\IpAdresses::find()
                            ->where(['id' => $inet_model->static_ip])
                            ->one();
                            $static_ip_M->status = '0';
                            $static_ip_M->save(false);
                        }
                        $model_user_ser_packets->delete();

                        $check_service_of_all_packets_del = \app\models\UsersServicesPackets::find()
                        ->where(['user_id' => $user_id, 'service_id' => $service_id])
                        ->all();

                        if ($check_service_of_all_packets_del == null) {
                            $services_model_deleting = \app\models\UsersSevices::find()
                            ->where(['user_id' => $user_id, 'service_id' => $service_id])
                            ->one()
                            ->delete();
                        }

                        $model_user->tariff = \app\models\UserBalance::CalcUserTariffDaily($user_id)['per_total_tariff'];
                        if ( $model_user->save(false) ) {
                            Logs::writeLog(
                                Yii::$app->user->username, 
                                intval($user_id), 
                                $logMessage, 
                                time()
                            );
                        };

                        $inet_model->delete();

                        return ['code' => 'success'];
                } else {
                    return ['code' => 'Dont have user"s packet on UsersInet table'];
                }
            }
            if ( $model_user_ser_packets->service->service_alias == "tv" ) {
                $tv_model = \app\models\UsersTv::find()->where(['user_id' => $user_id, 'packet_id' => $packet_id, 'u_s_p_i' => $usrp])->one();
                if ($tv_model) {
                    if ($tv_model->delete()) {
                        // api tv packet delete
                        $model_user_ser_packets->delete();

                        //deleting services
                        $check_service_of_all_packets_del = \app\models\UsersServicesPackets::find()
                        ->where(['user_id' => $user_id, 'service_id' => $service_id])
                        ->all();

                        if ($check_service_of_all_packets_del == null) {
                            $services_model_deleting = \app\models\UsersSevices::find()
                            ->where(['user_id' => $user_id, 'service_id' => $service_id])
                            ->one()
                            ->delete();
                        }
                        //deleting services end
                        $model_user->tariff = \app\models\UserBalance::CalcUserTariffDaily($user_id)['per_total_tariff'];
                       if (  $model_user->save(false) ) {
                            $logMessage = "{$packetName} was deleted";
                            Logs::writeLog(
                                Yii::$app->user->username, 
                                intval($user_id), 
                                $logMessage, 
                                time()
                            );
                       }



                        return ['code' => 'success'];
                    } else {
                        return ['code' => 'We have a error on Users tv packet delete function'];
                    }
                } else {
                    return ['code' => 'Dont have user"s packet on UsersTv table'];
                }
            }

            if ($model_user_ser_packets->service->service_alias == "wifi") {
                $wifi_model = \app\models\UsersWifi::find()
                ->where(['user_id' => $user_id, 'packet_id' => $packet_id, 'u_s_p_i' => $usrp])
                ->one();
                // delete from mikrotik with login password from UsersWifi
                if ($wifi_model) {
                    if ($wifi_model->delete()) {
                        $model_user_ser_packets->delete();
                        //deleting services
                        $check_service_of_all_packets_del = \app\models\UsersServicesPackets::find()
                        ->where(['user_id' => $user_id, 'service_id' => $service_id])
                        ->all();
                        if ($check_service_of_all_packets_del == null) {
                            $services_model_deleting = \app\models\UsersSevices::find()
                            ->where(['user_id' => $user_id, 'service_id' => $service_id])
                            ->one()
                            ->delete();
                        }
                        //deleting services end
                        $model_user->tariff = \app\models\UserBalance::CalcUserTariffDaily($user_id)['per_total_tariff'];
                       if (  $model_user->save(false) ) {
                            $logMessage = "{$packetName} was deleted";
                            Logs::writeLog(
                                Yii::$app->user->username, 
                                intval($user_id), 
                                $logMessage, 
                                time()
                            );
                       }
                        return ['code' => 'success'];
                    } else {
                        return ['code' => 'We have a error on Users wifi packet delete function'];
                    }
                } else {
                    return ['code' => 'Dont have user"s packet on UsersWifi table'];
                }
            }

            if ( $model_user_ser_packets->service->service_alias == "voip" ) {

                $voIpModel = \app\models\UsersVoip::find()
                ->where(['user_id' => $user_id, 'packet_id' => $packet_id, 'u_s_p_i' => $usrp])
                ->one();

                if ( $voIpModel ) {
                    if ( $voIpModel->delete() ) {
                        if ( $model_user_ser_packets->delete() ) {

                            $check_service_of_all_packets_del = \app\models\UsersServicesPackets::find()
                            ->where(['user_id' => $user_id, 'service_id' => $service_id])
                            ->all();
                            if ($check_service_of_all_packets_del == null) {
                                $services_model_deleting = \app\models\UsersSevices::find()
                                ->where(['user_id' => $user_id, 'service_id' => $service_id])
                                ->one()
                                ->delete();
                            }

                            $model_user->tariff = \app\models\UserBalance::CalcUserTariffDaily($user_id)['per_total_tariff'];
                               if (  $model_user->save(false) ) {
                                    $logMessage = "{$packetName} was deleted";
                                    Logs::writeLog(
                                        Yii::$app->user->username, 
                                        intval($user_id), 
                                        $logMessage, 
                                        time()
                                    );
                               }
                            return ['code' => 'success'];
                        }
                    }
                }
            }

        }
    }

    public function actionChangePacketValidate(){
        $model = new \app\models\UsersServicesPackets();
        $model->scenario = \app\models\UsersServicesPackets::CHANGE_PACKET;
        $request = \Yii::$app->getRequest();

        if ( $request->isPost && $model->load( $request->post() ) ) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionChangePacket( $id ){
            $model = UsersServicesPackets::find()
            ->where(['id' =>$id])
            ->one();

            $model->scenario = UsersServicesPackets::CHANGE_PACKET;
            $userModel = \app\models\Users::find()->where(['id'=>$model['user_id']])->asArray()->one();

            $packetsModel = \app\models\Packets::find()
            ->where(['service_id'=>$model->service_id])
            ->orderBy(['packet_name'=>SORT_ASC])
            ->all();


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
            $staticId = isset(Yii::$app->request->post('UsersServicesPackets')['static_ip_address']) ? Yii::$app->request->post('UsersServicesPackets')['static_ip_address'] : null;

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
            if ($model->service->service_alias == "internet") {


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

                $routerModel = \app\models\Routers::find()
                ->where(['id' => $userModel->district->router_id])
                ->asArray()
                ->one();

                if ($routerModel == null) {
                    return ['status'=>'error','message'=>Yii::t('app','routerModel not found,Please contact web developer :)')];
                }

                $inetModel = \app\models\UsersInet::find()
                ->where(['u_s_p_i' => $model->id])
                ->one();

                if ($inetModel == null) {
                    return ['status'=>'error','message'=>Yii::t('app','inetModel not found,Please contact web developer :)')];
                }
                if (empty($staticId)) {
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
                    ->where(['id'=>$staticId])
                    ->asArray()
                    ->one();

                    if ($findStaticIp == null ) {
                          return ['status'=>'error','message'=>Yii::t('app','Static ip not found,Packet was not changed.')];
                    }
                }
            }

            $logMessage = "{$model->packet->packet_name} packet has been changed to {$newPacketQuery['packet_name']}";
            Logs::writeLog(
                Yii::$app->user->username, 
                intval( $model->user_id ), 
                $logMessage, 
                time()
            );
            $model->packet_id = $packetId;
            $model->price = Yii::$app->request->post('UsersServicesPackets')['price'];
            if ($model->save(false)) {
                $userModel->tariff = \app\models\UserBalance::CalcUserTariffDaily($model->user_id)['per_total_tariff'];
                $userModel->save(false);

                if ($model->service->service_alias == "internet") {


                    if (empty($staticId)) {
                        if ($inetModel->static_ip != "") {
                            $staticIpModel->status ='0';
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
                        if ( $inetModel->save(false) ) {

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
                       
                            return [
                                'status' => 'success',
                                'url' => \yii\helpers\Url::to(['view?id='.$userModel->id], true)
                             ];
                        }
                    } else {
      

                        if ($inetModel->static_ip != "" ) {

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
                            $staticIpQuery->status = '0';
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
                                    'url' => \yii\helpers\Url::to(['view?id='.$userModel->id], true)

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
                            'url' => \yii\helpers\Url::to(['view?id='.$userModel->id], true)

                        ];
                    }
                   }  

                if ($model->service->service_alias == "wifi") {
                    $wifiModel = \app\models\UsersWifi::find()
                    ->where(['u_s_p_i' => $model->id])
                    ->one();
                    $wifiModel->packet_id = $newPacketQuery['id'];
                    if ( $wifiModel->save(false)) {
                       // WIFI api here 
                        return [
                            'status' => 'success', 
                            'url' => \yii\helpers\Url::to(['view?id='.$userModel->id], true)
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
                            'url' => \yii\helpers\Url::to(['view?id='.$userModel->id], true)
                        ];
                    
                   } 
            }
            
        }

        return $this->renderIsAjax(
            'change-packet',$variables
        );
    }

    public function actionUpdate($id){
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionAddNewService($id){

        $model = Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();
        
        $model->scenario = Users::SERVICE;
        if ($model->status == 1) {
            $services = "";
            if ($model->load(Yii::$app->request->post())) {
                foreach (Yii::$app->request->post('Users')['selected_services'] as $key => $value) {
                    $services .= $value . ",";
                }
                $services = substr($services, 0, -1);
                $userHistoryText = 'Active status user was ordered to new service';
                UsersHistory::AddHistory( 
                    $model->id, 
                    Yii::$app->user->username,
                    $userHistoryText, 
                    time() 
                );

                $service_of_names = '';
                $services_modell = \app\models\Services::find()->where(['id' => Yii::$app->request->post('Users')['selected_services']])->all();
                foreach ($services_modell as $key => $value_ser) {
                    $service_of_names .= $value_ser->service_name . ",";
                }

                $logText = substr($service_of_names, 0, -1) . " was selected and sent to  orders list";
                Logs::writeLog( 
                    Yii::$app->user->username, 
                    intval($model->id), 
                    $logText, 
                    time() 
                );

                $model->selected_services = $services;
                $model->second_status = '5';
                $model->request_at = time();
                $model->save();
                return $this->redirect(['request-order/index']);
            }
            return $this->renderAjax('add_new_service', ['model' => $model]);
        }
    }

    public function actionRefundBalance($id){
        $model = new \app\models\UserBalance;
        $model_user = Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();
        $services_array = \app\models\UsersServicesPackets::getServicesAsArray($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                if (\yii\widgets\ActiveForm::validate($model)) {
                    return \yii\widgets\ActiveForm::validate($model);
                }
                //refunded

                $pay_for = 7;
                \app\models\UserBalance::BalanceOut( 
                    $model_user->id, 
                    Yii::$app->request->post('UserBalance')['balance_in'], 
                    time(),
                    0,
                    $pay_for,
                    0, 
                    null
                );

                $model_user->balance = \app\models\UserBalance::CalcUserTotalBalance($id);
                $model_user->save(false);
                return [
                    'status' => 'success',
                    'url' => \yii\helpers\Url::to(['print', 'id' => $id], true),
                    'text' =>Yii::t("app","{balance} refunded",['balance'=>Yii::$app->request->post('UserBalance')['balance_in']]) ,
                ];
            }
        }
        return $this->renderAjax('refund', [
            'model' => $model,
            'model_user' => $model_user,
            'services_array' => $services_array,
            'id' => $id,
        ]);
    }

    public function actionAddBalance($id){
        $siteConfig = \app\models\SiteConfig::find()
        ->asArray()
        ->one();

        $model = new \app\models\UserBalance;

        $model_user = Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();

        $userService = \app\models\UsersSevices::find()->select('services.service_alias as service_alias')
        ->leftJoin('services','services.id=users_sevices.service_id')
        ->where(['user_id'=>$model_user->id])
        ->groupBy('service_id')
        ->asArray()
        ->all();

        $recipet = \app\models\Receipt::find()
        ->where(['status' => '0'])
        ->andWhere(['member_id' => Yii::$app->user->id])
        ->orderBy(['number' => SORT_ASC])
        ->asArray()
        ->one();

        if ($recipet != null) {
            $services_array = \app\models\UsersServicesPackets::getServicesAsArray($id);
            $user_packets = \app\models\UsersServicesPackets::find()
            ->where(['user_id' => $model_user->id])
            ->all();

            if ( $model_user->status == '2' ) {
                $daily_calc =  ( $model_user->paid_time_type == "0" &&  $siteConfig['half_month'] == "0" ) ? true : false;
                $half_month =  ( $model_user->paid_time_type == "0" &&  $siteConfig['half_month'] == "1" ) ? true : false;
            }else {
                $daily_calc =  false;
                $half_month =  false;
            }

            $tariffAndServiceArray = \app\models\UserBalance::CalcUserTariffDaily(
                $model_user->id, 
                $daily_calc, 
                $half_month
            );
     
            $user_tariff = $tariffAndServiceArray['services_total_tariff'];
            $user_credit_tariff = $tariffAndServiceArray['credit_tariff'];

            if ( $model_user->status == 2 ) {
                if ( $model->load( Yii::$app->request->post() ) && $model->validate() ) {
                    $calcUserBonusPayment = ( $model->balance_in > 0 ) ? \app\components\Utils::calcUserBonusPayment( $model->balance_in, $model_user->id ) : 0;
                    $model->bonus_in = $model->bonus_in + $calcUserBonusPayment;
                    $created_at = time();
                    $model->created_at = $created_at;

                    if ( Yii::$app->request->post('UserBalance')['per_day_rule'] == "1" &&   $model_user->paid_time_type == "0" ) {
                        $daily_calc =  true;
                        $half_month =  false;

                        $tariffAndServiceArray = \app\models\UserBalance::CalcUserTariffDaily(
                            $model_user->id, 
                            $daily_calc, 
                            $half_month
                        );
                        $user_tariff = $tariffAndServiceArray['services_total_tariff'];
                    }
  
                    if ( $model_user->paid_time_type == "0" ) {
                        $caclNextUpdateAtForUser = Users::caclNextUpdateAtForUser( 
                            $model_user->id,
                            $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                            Yii::$app->request->post('UserBalance')['balance_in'] ,
                            ['untilToMonthTariff'=>$user_tariff,'credit_tariff'=>$tariffAndServiceArray['credit_tariff'],'total_tariff'=>$model_user->tariff]
                        );
                    }

                    if ( $model_user->paid_time_type == "1" ) {
                        $caclNextUpdateAtForUser = Users::caclNextUpdateAtForUser( 
                            $model_user->id,
                            $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                            Yii::$app->request->post('UserBalance')['balance_in'],
                        );
                    }
        
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                        if (\yii\widgets\ActiveForm::validate($model)) {
                            return \yii\widgets\ActiveForm::validate($model);
                        }

                        if( $model->save(false) ){
                            $model_user->balance = \app\models\UserBalance::CalcUserTotalBalance($id);
                            $model_user->bonus = \app\models\UserBalance::CalcUserTotalBonus($id);
                            $model_user->save(false);
                        }

                        if ( round( $model_user->balance, 2 ) >= round( $user_tariff, 2 )  ) {
                            if ( $model_user['credit_status'] == 1 && round( $model_user->balance, 2 ) >= round( $user_tariff, 2 ) ) {
                                $model_user->credit_status = 0;
                                $model_user->save(false);
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
                                            ->where([
                                                'user_id' => $service['user_id'], 
                                                'u_s_p_i' => $service['u_s_p_i']
                                            ])
                                            ->one();
                                            $inetModel->status = 1;
                                            if ( $inetModel->save( false ) ) {


                                                 \app\components\MikrotikQueries::dhcpUnBlockMac(
                                                        $inetModel->login, 
                                                        $model_user->district->router->nas, 
                                                        $model_user->district->router->username, 
                                                        $model_user->district->router->password,
                                                        "dhcpUnBlockMac",
                                                        [
                                                            'login'=>$inetModel->login,
                                                            'nas'=> $model_user->district->router->nas,
                                                            'router_username'=>$model_user->district->router->username,
                                                            'router_password'=>$model_user->district->router->password,
                                                        ]
                                                    );



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
                                        $userServicePacketModel->save( false );
                                     }
                               }
                            }
                            if ( $caclNextUpdateAtForUser['monthCount'] >  0 ) {
                               if ( $model_user->bank_status == 0 ) {
                                    $recipetId = $recipet['id'];
                                }else{
                                    $recipetId = null;
                                }
                                for ( $i=0; $i < $caclNextUpdateAtForUser['monthCount']; $i++ ) { 
                                    if (  $i == 0 && $model_user->paid_time_type == 0  ) {
                                        $tariffAndServiceArray = \app\models\UserBalance::CalcUserTariffDaily(
                                            $model_user->id, 
                                            $daily_calc, 
                                            $half_month
                                        );
                                    }else{
                                        $daily_calc = false; 
                                        $half_month =  false;

                                        $tariffAndServiceArray = \app\models\UserBalance::CalcUserTariffDaily(
                                            $model_user->id, 
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
                                                    1, 
                                                    $recipetId,
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
                                                    1, 
                                                    $recipetId,
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
                                                    1, 
                                                    $recipetId,
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
                                                    1, 
                                                    $recipetId,
                                                    $service['packet_id']
                                                );
                                             }
                                       }
                                    }
                                    \app\models\UsersGifts::checkAndAddGiftHistory( $id );
                                    \app\models\UsersCredit::CheckAndAddCreditHistory( $id, 0, $recipet['id'] );
                                }
                                $model_user->updated_at =  $caclNextUpdateAtForUser['updateAt'];
                                $model_user->paid_day =  $caclNextUpdateAtForUser['paidDay'];
                                $model_user->status = 1;
                            }
        
                            if ( $model_user->bank_status == 0 ) {
                                $model->receipt_id = $recipet['id'];
                                if ( $model->save(false) ) {
                                    \app\models\Receipt::changeReceiptStatus($recipet['id']);
                                }
                            }else{
                                $model->receipt_id = null;
                                $model->save(false);
                            }


                            $model_user->balance = \app\models\UserBalance::CalcUserTotalBalance($id);
                            $model_user->bonus = \app\models\UserBalance::CalcUserTotalBonus($id);
                            $model_user->last_payment = time();
                            
                            if ( $model_user->save(false) ) {
                                $logMessage = Yii::$app->request->post('UserBalance')['balance_in'] . " AZN added to  balane and services will activated until ".date("d-m-Y H:i:s",$caclNextUpdateAtForUser['updateAt']);
                                Logs::writeLog(
                                    Yii::$app->user->username, 
                                    intval($model_user->id), 
                                    $logMessage, 
                                    time()
                                );

                                if ( $siteConfig['expired_service'] != "0" ) {
                                    \app\components\Utils::sendExperiedDate( 
                                        $model_user->id, 
                                        $model_user->contract_number, 
                                        $model_user->phone, 
                                        $model_user->message_lang, 
                                        $model_user->updated_at 
                                    );
                                }

                                return [
                                    'status' => 'success',
                                    'url' => \yii\helpers\Url::to(['view', 'id' => $model_user->id], true),
                                    'text' => Yii::t('app','Main balance increased {balanceIn} and bonus balance increased {bonusIn} {defaultCurrency} and services will activated until {date}',
                                        [
                                            'balanceIn'=>Yii::$app->request->post('UserBalance')['balance_in'],
                                            'bonusIn'=>Yii::$app->request->post('UserBalance')['bonus_in'],
                                            'defaultCurrency'=>$siteConfig['currency'],
                                            'date'=>date("d-m-Y H:i:s",$caclNextUpdateAtForUser['updateAt'])
                                        ]
                                    ),
                                ];
                            }
                        } else {
                            $model_user->balance = \app\models\UserBalance::CalcUserTotalBalance($id);
                            if ( $model_user->save(false) ) {
                                $logMessage = Yii::$app->request->post('UserBalance')['balance_in'] . " AZN added to balance";
                                Logs::writeLog(
                                    Yii::$app->user->username, 
                                    intval($model_user->id), 
                                    $logMessage, 
                                    time()
                                );
                                return [
                                    'status' => 'success',
                                    'url' => \yii\helpers\Url::to(['print', 'id' => $id], true),
                                    'text' => Yii::t('app','Main balance increased {balanceIn} and bonus balance increased {bonusIn} {defaultCurrency}',
                                        [
                                            'balanceIn'=>Yii::$app->request->post('UserBalance')['balance_in'],
                                            'bonusIn'=>Yii::$app->request->post('UserBalance')['bonus_in'],
                                            'defaultCurrency'=>$siteConfig['currency']
                                        ]
                                    ),
                                    
                                ];
                            }
                        }
                    }
                }
            } else {
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        if (\yii\widgets\ActiveForm::validate($model)) {
                            return \yii\widgets\ActiveForm::validate($model);
                        }
                    }
                    $caclNextUpdateAtForUser = Users::caclNextUpdateAtForUser( 
                        $model_user->id,
                        $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                        Yii::$app->request->post('UserBalance')['balance_in'] 
                    );
                    
                    $created_at = time();
                    $model->created_at = $created_at;

                    $calcUserBonusPayment = ( $model->balance_in > 0 ) ? \app\components\Utils::calcUserBonusPayment( $model->balance_in, $model_user->id ) : 0;
                    $model->balance_in = Yii::$app->request->post('UserBalance')['balance_in'];
                    $model->bonus_in = $model->bonus_in + $calcUserBonusPayment;
                    $model->payment_method = 0;
                    $model->status = 0;

                    if ( $caclNextUpdateAtForUser['monthCount'] > 0 ) {
                       if ( $model_user->bank_status == 0 ) {
                            $recipetId = $recipet['id'];
                        }else{
                            $recipetId = null;
                        }

                        for ($i=0; $i < $caclNextUpdateAtForUser['monthCount']; $i++) { 
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
                                            1, 
                                            $recipetId,
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
                                            1, 
                                            $recipetId,
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
                                            1, 
                                            $recipetId,
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
                                            1, 
                                            $recipetId,
                                            $service['packet_id']
                                        );
                                     }
                               }
                            }
                            \app\models\UsersGifts::checkAndAddGiftHistory( $id );
                            \app\models\UsersCredit::CheckAndAddCreditHistory( $id, 0, $recipet['id'] );
                        }
                        $model_user->updated_at = $caclNextUpdateAtForUser['updateAt'];
                        $logMessage = Yii::$app->request->post('UserBalance')['balance_in'] . " AZN added to " . $model_user->fullname . "'s balane and services will activated until ".date("d-m-Y",$caclNextUpdateAtForUser['updateAt']);
                        $responseText  = Yii::t('app','Main balance increased {balanceIn} and bonus balance increased {bonusIn} {defaultCurrency} and services will activated until {updatedAt}',
                                    [
                                        'balanceIn'=>$model->balance_in,
                                        'bonusIn'=>$model->bonus_in,
                                        'defaultCurrency'=>$siteConfig['currency'],
                                        'updatedAt'=>date("d-m-Y",$caclNextUpdateAtForUser['updateAt'])
                                    ]
                                );


                                if ( $siteConfig['expired_service'] != "0" ) {
                                    \app\components\Utils::sendExperiedDate( 
                                        $model_user->id, 
                                        $model_user->contract_number, 
                                        $model_user->phone, 
                                        $model_user->message_lang, 
                                        $model_user->updated_at 
                                    );
                                }
                        }else{
                            $responseText  = Yii::t('app','Main balance increased {balanceIn} and bonus balance increased {bonusIn} {defaultCurrency}',
                                        [
                                            'balanceIn'=>$model->balance_in,
                                            'bonusIn'=>$model->bonus_in,
                                            'defaultCurrency'=>$siteConfig['currency'],
                                        ]
                                    );
                            $logMessage = Yii::$app->request->post('UserBalance')['balance_in'] . " AZN added to " . $model_user->fullname . "'s balane";
                        }

                        if ( $model->balance_in > 0 ) {
                         if ( $model_user->bank_status == 0 ) {
                                 $model->receipt_id = $recipet['id'];
                                \app\models\Receipt::changeReceiptStatus($recipet['id']);
                            }else{
                                 $model->receipt_id = null;
                            }
                        }
                        if( $model->save( false ) ){
                            $model_user->balance = \app\models\UserBalance::CalcUserTotalBalance($id);
                            $model_user->bonus = \app\models\UserBalance::CalcUserTotalBonus($id);
                            if ( $model_user->save(false) ) {
                                Logs::writeLog(
                                    Yii::$app->user->username,
                                    intval($model_user->id),
                                    $logMessage,
                                    time()
                                );
                                return [
                                    'status' => 'success',
                                    'url' => \yii\helpers\Url::to(['print', 'id' => $id], true),
                                    'text' => $responseText
                                ];
                            }
                        }
                }
            }
            return $this->renderAjax('add-balance', ['model' => $model, 'id' => $id, 'model_user' => $model_user, 'operator' => Yii::$app->user->username, 'user_tariff' => $user_tariff + $user_credit_tariff , 'services_array' => $services_array,  'recipet' => $recipet]);
        } else {
            echo '<span class="badge badge-danger" style="display: block; margin: 0 auto; padding: 10px; font-size: 14px;">' . Yii::t('app', 'You dont have any recipet balance. Please contact  adminstration') . '</span>';
        }
    } 

    public function actionGiveCredit($id){
        $model = \app\models\Users::find()
        ->where(['id' => $id])
        ->withByLocation()
        ->one();
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $id = Yii::$app->request->get("id");
            return \app\models\Users::getGiveCredit( $id, time() + 259200 );

        }
        return $this->renderAjax('give-credit', ['model' => $model]);
    }

    public function actionAjaxPendingNotify(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $check_illegal_inet_usage = \app\models\Users::find()
            ->select('users.*,users_inet.status')
            ->leftJoin("users_inet", "users.id=users_inet.user_id")
            ->orderBy(['users.id' => SORT_DESC])
            ->where(['users.status' => 2])
            ->andWhere(['users_inet.status' => 1])
            ->withByLocation()
            ->all();

            $check_cont_number = \app\models\UsersSevices::find()
            ->select('users_sevices.*,users.fullname as fname')
            ->leftJoin("users", "users.id=users_sevices.user_id")
            ->where(['contract_number' => ''])
            ->asArray()
            ->all();
            
            $li = "";
            if ($check_illegal_inet_usage) {
                foreach ($check_illegal_inet_usage as $key => $inet) {
                    $user_name = $inet["fullname"];
                    $id = $inet["id"];
                    $li .= '<li class="unread notification-danger"> <a href="#"> <i class="ico-not_balance pull-right"></i> <span class="block-line strong">' . $inet["fullname"] . ' </span>is using temporary internet service with special permission <span style="color: red">(Balance not enought)</span> </a> </li>';
                }
            }
            if ($check_cont_number) {
                foreach ($check_cont_number as $key => $c_n) {
                    $li .= '<li class="unread notification-danger"> <a href="/users/contract?id=' . $c_n['user_id'] . '"> <i class="ico-not_contract pull-right"></i> <span class="block-line strong">' . $c_n['fname'] . ' </span> Contract number is empty! <span style="color: green">(Please fill it)</span> </a> </li>';
                }
            }
            return ['count' => (count($check_illegal_inet_usage) + count($check_cont_number)), 'list' => $li];
        }
    }

    public function actionEditable($id){
        $post = Yii::$app->request->post();
        $model_user = Users::find()->where(['id' => $id])->one();
        if ($post['name'] == "fullname") {
            $model_user->fullname = $post['value'];

            $model_user->save(false);
        } elseif ($post['name'] == "company") {
            $model_user->company = $post['value'];

            $model_user->save(false);
        } elseif ($post['name'] == "phone") {
            $model_user->phone = $post['value'];

            $model_user->save(false);
        } elseif ($post['name'] == "email") {
            $model_user->email = $post['value'];

            $model_user->save(false);
        } elseif ($post['name'] == "city") {
            $model_user->city_id = $post['value'];

            $model_user->save(false);
            return \app\models\District::getDistrictEditableValue($post['value']);
        } elseif ($post['name'] == "district") {
            $model_user->district_id = $post['value'];

            $model_user->save(false);
            return \app\models\Locations::getLocationEditableValue($post['value']);
        } elseif ($post['name'] == "location") {
            $model_user->location_id = $post['value'];

            $model_user->save(false);
        } elseif ($post['name'] == "room") {
            $model_user->room = $post['value'];

            $model_user->save(false);
        } elseif ($post['name'] == "port_payment") {
            $model_user->port_payment = $post['value'];
            $model_user->save(false);
        }
    }

    protected function findModel($id){
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

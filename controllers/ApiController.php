<?php

namespace app\controllers;

use app\models\api\Users;
use app\models\Logs;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;


class ApiController extends \app\controllers\ActiveController{

 
    public $modelClass = Users::class;

    public function behaviors(){
        $behaviors = parent::behaviors();
        $behaviors['verbs']['class'] = VerbFilter::className();
        $behaviors['verbs']['actions']['search'] =  ['POST'];
        $behaviors['verbs']['actions']['support'] =  ['POST'];
        $behaviors['verbs']['actions']['check-contract'] =  ['POST'];
        $behaviors['verbs']['actions']['send-request'] =  ['POST'];
        $behaviors['verbs']['actions']['add-balance'] =  ['POST'];
        $behaviors['verbs']['actions']['search-by-check-number'] =  ['POST'];
        $behaviors['authenticator']['only'] = ['search','search-by-check-number','add-balance','payment-history','send-request','support','check-contract'];
        $behaviors['authenticator']['authMethods'] = [
            HttpBearerAuth::class
        ];
        return $behaviors;
    }

    public function actions(){
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    /* APP OR WEB SITE API  start*/

    private function checkToken (){
        $post = Yii::$app->request->post();
        $model = \app\models\Users::find()->where(['auth_key'=>$post['token']])->one();

        if ( $model != null ) {
            if ( $model->token_expr + 86400 > time() ) {
               return [
                'status'=>'success',
                'statusCode'=>200,
                'message'=>'Token was expired'
               ];
            }else{
               return [
                'status'=>'error',
                'statusCode'=>401,
                'message'=>'Token was expired'
               ];
            }

        }
           return [
            'status'=>'error',
            'statusCode'=>401,
            'message'=>'Token was incorrect'
           ];
    }

    public function actionLogin ( $contract_number,$password ){

        $model = \app\models\Users::find()->where(['contract_number'=>$contract_number])->one();
        if (password_verify($password, $model['password'])) {
            $model->auth_key =  \Yii::$app->security->generateRandomString();
            $model->token_expr =  time();
            if ( $model->save(false ) ) {
               return [
                'status'=>'success',
                'statusCode'=>200,
                'fullname'=>$model->fullname,
                'token'=> $model->auth_key
               ];
            }
        }
         throw new \yii\web\UnauthorizedHttpException('Login or password is incorrect');  
    }

    public function actionUserService (){

        $memberModel = \webvimark\modules\UserManagement\models\User::find()
        ->where(['id'=>Yii::$app->user->id])
        ->asArray()
        ->one(); 

        $post = Yii::$app->request->post();

        if ($memberModel['api_access'] == '0') {
             throw new \yii\web\UnauthorizedHttpException('Your token access was disabled.Please contact adminstration');
        }

        if (!isset($post['token'])) {
            throw new \yii\web\UnauthorizedHttpException('Token can\'t be blank.');
        }

        if ( $this->checkToken($post['token'])['status'] != 'success' ) {
             throw new \yii\web\UnauthorizedHttpException('Your token was incorrect or expired');      
        }

        $userModel = \app\models\Users::find()
        ->where(['auth_key'=>$post['token']])
        ->asArray()
        ->one();

        $usersServicesPacketsModel = \app\models\UsersServicesPackets::find()
        ->select('services.service_name as service_name,service_packets.packet_name as packet_name,users_inet.login as login,users_inet.password as password,users_services_packets.id as user_service_packet_id,users_services_packets.status as status')
        ->leftJoin('users','users.id=users_services_packets.user_id')
        ->leftJoin('services','services.id=users_services_packets.service_id')
        ->leftJoin('service_packets','service_packets.id=users_services_packets.packet_id')
        ->leftJoin('users_inet','users_inet.u_s_p_i=users_services_packets.id')
        ->where(['auth_key'=>$post['token']])
        ->asArray()
        ->all();

        return [
            'data'=>[
                'fullname'=>$userModel['fullname'],
                'services'=>$usersServicesPacketsModel
            ]
        ];
    }

    public function actionPaymentHistory(){
        $memberModel = \webvimark\modules\UserManagement\models\User::find()
        ->where(['id'=>Yii::$app->user->id])
        ->asArray()
        ->one(); 

        $post = Yii::$app->request->post();

        if ($memberModel['api_access'] == '0') {
             throw new \yii\web\UnauthorizedHttpException('Your token access was disabled.Please contact adminstration');
        }

        if (!isset($post['token'])) {
            throw new \yii\web\ForbiddenHttpException('Token can\'t be blank.');
        }

        if ( $this->checkToken($post['token'])['status'] != 'success' ) {
             throw new \yii\web\UnauthorizedHttpException('Your token was incorrect or expired');      
        }


        if (!isset($post['page'])) {
            throw new \yii\web\BadRequestHttpException('Offset can\'t be blank.');
        }



        if (!isset($post['per-page'])) {
             $per_page = 32;
        }else{
            $per_page = ( $post['per-page'] < 15 ) ? $post['per-page'] : 15;
        }

         $model = Users::find()
        ->where(['auth_key' => $post['token']])
        ->one();

        $user_status = '';
        if ($model != null) {
            $userBalanceModel = \app\models\UserBalance::find()
            ->select('user_balance.*,receipt.code as receipt_id')
            ->leftJoin("receipt", "receipt.id=user_balance.receipt_id")
            ->where(['user_id'=>$model->id])
            ->orderBy(['user_balance.id'=>SORT_ASC])
            ->offset($per_page*$post['page'])
            ->limit($per_page)
            ->asArray()
            ->all();



            $paymentDetail = [];
            foreach ($userBalanceModel as $key => $payment) {
                     $paymentDetail[$key]['balance_in']  = $payment['balance_in'];
                     $paymentDetail[$key]['bonus_in']    = $payment['bonus_in']; 
                     $paymentDetail[$key]['balance_out'] = $payment['balance_out'];
                     $paymentDetail[$key]['bonus_out']   = $payment['bonus_out']; 
                     $paymentDetail[$key]['receipt_id']  = $payment['receipt_id']; 
                     $paymentDetail[$key]['created_at']  = date('d/m/Y H:i:s',$payment['created_at']); 
            }

           return [
            'data'=>[
                'fullname'=>$model['fullname'],
                'page'=>$post['page'],
                'per_page'=>$per_page,
                'paymentDetail'=>$paymentDetail,
            ]
           ];
       
        } else {
            return [
                'status' => 'error',
                'message' => 'Contract number is invalid.',
            ];
        }
    }

    public function actionSendRequest(){

        $memberModel = \webvimark\modules\UserManagement\models\User::find()
        ->where(['id'=>Yii::$app->user->id])
        ->asArray()
        ->one(); 


        $post = Yii::$app->request->post();
        if (!isset($post['name'])) {
            throw new \yii\web\BadRequestHttpException('name  can\'t be blank.');
        }

        if (!isset($post['lastname'])) {
            throw new \yii\web\BadRequestHttpException('lastName  can\'t be blank.');
        }

        if (!isset($post['phone'])) {
            throw new \yii\web\BadRequestHttpException('phone  can\'t be blank.');
        }

        if (!isset($post['email'])) {
            throw new \yii\web\BadRequestHttpException('email  can\'t be blank.');
        }


        if (!isset($post['description'])) {
            throw new \yii\web\BadRequestHttpException('description  can\'t be blank.');
        }

        $model = new \app\models\Users;
        $model->fullname = $post['lastname']." ".$post['name'];
        $model->phone = $post['phone'];
        $model->email = $post['email'];
        $model->description = $post['description'];
        if ( $model->save(false) ) {
          return [
                'status' => 'success',
            ];
        }
  
    
          return [
                'status' => 'error',
                'message' => 'Something went wrong',
            ];
    }

    public function actionSupport(){

        $memberModel = \webvimark\modules\UserManagement\models\User::find()
        ->where(['id'=>Yii::$app->user->id])
        ->asArray()
        ->one(); 


        $post = Yii::$app->request->post();
        if (!isset($post['contract_number'])) {
            throw new \yii\web\BadRequestHttpException('contract_number  can\'t be blank.');
        }

        if (!isset($post['reason'])) {
            throw new \yii\web\BadRequestHttpException('reason  can\'t be blank.');
        }

        if (!isset($post['description'])) {
            throw new \yii\web\BadRequestHttpException('description  can\'t be blank.');
        }

        $userModel = \app\models\Users::find()->where(['contract_number'=>$post['contract_number']])->asArray()->one();

        $model = new \app\models\UserDamages;
        $model->user_id = $userModel['id'];
        $model->member_id = $memberModel['id'];
        $model->damage_reason = $post['reason'];
        $model->message = $post['description'];
        if ( $model->save(false) ) {
          return [
                'status' => 'success',
            ];
        }
  
    
          return [
                'status' => 'error',
                'message' => 'Something went wrong',
            ];
    }

    public function actionCheckContract(){

        $memberModel = \webvimark\modules\UserManagement\models\User::find()
        ->where(['id'=>Yii::$app->user->id])
        ->asArray()
        ->one(); 


        $post = Yii::$app->request->post();
        if (!isset($post['contract_number'])) {
            return [
                'status' => 'error',
                'message' => 'contract_number can not blank',
            ];
        }

        $model = \app\models\Users::find()
        ->where(['contract_number'=>$post['contract_number']])
        ->asArray()
        ->one();

        if ( $model == null ) {
            return [
                'status' => 'error',
                'message' => 'contract_number is invalid',
            ];
        }

          return [
                'status' => 'success',
            ];
    }

    /* APP OR WEB SITE API  end*/

    public function actionSearch(){
        $memberModel = \webvimark\modules\UserManagement\models\User::find()
        ->where(['id'=>Yii::$app->user->id])
        ->asArray()
        ->one(); 

        $siteConfig = \app\models\SiteConfig::find()
        ->asArray()
        ->one();

        if ($memberModel['api_access'] == '0') {
             throw new \yii\web\UnauthorizedHttpException('Your token access was disabled.Please contact adminstration');
        }

        $post = Yii::$app->request->post();
        if (!isset($post['contract_number'])) {
            throw new \yii\web\BadRequestHttpException('Contract number can\'t be blank.');
        }
        $model = Users::find()
            ->where(['contract_number' => $post['contract_number']])
            ->one();


        if ( $model == null ) {
            return [
                'status' => 'error',
                'message' => 'contract_number is invalid',
            ];
        }

        if ($model->status == 7 ) {
            return [
                'status' => 'error',
                'message' => 'VIP user',
            ];
        }

        $user_status = '';

        if ( $model != null ) {
            if ( $model->status == '2' ) {
                $user_status = "Deaktiv";
                $daily_calc =  ( $model->paid_time_type == "0" &&  $siteConfig['half_month'] == "0" ) ? true : false;
                $half_month =  ( $model->paid_time_type == "0" &&  $siteConfig['half_month'] == "1" ) ? true : false;
            } elseif ($model->status == '3') {
                $user_status = "Arxiv";
                $daily_calc =  ( $model->paid_time_type == "0" &&  $siteConfig['half_month'] == "0" ) ? true : false;
                $half_month =  ( $model->paid_time_type == "0" &&  $siteConfig['half_month'] == "1" ) ? true : false;

            } else {
                $user_status = "Aktiv";
                $daily_calc =  false;
                $half_month =  false;
            }

            $tariff = \app\models\UserBalance::CalcUserTariffDaily(
                $model['id'], 
                $daily_calc, 
                $half_month
            )['per_total_tariff'];

            $modelService = \app\models\UsersSevices::find()
            ->where(['user_id'=>$model->id])
            ->all();

            $services = '';
            foreach ($modelService as $key => $userService) {
              $services .= $userService->service->service_name.",";
            }

            return [
                "fullname" => $model->fullname,
                "contract_number" => $model->contract_number,
                "service" =>  rtrim($services, ", "),
                "user_status" => $user_status,
                "user_tariff" => sprintf("%.2f", $tariff),
                "user_balance" => $model->balance,
                "user_bonus_balance" => $model->bonus,
                "expired_at" => date("d-m-Y H:i",$model->updated_at ),
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Contract number is invalid.',
            ];
        }
    }

    public function actionSearchByCheckNumber(){
        $memberModel = \webvimark\modules\UserManagement\models\User::find()
        ->where(['id'=>Yii::$app->user->id])
        ->asArray()
        ->one(); 

        if ($memberModel['api_access'] == '0') {
             throw new \yii\web\UnauthorizedHttpException('Your token access was disabled.Please contact adminstration');
        }

        $post =Yii::$app->request->post();
        if (!isset($post['receipt'])) {
             throw new \yii\web\BadRequestHttpException('Receipt can\'t be blank.');
        }
         $model = \app\models\Receipt::find()
        ->select('receipt.code as receipt_code,receipt.status as receipt_status ,user_balance.balance_in as paid,user_balance.created_at as paid_at,users.fullname as user_fullname')
        ->leftJoin('user_balance','receipt.id=user_balance.receipt_id')
        ->leftJoin('users','users.id=user_balance.user_id')
        ->andWhere(['receipt.code'=>$post['receipt']])
        ->asArray()
        ->one();
        if ($model == null) {
             return [
              'status'=>'error',
              'message'=>'Receipt is invalid.'
              ];
        }
        return $model;
    }



    public function actionAddBalance () {

            $siteConfig = \app\models\SiteConfig::find()
            ->asArray()
            ->one();

            $memberModel = \webvimark\modules\UserManagement\models\User::find()
            ->where(['id'=>Yii::$app->user->id])
            ->asArray()
            ->one(); 

            if ( $memberModel['api_access'] == '0' ) {
                 throw new \yii\web\UnauthorizedHttpException('Your token access was disabled.Please contact adminstration');
            }
            $post = Yii::$app->request->post();

            ### contract validation start
            if ( !isset($post['contract_number']) ) {
                throw new \yii\web\BadRequestHttpException('contract_number field is required');
            }

            if ( $post['contract_number'] == "" ) {
                throw new \yii\web\BadRequestHttpException('contract_number can\'t be blank');
            }
            ### contract validation end

            ### balance_in validation start
            if ( !isset($post['balance_in']) ) {
                throw new \yii\web\BadRequestHttpException('balance_in field is required');
            }

            if ( !is_numeric( $post['balance_in'] ) ) {
                throw new \yii\web\BadRequestHttpException('balance_in must be integer');
            }

            if ( $post['balance_in'] <= 0 ) {
                throw new \yii\web\BadRequestHttpException('balance_in must be no less than 0');
            }
            ### balance_in validation end

            ### transaction validation start
            if ( !isset($post['transaction']) ) {
                 throw new \yii\web\BadRequestHttpException('transaction field is required');
            }

            if ( $post['transaction'] == "" ) {
                 throw new \yii\web\BadRequestHttpException('transaction can\'t be blank');
            }
            ### transaction validation end


            ### receipt validation start
            if (!isset($post['receipt'])) {
                 throw new \yii\web\BadRequestHttpException('receipt field is required');
            }
            if ( $post['receipt'] == "" ) {
                 throw new \yii\web\BadRequestHttpException('receipt can\'t be blank.');
            }
            if (isset($post['receipt'])) {
                $check_exsist = \app\models\Receipt::find()
                ->where(['code' => $post['receipt']])
                ->asArray()
                ->one();

                if ($check_exsist != null) {
                    throw new \yii\web\ForbiddenHttpException('Duplicate payment');
                }

            }
            ### receipt validation end


            $check_contratc_number = \app\models\Users::find()
            ->where(['contract_number' => $post['contract_number']])
            ->one();

            if ($check_contratc_number == "" ) {
                return [
                    'status' => 'error',
                    'message' => 'contract_number is invalid.',
                ];
            }

            if ($check_contratc_number->status == 7 ) {
                return [
                    'status' => 'error',
                    'message' => 'VIP user',
                ];
            }



            if ( isset($post['receipt']) ) {
                if ( $post['receipt'] != "") {
                    $new_receipt = new \app\models\Receipt;
                    $new_receipt->code = $post['receipt'];
                    $new_receipt->member_id = Yii::$app->user->id;
                    $new_receipt->status = 1;
                    $new_receipt->type = 1;
                    $new_receipt->created_at = time();
                    $new_receipt->save(false);
                }
            }



           
            $model = new \app\models\UserBalance;
            $model_user = \app\models\Users::find()
            ->where(['id' => $check_contratc_number->id])
            ->one();

            $userService = \app\models\UsersSevices::find()->select('services.service_alias as service_alias')
            ->leftJoin('services','services.id=users_sevices.service_id')
            ->where(['user_id'=>$model_user->id])
            ->groupBy('service_id')
            ->asArray()
            ->all();


            $services_array = \app\models\UsersServicesPackets::getServicesAsArray( $model_user->id );
            $user_packets = \app\models\UsersServicesPackets::find()
            ->where(['user_id' => $model_user->id])
            ->all();

            if ( $model_user->status == 2 || $model_user->status == 3 ) {
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
                
            $created_at =  time();
            $user_tariff = $tariffAndServiceArray['services_total_tariff'];
            $user_credit_tariff = $tariffAndServiceArray['credit_tariff'];
            $model->created_at =  $created_at;
            
            if ( $model_user->status == 2 || $model_user->status == 3 ) {
                if ( $post ) {
                    $model->payment_method = 1;
                    $model->balance_in = $post['balance_in'];
                    $model->user_id = $model_user->id;
                    $model->transaction = $post['transaction'];
                    $model->receipt_id = $new_receipt->id;
                    $model->status = 0;


                    $calcUserBonusPayment = ( $model->balance_in > 0 ) ? \app\components\Utils::calcUserBonusPayment( $model->balance_in, $model_user->id ) : 0;
                    $model->bonus_in =  $calcUserBonusPayment;


                    if ( $model_user->paid_time_type == "0" ) {
                        $caclNextUpdateAtForUser = Users::caclNextUpdateAtForUser( 
                            $model_user->id,
                            $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                            $post['balance_in'] ,
                            ['untilToMonthTariff'=>$user_tariff,'credit_tariff'=>$tariffAndServiceArray['credit_tariff'],'total_tariff'=>$model_user->tariff]
                        );
                    }



                    if ( $model_user->paid_time_type == "1" ) {
                        $caclNextUpdateAtForUser = Users::caclNextUpdateAtForUser( 
                            $model_user->id,
                            $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                            $post['balance_in'],
                        );
                    }

                  

                    if( $model->save(false) ){
                        $model_user->balance = \app\models\UserBalance::CalcUserTotalBalance( $model_user->id );
                        $model_user->bonus = \app\models\UserBalance::CalcUserTotalBonus( $model_user->id );
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
                                             if ( $model_user->status == 2 ) {

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

                                             if ( $model_user->status == 3 ) {

                                                if ( $inetModel->static_ip != "" ) {
                                                    $staticIpModel = \app\models\IpAdresses::find()
                                                    ->where(['id'=>$inetModel->static_ip])
                                                    ->one();
                                

                                                     \app\components\MikrotikQueries::dhcpAddMacFromArchive(
                                                        $inetModel->login, 
                                                        $inetModel->packet->download."k"."/".$inetModel->packet->upload."k",
                                                        $staticIpModel->public_ip,
                                                        "iNet_yes",
                                                        $model_user->district->router->nas, 
                                                        $model_user->district->router->username, 
                                                        $model_user->district->router->password,
                                                        "dhcpAddMacFromArchive",
                                                        [
                                                            'login'=> $inetModel->login,
                                                            'rateLimit'=>$inetModel->packet->download."k"."/".$inetModel->packet->upload."k",
                                                            'ipAddress'=> $staticIpModel->public_ip,
                                                            'addressList'=>"iNet_yes",
                                                            'nas'=>$model_user->district->router->nas,
                                                            'router_username'=>$model_user->district->router->username,
                                                            'router_password'=>$model_user->district->router->password,
                                                        ]
                                                     );

                                                }else{

                                                    $cgnModel = \app\models\CgnIpAddress::find()
                                                    ->where(['inet_login'=>$inetModel->login])
                                                    ->asArray()
                                                    ->one();

                                                    if ( $cgnModel != null ) {
                                                        $internalIp = $cgnModel['internal_ip'];
                                                    }else{
                                                        $cgnModel = \app\models\CgnIpAddress::find()
                                                        ->where(['router_id'=>$routerModel['id']])
                                                        ->andWhere(['is', 'inet_login', new \yii\db\Expression('null')])
                                                        ->orderBy(['internal_ip'=>SORT_ASC])
                                                        ->one();

                                                        $cgnModel->inet_login = $inetModel->login;
                                                        $cgnModel->save(false);

                                                        $internalIp =   $cgnModel['internal_ip'];                                                   
                                                    }

                                                    \app\components\MikrotikQueries::dhcpAddMacFromArchive(
                                                        $inetModel->login, 
                                                        $inetModel->packet->download."k"."/".$inetModel->packet->upload."k",
                                                        $cgnModel['internal_ip'],
                                                        "iNet_yes",
                                                        $model_user->district->router->nas, 
                                                        $model_user->district->router->username, 
                                                        $model_user->district->router->password,
                                                        "dhcpAddMacFromArchive",
                                                        [
                                                            'login'=> $inetModel->login,
                                                            'rateLimit'=>$inetModel->packet->download."k"."/".$inetModel->packet->upload."k",
                                                            'ipAddress'=> $cgnModel['internal_ip'],
                                                            'addressList'=>"iNet_yes",
                                                            'nas'=>$model_user->district->router->nas,
                                                            'router_username'=>$model_user->district->router->username,
                                                            'router_password'=>$model_user->district->router->password,
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
                                        ->where(['user_id'=>$service['user_id']])
                                        ->andWhere(['u_s_p_i'=>$service['u_s_p_i']])
                                        ->one();
                                        $voIpModel->status = 1;
                                        $voIpModel->save( false );
                                    }
                                   
                                    
                                 }
                           }
                        }
              
                        $model->save(false);
                        if ( $caclNextUpdateAtForUser['monthCount'] > 0 ) {
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
                                                $new_receipt->id,
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
                                                $new_receipt->id,
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
                                                $new_receipt->id,
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
                                                $new_receipt->id,
                                                $service['packet_id']
                                            );
                                         }
                                   }
                                }

                                $model_user->status = 1;
                                $model_user->updated_at =  $caclNextUpdateAtForUser['updateAt'];
                                $model_user->paid_day =  $caclNextUpdateAtForUser['paidDay'];

                                \app\models\UsersGifts::checkAndAddGiftHistory( $model->user_id );
                                \app\models\UsersCredit::CheckAndAddCreditHistory( $model->user_id, 1, $post['receipt'] );
                            }
                        }
    
                        $model_user->balance = \app\models\UserBalance::CalcUserTotalBalance( $model_user->id  );
                        $model_user->bonus = \app\models\UserBalance::CalcUserTotalBonus( $model_user->id  );
                        $model_user->last_payment = time();
                        if ( $model_user->save(false) ) {
                            $logMessage = $post['balance_in'] . " AZN added to balance with API and Services will activated until ".date("d-m-Y H:i:s",$caclNextUpdateAtForUser['updateAt']);
                            Logs::writeLog(
                                Yii::$app->user->username, 
                                intval( $model_user->id ), 
                                $logMessage, 
                                $model_user->last_payment
                            );

                           ###### sending  experied_service sms template ######
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
                                'balance_in' => $post['balance_in'],
                                'receipt' => $new_receipt->code,
                                'transaction' => $post['transaction'],
                            ];
                        }
                    } else {
                        $model_user->balance = \app\models\UserBalance::CalcUserTotalBalance( $model_user->id );
                        if ( $model_user->save(false) ) {
                            $logMessage = $post['balance_in'] . " AZN added to balance with API";
                            Logs::writeLog(
                                Yii::$app->user->username, 
                                intval( $model_user->id ), 
                                $logMessage, 
                                time()
                            );
                            return [
                                'status' => 'success',
                                'balance_in' => $post['balance_in'],
                                'receipt' => $post['receipt'],
                                'transaction' => $post['transaction'],
                            ];
                        }

                    }
                }
            } else {
                if ( $post ) {
                    $caclNextUpdateAtForUser = Users::caclNextUpdateAtForUser( 
                        $model_user->id,
                        $tariffAndServiceArray['services_total_tariff'] + $tariffAndServiceArray['credit_tariff'] , 
                        $post['balance_in'] 
                    );
 
                    $calcUserBonusPayment = ( $model->balance_in > 0 ) ? \app\components\Utils::calcUserBonusPayment( $model->balance_in, $model_user->id ) : 0;

                    $model->balance_in = $post['balance_in'];
                    $model->user_id = $model_user->id;
                    $model->payment_method = 1;
                    $model->transaction = $post['transaction'];
                    $model->receipt_id = $new_receipt->id;
                    $model->status = 0;

                    $model->bonus_in = $model->bonus_in + $calcUserBonusPayment;

                    if ( $caclNextUpdateAtForUser['monthCount'] > 0 ) {
                        for ( $i=0; $i < $caclNextUpdateAtForUser['monthCount']; $i++ ) { 
                            \app\models\UsersGifts::checkAndAddGiftHistory( $model_user->id );
                            \app\models\UsersCredit::CheckAndAddCreditHistory( $model->user_id, 1, $post['receipt'] );

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
                                                $new_receipt->id,
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
                                                $new_receipt->id,
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
                                                $new_receipt->id,
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
                                                $new_receipt->id,
                                                $service['packet_id']
                                            );
                                         }
                                   }
                                }


                        }
                
                        $model_user->updated_at = $caclNextUpdateAtForUser['updateAt'];
                        $logMessage = $post['balance_in'] . " " .$siteConfig['currency']."  added to  balance with API on ACTIVE status. Services will activated again until ".date("d-m-Y",$caclNextUpdateAtForUser['updateAt']);

                       ###### sending  experied_service sms template ######
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
                        $logMessage = $post['balance_in'] . " " .$siteConfig['currency']."  added to " . $model_user->fullname . "'s balane with API on ACTIVE status";
                    }

                    if( $model->save( false ) ){
                        $model_user->balance = \app\models\UserBalance::CalcUserTotalBalance( $model_user->id );
                        $model_user->bonus = \app\models\UserBalance::CalcUserTotalBonus( $model_user->id );
                        if ( $model_user->save(false) ) {
                            
                            Logs::writeLog(
                                Yii::$app->user->username,
                                intval($model_user->id),
                                $logMessage,
                                time()
                            );
                             
                            return [
                                'status' => 'success',
                                'balance_in' => $post['balance_in'],
                                'receipt' => $new_receipt->code,
                                'transaction' => $post['transaction'],
                            ];
                        }
                    }
                }
            }
    }



}

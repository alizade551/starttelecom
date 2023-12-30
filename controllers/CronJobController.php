<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\UsersInet;

class CronJobController extends \yii\web\Controller{

    public static function getSiteConfig (){
        $siteConfig = \app\models\SiteConfig::find()
        ->asArray()
        ->one();

        return $siteConfig;
    }

    public function WriteExLog( $action_name, $start_time, $end_time, $execution_time ){
        $file = Yii::getAlias('@runtime') . '/cron_exec_info.txt';
        if (is_file($file)) {
            $text = $action_name . ' runned ' . $execution_time . ' second at ' . date('d-m-Y H:i:s', time()) . PHP_EOL;
            $fp = fopen($file, 'a');
            fwrite($fp, $text);
        }
    }

    public function actionSmsSend(){
        $siteConfig = \app\models\SiteConfig::find()
        ->asArray()
        ->one();

        if ( $this->getSiteConfig()['balance_alert_cron'] == "0" ) {
           return false;
        }

        if( $_SERVER["SERVER_ADDR"] == '127.0.0.1' ){
            $start_time = microtime(true);
            $timestamp = strtotime("+2 day");
            $data = date("Y-m-d",$timestamp);

            $userModel = \app\models\Users::find()
            ->where(['status'=>1])
            ->andWhere('tariff > balance')
            ->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME({{%users}}.updated_at), '%Y-%m-%d')", $data],['<=', "DATE_FORMAT(FROM_UNIXTIME({{%users}}.updated_at), '%Y-%m-%d')", $data]])
            ->asArray()
            ->all();

            foreach ($userModel as $key => $user ) {
                $messageModel = \app\models\MessageTemplate::find()
                ->where(['name'=>'balance_alert'])
                ->andWhere(['lang'=>$user['message_lang']])
                ->asArray()
                ->one();

                // sms selected on setting
                if ( $this->getSiteConfig()['balance_alert_cron'] == "1" ) {
                    $params = json_encode(
                        [
                            '{contract_number}'=>$user['contract_number'],
                            '{date}'=>date("d.m.Y", strtotime("+2 day")),
                        ]
                    );

                    $templateSmsAsText = \app\components\Utils::createText( 
                        $messageModel['sms_text'],
                        [
                            '{contract_number}'=>$user['contract_number'],
                            '{date}'=>date("d.m.Y", strtotime("+2 day")),
                        ] 
                    );

                    $checkSmsWasSent = \app\models\UsersMessage::sendSms( 
                        $user['id'] , 
                        "balance_alert_cron", 
                        substr( $user['phone'] , 1 ), 
                        $templateSmsAsText,
                        $params
                    );

                }
                // whatsapp selected on setting
                if ( $this->getSiteConfig()['balance_alert_cron'] == "2" ) {

                    $params = json_encode(
                        [
                            '{{1}}'=>$user['fullname'],
                            '{{2}}'=>date("d.m.Y", strtotime("+2 day")),
                        ]
                    );

                    $template = [
                      'name'=>$messageModel['name'],
                      'language'=>['code'=>$messageModel['lang']],
                      "components"=>[
                        ['type'=>'header'],
                        [
                            'type'=>'body',
                            'parameters'=>[
                              [ 'type'=>'text','text'=>$user['fullname'] ],
                              [ 'type'=>'text','text'=>date("d.m.Y", strtotime("+2 day")) ],
                            ]
                        ],
                      ]
                    ];

                    $templateWhatsappAsText = \app\components\Utils::createText( 
                        $messageModel['whatsapp_body_text'],
                        [
                            '{{1}}'=>$user['fullname'],
                            '{{2}}'=>date("d.m.Y", strtotime("+2 day")),
                        ],
           
                    );

                    $checkWhatsappMessage = \app\components\Utils::sendWhatsappMessage( 
                        $template, 
                        $user['id'], 
                        "balance_alert_cron", 
                        substr( $user['phone'] , 1 ),
                        $templateWhatsappAsText,
                        $params
                    );
                }
            
            }
              // End clock time in seconds
              $end_time = microtime(true);
              // Calculate script execution time
              $execution_time = ($end_time - $start_time);
              $this->WriteExLog('SmsSend',$start_time,$end_time,$execution_time );
        }else{
          echo "IP error";
        }
    }

    public function actionCheckArchive(){  
        if ( $_SERVER["SERVER_ADDR"] == '127.0.0.1' && $this->getSiteConfig()['check_archive'] == "1" ) {
            $archive_time = (time() - (86400 * 120));
            // Starting clock time in seconds
            $start_time = microtime(true);
            $all_packets = \app\models\UsersServicesPackets::find()
                ->select('users_services_packets.*,services.service_alias as service_alias_name,users.status as users_status,users.balance as user_balance,users.tariff as user_tariff,users.id as user_t_id,users_inet.login as user_inet_login,users_inet.password as user_inet_password,users_inet.static_ip as u_p_static_ip,service_packets.packet_price as user_packet_price,address_district.router_id as user_router_id,routers.nas as nas,routers.username as router_username,routers.password as router_password,max(user_balance.created_at) as last_user_payment')
                ->leftJoin('services', ' services.id=users_services_packets.service_id')
                ->leftJoin('users', 'users.id=users_services_packets.user_id')
                ->leftJoin('user_balance', 'user_balance.user_id=users_services_packets.user_id')
                ->leftJoin('address_district', 'address_district.id=users.district_id')
                ->leftJoin('routers', 'routers.id=address_district.router_id')
                ->leftJoin('users_inet', 'users_inet.u_s_p_i=users_services_packets.id')
                ->leftJoin('users_tv', 'users_tv.u_s_p_i=users_services_packets.id')
                ->leftJoin('users_wifi', 'users_wifi.u_s_p_i=users_services_packets.id')
                ->leftJoin('service_packets', 'service_packets.id=users_services_packets.packet_id')
                ->where(['users.status' => '2'])
                ->andWhere(['<', 'users.updated_at', $archive_time])
                ->groupBy('users_services_packets.id')
                ->limit( 20 )
                ->asArray()
                ->all();

            $archive_users = [];
            $archive_users_packet_id = [];
            $archive_internet_packets = [];
            $archive_tv_packets = [];
            $archive_wifi_packets = [];
            $static_ips = [];
            foreach ($all_packets as $key => $packet) {
                if ($packet['users_status'] == "2") {
                    $archive_users[] = $packet['user_id'];
                    $archive_users_packet_id[] = $packet['id'];
                    if ($packet['service_alias_name'] == "internet") {
                        $archive_internet_packets[] = $packet['id'];
     
                       if($packet['u_p_static_ip'] != ""){
                          $static_ips[] = $packet['u_p_static_ip'];
                        }

                        \app\models\MikrotikQueries::dhcpRemoveMac(
                            $packet['user_inet_login'], 
                            $packet['nas'], 
                            $packet['router_username'], 
                            $packet['router_password'],
                            "dhcpRemoveMac",
                            [
                                'login'=>$packet['user_inet_login'],
                                'nas'=> $packet['nas'],
                                'router_username'=>$packet['router_username'],
                                'router_password'=>$packet['router_password'],
                            ]
                        );

                    }
                    if ($packet['service_alias_name'] == "tv") {
                        $archive_tv_packets[] = $packet['id'];
                    }
                    if ($packet['service_alias_name'] == "wifi") {
                        $archive_wifi_packets[] = $packet['id'];
                    }

                }
            }

            if (count(array_unique($archive_users)) > 0) {
                \app\models\Users::updateAll(['status' => 3], ['id' => array_unique($archive_users)]);
                $userHistoryText = "Status was changed Deactive to Archive by archive checker cron";
                $data_archive = [];
                foreach (array_unique($archive_users) as $key => $user_id) {
                    $data_archive[$key]['user_id'] = $user_id;
                    $data_archive[$key]['text'] = $userHistoryText;
                    $data_archive[$key]['time'] = time();
                }
                Yii::$app->db->createCommand()->batchInsert('users_history', ['user_id', 'text', 'time'], $data_archive)->execute();
            }

            if (count($archive_users_packet_id) > 0) {
                \app\models\UsersServicesPackets::updateAll(['status' => 3], ['id' => $archive_users_packet_id]);
            }

            if (count(array_unique($archive_internet_packets)) > 0) {
                \app\models\UsersInet::updateAll(['status' => 3], ['u_s_p_i' => $archive_internet_packets]);
            }

            if (count(array_unique($static_ips)) > 0) {
                \app\models\IpAdresses::updateAll(['status' => 0], ['public_ip' => $static_ips]);
            }

            if (count(array_unique($archive_tv_packets)) > 0) {
                \app\models\UsersTv::updateAll(['status' => 3], ['u_s_p_i' => $archive_tv_packets]);
            }
            if (count(array_unique($archive_wifi_packets)) > 0) {
                \app\models\UsersWifi::updateAll(['status' => 3], ['u_s_p_i' => $archive_wifi_packets]);
            }

            // End clock time in seconds
            $end_time = microtime(true);
            // Calculate script execution time
            $execution_time = ($end_time - $start_time);
            $this->WriteExLog('CheckArchive', $start_time, $end_time, $execution_time);
        } else {
            echo "IP error";
        }
    }

    public function actionCheckBalance(){
        if (  $_SERVER["SERVER_ADDR"] == '127.0.0.1' && $this->getSiteConfig()['check_balance'] == "1" ) {
            // Starting clock time in seconds
            $start_time = microtime(true);

            $payment_history_data = [];
            $bonus_history_data = [];
            $user_data = [];
            $user_bonus_data = [];

            $userModel = \app\models\Users::find()
            ->where(['status' => '1'])
            ->andWhere(['<', 'users.updated_at', time()])
            ->limit( 20 )
            ->asArray()
            ->all();

            $activeIds = [];
            foreach ($userModel as $userKey => $userInfo) {
               $activeIds[] = $userInfo['id'];
            }

            if ($userModel != null) {
                $all_packets = \app\models\UsersServicesPackets::find()
                    ->select('users_services_packets.*,services.service_alias as service_alias_name,users.status as users_status,users.balance as user_balance,users.bonus as user_bonus,users.tariff as user_tariff,users.id as user_t_id,users.updated_at as user_updated_at,users.paid_time_type as user_paid_time_type,users.paid_day as user_paid_day,users_inet.login as user_inet_login,users_inet.password as user_inet_password,service_packets.packet_price as user_packet_price,address_district.router_id as user_router_id,routers.nas as nas,routers.username as router_username,routers.password as router_password')
                    ->leftJoin('services', 'services.id=users_services_packets.service_id')
                    ->leftJoin('users', 'users.id=users_services_packets.user_id')
                    ->leftJoin('address_district', 'address_district.id=users.district_id')
                    ->leftJoin('routers', 'routers.id=address_district.router_id')
                    ->leftJoin('users_inet', 'users_inet.u_s_p_i=users_services_packets.id')
                    ->leftJoin('users_tv', 'users_tv.u_s_p_i=users_services_packets.id')
                    ->leftJoin('users_wifi', 'users_wifi.u_s_p_i=users_services_packets.id')
                    ->leftJoin('service_packets', 'service_packets.id=users_services_packets.packet_id')
                    ->where(['users.status' => '1'])
                    ->andWhere(['users_services_packets.user_id'=>$activeIds])
                    ->andWhere(['<', 'users.updated_at', time()])
                    ->groupBy(['users_services_packets.id'])
                    ->asArray()
                    ->all();

                // which users dont have enought balance to activate services,this array keep that users's id
                $low_balance_users = [];
                $low_balance_users_internet = [];
                $low_balance_users_tv = [];
                $low_balance_users_wifi = [];
                $low_balance_users_voip = [];


             
                $highBonusUsers = [];
                $highBonusUsersId = [];

                foreach ($all_packets as $key => $packet_one) {

                     ### userin balansi kicikdirse tariden ve bonus balans tarifden kickdirse
                    if ( round( $packet_one['user_tariff'] , 2 )  > round( $packet_one['user_balance'] , 2 ) && round( $packet_one['user_tariff'] , 2 )  > round( $packet_one['user_bonus'] , 2 ) ) {


                        $low_balance_users[] = $packet_one['user_id'];

                        if ($packet_one['service_alias_name'] == "internet") {
                            $low_balance_users_internet[] = $packet_one['user_id'];

                            \app\components\MikrotikQueries::dhcpBlockMac(
                                $packet_one['user_inet_login'], 
                                $packet_one['nas'], 
                                $router_model['username'], 
                                $packet_one['router_password'],
                                "dhcpBlockMac",
                                [
                                    'login'=>$packet_one['user_inet_login'],
                                    'nas'=> $packet_one['nas'],
                                    'router_username'=>$packet_one['router_username'],
                                    'router_password'=>$packet_one['router_password']
                                ]
                            );



                        }
                        if ($packet_one['service_alias_name'] == "tv") {
                            $low_balance_users_tv[] = $packet_one['user_id'];
                        }

                        if ($packet_one['service_alias_name'] == "wifi") {
                            $low_balance_users_wifi[] = $packet_one['user_id'];
                        }

                        if ($packet_one['service_alias_name'] == "voip") {
                            $low_balance_users_voip[] = $packet_one['user_id'];
                        }

                    }
                    ### userin balansi kicikdirse tariden ve bonus balans tarifden boyukdurse
                    if ( round( $packet_one['user_tariff'] , 2 )  > round( $packet_one['user_balance'] , 2 ) && round( $packet_one['user_tariff'] , 2 )  <= round( $packet_one['user_bonus'] , 2 ) ) {

                        $highBonusUsers[$key]['user_id'] = $packet_one['user_id'];
                        $highBonusUsers[$key]['nextUpdateTime'] = \app\components\Utils::nextUpdateBonus( $packet_one['user_paid_day'], $packet_one['user_updated_at'] )['updateAt'];
                        $highBonusUsersId[] = $packet_one['user_id'];
                        
                        $user_bonus_data[$key]['user_id'] = $packet_one['user_id'];
                        $user_bonus_data[$key]['user_bonus'] = $packet_one['user_bonus'];

                        if ( $packet_one['price'] != null || $packet_one['price'] != 0 ) {
                            $packetPrice = $packet_one['price'];
                        }else{
                            $packetPrice = $packet_one['user_packet_price'];
                        }
                        $user_bonus_data[$key]['user_packet_price'] = $packetPrice;

                        if ($packet_one['service_alias_name'] == "internet") {
                            $bonus_history_data[] = [$packet_one['user_id'], 0, 0, 0, $packetPrice, 0, $packet_one['packet_id'], null, time()];
                        }

                        if ($packet_one['service_alias_name'] == "tv") {
                            $bonus_history_data[] = [$packet_one['user_id'], 0, 0, 0, $packetPrice, 1, $packet_one['packet_id'], null, time()];
                        }

                        if ($packet_one['service_alias_name'] == "wifi") {
                            $bonus_history_data[] = [$packet_one['user_id'], 0, 0, 0, $packetPrice, 2, $packet_one['packet_id'], null, time()];
                        }    

                        if ($packet_one['service_alias_name'] == "voip") {
                            $bonus_history_data[] = [$packet_one['user_id'], 0, 0, 0, $packetPrice, 4, $packet_one['packet_id'], null, time()];
                        } 
                    }
                }
 
                $sumArrayBonus = [];
                foreach ($user_bonus_data as $data_bonus) {
                    if (!isset($sumArrayBonus[$data_bonus['user_id']])) {
                        $sumArrayBonus[$data_bonus['user_id']] = $data_bonus;
                    } else {
                        $sumArrayBonus[$data_bonus['user_id']]['user_packet_price'] += $data_bonus['user_packet_price'];
                    }
                }
 
                ## her istifadecinin bonus balansinda paketinin qiymeti qeder cixilma etmek
                if ( count( $bonus_history_data ) > 0 ) {
                    Yii::$app->db->createCommand()->batchInsert('user_balance', ['user_id', 'balance_in', 'balance_out', 'bonus_in', 'bonus_out', 'pay_for', 'service_packet_id', 'payment_method', 'created_at'], $bonus_history_data)->execute();
                }

                ### bonus update all
                $sumArrayBonus = array_values($sumArrayBonus);
                if ( count( $sumArrayBonus ) > 0 ) {
                    $data_bonus = [];
                    foreach ( $sumArrayBonus as $param_bonus ) {
                        $data_bonus[] = "('" . $param_bonus['user_id'] . "','" . ($param_bonus['user_bonus'] - $param_bonus['user_packet_price']) . "')";
                    }
                    $str_bonus = implode(",", $data_bonus);
                    $sql_bonus = 'insert into users (id, bonus) values ';
                    $sql_bonus .= $str_bonus . ' ON DUPLICATE KEY UPDATE bonus = VALUES(bonus)';
                    $insertCount = Yii::$app->db->createCommand( $sql_bonus )->execute();
                }


                if ( count( $highBonusUsers ) > 0 ) {
                  ## update all highBonusUsersPaidConnectedDay update time
                    $highBonusUsersSummary = array_values( $highBonusUsers );
                    if ( count($highBonusUsersSummary ) > 0) {
                        $highBonusUsersData = [];
                        foreach ( $highBonusUsersSummary as $highBonusUsersParam ) {
                            $highBonusUsersData[] = "('" . $highBonusUsersParam['user_id'] . "','" . $highBonusUsersParam['nextUpdateTime'] . "')";
                        }
                        $strhighBonusUsers = implode(",", $highBonusUsersData);

                        $sqlhighBonusUsers = 'insert into users (id, updated_at) values ';
                        $sqlhighBonusUsers .= $strhighBonusUsers . ' ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at)';
                        $insertCount = Yii::$app->db->createCommand($sqlhighBonusUsers)->execute();
                    }
                   
                    if ( count( $highBonusUsersId ) > 0 ) {
                        \app\models\Users::updateAll(['last_payment' => time()], ['id' => array_unique( $highBonusUsersId )]);
                    }
                    
                }

                if (count($low_balance_users) > 0) {
                    /*multipe update user status*/
                    \app\models\Users::updateAll(['status' => 2], ['id' => array_unique($low_balance_users)]);
                    /*multipe update user_services_packet  status*/
                    \app\models\UsersServicesPackets::updateAll(['status' => 2], ['user_id' => $low_balance_users]);
                }

                if (count($low_balance_users_internet) > 0) {
                    /*multipe update user_inet status*/
                    \app\models\UsersInet::updateAll(['status' => 2], ['user_id' => $low_balance_users_internet]);
                }

                if (count($low_balance_users_tv) > 0) {
                    /*multipe update user_tv status*/
                    \app\models\UsersTv::updateAll(['status' => 2], ['user_id' => $low_balance_users_tv]);
                }

                if (count($low_balance_users_wifi) > 0) {
                    /*multipe update wifi status*/
                    \app\models\UsersWifi::updateAll(['status' => 2], ['user_id' => $low_balance_users_wifi]);
                }     

                if (count($low_balance_users_voip) > 0) {
                    /*multipe update wifi status*/
                    \app\models\UsersVoip::updateAll(['status' => 2], ['user_id' => $low_balance_users_voip]);
                } 
            }

            // End clock time in seconds
            $end_time = microtime(true);
            // Calculate script execution time
            $execution_time = ($end_time - $start_time);
            $this->WriteExLog('check-balance', $start_time, $end_time, $execution_time);
        } else {
            echo "IP error";
        }
    }

    // checked
    private function actionCheckCredit( $users ){
        // Starting clock time in seconds
        $start_time = microtime(true);
        $credit_history_data = [];
        $payment_credit_history_data = [];

        $creditItems = \app\models\ItemUsage::find()
            ->select('item_usage.*,users.balance as user_balance,users.tariff as user_tariff,users.status as user_status,((item_usage.quantity * item_stock.price)/item_usage.month) as credit_tariff')
            ->leftJoin('item_stock','item_stock.id=item_usage.item_stock_id')
            ->leftJoin('users', 'users.id=item_usage.user_id')
            ->where(['credit' => '1'])
            ->andWhere(['users.id' => $users])
            ->asArray()
            ->all();

        if ( $creditItems != null ) {
            $user_data = [];
            foreach ( $creditItems as $key => $creditItem ) {
                if ( $creditItem['user_status'] == 1 ) {
                    if ( $creditItem['user_balance'] >= intval( ceil( $creditItem['credit_tariff'] ) ) ) {
                        $credit_history_data[] = [$creditItem['user_id'], $creditItem['id'], intval(ceil($creditItem['credit_tariff'])), time()];
                        $payment_credit_history_data[] = [$creditItem['user_id'], 0, intval(ceil($creditItem['credit_tariff'])), 3, null, time(), $creditItem['id']];
                        $user_data[$key]['user_id'] = $creditItem['user_id'];
                        $user_data[$key]['credit_tariff'] = intval(ceil($creditItem['credit_tariff']));
                        $user_data[$key]['user_balance'] = $creditItem['user_balance'];
                    }
                }
            }

            $sumArray = [];
            foreach ( $user_data as $data ) {
                if ( !isset( $sumArray[$data['user_id']] ) ) {
                    $sumArray[$data['user_id']] = $data;
                } else {
                    $sumArray[$data['user_id']]['credit_tariff'] += $data['credit_tariff'];
                }
            }

            $sumArray = array_values($sumArray);
            if (count($sumArray) > 0) {
                $data = [];
                foreach ($sumArray as $param) {
                    $data[] = "('" . $param['user_id'] . "','" . ($param['user_balance'] - $param['credit_tariff']) . "')";
                }
                $str = implode(",", $data);
                $sql = 'insert into users (id, balance) values ';
                $sql .= $str . ' ON DUPLICATE KEY UPDATE balance = VALUES(balance)';
                $insertCount = Yii::$app->db->createCommand($sql)->execute();
            }

            if ( count( $payment_credit_history_data ) > 0 && count( $credit_history_data ) > 0) {
                $credit_paids = Yii::$app->db->createCommand()->batchInsert('user_balance', ['user_id', 'balance_in', 'balance_out', 'pay_for', 'payment_method', 'created_at', 'item_usage_id'], $payment_credit_history_data)->execute();
                $credit_history = Yii::$app->db->createCommand()->batchInsert('users_credit', ['user_id', 'item_usage_id', 'paid', 'paid_at'], $credit_history_data)->execute();

                $credits_payments = \app\models\UsersCredit::find()
                    ->select('users_credit.*,item_usage.credit as credit_status,item_usage.month as credit_month,item_usage.quantity as item_quantity,item_stock.price as stock_price,SUM(paid) as total_paid,users.tariff as user_tariff')
                    ->leftJoin('item_usage','users_credit.item_usage_id=item_usage.id')
                    ->leftJoin('item_stock','item_stock.id=item_usage.item_stock_id')
                    ->leftJoin('users', 'users_credit.user_id=users.id')
                    ->where(['item_usage.credit' => '1'])
                    ->groupBy('item_usage_id')
                    ->asArray()
                    ->all();
                $succesfull_credits = [];
                $credit_data_dupl = [];

                foreach ( $credits_payments as $key => $cp_one ) {
                    if ( $cp_one['total_paid'] >= $cp_one['stock_price'] * $cp_one['item_quantity'])  {
                        $succesfull_credits[] = $cp_one['item_usage_id'];
                        $credit_data_dupl[$key]['user_id'] = $cp_one['user_id'];
                        $credit_data_dupl[$key]['credit_tariff'] = ceil( ($cp_one['stock_price'] * $cp_one['item_quantity']) / $cp_one['credit_month'] );
                        $credit_data_dupl[$key]['user_tariff'] = $cp_one['user_tariff'];
                    }
                }

                if ( count( $credit_data_dupl ) > 0 ) {
                    $credit_array_data = [];
                    foreach ( $credit_data_dupl as $key => $value ) {
                        if ( array_key_exists( $value["user_id"], $credit_array_data ) ) {
                            $credit_array_data[$value["user_id"]]["credit_tariff"] = $credit_array_data[$value["user_id"]]["credit_tariff"] + $value["credit_tariff"];
                        } else {
                            $credit_array_data[$value["user_id"]] = $value;
                        }
                    }
                    $credit_data = [];
                    foreach ( $credit_array_data as $key => $c_val ) {
                        $credit_data[$key]['user_id'] = $c_val['user_id'];
                        $credit_data[$key]['tariff'] = $c_val['user_tariff'] - $c_val['credit_tariff'];
                    }

                    $data_ = [];
                    foreach ($credit_data as $credit_val) {
                        $data_[] = "('" . $credit_val['user_id'] . "','" . $credit_val['tariff'] . "')";
                    }
                    $str = implode(",", $data_);
                    $sql = 'insert into users (id, tariff) values ';
                    $sql .= $str . ' ON DUPLICATE KEY UPDATE tariff = VALUES(tariff)';
                    $insertCount = Yii::$app->db->createCommand($sql)->execute();
                }

                if ( count( $succesfull_credits ) > 0 ) {
                    \app\models\ItemUsage::updateAll(['credit' => 0], ['id' => $succesfull_credits]);
                }
                //

            }
        }

        // End clock time in seconds
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        // Calculate script execution time
        $this->WriteExLog('CheckCredit', $start_time, $end_time, $execution_time);
    }

    // checked
    private function actionCheckGifts( $users ){
        // Starting clock time in seconds
        $start_time = microtime(true);
        $gift_history_data = [];

        $giftItems = \app\models\ItemUsage::find()
        ->select('item_usage.*,users.status as user_status')
        ->leftJoin('users', 'users.id=item_usage.user_id')
        ->where(['credit' => '2'])
        ->andWhere(['users.id' => $users])
        ->asArray()
        ->all();
        foreach ( $giftItems as $giftKey => $giftItem ) {
            if ( $giftItem['user_status'] == 1 ) {
                $gift_history_data[] = [$giftItem['user_id'], $giftItem['id'], time()];
            }
        }
        if ( count( $gift_history_data ) > 0 ) {
            $gift_history = Yii::$app->db->createCommand()->batchInsert('users_gifts', ['user_id', 'item_usage_id', 'created_at'], $gift_history_data)->execute();
            $all_gifts_history = \app\models\UsersGifts::find()
                ->select('users_gifts.*,item_usage.credit as credit_status,item_usage.month as gift_month,COUNT(item_usage_id) as total_count')
                ->leftJoin('item_usage', 'users_gifts.item_usage_id=item_usage.id')
                ->leftJoin('users', 'users_gifts.user_id=users.id')
                ->where(['item_usage.credit' => '2'])
                ->groupBy('item_usage_id')
                ->asArray()
                ->all();

            $succesfullyGifts = [];
            foreach ( $all_gifts_history as $giftHistoryKey => $gift ) {
                if ( $gift['total_count'] >= $gift['gift_month'] ) {
                    $succesfullyGifts[] = $gift['item_usage_id'];
                }
            }

            if ( count( $succesfullyGifts ) > 0 ) {
                \app\models\ItemUsage::updateAll(['credit' => '3'], ['id' => $succesfullyGifts]);
            }

        }
        // End clock time in seconds
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        $this->WriteExLog('checkGifts', $start_time, $end_time, $execution_time);
    }

    public function actionCheckServiceCredit(){
        if( $_SERVER["SERVER_ADDR"] == '127.0.0.1' && $this->getSiteConfig()['check_service_credit'] == "1" ){
          // Starting clock time in seconds
          $start_time = microtime(true);
          $all_packets = \app\models\UsersServicesPackets::find()
          ->select('users_services_packets.*,services.service_alias as service_alias_name,users.status as users_status,users.balance as user_balance,users.tariff as user_tariff,users.id as user_t_id,users_inet.login as user_inet_login,users_inet.password as user_inet_password,service_packets.packet_price as user_packet_price,address_district.router_id as user_router_id,routers.nas as nas,routers.username as router_username,routers.password as router_password')
          ->leftJoin('services','services.id=users_services_packets.service_id')
          ->leftJoin('users','users.id=users_services_packets.user_id')
          ->leftJoin('address_district','address_district.id=users.district_id')
          ->leftJoin('routers','routers.id=address_district.router_id')
          ->leftJoin('users_inet','users_inet.u_s_p_i=users_services_packets.id')
          ->leftJoin('users_tv','users_tv.u_s_p_i=users_services_packets.id')
          ->leftJoin('users_wifi','users_wifi.u_s_p_i=users_services_packets.id')
          ->leftJoin('service_packets','service_packets.id=users_services_packets.packet_id')
          ->where(['users.credit_status'=>'1'])
          ->andWhere(['<', 'users.credit_time', time()])
          ->asArray()
          ->all();

          $user_service_credit = [];
          foreach ($all_packets as $packet_one_key => $packet_one) {
            $user_service_credit[] = $packet_one['user_id'];
            if ($packet_one['service_alias_name'] == "internet") {

                \app\components\MikrotikQueries::dhcpBlockMac(
                    $packet_one['user_inet_login'], 
                    $packet_one['nas'], 
                    $packet_one['router_username'], 
                    $packet_one['router_password'],
                    "dhcpBlockMac",
                    [
                        'login'=>$packet_one['user_inet_login'],
                        'nas'=> $packet_one['nas'],
                        'router_username'=>$packet_one['router_username'],
                        'router_password'=>$packet_one['router_password'],
                    ]
                );
               
            }
            if ($packet_one['service_alias_name'] == "tv") {
              //tv api
            }
            if ($packet_one['service_alias_name'] == "wifi") {
              //wifi api
            }
          }
          if (count($all_packets) > 0) {
            \app\models\Users::updateAll(['credit_status' => '0'], ['id'=>array_unique($user_service_credit)]);
            \app\models\UsersServicesPackets::updateAll(['status' => 2], ['user_id'=>array_unique($user_service_credit)]);
            \app\models\UsersInet::updateAll(['status' => 2], ['user_id'=>array_unique($user_service_credit)]);
            \app\models\UsersTv::updateAll(['status' => 2], ['user_id'=>array_unique($user_service_credit)]);
            \app\models\UsersWifi::updateAll(['status' => 2], ['user_id'=>array_unique($user_service_credit)]);
          }
          // End clock time in seconds
          $end_time = microtime(true);
          // Calculate script execution time
          $execution_time = ($end_time - $start_time);
          $this->WriteExLog('CheckServiceCredit',$start_time,$end_time,$execution_time );
        }else{
          echo " IP  error ";
        }
    }

    public function actionCalcTotal (){
        if( $_SERVER["SERVER_ADDR"] == '127.0.0.1'){
            $lastTotalModel = \app\models\TotalProfit::find()
            ->orderBy(['created_at'=>SORT_DESC])
            ->asArray()
            ->one();

            if ( $lastTotalModel != null ) {
   
                $lastTotal =  $lastTotalModel['amount'];
                $yesterday = date('Y-m-d',strtotime("-1 days"));

                $yesterdayBalances = \app\models\UserBalance::find()
                    ->leftJoin('users','users.id=user_balance.user_id')
                    ->andWhere(['!=', 'user_balance.status', '1'])
                    ->andWhere(['!=', 'user_balance.balance_in', '0'])
                    ->withByLocation()
                    ->andWhere(['DATE_FORMAT(FROM_UNIXTIME(user_balance.created_at), "%Y-%m-%d")' => $yesterday ])
                    ->asArray()
                    ->all();

                    $yesterdayBalanceTotal = 0;
                    foreach ( $yesterdayBalances as $key => $yesterdayBalance ) {
                       $yesterdayBalanceTotal += $yesterdayBalance['balance_in'];
                    }

                    $total = $yesterdayBalanceTotal + $lastTotal;

                    $totalProfit = new \app\models\TotalProfit;
                    $totalProfit->amount = $total;
                    $totalProfit->created_at = time();
                    $totalProfit->save(false);

            }else{
                $user_balace_model = \app\models\UserBalance::find()
                ->leftJoin('users','users.id=user_balance.user_id')
                ->withByLocation()
                ->asArray()
                ->all();

                $total_balace = 0;
                foreach ($user_balace_model as $user_key => $user_balance) {
                    $total_balace += $user_balance['balance_in'];
                }
                $lastTotal = $total_balace;

                $totalProfit = new \app\models\TotalProfit;
                $totalProfit->amount = $lastTotal;
                $totalProfit->created_at = time();
                $totalProfit->save(false);

            }
        }
    }

    public function actionFailProcessCron(){
        if( $_SERVER["SERVER_ADDR"] == '127.0.0.1'  ){
            $pendingFailedProcess = \app\models\FailProcess::find()
            ->where(['status'=>'0'])
            ->asArray()
            ->all();
            
            foreach ($pendingFailedProcess as $key => $fp) {
                $paramsArray = unserialize($fp['params']);
                $allParam = array_merge(array_values($paramsArray),[$fp['action']]);
                if ( call_user_func_array( ['\app\components\MikrotikQueries',$fp['action']], [...$allParam, $paramsArray, false] ) == true ){
                    $model = \app\models\FailProcess::find()
                    ->where(['id'=>$fp['id']])
                    ->one();
                    $model->status = '1';
                    $model->save(false);
                }
            }
        }
    }

}

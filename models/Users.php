<?php

namespace app\models;

use Yii;
use app\models\Logs;
use app\components\DefaultActiveRecord;
use app\models\UsersHistory;
use webvimark\modules\UserManagement\models\User;
use borales\extensions\phoneInput\PhoneInputValidator;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property int $request_id
 * @property string $fullname
 * @property string $company
 * @property string $phone
 * @property string $email
 * @property int $city_id
 * @property int $district_id
 * @property string $location
 * @property string $room
 * @property int $tariff
 * @property int $balance
 * @property int $status
 * @property string $updated_at
 *
 * @property UserInternet[] $userInternets
 * @property UserServices[] $userServices
 * @property UserWifi[] $userWifis
 * @property Cities $city
 * @property District $district
 * @property UsersServicesPackets[] $usersServicesPackets
 * @property UsersTv[] $usersTvs
 */
class Users extends DefaultActiveRecord
{


    public $personal;
    public $services_id;
    public $lang;
    public $type;
    public $updatedAt;
    public $archive_reason;

    const SERVICE = 'service_edit';
    const INTEGRATE_USER = 'integrate_user';
    const CONTRACT_UPDATE = 'contract_update';
    const PAID_UPDATE = 'paid_update';
    const CORDINATE_UPDATE = 'cordinate_update';
    const PHONES_UPDATE = 'phones_update';
    const SCENARIO_SEND_CONTRACT_NUMBER = 'send_contract_number';
    const SCENARIO_SEND_ARCHIVE = 'send_archive';

    public static function tableName()
    {
        return 'users';
    }


    public static function getCreditStatus()
    {
        return [
            '0'=>Yii::t('app','Deactive'),
            '1'=>Yii::t('app','Active')
        ];
    }


    public static function getStatus()
    {
        return [
            1=>Yii::t('app','Active'),
            2=>Yii::t('app','Deactive'),
            3=>Yii::t('app','Archive'),
            6=>Yii::t('app','Black list'),
            7=>Yii::t('app','VIP'),
        ];
    }

    public static function getArchiveReason()
    {
        return [
            ''=>Yii::t('app','Select'),
            0=>Yii::t('app','Your services is not good'),
            1=>Yii::t('app','Other provider'),
            2=>Yii::t('app','Home was selled'),
            3=>Yii::t('app','Other'),
        ];
    }

    public static function getCurrency()
    {
        $siteConfig = \app\models\SiteConfig::find()
        ->asArray()
        ->one();
        return $siteConfig['currency'];
    }    

   


    public function rules()
    {
        return [
            [[ 'fullname', 'phone', 'city_id', 'district_id', 'location_id', 'room'], 'required'],
            [['city_id', 'district_id', 'tariff', 'status','location_id','second_status','credit_time','archive_reason'], 'integer'],
            [['selected_services'], 'required' , 'on'=>'service_edit'],
            [['district_id','location_id'], 'required' , 'on'=>'integrate_user'],
            [['archive_reason'], 'required' , 'on'=>'send_archive'],
            [['contract_number'], 'required' , 'on'=>'contract_update'],
            [ ['phone','extra_phone'], PhoneInputValidator::className(), 'region' => ['AZ','RU','TR','US'], 'message' => Yii::t( 'app', 'The format of the phone is invalid or system not supported your country.' ) ,'on'=>'phones_update'],
            [['phone'], 'required' , 'on'=>'phones_update'],
            [['phone', 'extra_phone'], 'validateUniqePhoneOnUpdate', 'on'=>'phones_update'],
            [['lang','phone','type'], 'required' , 'on'=>'send_contract_number'],
            [['cordinate'], 'required' , 'on'=>'cordinate_update'],
            [['paid_time_type','updatedAt'], 'required' , 'on'=>'paid_update'],
            [['cordinate'], 'validateGPSCordinate' , 'on'=>'cordinate_update'],
            [['contract_number'], 'unique'],
            ['balance', 'double'],
            [['email'], 'string'],
            [['fullname', 'company', 'room'], 'string', 'max' => 120],
            [['message_lang'], 'string', 'max' => 20],
            ['email','email'],
            [['updated_at','created_at','cordinate'], 'string', 'max' => 255],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::className(), 'targetAttribute' => ['district_id' => 'id']],
        ];
    }




    public function validateGPSCordinate( $attribute, $params, $validator )
    {
        if ( $this->cordinate )
        {
            $cordinate = str_contains( $this->cordinate, ',' );
        
            if ( !$cordinate )
            {
                $validator->addError($this, $attribute, Yii::t('app', 'Inccorect gps format'));
                return false;
            }

            $latitude = explode( ",", $this->cordinate)[0];
            $longitude = explode( ",", $this->cordinate)[1];

            if ( $latitude > 90 ) {
               $validator->addError($this, $attribute, Yii::t('app', 'Latitude cannot be greater than 90'));
            }


            if ( $latitude < -90 ) {
                $validator->addError($this, $attribute, Yii::t('app', 'Latitude cannot be less than -90'));
            }

            if ( $longitude > 180 ) {
               $validator->addError($this, $attribute, Yii::t('app', 'Longitude cannot be greater than 180'));
            }


            if ( $longitude < -180 ) {
                $validator->addError($this, $attribute, Yii::t('app', 'Longitude cannot be less than -180'));
            }


        }
    }


    public function validateUniqePhoneOnUpdate($attribute, $params, $validator)
    {
         $phoneCheck = \app\models\Users::find()
         ->orWhere(['phone'=>$this->phone ])
         ->orWhere(['phone'=>$this->extra_phone ])
         ->orWhere(['extra_phone'=>$this->extra_phone])
         ->orWhere(['extra_phone'=>$this->phone])
         ->andWhere(['!=','id',$this->id])
         ->andWhere(['not', ['extra_phone' => ""]])
         ->andWhere(['not', ['extra_phone' => null]])
         ->asArray()
         ->one();

        if ( $phoneCheck != null) {
            $this->addError($attribute, Yii::t('app','Phone number already exist, please use another'));
        }
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SERVICE] = ['selected_services'];
        $scenarios[self::INTEGRATE_USER] = ['district_id','location_id'];
        $scenarios[self::CONTRACT_UPDATE] = ['contract_number'];
        $scenarios[self::CORDINATE_UPDATE] = ['cordinate'];
        $scenarios[self::PAID_UPDATE] = ['paid_time_type','updatedAt'];
        $scenarios[self::SCENARIO_SEND_CONTRACT_NUMBER] = ['lang','phone','type'];
        $scenarios[self::PHONES_UPDATE] = ['phone','extra_phone'];
        $scenarios[self::SCENARIO_SEND_ARCHIVE] = ['archive_reason'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'archive_reason' => Yii::t('app','Archive reason'),
            'fullname' => Yii::t('app','Customer'),
            'company' => Yii::t('app','Company'),
            'phone' => Yii::t('app','Phone'),
            'extra_phone' => Yii::t('app','Extra phone'),
            'email' => Yii::t('app','E-mail'),
            'cordinate' => Yii::t('app','Cordinate'),
            'selected_services' => Yii::t('app','Service'),
            'city_id' => Yii::t('app','City'),
            'district_id' => Yii::t('app','District'),
            'location_id' => Yii::t('app','Location'),
            'room' => Yii::t('app','Room'),
            'Contract Number'=>Yii::t('app','Contract number'),
            'personal'=>Yii::t('app','At connection'),
            'tariff' => Yii::t('app','Tariff'),
            'balance' => Yii::t('app','Balance'),
            'second_status' => Yii::t('app','Request status'),
            'status' => Yii::t('app','Status'),
            'updated_at' => Yii::t('app','Updated at'),
            'created'=> Yii::t('app','Created at'),
            'credit_time'=> Yii::t('app','Credit time'),
            'credit_status'=> Yii::t('app','Credit status'),
            'message_lang'=> Yii::t('app','Message language'),
            'paid_time_type'=> Yii::t('app','Paid type'),
            'paid_day'=> Yii::t('app','Paid day'),
            'type'=> Yii::t('app','Type'),
            'updatedAt'=> Yii::t('app','Renewal date'),
        ];
    }

    public function getUserBalances(){
        return $this->hasMany(UserBalance::className(), ['user_id' => 'id'])->orderBy(['time'=>SORT_DESC]);
    }
    public static function getAtConnections($user_id){
        $model = \app\models\PersonalUserActivty::find()
        ->select('members.fullname as member_fullaname')
        ->leftJoin('personal_activty','personal_activty.id=personal_user_activty.activty_id')
        ->leftJoin('members','members.id=personal_user_activty.member_id')
        ->where(['personal_activty.type'=>'0'])
        ->andWhere(['personal_activty.user_id'=>$user_id])
        ->asArray()
        ->all();
 
        $personals = '';
        foreach ($model as $key => $value) {
            $personals.=$value['member_fullaname'].",";
        }

        return  substr($personals, 0, -1);
    }



    public static function secondsToTime($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
    }



    public static function getGiveCredit($user_id, $time)
    {

        $model = \app\models\Users::find()
            ->where(['id' => $user_id])
            ->one();

        $current_year = date('Y');
        $current_month = date('m');
        if ($current_month == 12) {
            $current_year = date('Y') + 1;
        }

        $firstDayNextMonth = strtotime(date('m', strtotime('+1 month')) . '/01/' . $current_year . ' 00:01:00');
        $daysTilNextMonth = ($firstDayNextMonth - time());

        if ($model->status == '2') {
            if (date("m", $model->credit_time) != $current_month || $model->credit_time == null) {
                $model->credit_time = $time;
                $model->credit_status = '1';
                if ($model->save(false)) {
                    $user_services_packets = \app\models\UsersServicesPackets::find()->where(['user_id' => $user_id])->all();
                    foreach ($user_services_packets as $key => $packet) {
                        if ($packet->service->service_alias == "internet") {

                            $router_model = \app\models\Routers::find()->where(['id' => $model->district->router_id])->one();
                            $model_inet = \app\models\UsersInet::find()->where(['user_id' => $model->id, 'u_s_p_i' => $packet->id])->one();
                            $model_inet->status = 1;
                            if ($model_inet->save(false)) {
                                \app\models\UsersInet::turnOnInternetAccessPppo($model_inet->login, $router_model['nas'], $router_model['username'], $router_model['password']);
                            }
                        }
                        if ($packet->service->service_alias == "tv") {
                            \app\models\UsersTv::turnOnTvAccess($model->id, $packet->id);
                        }
                        if ($packet->service->service_alias == "wifi") {
                            \app\models\UsersWifi::turnOnWifiAccess($model->id, $packet->id);
                        }
                        $packet->status = 1;
                        $packet->save(false);
                    }

                    $userHistoryText = "Added credit";
                    UsersHistory::AddHistory( 
                        intval($model->id),
                        Yii::$app->user->username, 
                        $userHistoryText, 
                        time()
                    );

                    $logText = "Added credit";
                    Logs::writeLog(
                        Yii::$app->user->username, 
                        intval($model->id), 
                        $logText, 
                        time()
                    );

                    return ["status" => "success", "message" => Yii::t('app', 'User\'s credit activated for 3 days')];
                }
            } else {
                return [
                    "status" => "error", 
                    "message" => Yii::t("app", "Next credit at {daysTilNextMonth}",['daysTilNextMonth'=>Users::secondsToTime($daysTilNextMonth)])
                ];
            }
        } else {
            return ["status" => "error", "message" => Yii::t("app", 'User\'s status must be deactive')];
        }

    }



    public static  function calcMonthCount( $balanceIn, $tariff ) {
        $monthCount = floor( $balanceIn / $tariff);      
        return $monthCount;
    }



    public static function calcCreditsTariff($userId)
    {
        $creditItems = \app\models\ItemUsage::find()
            ->select('item_usage.*, item_stock.price as item_stock_price')
            ->leftJoin('item_stock', 'item_stock.id = item_usage.item_stock_id')
            ->where(['item_usage.user_id' => $userId, 'item_usage.credit' => '1', 'item_usage.status' => '6'])
            ->asArray()
            ->all();

        $totalCreditTariffs = 0;
        if (count($creditItems) > 0) {
            foreach ($creditItems as $key => $creditItem) {
                $totalCreditTariffs += round(ceil(($creditItem['item_stock_price'] * $creditItem['quantity']) / $creditItem['month']), 2);
            }
        }

        return $totalCreditTariffs;
    }



    public static function caclNextUpdateAtForUser( $userId, $tariff, $balanceIn, $moreParam = false ){
        $userModel = \app\models\Users::find()->where(['id'=>$userId])->one();
        $totalBalance = $userModel->balance + $balanceIn;
        $totalCreditTariffs = Users::calcCreditsTariff( $userModel->id );
        $currentUpdateAt = $userModel->updated_at;
        $monthCount =  Users::calcMonthCount( $totalBalance, $tariff );

        if( $userModel->status == 0  ){
           if( $userModel->paid_time_type == "1" ){
                $monthCountLaterTimestamp = \app\components\Utils::calculateNextPaymentTimestamp( 
                    $monthCount,
                    $userModel->status, 
                    $userModel->paid_time_type,
                    $userModel->paid_day,
                    $userModel->updated_at
                );

                if ( date('d') == 31 ) {
                    $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
                    if (  $monthCountLaterDay != 31 ) {
                      $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                      if ( date("d", $monthCountLaterTimestamp ) != 30 ) {
                          $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                          if ( date("d", $monthCountLaterTimestamp ) != 29 ){
                            $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                          }
                      }
                    }
                }
             
                if ( date('d') == 30 ) {
                    $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
                    if (  $monthCountLaterDay != 30 ) {
                      $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                      if ( date("d", $monthCountLaterTimestamp ) != 29 ) {
                          $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                      }
                    }
                }

                if ( date('d') == 29 ) {
                    $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
                    if (  $monthCountLaterDay != 29 ) {
                          $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                    }
                }
              
            return [
                'updateAt'=>$monthCountLaterTimestamp,
                'paidDay'=>date('d'),
                'monthCount'=>$monthCount,
                'removalAmount'=> ( $monthCount * $tariff ) - ( $monthCount * $totalCreditTariffs ),
            ];  
           }

           if( $userModel->paid_time_type == "0" ){
                 $monthCountLaterTimestamp = strtotime("+$monthCount month", $currentUpdateAt);
                if ( $moreParam && is_array( $moreParam ) && $totalBalance >= $moreParam['untilToMonthTariff']  ) {
                     if ( round( $moreParam['total_tariff'],2 ) > round( $moreParam['untilToMonthTariff'], 2 )  ) {
                          $leftBalance =   round( $totalBalance, 2) - round( $moreParam['untilToMonthTariff'], 2 );
                          $untilMonthPrice = round( $moreParam['untilToMonthTariff'], 2 );

                          if ( $leftBalance == 0 ) {
                            $monthCount = \app\components\Utils::monthCountAndRemaind( $leftBalance , $moreParam['total_tariff'] )['month']  + 1 ;
                            $removalAmount =    round( $moreParam['untilToMonthTariff'], 2 )   + ( ( $monthCount - 1 ) *  $moreParam['total_tariff'] ) - ( $monthCount * $totalCreditTariffs ) ;

                          }else{
                            $monthCount = \app\components\Utils::monthCountAndRemaind( $leftBalance , $moreParam['total_tariff'] )['month'] + 1;
                            $removalAmount =  round( $monthCount *  $moreParam['total_tariff'] , 2 )   + round( $moreParam['untilToMonthTariff'],2 )  -   (  round( $monthCount,2 )  * round( $totalCreditTariffs, 2 ) );
                          }
                     }
                     $monthCountLaterTimestamp = \app\components\Utils::calculateNextPaymentTimestamp( 
                        $monthCount, 
                        $userModel->status, 
                        $userModel->paid_time_type,
                        $userModel->paid_day,
                        $userModel->updated_at
                    );
                     $removalAmount = round( ( $moreParam['untilToMonthTariff'] ) + ( ( $monthCount - 1 ) *  $moreParam['total_tariff'] ) - ( $monthCount * $totalCreditTariffs ) ,2 );
                 }else{
                    $monthCount = $monthCount;
                    $removalAmount = round( ( $monthCount * $tariff ) - ( $monthCount * $totalCreditTariffs ) ,2 );
                 }
                   return [
                        'updateAt'=>$monthCountLaterTimestamp,
                        'monthCount'=>$monthCount,
                        'paidDay'=>"01",
                        'removalAmount'=> $removalAmount,
                    ];
           }
        }
        if ( $userModel->status == 1 ) {

            if( $userModel->paid_time_type == "1" ){

                if ( $userModel->status == 0 ) {
                     $updated_at = $userModel->created_at;
                }else{
                    $updated_at = $userModel->updated_at;
                }
               

                $monthCountLaterTimestamp = \app\components\Utils::calculateNextPaymentTimestamp( 
                    $monthCount,
                    $userModel->status, 
                    $userModel->paid_time_type,
                    $userModel->paid_day,
                    $updated_at,
                    // [
                    //     'updated_at'=>$updated_at,
                        
                    // ] 
                );

                if ( $userModel->paid_day == 31 ) {
                    $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
                    if (  $monthCountLaterDay != $userModel->paid_day ) {
                          $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                          if ( date("d", $monthCountLaterTimestamp ) != 30 ) {
                              $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                              if ( date("d", $monthCountLaterTimestamp ) != 29 ){
                                $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                              }
                          }
                    }
                }
             
                if ( $userModel->paid_day == 30 ) {
                    $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
                    if (  $monthCountLaterDay != $userModel->paid_day ) {
                          $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                          if ( date("d", $monthCountLaterTimestamp ) != 29 ) {
                              $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                          }
                    }
                }

                if ( $userModel->paid_day == 29 ) {
                    $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
                    if (  $monthCountLaterDay != $userModel->paid_day ) {
                          $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                    }
                }

            }

            if( $userModel->paid_time_type == "0" ){

                if ( $userModel->status == 0 ) {
                     $updated_at = $userModel->created_at;
                }else{
                    $updated_at = $userModel->updated_at;
                }
                
                $monthCountLaterTimestamp = \app\components\Utils::calculateNextPaymentTimestamp( 
                    $monthCount,
                    $userModel->status, 
                    $userModel->paid_time_type,
                    $userModel->paid_day,
                    $updated_at
                    // [
                    //     'updated_at'=>$updated_at,
           
                    // ] 
                );
            }

           return [
                'updateAt'=>$monthCountLaterTimestamp,
                'monthCount'=>$monthCount,
                'removalAmount'=> ( $monthCount * $tariff ) - ( $monthCount * $totalCreditTariffs ),
                'paidDay'=>$userModel->paid_day
            ];
        }
        if( $userModel->status == 2 || $userModel->status == 3  ){
           if( $userModel->paid_time_type == "1" ){
                $monthCountLaterTimestamp = \app\components\Utils::calculateNextPaymentTimestamp( 
                    $monthCount,
                    $userModel->status, 
                    $userModel->paid_time_type,
                    $userModel->paid_day,
                    $userModel->updated_at
                );

                if ( date('d') == 31 ) {
                    $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
                    if (  $monthCountLaterDay != 31 ) {
                      $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                      if ( date("d", $monthCountLaterTimestamp ) != 30 ) {
                          $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                          if ( date("d", $monthCountLaterTimestamp ) != 29 ){
                            $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                          }
                      }
                    }
                }
             
                if ( date('d') == 30 ) {
                    $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
                    if (  $monthCountLaterDay != 30 ) {
                      $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                      if ( date("d", $monthCountLaterTimestamp ) != 29 ) {
                          $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                      }
                    }
                }

                if ( date('d') == 29 ) {
                    $monthCountLaterDay = date('d',$monthCountLaterTimestamp);
                    if (  $monthCountLaterDay != 29 ) {
                          $monthCountLaterTimestamp  = strtotime('-1 day',$monthCountLaterTimestamp);
                    }
                }
              
            return [
                'updateAt'=>$monthCountLaterTimestamp,
                'paidDay'=>date('d'),
                'monthCount'=>$monthCount,
                'removalAmount'=> ( $monthCount * $tariff ) - ( $monthCount * $totalCreditTariffs ),
            ];  
           }

           if( $userModel->paid_time_type == "0" ){
                 $monthCountLaterTimestamp = strtotime("+$monthCount month", $currentUpdateAt);
                if ( $moreParam && is_array( $moreParam ) && $totalBalance >= $moreParam['untilToMonthTariff']  ) {
                     if ( round( $moreParam['total_tariff'],2 ) > round( $moreParam['untilToMonthTariff'], 2 )  ) {
                          $leftBalance =   round( $totalBalance, 2) - round( $moreParam['untilToMonthTariff'], 2 );
                          $untilMonthPrice = round( $moreParam['untilToMonthTariff'], 2 );

                          if ( $leftBalance == 0 ) {
                            $monthCount = \app\components\Utils::monthCountAndRemaind( $leftBalance , $moreParam['total_tariff'] )['month']  + 1 ;
                            $removalAmount =    round( $moreParam['untilToMonthTariff'], 2 )   + ( ( $monthCount - 1 ) *  $moreParam['total_tariff'] ) - ( $monthCount * $totalCreditTariffs ) ;

                          }else{
                            $monthCount = \app\components\Utils::monthCountAndRemaind( $leftBalance , $moreParam['total_tariff'] )['month'] + 1;
                            $removalAmount =  round( $monthCount *  $moreParam['total_tariff'] , 2 )   + round( $moreParam['untilToMonthTariff'],2 )  -   (  round( $monthCount,2 )  * round( $totalCreditTariffs, 2 ) );
                          }
                     }
                     $monthCountLaterTimestamp = \app\components\Utils::calculateNextPaymentTimestamp( 
                        $monthCount, 
                        $userModel->status, 
                        $userModel->paid_time_type,
                        $userModel->paid_day,
                        $userModel->updated_at
                    );
                     $removalAmount = round( ( $moreParam['untilToMonthTariff'] ) + ( ( $monthCount - 1 ) *  $moreParam['total_tariff'] ) - ( $monthCount * $totalCreditTariffs ) ,2 );
                 }else{
                    $monthCount = $monthCount;
                    $removalAmount = round( ( $monthCount * $tariff ) - ( $monthCount * $totalCreditTariffs ) ,2 );
                 }
                   return [
                        'updateAt'=>$monthCountLaterTimestamp,
                        'monthCount'=>$monthCount,
                        'paidDay'=>"01",
                        'removalAmount'=> $removalAmount,
                    ];
           }
        }
    }

    public function getUserDamages()
    {
        return $this->hasMany(UserDamages::className(), ['user_id' => 'id']);
    }

 

 
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }



    public function getUserPhotos()
    {
        return $this->hasMany(UserPhotos::className(), ['user_id' => 'id']);
    }


    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'district_id']);
    }
    public function getLocations()
    {
        return $this->hasOne(Locations::className(), ['id' => 'location_id']);
    }
  
    public function getUsersInets()
    {
        return $this->hasMany(UsersInet::className(), ['user_id' => 'id']);
    }

    public function getUsersServicesPackets()
    {
        return $this->hasMany(UsersServicesPackets::className(), ['user_id' => 'id']);
    }

    public function getUsersSevices(){
        return $this->hasMany(UsersSevices::className(), ['user_id' => 'id']);
    }

    public function getServiceOne()
    {
        return $this->hasMany(Services::className(), ['id' => 'service_id'])->viaTable('{{%users_sevices%}}', ['user_id' => 'id']);
    }

    public function getUsersTvs()
    {
        return $this->hasMany(UsersTv::className(), ['user_id' => 'id']);
    }


    public function getUsersWifis()
    {
        return $this->hasMany(UsersWifi::className(), ['user_id' => 'id']);
    }


    public static function AddGift( $userId, $itemUsageId ){
        $model = new \app\models\UsersGifts;
        $model->user_id = $userId;
        $model->item_usage_id = $itemUsageId;
        $model->created_at = time();
        $model->save(false);
        if ($model->save(false)) {
            \app\models\ItemUsage::CheckGifts( $userId, $itemUsageId );
        }
    }


    public static function getMonthlyGainPercent(){
        $date = date('Y-m');
        $maxDays=date('t');
        $first_m_d =  $date."-01";
        $last_m_d =  $date."-".$maxDays;
        $total_balance= 0;
        $total_tariff = 0;

        $userBalanceModel = \app\models\UserBalance::find()->where(['!=', 'balance_in', '0'])
        ->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(`created_at`), '%Y-%m-%d')", $first_m_d],['<=', "DATE_FORMAT(FROM_UNIXTIME(`created_at`), '%Y-%m-%d')",$last_m_d]])
        ->all();
        $user_model = \app\models\Users::find()
        ->where(['status'=>1])
        ->withByLocation()
        ->asArray()
        ->all();

            
         foreach ($user_model as $key => $user_one) {
            $total_tariff+=$user_one['tariff'];
         }
         foreach ($userBalanceModel as $key => $value) {
            $total_balance+=$value['balance_in'];
         }

          if ($total_tariff == 0) {
              $percent_m = 0;
          }else{
              $percent_m = ($total_balance/$total_tariff)*100;
          }
          $result = round($percent_m,2);       

          return ['result'=>$result,'total_balance'=>$total_balance];
    }


    public static function getNewUsers(){
        $date = date('Y-m');
        $maxDay = date('t');
        $first_m_d =  $date."-01";
        $last_m_d =  $date."-".$maxDay;
        $news_users_count = 0;

        $news_users = \app\models\Users::find()
        ->where(['users.status'=>1])
        ->andFilterWhere(['and',['>=', "DATE_FORMAT(FROM_UNIXTIME(`created_at`), '%Y-%m-%d')", $first_m_d],['<=', "DATE_FORMAT(FROM_UNIXTIME(`created_at`), '%Y-%m-%d')",$last_m_d]])
        ->withByLocation()
        ->asArray()
        ->all();
         foreach ($news_users as $key => $value) {
            $news_users_count++;
         }     
        return $news_users_count;
    }

   public function getUsersCredits()
    {
        return $this->hasMany(UsersCredit::className(), ['user_id' => 'id']);
    }

    public function getItemCount()
    {
        return $this->hasOne(StoreItemCount::className(), ['id' => 'item_count_id']);
    }
    

    public static function CalcUserTariff($user_id){
        $userModel = \app\models\Users::find()
        ->where(['users.id'=>$user_id])
        ->asArray()
        ->one();

         if ($userModel != null ) {
               $user_packets = \app\models\UsersServicesPackets::find()
               ->select('users_services_packets.*,service_packets.packet_price as price_of_packet')
               ->leftJoin('service_packets','service_packets.id=users_services_packets.packet_id')
               ->where(['user_id'=>$userModel['id']])
               ->asArray()
               ->all();

               $total = 0;
               $packetPrice = 0;

               foreach ($user_packets as $key_p => $user_packet) {
                    if( $user_packet['price'] != null || $user_packet['price'] != 0 ){
                         $packetPrice += $user_packet['price'];
                    }else{
                         $packetPrice += $user_packet['price_of_packet'];
                    }
                }
           }
           $creditModel = \app\models\ItemUsage::find()
           ->select('item_usage.*,item_stock.price as item_price')
           ->leftJoin('item_stock','item_stock.id=item_usage.item_stock_id')
           ->where(['user_id'=>$userModel['id'],'credit'=>'1'])
           ->asArray()
           ->all();

            $creditPrice = 0;
           if ( count( $creditModel ) > 0 ) {
               foreach ( $creditModel as $creditKey => $credit ) {
                $creditPrice += intval( ceil( ( $credit['quantity'] * $credit['item_price'] ) / $credit['month'] ) );
               }
           }
        $total = $creditPrice + $packetPrice;
        return $total;
    }

    public static function CalcNotify(){
      $permittedQuery = \app\models\Users::find()
      ->select('users.*,users_services_packets.status')
      ->leftJoin("users_services_packets","users.id=users_services_packets.user_id")
      ->orderBy(['users.id'=>SORT_DESC])
      ->where(['users.status'=>2])
      ->andWhere(['users_services_packets.status'=>1])
      ->withByLocation();

      $permitted = $permittedQuery->limit(10)->all();
      $permittedCount = $permittedQuery->count();
      
      $contractNumberQuery = \app\models\Users::find()
      ->where(['contract_number'=>null])
      ->withByLocation();

      $contractNumber = $contractNumberQuery->limit(10)->all();
      $contractNumberCount = $contractNumberQuery->count();


      $creditQuery =  \app\models\ItemUsage::find()
      ->select('item_usage.*,users.fullname as usern_name,users.status as user_status,items.name as item_name')
      ->leftJoin('users','users.id=item_usage.user_id')
      ->leftJoin('items','items.id=item_usage.item_id')
      ->where(['item_usage.credit'=>'1','item_usage.status'=>6,'users.status'=>3])
      ->withByLocation();

      $creditModel = $creditQuery->limit(10)->all();
      $creditCount = $creditQuery->count();


      $giftQuery =  \app\models\ItemUsage::find()
      ->select('item_usage.*,users.fullname as usern_name,users.status as user_status,items.name as item_name')
      ->leftJoin('users','users.id=item_usage.user_id')
      ->leftJoin('items','items.id=item_usage.item_id')
      ->where(['item_usage.credit'=>'2','item_usage.status'=>4,'users.status'=>3])
      ->withByLocation();

      $giftModel = $giftQuery->limit(10)->all();
      $giftCount = $giftQuery->count();



      $failProcessQuery = \app\models\FailProcess::find()
      ->select('fail_process.*,members.fullname as member_fullaname')
      ->leftJoin('members','members.id=fail_process.member_id')
      ->where(['fail_process.status'=>'0']);

      $failProcessCount = $failProcessQuery->count();


      $notf_count = $permittedCount + $contractNumberCount + $creditCount + $giftCount ;

      $result = [
        'permitted'=>$permitted,
        'contractNumber'=>$contractNumber,
        'creditModel'=>[],
        'giftModel'=>[],
        'notf_count'=>$notf_count,
        'permittedCount'=>$permittedCount,
        'contractNumberCount'=>$contractNumberCount,
        'creditCount'=>$creditCount,
        'giftCount'=>$giftCount,
        'failProcessCount'=>$failProcessCount
      ];

      return $result;
    }
}

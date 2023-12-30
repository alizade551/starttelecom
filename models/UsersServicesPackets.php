<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;

/**
 * This is the model class for table "users_services_packets".
 *
 * @property int $id
 * @property int $user_id
 * @property int $service_id
 * @property int $packet_id
 *
 * @property Services $service
 * @property Packets $packet
 * @property Users $user
 */
class UsersServicesPackets extends DefaultActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public $packet_tags;
    public $property;
    public $static_ip_address;
    public $port_type;
    public $devices;
    public $box;
    public $switch_port;
    public $box_port;

    public $phone_number;
    public $u_s_p_i;


    public $mac_address;
    public $packet_login;
    public $packet_password;


    const ADDING_PACKET = 'adding_packet';
    const CHANGE_PACKET = 'change_packet';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::CHANGE_PACKET] = ['service_id','user_id','packet_id','static_ip_address','price','phone_number','u_s_p_i'];

        return $scenarios;
    }

    public static function tableName()
    {
        return 'users_services_packets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        
        return [
            [['user_id', 'service_id', 'packet_tags'], 'required'],
            [['service_id','user_id','packet_id'], 'required', 'on' => self::CHANGE_PACKET],
            [['user_id', 'service_id', 'packet_id','status','switch_port','phone_number','u_s_p_i'], 'integer'],
            ['price', 'double'],
            [['created_at','static_ip_address','port_type','packet_login','packet_password'], 'string', 'max' => 255],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['packet_id'], 'exist', 'skipOnError' => true, 'targetClass' => Packets::className(), 'targetAttribute' => ['packet_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['packet_login'], 'validateLogin'],
            [['phone_number'], 'validateVoIpPhoneOnChangePacket','on'=>'change_packet'],
            [['phone_number'], 'validateVoIpPhoneOnAddingPacket','on'=>'adding_packet'],



            ['phone_number', 'required', 'when' => function($model) {
                if (isset($model->service->service_alias)) {
                    if ($model->service->service_alias == "voip") {
                        return true;
                    }
                    return false;
                }
                return false;
             
                },'on' => self::CHANGE_PACKET
             ],

            [
                'phone_number', 'required', 'when' => function($model) {
                if (isset($model->service->service_alias)) {
                    if ( $model->service->service_alias == "voip") {
                         return true;
                    }
                    return false;
                }
                return false;

                }
            ],
            
            [
                'mac_address', 'required', 'when' => function($model) {
                if (isset($model->service->service_alias)) {
                    if ($model->service->service_alias == "internet" ) {
                         return true;
                    }
                    return false;
                }
                return false;

                }
            ],

            
            [
                'devices', 'required', 'when' => function($model) {
                if (isset($model->service->service_alias)) {
                    if ($model->service->service_alias == "internet" && $model->user->district->device_registration == "1" ) {
                         return true;
                    }
                    return false;
                }
                return false;

                }
            ],

            [
                'property', 'required', 'when' => function($model) {
                if (isset($model->service->service_alias)) {
                    if ($model->service->service_alias == "tv") {
                         return true;
                    }
                    return false;
                }
                return false;

                }
            ],
        
        
        
            [
                'switch_port', 'required', 'when' => function($model) {
                if (isset($model->service->service_alias)) {
                    if ($model->service->service_alias == "internet") {

                        if (isset($model->port_type)) {
                            if ($model->port_type == "switch") {
                                return true;
                            }
                        }
             
                         return false;
                    }
                    return false;
                }
                return false;

                }
            ],

            [
                'port_type', 'required', 'when' => function($model) {
                if (isset($model->service->service_alias)) {
                    if ($model->service->service_alias == "internet" && $model->user->district->device_registration == "1" ) {
                         return true;
                    }
                    return false;
                }
                return false;

                }
            ],


            [
                'box', 'required', 'when' => function($model) {
                if (isset($model->service->service_alias)) {
                    if ( $model->service->service_alias == "internet") {

                        if (isset($model->port_type)) {
                            if ($model->port_type == "epon" || $model->port_type == "gpon" || $model->port_type == "xpon" ) {
                                return true;
                            }
                        }
             
                         return false;
                    }
                    return false;
                }
                return false;

                }
            ],

            [
                'box_port', 'required', 'when' => function($model) {
                if (isset($model->service->service_alias)) {
                    if ($model->service->service_alias == "internet") {

                        if (isset($model->port_type)) {
                            if ($model->port_type == "epon" || $model->port_type == "gpon"  || $model->port_type == "xpon") {
                                return true;
                            }
                        }
             
                         return false;
                    }
                    return false;
                }
                return false;

                }
            ],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t("app","Customer"),
            'mac_address' => Yii::t("app","Mac address"),
            'service_id' => Yii::t("app","Service"),
            'packet_login' => Yii::t("app","Packet login"),
            'packet_password' => Yii::t("app","Packet password"),
            'packet_id' => Yii::t("app","Packet"),
            'property' => '',
            'packet_tags'=>Yii::t("app","Packet"),
            'static_ip_address'=>Yii::t("app","Static ip"),
            'price' => Yii::t("app","Custom price"),
            'mac_address' => Yii::t("app","Mac address"),
        ];
    }


   public function validateLogin( $attribute, $params, $validator ){
    $loginExsistOnInet = \app\models\UsersInet::find()->where(['login'=> $this->packet_login])->count();
   
        if ( $this->service->service_alias == "internet") {
            if (  $loginExsistOnInet > 0  )
            {
                $validator->addError($this, $attribute, Yii::t('app', 'Packet login already exist'));
                return false;
            }
        }
    }

   public function validateVoIpPhoneOnAddingPacket( $attribute, $params, $validator ){
        $phoneExsist = \app\models\UsersVoip::find()
        ->where(['phone_number'=> $this->phone_number])
        ->count();
       
            if ( $this->service->service_alias == "voip") {
                if (  $phoneExsist > 0  ){
                    $validator->addError($this, $attribute, Yii::t('app', 'Phone number already exist'));
                    return false;
                }
            }
    }

   public function validateVoIpPhoneOnChangePacket( $attribute, $params, $validator ){

        $phoneExsist = \app\models\UsersVoip::find()
        ->where(['phone_number'=> $this->phone_number])
        ->andWhere(['!=','u_s_p_i',$this->u_s_p_i])
        ->count();
       
            if ( $this->service->service_alias == "voip") {
                if (  $phoneExsist > 0  ){
                    $validator->addError($this, $attribute, Yii::t('app', 'Phone number already exist'));
                    return false;
                }
            }
    }



    // public function validateMacAddress( $attribute, $params, $validator )
    // {
    //     if ( $this->mac_address )
    //     {
    //      $pattern = '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';

    //         if ( !preg_match( $pattern, $this->mac_address ) ) {
    //             $validator->addError($this, $attribute, Yii::t('app', 'Incorrect mac address format'));
    //             return false;
    //         }
      
    //         $macExsistOnInet = \app\models\UsersInet::find()
    //         ->where(['mac_address'=>$this->mac_address])
    //         ->count();


    //         if (  $macExsistOnInet > 0  )
    //         {
    //             $validator->addError($this, $attribute, Yii::t('app', 'Mac address already exist'));
    //             return false;
    //         }


    //     }
    // }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Services::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacket()
    {
        return $this->hasOne(Packets::className(), ['id' => 'packet_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

   public static function getRandomString( $length = 8 ) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function getUsersInet()
    {
        return $this->hasOne(UsersInet::className(), ['u_s_p_i' => 'id']);
    }


    public function getUsersTv()
    {
        return $this->hasOne(UsersTv::className(), ['u_s_p_i' => 'id']);
    }


    public function getUsersWifi()
    {
        return $this->hasOne(UsersWifi::className(), ['u_s_p_i' => 'id']);
    }

    public function getUsersVoip()
    {
        return $this->hasOne(UsersVoip::className(), ['u_s_p_i' => 'id']);
    }


    public  static function getServicesAsArray($user_id){
        $services_array = []; 
        $model = \app\models\UsersServicesPackets::find()
        ->select('users_services_packets.*,services.service_alias as serviceAlias,service_packets.packet_name as service_packet_name,service_packets.packet_price as service_packet_price')
        ->leftJoin("services","services.id=users_services_packets.service_id")
        ->leftJoin("service_packets","service_packets.id=users_services_packets.packet_id")
        ->where(['user_id'=>$user_id])
        ->asArray()
        ->all();

          foreach ($model as $key_ser_p => $services_packets) {
                $packetPrice = ( $services_packets['price'] != null ||  $services_packets['price'] != 0 ) ? $services_packets['price'] : $services_packets['service_packet_price'];
            if ($services_packets['serviceAlias'] == "internet") {
                  $services_array['internet'][] = ['packet_name'=>$services_packets['service_packet_name'],'packet_price'=>$packetPrice ];
            }
            if ($services_packets['serviceAlias'] == "tv") {
               $services_array['tv'][] =   ['packet_name'=>$services_packets['service_packet_name'],'packet_price'=>$packetPrice ];
            }
            if ($services_packets['serviceAlias'] == "wifi") {
                 $services_array['wifi'][] =  ['packet_name'=>$services_packets['service_packet_name'],'packet_price'=>$packetPrice];
            }
            if ($services_packets['serviceAlias'] == "voip") {
                 $services_array['voip'][] =  ['packet_name'=>$services_packets['service_packet_name'],'packet_price'=>$packetPrice];
            }

         
          }

          return  $services_array;
    }






    public static function getPortType( $user_id ){
        $userModel = \app\models\Users::find()
        ->where(['id'=>$user_id])
        ->asArray()
        ->one();

        $userDistrict = $userModel['district_id'];


        $model = \app\models\DeviceLocations::find()
        ->select('device_locations.*,devices.type as device_type')
        ->leftJoin('devices','devices.id=device_locations.device_id')
        ->where(['device_locations.district_id'=>$userDistrict])
        ->groupBy('devices.type')
        ->asArray()
        ->all();

        return $model;
    }

    public static function getSwitches($user_id){
        $userModel = \app\models\Users::find()
        ->where(['id'=>$user_id])
        ->asArray()
        ->one();

        $userLocation = $userModel['location_id'];

        $model = \app\models\DeviceLocations::find()
        ->select('device_locations.*,devices.type as device_type,devices.name as device_name')
        ->leftJoin('devices','devices.id=device_locations.device_id')
        ->where(['device_locations.location_id'=>$userLocation])
        ->andWhere(['published'=>'1'])
        ->andWhere(['devices.type'=>'switch'])
        ->asArray()
        ->all();

        return $model;
    }

    public static function getOlt($user_id,$oltType){
        $userModel = \app\models\Users::find()
        ->where(['id'=>$user_id])
        ->asArray()
        ->one();

        $userDistrict = $userModel['district_id'];

        $model = \app\models\DeviceLocations::find()
        ->select('device_locations.*,devices.type as device_type,devices.name as device_name')
        ->leftJoin('devices','devices.id=device_locations.device_id')
        ->where(['device_locations.district_id'=>$userDistrict])
        ->andWhere(['published'=>'1'])
        ->andWhere(['type'=>$oltType])
        ->andWhere(['!=', 'devices.type', 'switch'])
        ->asArray()
        ->all();

        return $model;
    }





    public static function getSwitchPort($device_id){
        $model = \app\models\SwitchPorts::find()
        ->where(['device_id'=>$device_id])
        ->andWhere(['status'=>'0'])
        ->andWhere(['is', 'u_s_p_i', new \yii\db\Expression('null')])
        ->asArray()
        ->all();

        return $model;
    }

   public static function getOltBox($device_id,$user_id){
        $userModel = \app\models\Users::find()
        ->where(['id'=>$user_id])
        ->asArray()
        ->one();

        $userLocation = $userModel['location_id'];

        $model = \app\models\EgponBox::find()
        ->where(['device_id'=>$device_id])
        ->andWhere(['location_id'=>$userLocation])
        ->asArray()
        ->all();

        return $model;
    }



    public static function getOltBoxPort($box_id){
        $model = \app\models\EgonBoxPorts::find()
        ->where(['egon_box_id'=>$box_id])
        ->andWhere(['status'=>'0'])
        ->andWhere(['is', 'u_s_p_i', new \yii\db\Expression('null')])
        ->asArray()
        ->all();

        return $model;
    }

}

<?php

namespace app\models;

use Yii;
use app\components\RouterosApi;
/**
 * This is the model class for table "users_inet".
 *
 * @property int $id
 * @property int $user_id
 * @property int $packet_id
 * @property string $login
 * @property string $password
 * @property string $static_ip
 * @property string $router_id
 * @property int $status
 * @property string $created_at
 *
 * @property Users $user
 */
class UsersInet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_inet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'packet_id', 'login', 'password'], 'required'],
            [['user_id', 'packet_id', 'status','u_s_p_i','router_id'], 'integer'],
            [['login', 'password', 'static_ip', 'created_at','mac_address'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app','Customer'),
            'router_id' => Yii::t('app','Router'),
            'packet_id' => Yii::t('app','Packet'),
            'mac_address' => Yii::t('app','Mac address'),
            'login' => Yii::t('app','Inet login'),
            'password' =>Yii::t('app','Inet password'),
            'static_ip' => Yii::t('app','Static Ip'),
            'nas' => Yii::t('app','NAS'),
            'status' => Yii::t('app','Status'),
            'created_at' => Yii::t('app','Created at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function getRouter()
    {
        return $this->hasOne(\app\models\Routers::className(), ['id' => 'router_id']);
    }


    public function getPacket(){
        return $this->hasOne(Packets::className(), ['id' => 'packet_id']);
    }   



    public static function isUserOnline( $login ){
        $user_inet_model = \app\models\UsersInet::find()
        ->where(['login' => $login])
        ->one();

        $router_model = \app\models\Routers::find()
        ->where(['id' => $user_inet_model->user->district->router_id])
        ->one();

        $dataActivePrint = \app\components\MikrotikQueries::pppActivePrint(
            $login, 
            $router_model['nas'], 
            $router_model['username'], 
            $router_model['password']
        );


        if ( $dataActivePrint != null ) {
            $data = $dataActivePrint[0];
        }else{
            $data = null;
        }   


       return ( $data !== null );
    }


}





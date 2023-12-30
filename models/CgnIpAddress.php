<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgn_ip_address".
 *
 * @property int $id
 * @property int $ip_address_id
 * @property int $internal_ip
 * @property string $port_range
 */
class CgnIpAddress extends \yii\db\ActiveRecord
{

   public $start_ip;
   public $end_ip;
   
 
   const SCENARIO_DEFINE_NATS_ROUTER = 'define_member';
   const SCENARIO_CLEAR_NATS_ROUTER = 'define_member';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cgn_ip_address';
    }


     public function scenarios(){
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DEFINE_NATS_ROUTER] = ['start_ip','end_ip','router_id'];
        $scenarios[self::SCENARIO_CLEAR_NATS_ROUTER] = ['start_ip','end_ip'];
        return $scenarios;
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip_address_id', 'port_range'], 'required'],
            [['start_ip','end_ip'], 'ip'],
            [['start_ip','end_ip','router_id'], 'required', 'on' => self::SCENARIO_DEFINE_NATS_ROUTER],
            [['start_ip','end_ip'], 'required', 'on' => self::SCENARIO_CLEAR_NATS_ROUTER],
            [['ip_address_id', 'internal_ip','inet_login','router_id'], 'integer'],
            [['port_range','internal_ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ip_address_id' => Yii::t('app', 'Ip address'),
            'internal_ip' => Yii::t('app', 'Internal ip'),
            'port_range' => Yii::t('app', 'Port range'),
            'inet_login' => Yii::t('app', 'Inet Login'),
            'router_id' => Yii::t('app', 'Inet Router')
        ];
    }



    public static function checkIp( $startIp,$endIp ){
        $ips = [];
        $data  = [];
        for ($i=$startIp; $i <= $endIp ; $i++) { 
            $ips[] = long2ip($i);
        }
        $model = \app\models\CgnIpAddress::find()
        ->where(['internal_ip'=>$ips])
        ->andWhere(['is not', 'router_id', new \yii\db\Expression('null')])
        ->asArray()
        ->all();

        if ( count( $model ) == 0 ) {
            $result = true;
        } else {
            $result = false;
        }
        
        return $result;
    }


    public static function checkIpExsistRouter( $startIp,$endIp ){
        $ips = [];
        $data  = [];
        for ($i=$startIp; $i <= $endIp ; $i++) { 
            $ips[] = long2ip($i);
        }
        $model = \app\models\CgnIpAddress::find()
        ->where(['internal_ip'=>$ips])
        ->andWhere(['is not', 'router_id', new \yii\db\Expression('null')])
        ->asArray()
        ->all();

        if ( count( $model ) == 0 ) {
            $result = false;
        } else {
            $result = true;
        }
        
        return $result;
    }




    public static function staticIpAlert( $routerId ){


        $staticIpAddressCount = \app\models\IpAdresses::find()
        ->leftJoin('routers','routers.id=ip_adresses.router_id')
        ->where(['router_id'=>$routerId])
        ->andWhere(['type'=>'1'])
        ->count();

        $routerModel = \app\models\Routers::findOne($routerId);
        $usersPacketsOnRouterCount = \app\models\UsersInet::find()
        ->where(['router_id'=>$routerId])
        ->andWhere(['!=','status','3'])
        ->andWhere("static_ip IS NOT NULL AND TRIM(static_ip) <> ''")
        ->asArray()
        ->count();

        $diffrence = $staticIpAddressCount - $usersPacketsOnRouterCount;

        if ( $diffrence >  0 ) {
            $result = [
                'status'=>true,
                'capacity'=>$usersPacketsOnRouterCount."/".$staticIpAddressCount,
            ];
        }else{
            $result = [
                'status'=>false,
                'capacity'=>$usersPacketsOnRouterCount."/".$staticIpAddressCount,
                'message'=>Yii::t('app','Please add {static_ip_count} ip to {router_name}',[ 'static_ip_count'=> abs( $diffrence ),'router_name'=> $routerModel['name']]),
            ];

        }

        return $result;
    }


    public static function ipAlert( int $routerId, string $routerName ){

        $cgnIpAddressModel = \app\models\CgnIpAddress::find()
        ->select('count(cgn_ip_address.id) as ip_count')
        ->leftJoin('ip_adresses','ip_adresses.id=cgn_ip_address.ip_address_id')
        ->leftJoin('routers','routers.id=ip_adresses.router_id')
        ->where(['router_id'=>$routerId])
        ->where(['parent'=>0])
        ->asArray()
        ->one();


        $usersPacketsOnRouterCount = \app\models\UsersInet::find()
        ->where(['router_id'=>$routerId])
        ->andWhere(['!=','status','3'])
        ->andWhere([
            'or',
            ['is', 'static_ip', null],
            ['=', 'static_ip', '']
        ])
        ->asArray()
        ->count();

        $diffrence = $cgnIpAddressModel['ip_count'] - $usersPacketsOnRouterCount;

        if ( $diffrence >  0 ) {
            $result = [
                'status'=>true,
                'capacity'=>$usersPacketsOnRouterCount."/".$cgnIpAddressModel['ip_count'],
            ];
        }elseif( $diffrence ==  0 ){
            $result = [
                'status'=>false,
                'capacity'=>$usersPacketsOnRouterCount."/".$cgnIpAddressModel['ip_count'],
                'message'=>Yii::t('app','Please add dynamic ip to {router_name} router',[ 'ip_count'=> abs($diffrence) ,'router_name'=> $routerName ]),
            ];
        }else{
            $result = [
                'status'=>false,
                'capacity'=>$usersPacketsOnRouterCount."/".$cgnIpAddressModel['ip_count'],
                'message'=>Yii::t('app','Please add {ip_count} dynamic ip to {router_name} router',[ 'ip_count'=> abs($diffrence) ,'router_name'=> $routerName ]),
            ];

        }

        return $result;
    }







}

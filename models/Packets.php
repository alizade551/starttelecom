<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "packets".
 *
 * @property int $id
 * @property int $servis_id
 * @property string $packet_name
 * @property int $packet_price
 *
 * @property Services $servis
 */
class Packets extends \yii\db\ActiveRecord
{
    public $transfer_packet;
    public $query_count;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_packets';
    }



    const SCENARIO_TRANSFER = 'transfer_packet';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_TRANSFER] = ['transfer_packet','query_count'];
        return $scenarios;
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'packet_name', 'packet_price'], 'required'],
            [['transfer_packet','query_count'], 'required' , 'on'=>'transfer_packet'],
            [['service_id', 'packet_price','position','created_at','download','upload'], 'integer'],
            [['packet_price'], 'number', 'min' => 1, 'max'=>9999 ],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::className(), 'targetAttribute' => ['service_id' => 'id']],
            ['query_count','validateQueryCount'],

            [
                'download', 'required', 'when' => function($model) {
                    if ($model->service_id != null) {
                      return $model->service->service_alias === 'internet'; // boolean should be returned
                    }
                }
            ],

            [
                'upload', 'required', 'when' => function($model) {
                     if ($model->service_id != null) {
                      return $model->service->service_alias === 'internet'; // boolean should be returned
                    }
                }
            ],

        ];
    }

 


    public function validateQueryCount($attribute, $params, $validator)
    {
        if ( !in_array($this->$attribute, [ 10 , 20 , 50 ] ) ) {
            $this->addError($attribute, Yii::t('app','Query count must be 10, 20 and 50'));
        }
    }  

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'service_id' => Yii::t('app','Service'),
            'packet_name' => Yii::t('app','Packet name'),
            'transfer_packet' => Yii::t('app','Transmitted'),
            'packet_price' => Yii::t('app','Price'),
            'query_count' => Yii::t('app','Query count'),
            'download' => Yii::t('app','Download'),
            'upload' => Yii::t('app','Upload'),
        ];
    }



    public static function getQueryCount(){
        return [
            10 => 10,
            20 => 20,
            50 => 50
        ];
    }

    public static function checkPacketPrice(){
        $integradedPacketCount = \app\models\Packets::find()
        ->where(['packet_price'=>0])
        ->count();

        return ( $integradedPacketCount > 0 ) ? true : false ;
    }


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
    public function getUsersInets()
    {
        return $this->hasMany(UsersInet::className(), ['packet_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersServicesPackets()
    {
        return $this->hasMany(UsersServicesPackets::className(), ['packet_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersTvs()
    {
        return $this->hasMany(UsersTv::className(), ['packet_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersWifis()
    {
        return $this->hasMany(UsersWifi::className(), ['packet_id' => 'id']);
    }
}

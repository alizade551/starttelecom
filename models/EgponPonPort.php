<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "egpon_pon_port".
 *
 * @property int $id
 * @property int $device_id
 * @property int $pon_port_number
 * @property string $pon_port_name
 * @property string $splitting
 *
 * @property Devices $device
 * @property EgponPonPortLocation[] $egponPonPortLocations
 */
class EgponPonPort extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    const SPLIT_PON_PORT = 'split_pon_port';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SPLIT_PON_PORT] = ['split_pon_port'];
        return $scenarios;
    }


    public static function tableName()
    {
        return 'egpon_pon_port';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_id', 'pon_port_number', 'splitting'], 'required'],
            [['splitting','status'], 'required', 'on' => self::SPLIT_PON_PORT],
            [['device_id', 'pon_port_number'], 'integer'],
            [['splitting'], 'string'],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Devices::className(), 'targetAttribute' => ['device_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'device_id' => Yii::t('app', 'Device name'),
            'pon_port_number' => Yii::t('app', 'Pon port number'),
            'splitting' => Yii::t('app', 'Splitter'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Devices::className(), ['id' => 'device_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEgponPonPortLocations()
    {
        return $this->hasMany(EgponPonPortLocation::className(), ['egpon_pon_port_id' => 'id']);
    }

    public static function splitPonPort(){
        return [
            2=>2,
            4=>4,
            8=>8,
            16=>16,
            32=>32,
  
        ];
    }    

    public static function ponPortStatus(){
        return [
            0=>Yii::t('app','Active'),
            1=>Yii::t('app','Deactive'),
            2=>Yii::t('app','Broken'),
  
        ];
    }

}

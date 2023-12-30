<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "egpon_box".
 *
 * @property int $id
 * @property int $device_id
 * @property int $egpon_pon_port_id
 * @property int $location_id
 * @property string $box_name
 * @property int $pon_port_number
 * @property int $box_number
 *
 * @property EgonBoxPorts[] $egonBoxPorts
 * @property EgponPonPort $egponPonPort
 * @property AddressLocations $location
 * @property Devices $device
 */
class EgponBox extends \yii\db\ActiveRecord
{
    const CHANGE_BOX_CORDINATE = 'change_box_cordinate';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'egpon_box';
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::CHANGE_BOX_CORDINATE] = ['cordinate'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_id', 'egpon_pon_port_id', 'pon_port_number', 'box_number'], 'required'],
            [['location_id','cordinate'], 'required', 'on' => self::CHANGE_BOX_CORDINATE],
            [['device_id', 'egpon_pon_port_id', 'location_id', 'pon_port_number', 'box_number'], 'integer'],
            [['box_name','cordinate'], 'string', 'max' => 255],
            [['egpon_pon_port_id'], 'exist', 'skipOnError' => true, 'targetClass' => EgponPonPort::className(), 'targetAttribute' => ['egpon_pon_port_id' => 'id']],
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Locations::className(), 'targetAttribute' => ['location_id' => 'id']],
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
            'egpon_pon_port_id' => Yii::t('app', 'Egpon Pon Port'),
            'location_id' => Yii::t('app', 'Location'),
            'box_name' => Yii::t('app', 'Box name'),
            'pon_port_number' => Yii::t('app', 'Pon Port Number'),
            'cordinate' => Yii::t('app', 'Cordinate'),
            'box_number' => Yii::t('app', 'Box number'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEgonBoxPorts()
    {
        return $this->hasMany(EgonBoxPorts::className(), ['egon_box_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEgponPonPort()
    {
        return $this->hasOne(EgponPonPort::className(), ['id' => 'egpon_pon_port_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Locations::className(), ['id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Devices::className(), ['id' => 'device_id']);
    }
}

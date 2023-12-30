<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device_locations".
 *
 * @property int $id
 * @property int $device_id
 * @property int $city_id
 * @property int $district_id
 * @property int $location_id
 *
 * @property Devices $device
 * @property AddressCities $city
 * @property AddressDistrict $district
 * @property AddressLocations $location
 */
class DeviceLocations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    const ADD_LOCATION = 'add_location';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::ADD_LOCATION] = ['device_id','city_id','district_id','location_id'];
        return $scenarios;
    }

    public static function tableName()
    {
        return 'device_locations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_id', 'city_id', 'district_id'], 'required'],
            [['device_id', 'city_id', 'district_id'], 'integer'],
            [['location_id'], 'safe'],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Devices::className(), 'targetAttribute' => ['device_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::className(), 'targetAttribute' => ['district_id' => 'id']],
            ['location_id', 'required', 'when' => function($model) {
                if (isset($model->device->type)) {
                    if ($model->device->type == "switch") {
             
                         return true;
                    }
                    return false;
                }
                return false;
             
            },'on' => self::ADD_LOCATION],


  
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
            'city_id' => Yii::t('app', 'City'),
            'district_id' => Yii::t('app', 'District'),
            'location_id' => Yii::t('app', 'Location'),
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
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'district_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Locations::className(), ['id' => 'location_id']);
    }
}

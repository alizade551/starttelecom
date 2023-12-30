<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "devices".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $port_count
 * @property int $pon_port_count
 * @property string $address
 * @property int $created_at
 *
 * @property DeviceLocations[] $deviceLocations
 * @property SwitchPorts[] $switchPorts
 */
class Devices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'devices';
    }


    const SCENARIO_CORDINATE = 'cordinate_update';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CORDINATE] = ['city_id','district_id','location_id','cordinate'];
        return $scenarios;
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vendor_name','type','description','ip_address'], 'required'],
            [['city_id','district_id','location_id','cordinate'], 'required' , 'on'=>'cordinate_update'],
            [['type','published'], 'string'],
            [['port_count', 'pon_port_count', 'created_at','city_id','district_id','location_id'], 'integer'],
            [['name', 'description','vendor_name','cordinate'], 'string', 'max' => 255],
            ['ip_address', 'ip'],
            ['pon_port_count', 'required', 'when' => function($model) {
                if ($model->type != "switch") {
                   return true;
                }
                return false;

                }
            ],

            ['port_count', 'required', 'when' => function($model) {
                if ($model->type == "switch") {
                   return true;
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
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Device name'),
            'type' => Yii::t('app', 'Device type'),
            'vendor_name' => Yii::t('app', 'Vendor name'),
            'city_id' => Yii::t('app', 'City'),
            'district_id' => Yii::t('app', 'District'),
            'location_id' => Yii::t('app', 'Location'),
            'ip_address' => Yii::t('app', 'Ip address'),
            'port_count' => Yii::t('app', 'Port count'),
            'pon_port_count' => Yii::t('app', 'Pon port Count'),
            'description' => Yii::t('app', 'Description'),
            'published' => Yii::t('app', 'Published'),
            'created_at' => Yii::t('app', 'Created at'),
        ];
    }

    public static function getPortCount()
    {
        return [
            8=>8,
            16=>16,
            24=>24,
            32=>32,
            48=>48,
        ];
    }

   public static function getPonPortCount()
    {
        return [
            4=>4,
            8=>8,
            16=>16,
            32=>32
        ];
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceLocations()
    {
        return $this->hasMany(DeviceLocations::className(), ['device_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSwitchPorts()
    {
        return $this->hasMany(SwitchPorts::className(), ['device_id' => 'id']);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "warehouses".
 *
 * @property int $id
 * @property string $name
 * @property int $location_id
 * @property string $cordinate
 * @property int $created_at
 *
 * @property ItemStock[] $itemStocks
 */
class Warehouses extends \yii\db\ActiveRecord
{

    public $city;
    public $district;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'location_id', 'cordinate','city','district'], 'required'],
            [['location_id', 'created_at'], 'integer'],
            [['name', 'cordinate'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Warehouse mame'),
            'city' => Yii::t('app', 'City'),
            'district' => Yii::t('app', 'District'),
            'location_id' => Yii::t('app', 'Location'),
            'cordinate' => Yii::t('app', 'Cordinate'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemStocks()
    {
        return $this->hasMany(ItemStock::className(), ['warehouse_id' => 'id']);
    }

    public function getLocation()
    {
        return $this->hasOne(Locations::className(), ['id' => 'location_id']);
    }

}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "routers".
 *
 * @property int $id
 * @property string $name
 * @property string $nas
 * @property string $username
 * @property string $password
 */
class Routers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'routers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'nas', 'username','city_id', 'district_id','location_id','password','vendor_name','ip_pool_var','interface','cordinate','parent'], 'required'],
            [['name', 'nas', 'username', 'password','vendor_name','ip_pool_var'], 'string', 'max' => 255],
            [['parent'], 'integer'],
            [['nas'], 'ip'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'name' =>Yii::t('app','Router name'),
            'use_ip' =>Yii::t('app','Self ip using'),
            'city_id' =>Yii::t('app','City'),
            'district_id' =>Yii::t('app','District'),
            'location_id' =>Yii::t('app','Location'),
            'interface' =>Yii::t('app','Interface'),
            'ip_pool_var' =>Yii::t('app','Ip pool variable'),
            'vendor_name' => Yii::t('app','Vendor name'),
            'nas' =>Yii::t('app','NAS'),
            'username' =>Yii::t('app','Username'),
            'password' =>Yii::t('app','Password'),
            'cordinate' =>Yii::t('app','Cordinate')
        ];
    }

    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'district_id']);
    }

}

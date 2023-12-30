<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "services".
 *
 * @property int $id
 * @property string $service_name
 */
class Services extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'services';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_name','service_alias'], 'required'],
            [['service_name','service_alias'], 'string', 'max' => 50],
            [['updated_at', 'created_at'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'service_name' => Yii::t('app','Servis name'),
            'service_alias' => Yii::t('app','Service alias'),
            'updated_at' => Yii::t('app','Updated at'),
            'created_at' => Yii::t('app','Created at')
        ];
    }

    public function getPackets()
    {
        return $this->hasMany(Packets::className(), ['service_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getUsersServicesPackets()
    {
        return $this->hasMany(UsersServicesPackets::className(), ['service_id' => 'id']);
    }


}

<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $city_name
 *
 * @property Location[] $locations
 * @property RequestOrder[] $requestOrders
 * @property Users[] $users
 */
class Cities extends DefaultActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address_cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_name'], 'required'],
            [['city_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_name' => Yii::t('app','City'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasMany(Locations::className(), ['city_id' => 'id']);
    }

    public static function getCityEditableValue(){
        $model =  Cities::find()->asArray()->all();
        $data = [];
        $c=0;
        foreach ($model as $key => $value) {
            $data[$c]['value'] = $value['id'];
            $data[$c]['text'] = $value['city_name'];
        $c++;
        }

        return json_encode($data);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestOrders()
    {
        return $this->hasMany(RequestOrder::className(), ['city_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['city_id' => 'id']);
    }
}

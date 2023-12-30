<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;
use app\models\radius\Nas;

/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property string $location_name
 * @property int $city_id
 *
 * @property Cities $city
 */
class District extends DefaultActiveRecord
{




    const SCENARIO_ADD_ROUTER = 'add_router';





    public static function tableName()
    {
        return 'address_district';
    }


     public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADD_ROUTER] = ['nas_id'];
        return $scenarios;
    }




    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['district_name', 'city_id','cordinate','device_registration'], 'required'],
            ['nas_id', 'required', 'on'=>[self::SCENARIO_ADD_ROUTER]],
            [['city_id','nas_id','created_at'], 'integer'],
            [['district_name'], 'string', 'max' => 255],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'district_name' =>Yii::t("app","District"),
            'cordinate' =>Yii::t("app","Cordinate"),
            'city_id' =>Yii::t("app","City"),
            'nas_id' =>Yii::t("app","Router name"),
            'device_registration' =>Yii::t("app","Customer registration on device"),
            'created_at' =>Yii::t("app","Created at"),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }

    public function getNas()
    {
        return $this->hasOne(Nas::className(), ['id' => 'nas_id']);
    }

    public static function getDistrictUserRegistrationOnDeviceStatus()
    {
        return [
            '1'=>Yii::t('app','Yes'),
            '0'=>Yii::t('app','No'),

        ];
    }

    public static function getDistrictEditableValue($city){
        $model =  District::find()->where(['city_id'=>$city])->asArray()->all();
        $data = [];
        $c=0;
        foreach ($model as $key => $value) {
            $data[$c]['value'] = $value['id'];
            $data[$c]['text'] = $value['district_name'];
        $c++;
        }

        return json_encode($data);
    }



}

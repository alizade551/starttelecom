<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;

/**
 * This is the model class for table "locations".
 *
 * @property int $id
 * @property int $city_id
 * @property int $district_id
 * @property string $name
 *
 * @property Cities $city
 * @property District $district
 */
class Locations extends DefaultActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address_locations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'district_id', 'name','cordinate'], 'required'],
            [['city_id', 'district_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['cordinate'], 'string'],
            ['cordinate','validateGPSCordinate'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::className(), 'targetAttribute' => ['district_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'city_id' => Yii::t('app','City'),
            'district_id' => Yii::t('app','District'),
            'name' => Yii::t('app','Location'),
            'cordinate' => Yii::t('app','Cordinate')
        ];
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

    public static function getLocationEditableValue($dis){

        $model =  Locations::find()->where(['district_id'=>$dis])->asArray()->all();
        $data = [];
        $c=0;
        foreach ($model as $key => $value) {
            
            $data[$c]['value'] = $value['id'];
            $data[$c]['text'] = $value['name'];
            $c++;
        }

        return json_encode($data);
    }


    public function validateGPSCordinate( $attribute, $params, $validator )
    {
        if ( $this->cordinate )
        {
            $cordinate = str_contains( $this->cordinate, ',' );
        
            if ( !$cordinate )
            {
                $validator->addError($this, $attribute, Yii::t('app', 'Inccorect gps format'));
                return false;
            }

            $latitude = explode( ",", $this->cordinate)[0];
            $longitude = explode( ",", $this->cordinate)[1];

            if ( $latitude > 90 ) {
               $validator->addError($this, $attribute, Yii::t('app', 'Latitude cannot be greater than 90'));
            }


            if ( $latitude < -90 ) {
                $validator->addError($this, $attribute, Yii::t('app', 'Latitude cannot be less than -90'));
            }

            if ( $longitude > 180 ) {
               $validator->addError($this, $attribute, Yii::t('app', 'Longitude cannot be greater than 180'));
            }


            if ( $longitude < -180 ) {
                $validator->addError($this, $attribute, Yii::t('app', 'Longitude cannot be less than -180'));
            }


        }
    }


}

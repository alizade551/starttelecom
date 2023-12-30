<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;
use borales\extensions\phoneInput\PhoneInputValidator;

/**
 * This is the model class for table "request_order".
 *
 * @property int $id
 * @property string $fullname
 * @property string $company
 * @property string $phone
 * @property string $email
 * @property int $city_id
 * @property int $district_id
 * @property string $location
 * @property int $room
 * @property string $selected_services
 * @property string $note
 * @property int $tariff
 * @property int $status
 * @property string $created_at
 *
 * @property District $district
 * @property Cities $city
 * @property RequestOrderService[] $requestOrderServices
 */
class RequestOrder extends DefaultActiveRecord
{


    public $photos;
    public $selected_service_form;

    public $personals;
    public $request_type;
    public $balance_in;
    public $temporary_day;
    public $customer_id;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_ACCEPT_ORDER = 'accept_order';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }
    

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['fullname','phone','extra_phone','photos','city_id','district_id','location_id','room','selected_service_form','request_at','cordinate','password','paid_time_type','paid_day','company','email','created_at',];
        $scenarios[self::SCENARIO_UPDATE] = ['fullname','phone','extra_phone','photos','city_id','district_id','location_id','room','selected_service_form','cordinate','password','paid_time_type','paid_day','company','email','request_at','message_lang'];

         $scenarios[self::SCENARIO_ACCEPT_ORDER] = ['contract_number','personals','request_type','balance_in','temporary_day','customer_id'];
        return $scenarios;
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fullname', 'phone', 'city_id', 'district_id', 'location_id', 'room', 'selected_service_form','request_at','password','paid_time_type','paid_day','message_lang'], 'required'],
            [['contract_number','status','second_status','personals','request_type','balance_in'], 'required' , 'on'=>'accept_order'],

            [['city_id', 'district_id', 'room', 'status','location_id','paid_day'], 'integer'],
            [['paid_day'], 'integer', 'min' => 1, 'max'=> 31 ],
            [['password'], 'string', 'min' => 4 ],
            // [['phone', 'extra_phone'], 'validateUniqeOnCreate', 'on'=>'create'],
            // [['phone', 'extra_phone'], 'validateUniqeOnUpdate', 'on'=>'update'],

            [['contract_number'], 'validateUniqeContract','on'=>'accept_order'],



            [['fullname', 'company', 'email'], 'string', 'max' => 120],
            [['email'], 'email'],
            [['damage_status', 'second_status', 'note','paid_time_type'], 'string'],
            [['phone','extra_phone'], 'string'],
            [ ['phone','extra_phone'], PhoneInputValidator::className(), 'region' => ['AZ','RU','TR','US'], 'message' => Yii::t( 'app', 'The format of the phone is invalid or system not supported your country.' ) ],
            [['created_at','updated_at','second_status','request_at','photos','password'], 'string', 'max' => 255],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::className(), 'targetAttribute' => ['district_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],



            [
                'temporary_day', 'required', 'on'=>'accept_order','when' => function($model) {

                if ( isset( $model->request_type ) ) {
                    if ( $model->request_type == "3" ) {
                         return true;
                    }
                    return false;
                }
                return false;

                },
            ],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'fullname' => Yii::t('app','Customer'),
            'temporary_day' => Yii::t('app','Temporary day'),
            'balance_in' => Yii::t('app','Balance in'),
            'photos' => Yii::t('app','Photos'),
            'personals' => Yii::t('app','Personal'),
            'company' => Yii::t('app','Company'),
            'request_type' => Yii::t('app','Request type'),
            'contract_number' => Yii::t('app','Contract number'),
            'password' => Yii::t('app','Password'),
            'phone' => Yii::t('app','Phone'),
            'extra_phone' => Yii::t('app','Extra phone'),
            'message_lang' => Yii::t('app','Message language'),
            'email' => Yii::t('app','E-mail'),
            'paid_day' => Yii::t('app','Paid day'),
            'paid_time_type' => Yii::t('app','Paid type'),
            'selected_service_form' => Yii::t('app','Services'),
            'city_id' => Yii::t('app','City'),
            'district_id' => Yii::t('app','District'),
            'location_id' => Yii::t('app','Location'),
            'room' => Yii::t('app','Room'),
            'tariff' => Yii::t('app','Tariff'),
            'balance' => Yii::t('app','Balance'),
            'damage_status'=>Yii::t('app','Damage status'),
            'second_status' => Yii::t('app','Request status'),
            'status' => Yii::t('app','Status'),
            'request_at' => Yii::t('app','Request at'),
            'updated_at' => Yii::t('app','Updated at'),
            'created'=> Yii::t('app','Created at'),
        ];
    }


    public function validateUniqeContract($attribute, $params, $validator)
    {
      
         $model = \app\models\RequestOrder::find()
        ->where(['contract_number'=>$this->contract_number])
        ->andWhere(['!=', 'id', $this->customer_id])
        ->one();

        if ( $model != null  ) {
            $this->addError($attribute, Yii::t('app','Contract number already exist, please use another'));
        }
    }

    public function validateUniqeOnCreate($attribute, $params, $validator)
    {
  
         $phoneCheck = \app\models\RequestOrder::find()
        ->andWhere(['or',
            ['phone'=>$this->extra_phone],
            ['extra_phone'=>$this->phone],
            ['phone'=>$this->phone],
            ['extra_phone'=>$this->extra_phone]
        ])
        ->andWhere(['not', ['extra_phone' => ""]])
        ->andWhere(['not', ['extra_phone' => null]])
        ->one();

        if ( $phoneCheck != null  ) {
            $this->addError($attribute, Yii::t('app','  '));
        }
    }

    public function validateUniqeOnUpdate($attribute, $params, $validator)
    {
         $phoneCheck = \app\models\RequestOrder::find()
         ->orWhere(['phone'=>$this->phone ])
         ->orWhere(['phone'=>$this->extra_phone ])
         ->orWhere(['extra_phone'=>$this->extra_phone])
         ->orWhere(['extra_phone'=>$this->phone])
         ->andWhere(['!=','id',$this->id])
         ->andWhere(['not', ['extra_phone' => ""]])
         ->andWhere(['not', ['extra_phone' => null]])
         ->asArray()
         ->one();

        if ( $phoneCheck != null) {
            $this->addError($attribute, Yii::t('app','Phone number already exist, please use another'));
        }
    }



    public static function getStatus(){
        return [
            '4'=>Yii::t('app','Reconnect'),
            '5'=>Yii::t('app','New service'),
        ];
    }



    public static function getOrderRequestType(){
        return [
            '0'=>Yii::t('app','With use price'),
            '1'=>Yii::t('app','Only this month is free'),
            '2'=>Yii::t('app','As VIP customer'),
            '3'=>Yii::t('app','With temporary usage permission')
        ];
    }


    public static function getTemporaryDays(){
        return [
            '24'=>Yii::t('app','1 day'),
            '48'=>Yii::t('app','2 days'),
            '72'=>Yii::t('app','3 days')
        ];
    }



    public static function getDamageStatus()
    {
        return [
            1=>Yii::t('app','Damage')
        ];
    }



    public static function getPaidDayType()
    {
        return  [
            ''=>Yii::t('app','Select'),
            '0'=>Yii::t('app','First day of month'),
            '1'=>Yii::t('app','Connected day'),
        ];
    }

    public static function getDays()
    {
        return  [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9,
            10 => 10,
            11 => 11,
            12 => 12,
            13 => 13,
            14 => 14,
            15 => 15,
            16 => 16,
            17 => 17,
            18 => 18,
            19 => 19,
            20 => 20,
            21 => 21,
            22 => 22,
            23 => 23,
            24 => 24,
            25 => 25,
            26 => 26,
            27 => 27,
            28 => 28,
            29 => 29,
            30 => 30,
            31 => 31
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'district_id']);
    }
    public function getLocations()
    {
        return $this->hasOne(Locations::className(), ['id' => 'location_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }

    public function getUserPhotos()
    {
        return $this->hasMany(UserPhotos::className(), ['user_id' => 'id']);
    }

}

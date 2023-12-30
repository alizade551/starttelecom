<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;

/**
 * This is the model class for table "user_damages".
 *
 * @property int $id
 * @property int $user_id
 * @property string $damage_reason
 * @property string $message
 * @property int $status
 * @property string $created_at
 *
 * @property Users $status0
 */
class UserDamages extends DefaultActiveRecord
{
     public $personals;

    const UPDATE_DAMAG = 'update_damage';
    const ADD_REPORT = 'add_report';


    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'user_damages';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::UPDATE_DAMAG] = ['damage_result','personals','status'];
        $scenarios[self::ADD_REPORT] = ['damage_reason','message'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['damage_result','personals'], 'required'],
            [['damage_result','personals','status'], 'required' , 'on'=>'update_damage'],
            [['damage_reason'], 'required' , 'on'=>'add_report'],
            [['user_id', 'status','member_id','personal'], 'integer'],
            [['message','damage_result'], 'string'],
            [['damage_reason', 'created_at'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],

                [
                    'message', 'required', 'on'=>'add_report','when' => function($model) {

                    if ( isset( $model->damage_reason ) ) {
                        if ( $model->damage_reason == "5" ) {
                             return true;
                        }
                        return false;
                    }
                    return false;

                    },
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t("app","Customer"),
            'personal' => Yii::t("app","Personal"),
            'personals' => Yii::t("app","Personal"),
            'member_id' => Yii::t("app","User"),
            'damage_reason' =>Yii::t("app","Reported reason"),
            'damage_result' =>Yii::t('app','Report result'),
            'message' =>Yii::t("app","More detail"),
            'status' => Yii::t("app","Status"),
            'created_at' =>Yii::t("app","Created at")
        ];
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function getMember()
    {
        return $this->hasOne(\webvimark\modules\UserManagement\models\User::className()::className(), ['id' => 'member_id']);
    }


    public function getDamagePersonals()
    {
        return $this->hasMany(DamagePersonal::className(), ['damage_id' => 'id']);
    }

    public static function getStatus()
    {
        return [
            0 => Yii::t('app','Damaged'),
            1 => Yii::t('app','Solved'),
        ];
    }

    public static function getDamageReason(){
        return [
            0=>Yii::t('app','Internet speed is very low'),
            1=>Yii::t('app','Router was rested'),
            2=>Yii::t('app','Internet dont work'),
            3=>Yii::t('app','Tv dont accept signal'),
            4=>Yii::t('app','Some chanels dont work'),
            5=>Yii::t('app','Other reason'),
        ];
    }


}

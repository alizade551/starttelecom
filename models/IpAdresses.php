<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ip_adresses".
 *
 * @property int $id
 * @property string $public_ip
 * @property string $internal_ip
 * @property int $router_id
 * @property string $type
 * @property int $split
 * @property int $created_at
 *
 * @property Routers $router
 */
class IpAdresses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $start_ip;
    public $end_ip;

   const SCENARIO_CREATE = 'create';

     public function scenarios(){
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['start_ip','end_ip','type','split','router_id'];
        return $scenarios;
    }


    public static function tableName()
    {
        return 'ip_adresses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'router_id','type'], 'required'],
            [['start_ip','end_ip','public_ip','router_id','type'], 'required', 'on' => self::SCENARIO_CREATE],
            ['end_ip', 'compareIp', 'on' => self::SCENARIO_CREATE ],

            ['split', 'required', 'when' => function($model) {
                if ($model->type == "0") {
                   return true;
                }
                return false;

                }
            ],

            [['router_id', 'split', 'created_at'], 'integer'],
            [['start_ip','end_ip'], 'ip'],
            [['type'], 'string'],
            [['public_ip'], 'string', 'max' => 255],
            [['router_id'], 'exist', 'skipOnError' => true, 'targetClass' => Routers::className(), 'targetAttribute' => ['router_id' => 'id']],
        ];
    }


    public function compareIp( $attribute, $params, $validator )
    {
        if ( $this->end_ip )
        {
            $start_ip = sprintf("%u", ip2long($this->start_ip));
            $end_ip = sprintf("%u", ip2long($this->end_ip));

            if ( $start_ip > $end_ip )
            {
                $validator->addError($this, $attribute, Yii::t('app', '{value} ip greather than start ip'));
            }
        }

    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'public_ip' => Yii::t('app', 'Public ip'),
            'router_id' => Yii::t('app', 'Bras'),
            'type' => Yii::t('app', 'Ip type'),
            'split' => Yii::t('app', 'Split'),
            'status' => Yii::t('app', 'Status'),
            'start_ip' => Yii::t('app', 'Start ip'),
            'end_ip' => Yii::t('app', 'End ip'),
            'created_at' => Yii::t('app', 'Created at'),
        ];
    }

    public static function getType()
    {
        return [
            0 => Yii::t('app','Dynamic'),
            1 => Yii::t('app','Static'),
        ];
    }


    public static function getStatus()
    {
        return [
            "0" => Yii::t('app','Free'),
            "1" => Yii::t('app','Busy'),
        ];
    }


    public static function getSplitValues()
    {
        return [
            32 => Yii::t('app','32'),
            47 => Yii::t('app','48'),
            63 => Yii::t('app','64'),
            127 => Yii::t('app','128'),
            255 => Yii::t('app','256'),
            511 => Yii::t('app','512'),
        ];
    }


    public static function checkIp( $startIp,$endIp ){
        $ips = [];
        $data  = [];
        for ($i=$startIp; $i <= $endIp ; $i++) { 
            $ips[] = long2ip($i);
        }
        $model = \app\models\IpAdresses::find()
        ->where(['public_ip'=>$ips])
        ->asArray()
        ->all();
        
        foreach ($model as $key => $value) {
            $data[] = $value['public_ip'];
        }

        if ( $data == null ) {
           return true;
        }
        
        return false;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRouter()
    {
        return $this->hasOne(Routers::className(), ['id' => 'router_id']);
    }
}

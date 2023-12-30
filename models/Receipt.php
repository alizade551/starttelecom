<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "receipt".
 *
 * @property int $id
 * @property string $seria
 * @property int $code
 * @property string $status
 * @property int $created_at
 */
class Receipt extends \yii\db\ActiveRecord
{

   public $start_int;
   public $end_int;
   
   const SCENARIO_UPDATE = 'update';
   const SCENARIO_DEFINE_MEMBER = 'define_member';



    public static function tableName()
    {
        return 'receipt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['seria','start_int','end_int'], 'required'],
            [['seria','start_int','end_int','member_id'], 'required', 'on' => self::SCENARIO_DEFINE_MEMBER],
            [['created_at','start_int','end_int','member_id','number'], 'integer'],
            [['status','type'], 'string'],
            [['seria','code'], 'string', 'max' => 255],
            ['end_int','customValidateStartEnd'],
            ['start_int','customValidateStartEnd'],
        ];
    }


     public function scenarios(){
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['code','status'];
        $scenarios[self::SCENARIO_DEFINE_MEMBER] = ['seria','start_int','end_int','member_id'];
        return $scenarios;
    }




    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'start_int' => Yii::t('app','Start to'),
            'seria' => Yii::t('app','Serial number'),
            'number' => Yii::t('app','Number'),
            'receipt_id' => Yii::t('app','Receipt'),
            'end_int' => Yii::t('app','End to'),
            'code' => Yii::t('app','Receipt number'),
            'member_id' => Yii::t('app','User'),
            'status' => Yii::t('app','Status'),
            'created_at' => Yii::t('app','Created at')
        ];
    }


    public  function getMember()
    {
        return $this->hasOne(\webvimark\modules\UserManagement\models\User::className(), ['id' => 'member_id']);
    }

    public function customValidateStartEnd(){
        if($this->end_int < $this->start_int){
            $this->addError('start_int', Yii::t('app','Start value  must smaller than end') );
            $this->addError('end_int', Yii::t('app','End value  must bigger than start'));
        }
    }



    public static function receiptHave($code,$start,$end){
        $array = [];
        $data  = [];
        for ($i=$start; $i <= $end ; $i++) { 
            $array[] = $code.sprintf("%06d",$i);
        }
        $model = \app\models\Receipt::find()->select('receipt.code')->where(['code'=>$array])->asArray()->all();
        
        foreach ($model as $key => $value) {
            $data[] = $value['code'];
        }
        return $data;
    }

    public static function ReceiptStatus(){
        return [
            0=>Yii::t('app','Free'),
            1=>Yii::t('app','Busy'),
            2=>Yii::t('app','Cancelled'),
  
        ];
    }

    public static function ReceiptType(){
        return [
            0=>Yii::t('app','Internal'),
            1=>Yii::t('app','External'),
        ];
    }

    public static function changeReceiptStatus($receipt_id)
    {
        $model = \app\models\Receipt::find()->where(['id'=>$receipt_id])->one();
        $model->status = '1';
        $model->save(false);
    }


}


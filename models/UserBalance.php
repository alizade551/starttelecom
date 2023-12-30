<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;


class UserBalance extends DefaultActiveRecord
{

    public $balance_for;
    public $per_day_rule;
    public $contract_number;
    public $receipt_checkbox;



    const SCENARIO_API_ADD_BALANCE = 'create';
    const SCENARIO_ADD_DEBIT = 'add_debit';
    const SCENARIO_TRANSFER_AMOUNT = 'transfer_amount';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_balance';
    }


     public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_API_ADD_BALANCE] = ['contract_number','balance_in','per_day_rule'];
        $scenarios[self::SCENARIO_ADD_DEBIT] = ['balance_out','user_id'];
        $scenarios[self::SCENARIO_TRANSFER_AMOUNT] = ['contract_number'];
        return $scenarios;
    }


    public static function PayFor()
    {
        return [
            0=>Yii::t('app','internet'),
            1=>Yii::t('app','tv'),
            2=>Yii::t('app','wifi'),
            3=>Yii::t('app','item'),
            4=>Yii::t('app','voip'),
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'balance_in','bonus_in'], 'required'],
            [['balance_out','user_id'], 'required','on'=>'add_debit'],
            [['contract_number'], 'required','on'=>'transfer_amount'],
            ['contract_number', 'validateContract', 'on'=>'transfer_amount' ],
            [['user_id','created_at','item_usage_id','receipt_id','service_packet_id'], 'integer'],
            [['balance_in', 'balance_out', 'bonus_in' , 'bonus_out'   ], 'double','max' => 9999, 'min' => 0],
            [['payment_method'], 'string'],
            [['receipt_checkbox','contract_number','per_day_rule'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'contract_number' => Yii::t("app","Transferred contact number"),
            'receipt_id'=>Yii::t("app","Receipt name"),
            'transaction'=>Yii::t("app","Transaction"),
            'balance_in' =>  Yii::t("app","Balance in"),
            'balance_out' => Yii::t("app","Balance out"),
            'bonus_in' => Yii::t("app","Bonus in"),
            'bonus_out' => Yii::t("app","Bonus out"),
            'payment_method' =>Yii::t("app","Payment method"), 
            'created_at' =>Yii::t("app","Created at"),
            'receipt_checkbox' =>Yii::t("app","Print ?"),
            'per_day_rule' =>Yii::t("app","Per day rule ?")
            
        ];
    }

    public static function getDropDownListPayFor(){
        $data = [
            0=>Yii::t('app','Internet'),
            1=>Yii::t('app','TV'),
            2=>Yii::t('app','Wi-fi'),
            3=>Yii::t('app','Item'),
            4=>Yii::t('app','VoIP'),
        ];
      
        return $data;
    }

    public static function getDropDownListPaymentMethod(){
        $data = [
            0 => Yii::t('app','Internal'),
            1 => Yii::t('app','External'),
        ];
      
        return $data;
    }

    public static function getDropDownListPaymentStatus(){
        $data = [
            0 => Yii::t('app','Paid'),
            1 => Yii::t('app','Free usage'),
            2 => Yii::t('app','Cancelled'),
        ];
      
        return $data;
    }

    

    public function validateContract( $attribute, $params, $validator )
    {
        if ( $this->contract_number )
        {
            $userModel = \app\models\Users::find()
            ->where(['contract_number'=>$this->contract_number])
            ->asArray()
            ->one();
            if ( $userModel == null )
            {
                $validator->addError($this, $attribute, Yii::t('app', 'Contract number not found'));
            }
        }

    }


    public function getUserServicePacket()
    {
        return $this->hasOne(UsersServicesPackets::className(), ['id' => 'service_packet_id']);
    }
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function getItemUsage()
    {
        return $this->hasOne(ItemUsage::className(), ['id' => 'item_usage_id']);
      
    }


    public static function BonusOut( $user_id, $tariff, $status, $pay_for, $payment_method, $service_packet_id = false ){

        $model =  new \app\models\UserBalance;
        $model->user_id = $user_id;
        $model->bonus_out =  $tariff;
        $model->receipt_id =  null;
        $model->item_usage_id =  null;
        $model->pay_for =  $pay_for;
        if ( $service_packet_id == false ) {
            $model->service_packet_id =  null;
        }else{
            $model->service_packet_id =  $service_packet_id;
        }
        $model->payment_method =  $payment_method;
        $model->status =  $status;
        $model->created_at =  time();
        if ($model->save(false)) {
            if ( $user_id != null ) {
                $userModel = \app\models\Users::find()->where(['id'=>$user_id])->one();
                $userModel->last_payment = time();
                $userModel->save(false);
            }
        }
    }
    



    public static function BalanceOut( $user_id, $tariff, $created_at, $status, $pay_for, $payment_method, $receipt_id, $service_packet_id = false, $item_usage_id = false ){

        $model =  new \app\models\UserBalance;
        $model->user_id = $user_id;
        $model->balance_out =  $tariff;
        $model->receipt_id =  $receipt_id;
        $model->pay_for =  $pay_for;
        if ( $service_packet_id == false ) {
            $model->service_packet_id =  null;
        }else{
            $model->service_packet_id =  $service_packet_id;
        }
        if ( $item_usage_id == false ) {
            $model->item_usage_id =  null;
        }else{
           $model->item_usage_id =  $item_usage_id;
        }
        $model->payment_method =  $payment_method;
        $model->status =  $status;
        $model->created_at =  $created_at;
        if ($model->save(false)) {
            if ( $user_id != null ) {
                $userModel = \app\models\Users::find()->where(['id'=>$user_id])->one();
                $userModel->last_payment = $model->created_at;
                $userModel->save(false);
            }
        }

    }
    
    public static function BalanceAdd( $user_id, $amount, $created_at, $payment_method, $receipt_id = false ){
        $model =  new \app\models\UserBalance;
        $model->user_id = $user_id;
        $model->balance_in =  $amount;
        if ($receipt_id != false) {
            $model->receipt_id =  $receipt_id;
        }else{
            $model->receipt_id =  null;
        }
        $model->item_usage_id =  null;
        $model->pay_for =  null;
        $model->service_packet_id =  null;
        $model->payment_method =  $payment_method;
        $model->created_at =  $created_at;
        $model->save(false);
    }

    public static function CalcUserTariffDaily( $user_id, $daily_calc = false, $half_month = false ){
        $userModel = \app\models\Users::find()
        ->where(['users.id'=>$user_id])
        ->asArray()
        ->one();

        $userPacketsModel = \app\models\UsersServicesPackets::find()
        ->where([ 'user_id' => $userModel['id'] ])
        ->all();


        if ( $userPacketsModel == null ) {
            return [
                'per_total_tariff' => 0, 
                'service_tariff_array' => []
            ];
        }

        $user_credit = \app\models\ItemUsage::find()
        ->select('item_usage.*,item_stock.price as item_price')
        ->leftJoin('item_stock','item_stock.id=item_usage.item_stock_id')
        ->where(['status' => '6', 'credit' => '1', 'user_id' => $userModel['id'] ])
        ->asArray()
        ->all();
    
        $credit_tariff = 0;
        $service_tariff = 0;
        $service_tariff_array = [];
        $result = [];

        foreach ( $user_credit as $key => $credit_one ) {
            $credit_tariff += ceil( $credit_one['item_price'] / $credit_one['month'] );
        }

        if ( $daily_calc == true && $userModel['paid_time_type'] == "0" ) {

            $current_day = date("d");
            $month_day = date("t");
            $diff = ( $month_day - date("d") ) + 1;


            foreach ($userPacketsModel as $key => $packet_one) {

                if ( $packet_one->price != null || $packet_one->price != 0 ) {
                    $service_tariff += round( ( $packet_one->price   / $month_day ) * $diff , 1);
                    $service_tariff_array[$packet_one->service->service_alias][$key]['packet_price'] = round( ( $packet_one->price / $month_day ) * $diff , 1);
                }else{
                    $service_tariff += round( ( ( $packet_one->packet->packet_price  / $month_day ) * $diff ), 1);
                    $service_tariff_array[$packet_one->service->service_alias][$key]['packet_price'] = round( ( ( $packet_one->packet->packet_price / $month_day ) * $diff), 1);
                }
                

                $service_tariff_array[$packet_one->service->service_alias][$key]['packet_name'] = $packet_one->packet->packet_name;
                $service_tariff_array[$packet_one->service->service_alias][$key]['service_alias'] = $packet_one->service->service_alias;
                $service_tariff_array[$packet_one->service->service_alias][$key]['user_id'] = $packet_one->user_id;
                $service_tariff_array[$packet_one->service->service_alias][$key]['u_s_p_i'] = $packet_one->id;
                $service_tariff_array[$packet_one->service->service_alias][$key]['packet_id'] = $packet_one->id;
            }
        } elseif ( $half_month == true && $userModel['paid_time_type'] == "0" ) {
            $factor = (  date("d") >= 15 ) ? 0.5 : 1;
            $month_day = date("t");
            foreach ($userPacketsModel as $key => $packet_one) {
                if ( $packet_one->price != null || $packet_one->price != 0 ) {
                    $service_tariff += round( $packet_one->price * $factor, 1 );
                    $service_tariff_array[$packet_one->service->service_alias][$key]['packet_price'] = round( $packet_one->price  * $factor , 1 );
                }else{
                    $service_tariff += round( $packet_one->packet->packet_price * $factor, 1 );
                    $service_tariff_array[$packet_one->service->service_alias][$key]['packet_price'] = round( $packet_one->packet->packet_price * $factor , 1);
                }
            
                $service_tariff_array[$packet_one->service->service_alias][$key]['packet_name'] = $packet_one->packet->packet_name;
                $service_tariff_array[$packet_one->service->service_alias][$key]['service_alias'] = $packet_one->service->service_alias;
                $service_tariff_array[$packet_one->service->service_alias][$key]['user_id'] = $packet_one->user_id;
                $service_tariff_array[$packet_one->service->service_alias][$key]['u_s_p_i'] = $packet_one->id;
                $service_tariff_array[$packet_one->service->service_alias][$key]['packet_id'] = $packet_one->id;
            }
        } else {

            foreach ($userPacketsModel as $key => $packet_one) {

                if ( $packet_one->price != null || $packet_one->price != 0 ) {
                    $service_tariff += round( $packet_one->price , 1 );
                    $service_tariff_array[$packet_one->service->service_alias][$key]['packet_price'] = round( $packet_one->price , 1 );
                }else{
                    $service_tariff += round( $packet_one->packet->packet_price , 1 );
                    $service_tariff_array[$packet_one->service->service_alias][$key]['packet_price'] = round( $packet_one->packet->packet_price , 1 );                        
                }

                $service_tariff_array[$packet_one->service->service_alias][$key]['packet_name'] = $packet_one->packet->packet_name;
                $service_tariff_array[$packet_one->service->service_alias][$key]['service_alias'] = $packet_one->service->service_alias;
                $service_tariff_array[$packet_one->service->service_alias][$key]['user_id'] = $packet_one->user_id;
                $service_tariff_array[$packet_one->service->service_alias][$key]['u_s_p_i'] = $packet_one->id;
                $service_tariff_array[$packet_one->service->service_alias][$key]['packet_id'] = $packet_one->id;
            }
        }

        return $result = [
            'per_total_tariff' => round( $service_tariff + $credit_tariff, 2 ), 
            'services_total_tariff' => round( $service_tariff, 2 ), 
            'credit_tariff' =>round( $credit_tariff, 2 ), 
            'service_tariff_array' => $service_tariff_array
        ];

    }
    /*
        return user current balance
    */
    public static function CalcUserTotalBalance( $userId ){
       $model = \app\models\UserBalance::find()
       ->where(['user_id'=>$userId])
       ->andWhere(['status'=>'0'])
       ->asArray()
       ->all();

       $totalBalanceIn = 0;
       $totalBalanceOut = 0;
       foreach ( $model as $transactionKey => $transaction ) {
          $totalBalanceIn += $transaction['balance_in'];
          $totalBalanceOut += $transaction['balance_out'];
       }
       $balance = $totalBalanceIn - $totalBalanceOut;

       return $balance;
    }

    
    /*
        return user current bonus balance
    */    
    public static function CalcUserTotalBonus( $userId ){
       $model = \app\models\UserBalance::find()
       ->where(['user_id'=>$userId])
       ->asArray()
       ->all();

       $totalBonusIn = 0;
       $totalBonusOut = 0;

       foreach ( $model as $transactionKey => $transaction ) {
          $totalBonusIn += $transaction['bonus_in'];
          $totalBonusOut += $transaction['bonus_out'];
       }
       
       $bonus = $totalBonusIn - $totalBonusOut;
       
       return $bonus;
    }


}

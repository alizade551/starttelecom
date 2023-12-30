<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_credit".
 *
 * @property int $id
 * @property int $user_id
 * @property int $item_usage_id
 * @property int $paid
 * @property int $paid_at
 *
 * @property Users $user
 * @property ItemUsage $itemUsage
 */
class UsersCredit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_credit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'item_usage_id', 'paid', 'paid_at'], 'required'],
            [['user_id', 'item_usage_id', 'paid', 'paid_at'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['item_usage_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemUsage::className(), 'targetAttribute' => ['item_usage_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'Customer'),
            'item_usage_id' => Yii::t('app', 'Item'),
            'paid' => Yii::t('app', 'Paid'),
            'paid_at' => Yii::t('app', 'Paid at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }


    public static function CheckAndAddCreditHistory( $userId, $paymentMethod, $receiptId ){

        $creditItems = \app\models\ItemUsage::find()
        ->select('item_usage.*,item_stock.price as item_stock_price')
        ->leftJoin('item_stock','item_stock.id=item_usage.item_stock_id')
        ->where(['item_usage.user_id'=>$userId,'item_usage.credit'=>'1','item_usage.status'=>'6'])
        ->asArray()
        ->all();

       if ( count( $creditItems ) > 0 ) {
            foreach ( $creditItems as $key => $creditItem ) {
              $creditTariff = round( ceil( ( $creditItem['item_stock_price'] * $creditItem['quantity'] ) / $creditItem['month'] ), 2 );
              $total_price =  $creditTariff * $creditItem['month'] * $creditItem['quantity'];

              $userCreditModel =  new \app\models\UsersCredit;
              $userCreditModel->user_id = $userId;
              $userCreditModel->item_usage_id = $creditItem['id'];
              $userCreditModel->paid =  $creditTariff;
              $userCreditModel->paid_at =  time();
              if ($userCreditModel->save(false)) {
                \app\models\ItemUsage::getCheckCredit( $userId, $creditItem['id'], $total_price );
                    
                $pay_for = 3;
                 \app\models\UserBalance::BalaceOut(
                    $userId, 
                    $creditTariff,
                    0,
                    $pay_for,
                    $paymentMethod,
                    $receiptId,
                    false,
                    $creditItem['id']
                );
              }
            }
       }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemUsage()
    {
        return $this->hasOne(ItemUsage::className(), ['id' => 'item_usage_id']);
    }
}

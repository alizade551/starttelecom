<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;
/**
 * This is the model class for table "item_usage".
 *
 * @property int $id
 * @property int $item_id
 * @property int $item_stock_id
 * @property int $user_id
 * @property int $credit
 * @property int $month
 * @property string $mac_address
 * @property int $location_id
 * @property int $status
 * @property int $created_at
 *
 * @property Items $item
 * @property ItemStock $itemStock
 * @property UsersCredit[] $usersCredits
 * @property UsersGifts[] $usersGifts
 */
class ItemUsage extends DefaultActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item_usage';
    }


    public $personals;
    public $city;
    public $district;

    const SCENARIO_USE_ITEM = 'useful_count';
    const SCENARIO_USE_ITEM_TO_USER = 'useful_count_to_user';

     public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_USE_ITEM] = ['item_id','item_stock_id','created_at','personals','location_id','city','district','status','mac_address','quantity'];
        $scenarios[self::SCENARIO_USE_ITEM_TO_USER] = ['item_id','item_stock_id','created_at','personals','status','quantity','mac_address','month'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'item_stock_id', 'user_id', 'credit', 'month', 'location_id', 'status'], 'integer'],
            [['item_stock_id'], 'required'],
            [['item_stock_id','item_id','city','district','location_id','quantity','personals','status','created_at'], 'required','on'=>'useful_count'],
            [['item_stock_id','item_id','quantity','personals','status','created_at'], 'required','on'=>'useful_count_to_user'],
            [['mac_address'], 'string', 'max' => 255],
            [['mac_address'], 'validateMacAddress'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Items::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['item_stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemStock::className(), 'targetAttribute' => ['item_stock_id' => 'id']],
            ['quantity','customValidateQuantity'],

            [
                'mac_address', 'required', 'when' => function($model) {
                    if( isset(  $this->item->category->mac_address_validation ) ){
                        if ( $this->item->category->mac_address_validation == "1") {
                           return true;
                        }
                    }
                return false;

                }
            ],
           [
                'month', 'required', 'when' => function($model) {
                if ( $this->status == "4" || $this->status == "6") {
                   return true;
                }
                return false;

                }
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'item_id' => Yii::t('app', 'Item'),
            'quantity' => Yii::t('app', 'Quantity'),
            'item_stock_id' => Yii::t('app', 'Item stock'),
            'user_id' => Yii::t('app', 'User'),
            'credit' => Yii::t('app', 'Credit'),
            'month' => Yii::t('app', 'Month count'),
            'mac_address' => Yii::t('app', 'Mac address'),
            'personals' => Yii::t('app', 'Personal'),
            'city' => Yii::t('app', 'City'),
            'district' => Yii::t('app', 'District'),
            'location_id' => Yii::t('app', 'Location'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Installation time'),
        ];
    }


    public function validateMacAddress( $attribute, $params, $validator )
    {
        if ( $this->mac_address )
        {
         $pattern = '/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/';

            if ( !preg_match( $pattern, $this->mac_address ) ) {
                $validator->addError($this, $attribute, Yii::t('app', 'Incorrect mac address format'));
                return false;
            }
      
            $macExsistOnUsage = \app\models\ItemUsage::find()
            ->where(['mac_address'=>$this->mac_address])
            ->count();


            if ( $macExsistOnUsage > 0   )
            {
                $validator->addError($this, $attribute, Yii::t('app', 'Mac address already exist'));
                return false;
            }


        }
    }

    public function customValidateQuantity(){
    
        if ( $this->itemStock->quantity < $this->quantity  ) {
            $this->addError('quantity',Yii::t('app','Out of stock,Max stock is {stock}',['stock'=>$this->itemStock->quantity ]));
        }


        if ( is_numeric( $this->quantity ) ) {
            if ( $this->quantity <= 0 ) {
                $this->addError('quantity',Yii::t('app','Quantity must be a positive number.'));
            }
            if( filter_var( $this->quantity, FILTER_VALIDATE_INT) == false  ){

                if ( $this->itemStock->item->category->unit_type == 0 ) {
                    $this->addError('quantity',Yii::t('app','Quantity must be an integer.'));
                }

            }
        }else{
             $this->addError('quantity',Yii::t('app','Quantity cannot be string.'));
        }
    }


    public static function getItemStatus()
    {
        return [
            0=>Yii::t('app','Paid (used for initial Setup)'),
            1=>Yii::t('app','Paid'),
            2=>Yii::t('app','Free (used for initial Setup)'),
            3=>Yii::t('app','Free usage'),
            4=>Yii::t('app','Gift'),
            6=>Yii::t('app','Credit'),
        ];
    }

    public static function StoreItemStatus()
    {
        return [
            0=>Yii::t('app','Paid (used for initial Setup)'),
            1=>Yii::t('app','Paid'),
            2=>Yii::t('app','Free (used for initial Setup)'),
            3=>Yii::t('app','Free'),
            4=>Yii::t('app','Gift'),
            5=>Yii::t('app','Internal use of the company'),
            6=>Yii::t('app','Credit'),
        ];
    }


    public static function getMonth()
    {
        return [
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
            12 => 12
        ];
    }

    public static function getItemCompanyStatus()
    {
        return [
            5 =>Yii::t('app','Internal use of the company'),
        ];
    }


    public static function getItemCredit()
    {
        return [
            0 => Yii::t('app','Credit (finshed)'),
            1 => Yii::t('app','Credit (continue)'),
            2 => Yii::t('app','Gift (continue)'),
            3 => Yii::t('app','Gift (finshed)')
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */


    public static function  getCheckCredit( $userId, $itemUsageId, $totalPrice  )
    {
          $userCredit = \app\models\UsersCredit::find()
          ->where(['item_usage_id'=>$itemUsageId])
          ->andWhere(['user_id'=>$userId])
          ->asArray()
          ->all();

          if ( $userCredit != null ) {
                $totalPaid = 0;
                foreach ( $userCredit as $key => $credit ) {
                    $totalPaid += $credit['paid'];
                }
                    $itemUsage = \app\models\ItemUsage::find()
                    ->where(['id'=>$itemUsageId])
                    ->andWhere(['user_id'=>$userId])
                    ->one();
                    $itemTariff = round( ceil( ( $itemUsage->itemStock->price * $itemUsage->quantity ) / $itemUsage->month ), 2 );
                if( $totalPaid >= $totalPrice ){
                    $itemUsage->credit = '0';
                   if ( $itemUsage->save(false) ) {
                      $userModel = \app\models\Users::find()
                      ->where(['id'=>$userId])
                      ->one();
                      $userModel->tariff =  round( $userModel->tariff  -  $itemTariff, 2 );
                      $userModel->save( false );
                   }
                }
          }
    }


    public static function CheckGifts( $userId, $itemUsageId, $paidCount  = 9 )
    {
          $userGiftCount = \app\models\UsersGifts::find()
          ->where(['item_usage_id'=>$itemUsageId])
          ->andWhere(['user_id'=>$userId])
          ->count();

          if ( $userGiftCount > 0 ) {
                if( $userGiftCount >= $paidCount ){
                    $itemUsageModel = \app\models\ItemUsage::find()
                    ->where(['id'=>$itemUsageId])
                    ->andWhere(['user_id'=>$userId])
                    ->andWhere(['status'=>'4'])
                    ->one();
                    $itemUsageModel->credit = '3';
                    $itemUsageModel->save(false);
                }
          }
    }





    public static function getCalcUserTariff( $userId, $creditTariff )
    {
          $userModel = \app\models\Users::find()
          ->where(['id'=>$userId])
          ->one();
          
          $total_tariff = $userModel->tariff + $creditTariff;
          $userModel->tariff = $total_tariff;
          $userModel->save(false);
    }


    public static function calcDeletedStock( $itemStockId, $quantity )
    {
        $model = \app\models\ItemStock::findOne( $itemStockId );
        $model->quantity = round( $model->quantity + $quantity,2 );
       if ( $model->save(false) ) {
           \app\models\ItemStock::calcTotalStock( $model->item_id );
       }
    }


    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }



    public static function getItemUsagePersonals($itemUsageId)
    {
        $model = \app\models\ItemUsagePersonal::find()
        ->select('item_usage_personal.*,members.fullname as personal_name')
        ->leftJoin('members','members.id=item_usage_personal.personal_id')
        ->where(['item_usage_id' => $itemUsageId])
        ->asArray()
        ->all();
        $personals = '';
        foreach ( $model as $key => $personal ) {
         $personals .= $personal['personal_name'].", ";
        }
        return  mb_substr( $personals, 0,-2, "utf-8");
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemStock()
    {
        return $this->hasOne(ItemStock::className(), ['id' => 'item_stock_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersCredits()
    {
        return $this->hasMany(UsersCredit::className(), ['item_usage_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersGifts()
    {
        return $this->hasMany(UsersGifts::className(), ['item_usage_id' => 'id']);
    }
}

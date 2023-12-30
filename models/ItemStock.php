<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "item_stock".
 *
 * @property int $id
 * @property int $item_id
 * @property int $warehouse_id
 * @property int $quantity
 * @property double $price
 * @property int $updated_at
 * @property int $created_at
 *
 * @property Items $item
 * @property Warehouses $warehouse
 * @property ItemUsage[] $itemUsages
 * @property StoreItemUsagePersonal[] $storeItemUsagePersonals
 */
class ItemStock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item_stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'quantity', 'price'], 'required'],
            [['item_id', 'warehouse_id', 'updated_at', 'created_at'], 'integer'],
            [['price'], 'number'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Items::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouses::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
            ['quantity','customValidateQuantity'],
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
            'warehouse_id' => Yii::t('app', 'Warehouse'),
            'quantity' => Yii::t('app', 'Quantity'),
            'price' => Yii::t('app', 'Price'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }


    public function customValidateQuantity(){
    
        if ( is_numeric( $this->quantity ) ) {
            if ( $this->quantity <= 0 ) {
                $this->addError('quantity',Yii::t('app','Quantity must be a positive number.'));
            }
            if( filter_var( $this->quantity, FILTER_VALIDATE_INT) == false  ){

                if ( $this->item->category->unit_type == 0 ) {
                    $this->addError('quantity',Yii::t('app','Quantity must be an integer.'));
                }

            }
        }else{
             $this->addError('quantity',Yii::t('app','Quantity cannot be string.'));
        }
    }


    public static function calcTotalStock( $itemId ){
       $allStock = \app\models\ItemStock::find()
       ->where(['item_id'=>$itemId])
       ->andWhere(['!=', 'quantity', 0 ])
       ->asArray()
       ->all();
       $totalStock  = 0;
       foreach ($allStock as $stockKey => $stock) {
           $totalStock  += $stock['quantity'];
       }
       $itemModel = \app\models\Items::find()->where(['id'=>$itemId])->one();
       $itemModel->total_stock = $totalStock;
       $itemModel->save( false );
    }



    public static function updateStock( $itemStockId, $itemId, $quantity = false ){
       $model = \app\models\ItemStock::find()
       ->where(['id'=>$itemStockId])
       ->one();

       if ( $quantity != false ) {
           $updatedStock =  round( $model->quantity - $quantity , 2 );
           $model->quantity =  $updatedStock;
          if ( $model->save(false) ) {
              \app\models\ItemStock::calcTotalStock( $itemId );
          }
       }
        \app\models\ItemStock::calcTotalStock( $itemId );
    }




    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouses::className(), ['id' => 'warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemUsages()
    {
        return $this->hasMany(ItemUsage::className(), ['item_stock_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoreItemUsagePersonals()
    {
        return $this->hasMany(StoreItemUsagePersonal::className(), ['item_stock_id' => 'id']);
    }
}

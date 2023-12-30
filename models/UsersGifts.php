<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_gifts".
 *
 * @property int $id
 * @property int $item_usage_id
 * @property int $user_id
 * @property int $created_at
 *
 * @property ItemUsage $itemUsage
 * @property Users $user
 */
class UsersGifts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_gifts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_usage_id', 'user_id', 'created_at'], 'required'],
            [['item_usage_id', 'user_id', 'created_at'], 'integer'],
            [['item_usage_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemUsage::className(), 'targetAttribute' => ['item_usage_id' => 'id']],
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
            'item_usage_id' => 'Item Count ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemCount()
    {
        return $this->hasOne(ItemUsage::className(), ['id' => 'item_usage_id']);
    }

    
    public static function checkAndAddGiftHistory( $userId )
    {
        $gifts = \app\models\ItemUsage::find()
        ->where(['user_id'=>$userId,'status'=>'4','credit'=>'2'])
        ->asArray()
        ->all();

        if ( count( $gifts ) > 0 ) {
          foreach ( $gifts as $giftKey => $gift ) {
            \app\models\Users::AddGift( $userId, $gift['id'] );
            \app\models\ItemUsage::CheckGifts( $userId, $gift['id'], $gift['month'] );
          }
        }
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}


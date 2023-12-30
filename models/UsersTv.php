<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_tv".
 *
 * @property int $id
 * @property int $user_id
 * @property int $packet_id
 * @property string $card_number
 * @property int $status
 * @property string $created_at
 *
 * @property Users $user
 * @property ServicePackets $packet
 */
class UsersTv extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_tv';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'packet_id', 'card_number'], 'required'],
            [['user_id', 'packet_id', 'status','u_s_p_i'], 'integer'],
            [['card_number'], 'string', 'max' => 255],
            [['created_at'], 'string', 'max' => 11],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['packet_id'], 'exist', 'skipOnError' => true, 'targetClass' => Packets::className(), 'targetAttribute' => ['packet_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'packet_id' => 'Packet ID',
            'card_number' => 'Card Number',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public static function turnOnTvAccess($user_id,$services_packets_id)
    {
       $model_tv = \app\models\UsersTv::find()->where(['user_id'=>$user_id,'u_s_p_i'=>$services_packets_id])->one();
       $model_tv->status = 1;
       $model_tv->save(false);
    }



    public  function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacket()
    {
        return $this->hasOne(Packets::className(), ['id' => 'packet_id']);
    }
}

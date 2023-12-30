<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_voip".
 *
 * @property int $id
 * @property int $user_id
 * @property int $packet_id
 * @property int $u_s_p_i
 * @property int $phone_number
 * @property int $status
 * @property int $created_at
 *
 * @property Users $user
 */
class UsersVoip extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_voip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'packet_id', 'u_s_p_i', 'phone_number', 'status', 'created_at'], 'integer'],
            [['phone_number'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'packet_id' => Yii::t('app', 'Packet'),
            'u_s_p_i' => Yii::t('app', 'U S P I'),
            'phone_number' => Yii::t('app', 'Phone number'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function getPacket()
    {
        return $this->hasOne(Packets::className(), ['id' => 'packet_id']);
    }



    public static function getPacketStatus()
    {
        return [
            0=>Yii::t("app","Pending"),
            1=>Yii::t("app","Active"),
            2=>Yii::t("app","Deactive"),
            3=>Yii::t("app","Archive")
        ];
    }


}

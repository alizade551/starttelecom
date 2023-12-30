<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_wifi".
 *
 * @property int $id
 * @property int $user_id
 * @property int $packet_id
 * @property string $login
 * @property string $password
 * @property int $status
 * @property string $created_at
 *
 * @property Users $user
 * @property ServicePackets $packet
 */
class UsersWifi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_wifi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'packet_id', 'login', 'password'], 'required'],
            [['user_id', 'packet_id', 'status','u_s_p_i'], 'integer'],
            [['login'], 'string', 'max' => 50],
            [['password', 'created_at'], 'string', 'max' => 255],
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
            'login' => 'Login',
            'password' => 'Password',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public static function turnOnWifiAccess($user_id,$services_packets_id)
    {
        $model_wifi = \app\models\UsersWifi::find()->where(['user_id'=>$user_id,'u_s_p_i'=>$services_packets_id])->one();
        $model_wifi->status = 1;
        $model_wifi->save(false);
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
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


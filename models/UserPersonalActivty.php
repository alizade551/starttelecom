<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_personal_activty".
 *
 * @property int $id
 * @property int $user_id
 * @property int $member_id
 * @property string $activty_type
 * @property int $created_at
 *
 * @property Members $member
 * @property Users $user
 */
class UserPersonalActivty extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_personal_activty';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'member_id', 'created_at'], 'required'],
            [['user_id', 'member_id', 'created_at'], 'integer'],
            [['activty_type'], 'string'],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Members::className(), 'targetAttribute' => ['member_id' => 'id']],
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
            'user_id' => 'User ID',
            'member_id' => 'Member ID',
            'activty_type' => 'Activty Type',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Members::className(), ['id' => 'member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}

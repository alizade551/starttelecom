<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;
/**
 * This is the model class for table "users_note".
 *
 * @property int $id
 * @property int $user_id
 * @property string $member_name
 * @property string $note
 * @property int $time
 *
 * @property Users $user
 */
class UsersNote extends DefaultActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_note';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'member_name', 'note', 'time'], 'required'],
            [['user_id', 'time'], 'integer'],
            [['note'], 'string'],
            [['member_name'], 'string', 'max' => 255],
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
            'user_id' => Yii::t('app','Customer'),
            'member_name' => Yii::t('app','User'),
            'note' =>Yii::t('app','Note'),
            'time' => Yii::t('app','Created at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}

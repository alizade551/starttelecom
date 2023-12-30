<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;
/**
 * This is the model class for table "users_history".
 *
 * @property int $id
 * @property int $user_id
 * @property string $text
 * @property string $time
 *
 * @property Users $user
 */
class UsersHistory extends DefaultActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'text', 'time'], 'required'],
            [['user_id'], 'integer'],
            [['text', 'time','member'], 'string', 'max' => 255],
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
            'member' => Yii::t('app','User'),
            'text' => Yii::t('app','Text'),
            'time' => Yii::t('app','Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser(){
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public static function AddHistory( $user_id, $member, $text, $time )
    {
      $user_history = new \app\models\UsersHistory;
      $user_history->user_id = $user_id;
      $user_history->member = $member;
      $user_history->text = $text;
      $user_history->time = $time;
      $user_history->save(false);
    }

}

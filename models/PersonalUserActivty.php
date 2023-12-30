<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "personal_user_activty".
 *
 * @property int $id
 * @property int $activty_id
 * @property int $user_id
 * @property int $member_id
 *
 * @property Members $member
 * @property Users $user
 * @property PersonalActivty $activty
 */
class PersonalUserActivty extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personal_user_activty';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activty_id', 'member_id'], 'required'],
            [['activty_id', 'member_id'], 'integer'],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => Members::className(), 'targetAttribute' => ['member_id' => 'id']],
            [['activty_id'], 'exist', 'skipOnError' => true, 'targetClass' => PersonalActivty::className(), 'targetAttribute' => ['activty_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'activty_id' => Yii::t('app', 'Activty'),
            'user_id' => Yii::t('app', 'Customer'),
            'member_id' => Yii::t('app', 'User'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(\webvimark\modules\UserManagement\models\User::className(), ['id' => 'member_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivty()
    {
        return $this->hasOne(PersonalActivty::className(), ['id' => 'activty_id']);
    }
}

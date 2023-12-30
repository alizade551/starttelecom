<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "member_location".
 *
 * @property int $id
 * @property int $member_id
 * @property string $city_id
 * @property string $district_id
 *
 * @property Members $member
 */
class MemberLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'member_location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id','district_id' ], 'required'],
            [['member_id'], 'integer'],
            [['city_id', 'district_id'], 'safe'],
            [['member_id'], 'exist', 'skipOnError' => true, 'targetClass' => \webvimark\modules\UserManagement\models\User::className(), 'targetAttribute' => ['member_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => Yii::t('app','User'),
            'city_id' => Yii::t('app','City'),
            'district_id' => Yii::t('app','District'),
            'location_id' => Yii::t('app','Location'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Members::className(), ['id' => 'member_id']);
    }
}

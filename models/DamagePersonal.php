<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "damage_personal".
 *
 * @property int $id
 * @property int $damage_id
 * @property int $personal_id
 */
class DamagePersonal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'damage_personal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['damage_id', 'personal_id'], 'required'],
            [['damage_id', 'personal_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'damage_id' => Yii::t('app', 'Damage'),
            'personal_id' => Yii::t('app', 'Personal fullname'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDamage()
    {
        return $this->hasOne(UserDamages::className(), ['id' => 'damage_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonal()
    {
        return $this->hasOne(\webvimark\modules\UserManagement\models\User::className(), ['id' => 'personal_id']);
    }


}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bonus_except_packets".
 *
 * @property int $id
 * @property int $bonus_id
 * @property int $packet_id
 *
 * @property Bonus $bonus
 * @property ServicePackets $packet
 */
class BonusExceptPackets extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonus_except_packets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bonus_id', 'packet_id'], 'required'],
            [['bonus_id', 'packet_id'], 'integer'],
            [['bonus_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bonus::className(), 'targetAttribute' => ['bonus_id' => 'id']],
            [['packet_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServicePackets::className(), 'targetAttribute' => ['packet_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'bonus_id' => Yii::t('app', 'Bonus'),
            'packet_id' => Yii::t('app', 'Packet'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBonus()
    {
        return $this->hasOne(Bonus::className(), ['id' => 'bonus_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacket()
    {
        return $this->hasOne(ServicePackets::className(), ['id' => 'packet_id']);
    }
}

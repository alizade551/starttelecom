<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "routers_service_packets".
 *
 * @property int $id
 * @property int $router_id
 * @property int $packet_id
 *
 * @property Routers $router
 * @property ServicePackets $packet
 */
class RoutersServicePackets extends \yii\db\ActiveRecord
{
    public $packets;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'routers_service_packets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['router_id', 'packets'], 'required'],
            [['router_id', 'packet_id'], 'integer'],
            [['packets'], 'safe'],
            [['status'], 'string'],
            [['router_id'], 'exist', 'skipOnError' => true, 'targetClass' => Routers::className(), 'targetAttribute' => ['router_id' => 'id']],
            [['packet_id'], 'exist', 'skipOnError' => true, 'targetClass' => Packets::className(), 'targetAttribute' => ['packet_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'router_id' => Yii::t('app', 'Router name'),
            'packet_id' => Yii::t('app', 'Packet name'),
            'packets' => Yii::t('app', 'Packets'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRouter()
    {
        return $this->hasOne(Routers::className(), ['id' => 'router_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacket()
    {
        return $this->hasOne(Packets::className(), ['id' => 'packet_id']);
    }
}

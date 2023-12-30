<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "item_usage_personal".
 *
 * @property int $id
 * @property int $item_usage_id
 * @property int $personal_id
 *
 * @property ItemUsage $itemUsage
 * @property Members $personal
 */
class ItemUsagePersonal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item_usage_personal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_usage_id', 'personal_id'], 'integer'],
            [['item_usage_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemUsage::className(), 'targetAttribute' => ['item_usage_id' => 'id']],
            [['personal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Members::className(), 'targetAttribute' => ['personal_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'item_usage_id' => Yii::t('app', 'Item Usage ID'),
            'personal_id' => Yii::t('app', 'Personal ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemUsage()
    {
        return $this->hasOne(ItemUsage::className(), ['id' => 'item_usage_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonal()
    {
        return $this->hasOne(Members::className(), ['id' => 'personal_id']);
    }
}

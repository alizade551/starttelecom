<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bonus".
 *
 * @property int $id
 * @property int $month_count
 * @property int $factor
 * @property string $published
 * @property int $created_at
 */
class Bonus extends \yii\db\ActiveRecord
{
    public $packets;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','month_count','factor','published'], 'required'],
            [['name', 'month_count'], 'unique'],
            [['month_count', 'created_at'], 'integer'],
            [['factor'], 'number'],
            [['published'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app','Bonus name'),
            'month_count' => Yii::t('app','Month count'),
            'factor' => Yii::t('app','Factor'),
            'packets' => Yii::t('app','Packet except'),
            'published' => Yii::t('app','Published'),
            'created_at' => Yii::t('app','Created at'),
        ];
    }
}

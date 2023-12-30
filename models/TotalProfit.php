<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "total_profit".
 *
 * @property int $id
 * @property double $amount
 * @property int $created_at
 */
class TotalProfit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'total_profit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'created_at'], 'required'],
            [['amount'], 'number'],
            [['created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'amount' => Yii::t('app', 'Amount'),
            'created_at' => Yii::t('app', 'Created at'),
        ];
    }
}

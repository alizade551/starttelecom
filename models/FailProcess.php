<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fail_process".
 *
 * @property int $id
 * @property string $action
 * @property int $member_id
 * @property string $params
 * @property string $status
 * @property int $created_at
 */
class FailProcess extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fail_process';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'created_at'], 'integer'],
            [['params', 'status'], 'string'],
            [['action'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'action' => Yii::t('app', 'Action'),
            'member_id' => Yii::t('app', 'Member'),
            'params' => Yii::t('app', 'Params'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created at'),
        ];
    }

    public static function getStatus()
    {
        return [
            0 => Yii::t('app','Pending'),
            1 => Yii::t('app','Successfully'),
        ];
    }


}

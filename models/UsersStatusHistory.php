<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_status_history".
 *
 * @property int $id
 * @property int $active_count
 * @property int $deactive_count
 * @property int $archive_count
 * @property int $pending_count
 * @property int $vip_count
 * @property int $new_service
 * @property int $black_list_count
 * @property int $damage_count
 * @property int $created_at
 */
class UsersStatusHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_status_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_count', 'deactive_count', 'archive_count', 'pending_count', 'vip_count', 'new_service', 'black_list_count', 'damage_count', 'created_at'], 'required'],
            [['active_count', 'deactive_count', 'archive_count', 'pending_count', 'vip_count', 'new_service', 'black_list_count', 'damage_count', 'created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'active_count' => Yii::t('app', 'Active count'),
            'deactive_count' => Yii::t('app', 'Deactive count'),
            'archive_count' => Yii::t('app', 'Archive count'),
            'pending_count' => Yii::t('app', 'Pending count'),
            'vip_count' => Yii::t('app', 'Vip count'),
            'new_service' => Yii::t('app', 'New Service count'),
            'black_list_count' => Yii::t('app', 'Black List count'),
            'damage_count' => Yii::t('app', 'Damage count'),
            'created_at' => Yii::t('app', 'Created at'),
        ];
    }
}

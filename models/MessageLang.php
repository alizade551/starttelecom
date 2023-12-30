<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "message_lang".
 *
 * @property int $id
 * @property string $alias
 * @property string $lang
 * @property string $published
 */
class MessageLang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message_lang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alias', 'name'], 'required'],
            [['published'], 'string'],
            [['alias'], 'string', 'max' => 15],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'alias' => Yii::t('app', 'Message alias'),
            'name' => Yii::t('app', 'Message name'),
            'published' => Yii::t('app', 'Published'),
        ];
    }
}

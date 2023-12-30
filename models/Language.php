<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "language".
 *
 * @property string $id
 * @property string $name
 * @property string $alias
 * @property string $default
 * @property string $published
 *
 * @property CategoryLang[] $categoryLangs
 */
class Language extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language';
    }




    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'alias', 'published'], 'required'],
            [['published'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['alias'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t("app",'Language name'),
            'alias' => Yii::t("app",'Language alias'),
            'published' => Yii::t("app",'Published'),
        ];
    }


}

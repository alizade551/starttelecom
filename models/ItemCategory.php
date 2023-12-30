<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "item_category".
 *
 * @property int $id
 * @property string $name
 * @property int $parent
 * @property int $position
 * @property int $created_at
 *
 * @property Item[] $Items
 */

class ItemCategory extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'item_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'position','unit_type','mac_address_validation'], 'required'],
            [['position', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['unit_type','mac_address_validation'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'name' => Yii::t('app','Category name'),
            'unit_type' => Yii::t('app','Unit type'),
            'mac_address_validation' => Yii::t('app','Mac address validation'),
            'position' => Yii::t('app','Position'),
            'created_at' => Yii::t('app','Created At')
        ];
    }

    public static  function getUnites( ){
        return [
            0=>Yii::t('app','count'),
            1=>Yii::t('app','metr'),
            2=>Yii::t('app','kg'),
            3=>Yii::t('app','litr')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Items::className(), ['category_id' => 'id']);
    }


}

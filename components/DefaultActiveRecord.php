<?php
namespace app\components;

use Yii;
use \app\models\ActiveQuery;

class DefaultActiveRecord extends \yii\db\ActiveRecord{
    public static $commonQuery = ActiveQuery::class;
    
    public static function find() {
        return new static::$commonQuery(get_called_class());
    }
}
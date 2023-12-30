<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;

/**
 * This is the model class for table "logs".
 *
 * @property int $id
 * @property string $member
 * @property int $user_id
 * @property string $text
 * @property string $time
 */
class Logs extends DefaultActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'required'],
            [['user_id','time'], 'integer'],
            [['text'], 'string'],
            [['member'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'member' => Yii::t('app','User'),
            'user_id' => Yii::t('app','Customer'),
            'text' => Yii::t('app','Text'),
            'time' => Yii::t('app','Created at'),
        ];
    }

    public  function getUser(){
         return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public static  function writeLog( $member , $user_id, $text , $time ){
        $model = new Logs;
        $model->member = $member; 
        $model->user_id = intval($user_id);
        $model->text =  $text; 
        $model->time = $time; 
        $model->save();  
    }


}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "egon_box_ports".
 *
 * @property int $id
 * @property int $egon_box_id
 * @property int $port_number
 * @property int $u_s_p_i
 * @property string $status
 *
 * @property EgponBox $egonBox
 */
class EgonBoxPorts extends \yii\db\ActiveRecord
{
    const TAG_USER_INET = 'tag_user_inet';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'egon_box_ports';
    }



    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::TAG_USER_INET] = ['u_s_p_i'];
        return $scenarios;
    }   

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['egon_box_id', 'port_number', 'status','u_s_p_i'], 'required'],
            [['u_s_p_i'], 'required', 'on' => self::TAG_USER_INET],
            [['egon_box_id', 'port_number', 'u_s_p_i'], 'integer'],
            [['status'], 'string'],
            [['egon_box_id'], 'exist', 'skipOnError' => true, 'targetClass' => EgponBox::className(), 'targetAttribute' => ['egon_box_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'egon_box_id' => Yii::t('app', 'Egon Box ID'),
            'port_number' => Yii::t('app', 'Port number'),
            'u_s_p_i' => Yii::t('app', 'Service login'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEgonBox()
    {
        return $this->hasOne(EgponBox::className(), ['id' => 'egon_box_id']);
    }


    public function getUserInet()
    {
        return $this->hasOne(UsersInet::className(), ['u_s_p_i' => 'u_s_p_i']);
    }

    public static function boxPortStatus(){
        return [
            0=>Yii::t('app','Free'),
            1=>Yii::t('app','Busy'),
            2=>Yii::t('app','Broken'),
  
        ];
    }

}

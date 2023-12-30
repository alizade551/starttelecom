<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "switch_ports".
 *
 * @property int $id
 * @property int $device_id
 * @property int $port_number
 * @property int $u.s.p.i
 * @property string $status
 *
 * @property Devices $device
 */
class SwitchPorts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'switch_ports';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['device_id', 'port_number', 'u_s_p_i'], 'integer'],
            [['status'], 'string'],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Devices::className(), 'targetAttribute' => ['device_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'device_id' => Yii::t('app', 'Device name'),
            'port_number' => Yii::t('app', 'Port number'),
            'u_s_p_i' => Yii::t('app', 'Service login'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Devices::className(), ['id' => 'device_id']);
    }
    public function getUserInet()
    {
        return $this->hasOne(UsersInet::className(), ['u_s_p_i' => 'u_s_p_i']);
    }


    public static function switchPortStatus(){
        return [
            0=>Yii::t('app','Free'),
            1=>Yii::t('app','Busy'),
            2=>Yii::t('app','Broken'),
  
        ];
    }

}

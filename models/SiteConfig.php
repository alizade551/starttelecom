<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "site_config".
 *
 * @property string $name
 * @property string $short_name
 * @property string $email
 * @property string $phone
 * @property string $sms_username
 * @property string $sms_password
 * @property string $sms_numberid
 * @property string $inet_ppoe_login_start
 * @property string $wifi_ppoe_login_start
 * @property string $google_map_js_token
 * @property string $whatsapp_token
 */
class SiteConfig extends \yii\db\ActiveRecord
{

    public $logo_photo;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'site_config';
    }

    public static function primaryKey()
    {
         return ['name', 'short_name','logo','currency', 'email', 'phone','balance_alert_cron','check_balance','check_archive','check_service_credit','half_month', 'sms_username', 'sms_password', 'sms_numberid', 'inet_ppoe_login_start', 'wifi_ppoe_login_start', 'google_map_js_token', 'whatsapp_token','whatsapp_number_id','paid_day_refresh','expired_service'];
    }
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name', 'email', 'phone', 'sms_username', 'sms_password', 'sms_numberid', 'inet_ppoe_login_start', 'wifi_ppoe_login_start', 'google_map_js_token','whatsapp_number_id', 'whatsapp_token','balance_alert_cron','check_balance','check_archive','check_service_credit','half_month','currency','paid_day_refresh','expired_service'], 'required'],
            [['name', 'email', 'sms_username', 'sms_numberid','message_lang'], 'string', 'max' => 50],
            [['short_name'], 'string', 'max' => 2],
            [['logo_photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg,svg'],
            [['email'], 'email'],
            [['phone', 'inet_ppoe_login_start', 'wifi_ppoe_login_start'], 'string', 'max' => 20],
            [['sms_password', 'google_map_js_token', 'whatsapp_token','whatsapp_number_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app','Company name'),
            'short_name' => Yii::t('app','Company short name'),
            'email' => Yii::t('app','E-mail'),
            'currency' => Yii::t('app','Currency'),
            'message_lang' => Yii::t('app','Default message language'),
            'phone' => Yii::t('app','Phone'),
            'balance_alert_cron' => Yii::t('app','Balance alert cron'),
            'check_balance' => Yii::t('app','Check balance cron'),
            'check_archive' => Yii::t('app','Check archive cron'),
            'check_service_credit' => Yii::t('app','Check service credit cron'),
            'half_month' => Yii::t('app','Half month'),
            'logo_photo' => Yii::t('app','Logo'),
            'sms_username' => Yii::t('app','SMS api username'),
            'sms_password' => Yii::t('app','SMS api password'),
            'sms_numberid' => Yii::t('app','SMS api numberid'),
            'inet_ppoe_login_start' => Yii::t('app','Internet pppoe login start'),
            'wifi_ppoe_login_start' => Yii::t('app','Wifi login start'),
            'google_map_js_token' => Yii::t('app','Google Map JS token'),
            'whatsapp_token' => Yii::t('app','Web Whatsapp token'),
            'whatsapp_number_id' => Yii::t('app','Web Whatsapp number id'),
        ];
    }


    public static function getCurrencies()
    {
        return  yii\helpers\ArrayHelper::map(\app\models\Currencies::find()->asArray()->all(),'name','name');

    }

    public static function getMessageLanguages()
    {
        return  yii\helpers\ArrayHelper::map(\app\models\MessageLang::find()->where(['published'=>'1'])->asArray()->all(),'alias','name');

    }


    public static function getCronStatus()
    {
        return [
            0=>Yii::t('app','Disable'),
            1=>Yii::t('app','Enable'),
        ];
    }


    public static function getCronCheckBalanceStatus()
    {
        return [
            0=>Yii::t('app','Disable'),
            1=>Yii::t('app','Send with by sms'),
            2=>Yii::t('app','Send with by whatsapp'),
        ];
    }

}

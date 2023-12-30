<?php

namespace app\models;

use Yii;
use app\components\DefaultActiveRecord;

/**
 * This is the model class for table "users_message".
 *
 * @property int $id
 * @property int $user_id
 * @property string $user_status
 * @property string $sms_text
 * @property string $message_time
 */
class UsersMessage extends DefaultActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $user_status;
    public $service;
    public $cities;
    public $districts;
    public $locations;
    public $contract_n;
    public $packet_detail;
    public $lang;
    public $template;

    public $dynamic_param = [];


    const SCENARIO_SEND_PACKET_DETAIL = 'send-packet-detail';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SEND_PACKET_DETAIL] = ['user_phone','lang','type'];
        return $scenarios;
    }

    public static function tableName()
    {
        return 'users_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'user_status', 'locations','cities','districts','type','template'], 'required'],
            [['user_phone','lang','type'], 'required' , 'on'=>'send-packet-detail'],
            [['user_id', 'message_time', 'service'], 'integer'],
            [['message_time', 'member_name', 'contract_n', 'packet_detail'], 'string', 'max' => 255],
            [['user_phone','status','type'], 'string', 'max' => 50],
            [['text'], 'string', 'max' => 168],
            ['dynamic_param', 'each', 'rule' => ['required']],
        ];
    }



    public function validateParam( $attribute, $params, $validator )
    {

       if ( $this->name == "maintenance_alert") {
            preg_match_all('/{(.*?)}/', $this->sms_text, $matches);
            $diffrence = array_diff( $matches[0] , [ 0=>'{fullname}',1=>'{date}' ] );

 
          if ( count( $matches[0] ) == 0 ) {
             $this->addError($attribute, Yii::t('app','{sms_text} must be contain {fullname} and {date},',['sms_text'=>Yii::t('app','Text')]));
          }else{
            if ( !empty( $diffrence )  ) {
                 $this->addError($attribute, Yii::t('app','{sms_text} must be contain {fullname} and {date},',['sms_text'=>Yii::t('app','Text')]));
            }
          }
       }

  
    }





    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app','Customer'),
            'type' => Yii::t('app','Message type'),
            'user_phone' => Yii::t('app','Phone'),
            'params' => Yii::t('app', 'Params'),
            'status' => Yii::t('app','Status'),
            'cities' => Yii::t('app','Cities'),
            'districts' => Yii::t('app','Districts'),
            'locations' => Yii::t('app','Locations'),
            'service' => Yii::t('app','Service'),
            'contract_n' => Yii::t('app','Contract number'),
            'packet_detail' => Yii::t('app','Packet'),
            'message_time' => Yii::t('app','Created at'),
            'user_status' => Yii::t('app','Customer status'),
            'member_name' => Yii::t('app','Member'),
            'lang' => Yii::t('app','Language'),
            'text' => Yii::t('app','Text'),
            'template' => Yii::t('app','Template'),
            'dynamic_param' => Yii::t('app','Parameter'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public static function getMessageType()
    {
        return [
            'sms'=>Yii::t('app','SMS'),
            'whatsapp'=>Yii::t('app','Whatsapp'),

        ];
    }


    public static function getMessageStatus()
    {
        return [
            '0'=>Yii::t('app','Unsuccessfully'),
            '1'=>Yii::t('app','Successfully'),

        ];
    }

    public static  function saveMessage( $user_id, $member_name, $user_phone, $text, $type, $status, $params, $response = false ){

        $model = new UsersMessage;
        $model->user_id = intval( $user_id );
        $model->member_name = $member_name; 
        $model->user_phone =  $user_phone; 
        $model->text = $text; 
        $model->type = $type; 
        $model->params = $params; 
        $model->message_time = time(); 
        $model->status = $status;
        if ( $response != false ) {
            $model->detail = $response;
        }
       
        $model->save(false);

        if ( $status == '1' ) {
            return true;
        }else{
            return false;
        }
    }

    public static  function sendSms( $user_id ,$member_name, $user_phone, $templateSmsAsText, $params, $messageId = false){
        $siteConfig = \app\models\SiteConfig::find()->one();

        $curl = \curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://213.172.86.6:8080/SmileWS2/webSmpp.jsp?username='.$siteConfig['sms_username'].'&password='.$siteConfig['sms_password'].'&numberId='.$siteConfig['sms_numberid'].'&msisdn=' . $user_phone . '&msgBody=' . urlencode( \app\components\Utils::transliterate( $templateSmsAsText ) ),
            CURLOPT_USERAGENT => $siteConfig['name'],
        ));
        $resp = \curl_exec($curl);
        curl_close($curl);

        $isOk =  preg_match("/Ok/i", $resp);
        $response = json_encode($resp);
        $status = ( $isOk == 1 ) ? '1' : '0';
        if ( $messageId == false ) {
            return \app\models\UsersMessage::saveMessage( 
                $user_id, 
                $member_name, 
                $user_phone, 
                $templateSmsAsText, 
                "sms", 
                $status, 
                $params, 
                $response 
            );
        }else{

            $messageModel = \app\models\UsersMessage::find()
            ->where([ 'id'=>$messageId ])
            ->one();

              if ( $status == "1" ) {
                $messageModel->status = "1";
                $messageModel->save(false);
                return ['status'=>'success','message'=>Yii::t("app","Message was sent")];
              }else{

                return ['status'=>'error','message'=>Yii::t("app","Check your sms balance or something went wrong")];
              }


        }


    }   

}

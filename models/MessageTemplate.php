<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "message_template".
 *
 * @property int $id
 * @property string $name
 * @property string $lang
 * @property string $status
 * @property string $name
 * @property string $sms_text
 */
class MessageTemplate extends \yii\db\ActiveRecord
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'lang', 'sms_text','whatsapp_body_text'], 'required'],
            [['name','whatsapp_body_text'], 'string'],
            [['lang'], 'string', 'max' => 10],
            [['sms_text'], 'string', 'max' => 168],
            [['whatsapp_header_text','whatsapp_footer_text'], 'string', 'max' => 168],
            [['lang', 'name'], 'unique', 'targetAttribute' => ['lang', 'name'],'on'=>'create'],
            ['sms_text', 'validateSmsText'],
            ['name', 'validateUniqe','on'=>'update'],
        
        ];
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['sms_text','name','lang','whatsapp_header_text','whatsapp_body_text','whatsapp_footer_text'];
        $scenarios[self::SCENARIO_UPDATE] = ['sms_text','name','lang','whatsapp_header_text','whatsapp_body_text','whatsapp_footer_text'];
        return $scenarios;
    }


    public function validateUniqe($attribute, $params, $validator)
    {

        $model = \app\models\MessageTemplate::find()->where(['name'=>$this->name])->andWhere(['lang'=>$this->lang])->andWhere(['id'=>$this->id])->asArray()->one();
       if ( $model == null) {
        $this->addError($attribute, Yii::t('app','Template already exist with this language',['sms_text'=>Yii::t('app','Text')]));
       }
    }

    public function validateSmsText( $attribute, $params, $validator )
    {

       if ( $this->name == "balance_alert" ) {
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

       if ( $this->name == "expired" ) {
            preg_match_all('/{(.*?)}/', $this->sms_text, $matches);
            $diffrence = array_diff( $matches[0] , [ 0=>'{contract_number}',1=>'{date}' ] );

 
          if ( count( $matches[0] ) == 0 ) {
             $this->addError($attribute, Yii::t('app','{sms_text} must be contain {contract_number} and {date},',['sms_text'=>Yii::t('app','Text')]));
          }else{
            if ( !empty( $diffrence )  ) {
                 $this->addError($attribute, Yii::t('app','{sms_text} must be contain {contract_number} and {date},',['sms_text'=>Yii::t('app','Text')]));
            }
          }
       }
       

       if ( $this->name == "maintenance_alert") {
            preg_match_all('/{(.*?)}/', $this->sms_text, $matches);
            $diffrence = array_diff( $matches[0] , [ 0=>'{date}',1=>'{clock_interval}' ] );

 
          if ( count( $matches[0] ) == 0 ) {
             $this->addError($attribute, Yii::t('app','{sms_text} must be contain {date} and {clock_interval},',['sms_text'=>Yii::t('app','Text')]));
          }else{
            if ( !empty( $diffrence )  ) {
                 $this->addError($attribute, Yii::t('app','{sms_text} must be contain {date} and {clock_interval},',['sms_text'=>Yii::t('app','Text')]));
            }
          }
       }


       if ( $this->name == "packet_info") {
            preg_match_all('/{(.*?)}/', $this->sms_text, $matches);
                $diffrence = array_diff( $matches[0] , [ 0=>'{fullname}', 1=>'{packet_name}', 2=>'{login}', 3=>'{password}' ] );
            if ( count( $matches[0] ) == 0 ) {
             $this->addError($attribute, Yii::t('app','{sms_text} must be contain {fullname} , {packet_name}, {login} and {password}',['sms_text'=>Yii::t('app','Text')]));
            }else{
                if ( !empty( $diffrence )  ) {
                    $this->addError($attribute, Yii::t('app','{sms_text} must be contain {fullname} , {packet_name}, {login} and {password}',['sms_text'=>Yii::t('app','Text')]));
                }
            }
       }

       if ( $this->name == "contract_info") {
            preg_match_all('/{(.*?)}/', $this->sms_text, $matches);
            $diffrence = array_diff( $matches[0] , [ 0=>'{fullname}', 1=>'{contract_number}' ] );

         if ( count( $matches[0] ) == 0 ) {
                $this->addError($attribute, Yii::t('app','{sms_text} must be contain {fullname} and {contract_number}',['sms_text'=>Yii::t('app','Text')]));
            }else{
                if ( !empty( $diffrence )  ) {
                    $this->addError($attribute, Yii::t('app','{sms_text} must be contain {fullname} and {contract_number}',['sms_text'=>Yii::t('app','Text')]));
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
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'lang' => Yii::t('app', 'Language'),
            'sms_text' => Yii::t('app', 'Sms text'),
            'whatsapp_header_text' => Yii::t('app', 'Whatsapp header text'),
            'whatsapp_body_text' => Yii::t('app', 'Whatsapp body text'),
            'whatsapp_footer_text' => Yii::t('app', 'Whatsapp footer text'),
        ];
    }

}

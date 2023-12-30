<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\MessageTemplate */
/* @var $form yii\widgets\ActiveForm */
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

$validationUrl = ( $model->isNewRecord ) ? $langUrl.'/message-template/add-message-template-validate' : $langUrl.'/message-template/update-message-template-validate?id='.$model->id;

?>



<div class="message-template-form" style="padding:0">
    <?php $form = ActiveForm::begin([
            'id'=>"message-template-form",
            'enableAjaxValidation' => true,
            'validateOnSubmit'=> true,
            'enableClientValidation'=>false,
            'validationUrl' => $validationUrl ,
            'options' => ['autocomplete' => 'off']]);
     ?>

    <?= $form->field($model, 'sms_text')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'whatsapp_header_text')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'whatsapp_body_text')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'whatsapp_footer_text')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->dropDownList([
     'balance_alert' => Yii::t('app','Balance alert'), 
     'packet_info' => Yii::t('app','Packet info'), 
     'contract_info' => Yii::t('app','Contract info'), 
     'technical_warning' => Yii::t('app','Technical warning'),
     'technical_info' => Yii::t('app','Technical info') ,
     'maintenance_alert' => Yii::t('app','Maintenance alert'),  
     'expired' => Yii::t('app','Expired service')
 ], ['prompt' => '']) ?>

    <?= $form->field($model, 'lang')->dropDownList(ArrayHelper::map(\app\models\MessageLang::find()->where(['published'=>'1'])->asArray()->all(),'alias','name'), ['prompt' => '']) ?>

    <?php if ( $model->isNewRecord ): ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php else: ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div> 
    <?php endif ?>

    <?php ActiveForm::end(); ?>

</div>

<?php 
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use borales\extensions\phoneInput\PhoneInput;

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
?>
<div style="width: 500px;">
    <h5><?=Yii::t('app','{fullname} phones updating',['fullname'=>$model->fullname]) ?></h5>
    <?php $form = ActiveForm::begin([
            'id'=>"message-template-form",
            'enableAjaxValidation' => true,
            'validateOnSubmit'=> true,
            'enableClientValidation'=>false,
            'validationUrl' => $langUrl.'/users/update-phones-validate?id='.$model->id,
            'options' => ['autocomplete' => 'off']]);
     ?>

        <?=$form->field($model, 'phone')->widget(PhoneInput::className(), [
        'jsOptions' => [
            'preferredCountries'=>['az','tr','ru','us'],
            'formatOnDisplay' => true,
            'separateDialCode' => false,
            'autoHideDialCode' => true,
            'nationalMode' => false,
        ]
        ]); ?>
        <?=$form->field($model, 'extra_phone')->widget(PhoneInput::className(), [
        'jsOptions' => [
            'preferredCountries'=>['az','tr','ru','us'],
            'formatOnDisplay' => true,
            'separateDialCode' => false,
            'autoHideDialCode' => true,
            'nationalMode' => false,
        ]
        ]); ?>		

        <?= Html::submitButton(Yii::t("app","Update"), ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>
</div>



<?php 
$this->registerJs('

var xhr_item;
var xhr_active_item=false;
var form_item = $("form#update-phones");
form_item.on("beforeSubmit", function (e) {
if( form_item.find("button").prop("disabled")){
return false;
}
if(xhr_active_item) { xhr_item.abort(); }
     xhr_active_item=true;
     form_item.find("button").prop("disabled",true);
     xhr_item = $.ajax({
          url: "'.\yii\helpers\Url::to(["/users/update-phones?id="]).$model['id'].'",
          type: "post",
          data: form_item.serialize(),
          success: function (response) {
              if(response.status == "error"){
                alertify.set("notifier","position", "top-right");
                alertify.error(response.message);

                xhr_active_item=false;
                form_item.find("button").prop("disabled",false);
                // $.fancybox.close();
              }

          }
     });
     return false;
}); 
');

 ?>

 <style type="text/css">
     .iti{display: block;}
 </style>
<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = Yii::t('app', 'Şifrə bərpası');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="custom-modal" style="max-width: 360px; min-width: 460px;">
    <div class="success-animation">
     <h4 style="text-align: center;"><?=Yii::t("app","Şifrənizi dəyişmək üçün e-poçtunuzu yoxlayin") ?></h4>
     <br>
        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" /><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" /></svg>
    </div>




    <div class="site-request-password-reset" style="padding: 0 35px;" >
			<?php $form = ActiveForm::begin([
				'id'=>'user-password-reset-form',
                  'enableAjaxValidation' => true,
                  'enableClientValidation'=>true,

				
				'layout'=>'horizontal',
				'validationUrl' => Url::toRoute('validate-request-password-reset-form'),
				'validateOnBlur'=>false,
			]); ?>
     	   <div class="row">
        <h3 style="text-align: center;"><?= Html::encode($this->title) ?></h3>
        <br>
        <p><?=Yii::t("app","Zəhmət olmasa elektron poçtunuzu doldurun. Şifrəni sıfırlamaq üçün sizə email göndərək.") ?>.</p>
            <div class="col-xs-8">
				<?= $form->field($model, 'email')->textInput(['maxlength' => 255, 'autofocus'=>true]) ?>
            </div>

            <div class="col-xs-4">
			    <?=Html::submitButton(Yii::t('app','Göndər'), ['class' => 'btn btn-primary ','style'=>'margin-left: 25px;']) ?>
            </div>
           </div>  
            	<?php ActiveForm::end(); ?>
        </div>
		
    </div>
</div>


<?php
$langUrl = (Yii::$app->language == "az") ? "/" : "/".Yii::$app->language."/";
$this->registerJs('
var xhr;
var xhr_active=false;

var form = $("form#user-password-reset-form");


  form.on(\'beforeSubmit\', function (e) {
if( form.find("button").prop("disabled")){
return false;
}
       if(xhr_active) { xhr.abort(); }
        xhr_active=true;
     form.find("button").prop("disabled",true);

     xhr = $.ajax({
          url: "'.Url::to($langUrl."user-management/auth/password-recovery").'",
          type: "post",
          data: form.serialize(),
          success: function (response) {

              if(response){
                if(response.status = "success"){
	                    $(".site-request-password-reset").hide(0);
	                    $(".success-animation").show(0);
                }
              }

          }
     });
     return false;
});   
  ');


 ?>
 <style type="text/css">
.success-animation { margin:20px auto;display: none;}
#user-password-reset-form{width: 100%}
.checkmark {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #4bb71b;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #4bb71b;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    position:relative;
    top: 5px;
    right: 5px;
   margin: 0 auto;
}
.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4bb71b;
    fill: #fff;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
 
}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes scale {
    0%, 100% {
        transform: none;
    }

    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

@keyframes fill {
    100% {
        box-shadow: inset 0px 0px 0px 30px #4bb71b;
    }
}
</style>
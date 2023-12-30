<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\Json;



/* @var $this yii\web\View */
/* @var $model app\models\IpAdresses */
/* @var $form yii\widgets\ActiveForm */
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

?>

<div class="ip-adresses-form">
    <?php $form = ActiveForm::begin([
        'id'=>"ip-adresses-form",
        // 'layout' => 'horizontal',
        'enableAjaxValidation' => true,
        'validateOnSubmit'=> true,
        'enableClientValidation'=>false,
        'validationUrl' => $langUrl.'/ip-adresses/create-validate',
        'options' => ['autocomplete' => 'off']

    ]); ?>

    <?= $form->field($model, 'router_id')->dropDownList(ArrayHelper::merge([''=>Yii::t('app','Select')],ArrayHelper::map(\app\models\Routers::find()->asArray()->all(),'id','name'))) ?>

    <?= $form->field($model, 'start_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'end_ip')->textInput(['maxlength' => true]) ?>

  

    <?= $form->field($model, 'type')->dropDownList(
        \app\models\IpAdresses::getType(), 
        [   
            'onchange'=>'
                $.pjax.reload({
                    url: "'.Url::to(['/ip-adresses/create']).'?type="+$(this).val(),
                    container: "#pjax-ip-adresses-form",
                    timeout: 5000
                });
                $(document).on("pjax:complete", function() {
                  $(".select_loader").hide();
                });
            ',
            'prompt'=>Yii::t('app','Select')
        ]
    ) ?>

    <?php  Pjax::begin(['id'=>'pjax-ip-adresses-form','enablePushState'=>true]);  ?>

    <?php if ( Yii::$app->request->get('type') == "0" && Yii::$app->request->isPjax ): ?>
    <?= $form->field($model, 'split')->dropDownList(\app\models\IpAdresses::getSplitValues(), ['prompt' => '']) ?>

    <?php 
        foreach ($form->attributes as $attribute) {
            $attribute = Json::htmlEncode($attribute);
            $this->registerJs("jQuery('form').yiiActiveForm('add', $attribute);");
        } 
    ?>

    <?php endif ?>

    <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <i class="icon fa fa-info"></i>
             <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('<span class="spinner-border  mr-2 align-self-center "></span>'.Yii::t('app', 'Create an ip adresses range'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php Pjax::end(); ?>  
    <?php ActiveForm::end(); ?>

</div>
<style type="text/css">
.custom-alert-error {
    color: #ff0018;
    background-color: #fdba45;
    text-align: center;
    font-size: 16px;
    position: absolute;
    left: calc(50% - 100px);
    top: 70px;
    width: 200px;
    height: 40px;
    line-height: 40px;
    border-radius: 5px;
}

.spinner-border {
    width: 1rem;
    height: 1rem;
    vertical-align: text-top;
    display: none;

}
.spinner-border.show{
    display: inline-block;
}
</style>


<?php 
$this->registerJs('

var xhr;
var xhr_active=false;
var form = $("form#ip-adresses-form");
form.on("beforeSubmit", function (e) {
if( form.find("button").prop("disabled")){
return false;
}
       if(xhr_active) { xhr.abort(); }
        xhr_active=true;
     form.find("button").prop("disabled",true);
   
     xhr = $.ajax({
          url: "'.\yii\helpers\Url::to(["ip-adresses/create"]).'",
          type: "post",
          beforeSend:function(){
            form.find(".btn-primary .spinner-border").addClass("show");
            $(".loader").show();
            $(".overlay").show();
          },

          data: form.serialize(),
          success: function (response) {
              if(response.status == "success"){
                form.find(".btn-primary .spinner-border").removeClass("show");
                alertify.set("notifier","position", "top-right");
                alertify.success(response.message);
                $("#modal").modal("hide");
                window.location.href = response.url
              }else{
                 form.find(".btn-primary .spinner-border").removeClass("show");
                alertify.set("notifier","position", "top-right");
                alertify.error(response.message);
                xhr_active=false;
                form.find("button").prop("disabled",false);
              }
          }
     }).done(function(){
                $(".loader").hide();
                $(".overlay").hide();
            });
     return false;
}); 

');


 ?>
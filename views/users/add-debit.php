<?php 
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;



$siteConfig = \app\models\SiteConfig::find()->asArray()->one();

?>
 <?php $form = ActiveForm::begin(['id'=>'debit','options' => ['autocomplete' => 'off']]); ?>

<div class="row">
    <div class="col-sm-12">
   
          <div class="form-group field-userbalance-user_id required">
            <label class="control-label" for="userbalance-user_id"><?=Yii::t("app","Customer") ?></label>
            <input type="text"  class="form-control"  value="<?=$userModel['fullname'] ?>" disabled="" aria-required="true">
        </div>
  		<?=$form->field($model, 'balance_out')->textInput(['maxlength' => true,'placeholder' => $siteConfig['currency']])->label(Yii::t("app","Balance out")); ?>
        <?=$form->field($model, 'user_id')->hiddenInput(['value' => $userModel['id']])->label(false) ?>
        <?=$form->field($model, 'payment_method')->hiddenInput(['value' => 0])->label(false) ?>
        <?=$form->field($model, 'created_at')->hiddenInput(['value' => time()])->label(false) ?>

        <?= Html::submitButton(Yii::t("app","Add"),['class'=>'btn btn-primary btn-debit']) ?>
   
 </div>

    
  <?php ActiveForm::end(); ?>

<?php 
$this->registerJs('


var xhr;
var xhr_active=false;
var form = $("form#debit");
form.on("beforeSubmit", function (e) {

if( form.find("button").prop("disabled")){
return false;
}
if(xhr_active) { xhr.abort(); }
xhr_active=true;
form.find("button").prop("disabled",true);
xhr = $.ajax({
      url: "'.\yii\helpers\Url::to(["users/add-debit?id="]).$userModel['id'].'",
      type: "post",
      data: form.serialize(),
      beforeSend:function(){
          $(".loader").show();
          $(".overlay").addClass("show");
      },
      success: function (response) {
          if(response.status == "success"){
            $(".loader").hide();
            $(".overlay").removeClass("show");
            alertify.set("notifier","position", "top-right");
            alertify.success(response.message);
            $("#modal").modal("hide");
            setTimeout(()=>{
              location.reload();

            },1000);

          }else{
            $(".loader").hide();
            $(".overlay").removeClass("show");
            alertify.set("notifier","position", "top-right");
            alertify.error(response.message);
            xhr_active=false;
            form.find("button").prop("disabled",false);
          }
      }
 });
 return false;
}); 
');
 ?>


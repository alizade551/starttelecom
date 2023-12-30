<?php 
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;


$username = \app\models\Users::find()->where(['id'=>$id])->one();
$siteConfig = \app\models\SiteConfig::find()->asArray()->one();

?>
 <?php $form = ActiveForm::begin(['id'=>'balance','options' => ['autocomplete' => 'off']]); ?>

<div class="row">
    <div class="col-sm-12">
   
          <div class="form-group field-userbalance-user_id required">
            <label class="control-label" for="userbalance-user_id"><?=Yii::t("app","Customer") ?></label>
            <input type="text"  class="form-control"  value="<?=$username->fullname ?>" disabled="" aria-required="true">
        </div>
     
          <div class="form-group field-userbalance-user_id required">
            <label class="control-label" for="userbalance-user_id"><?=Yii::t("app","Recipet") ?></label>
            <input type="text"  class="form-control"  value="<?=$recipet['code'] ?>" disabled="" aria-required="true">
        </div>

        <?php 
         if ( $model_user->status == '2' && $model_user->paid_time_type == "0" ) {
            $daily_price = \app\models\UserBalance::CalcUserTariffDaily($model_user->id,true)['per_total_tariff'];
         }else{
            $daily_price = \app\models\UserBalance::CalcUserTariffDaily($model_user->id,false,false)['per_total_tariff'];
         }
          echo $form->field($model, 'balance_in')->textInput(['maxlength' => true,'placeholder' => $user_tariff." ".$siteConfig['currency']])->label(Yii::t("app","Balance in"));
         ?>
          <?=$form->field($model, 'bonus_in')->textInput(['maxlength' => true,'value'=>0.00])->label(Yii::t("app","Bonus in")); ?>
  
  
        <?=$form->field($model, 'user_id')->hiddenInput(['value' => $id])->label(false) ?>
        <?=$form->field($model, 'payment_method')->hiddenInput(['value' => 0])->label(false) ?>
        <?=$form->field($model, 'created_at')->hiddenInput(['value' => time()])->label(false) ?>
      <?php if ( $model_user->status == "2" && $model_user->paid_time_type == "0" ): ?>
          <?=$form->field($model, 'per_day_rule')->checkBox(); ?>
      <?php else: ?>
          <?=$form->field($model, 'per_day_rule')->hiddenInput(['value' => '0'])->label(false); ?>
      <?php endif ?>
        <?= Html::submitButton(Yii::t("app","Add"),['class'=>'btn btn-primary btn-balance']) ?>
   
 </div>

    
  <?php ActiveForm::end(); ?>

<?php 
$this->registerJs('
$("#userbalance-per_day_rule").on("change",function () {
    if($(this).prop("checked")) {
      $("#userbalance-balance_in").val('.$daily_price.');
    }else{
      $("#userbalance-balance_in").val('.$user_tariff.');
    }
});

var xhr;
var xhr_active=false;
var form = $("form#balance");
form.on("beforeSubmit", function (e) {

if( form.find("button").prop("disabled")){
return false;
}
if(xhr_active) { xhr.abort(); }
xhr_active=true;
form.find("button").prop("disabled",true);
xhr = $.ajax({
      url: "'.\yii\helpers\Url::to(["users/add-balance?id="]).$id.'",
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
            alertify.success(response.text);
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


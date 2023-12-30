<?php 
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;


$siteConfig = \app\models\SiteConfig::find()->asArray()->one();

?>
 <?php $form = ActiveForm::begin(['id'=>'debit','options' => ['autocomplete' => 'off']]); ?>

<div style="width: 100%;">

<?php if ( $model->status == "2"  || $model->status == "1" ): ?>

	<div class="form-group field-userbalance-user_id required">
		<label class="control-label" for="userbalance-user_id"><?=Yii::t("app","Customer") ?></label>
		<input type="text"  class="form-control"  value="<?=$model['fullname'] ?>" disabled="" aria-required="true">
	</div>
  		   <?= $form->field($model, 'paid_time_type')->dropDownList(
                \app\models\RequestOrder::getPaidDayType()
            ); 
            ?>
        

            <?php 
            $model->updatedAt =  date("d-m-Y",$model->updated_at);
            echo $form->field($model, 'updatedAt')->widget(DateTimePicker::classname(), 
                [
                    'bsVersion' => '4.x',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy',
                        'minView' => 2
                    ]
                ]
            );
            ?>
	   <br>



       <?php if ( $model->status == "2" ): ?>
        <div class="alert alert-danger mb-4" role="alert">
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert">
                 <line x1="18" y1="6" x2="6" y2="18"></line>
                 <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
           </button>
           <strong><?=Yii::t('app','Note') ?> !</strong> <?=Yii::t('app','All services of the subscriber will be activated') ?> 
        </div>
       <?php endif ?>

       <?php if ( $model->status == "1" ): ?>
        <div class="alert alert-warning mb-4" role="alert">
           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert">
                 <line x1="18" y1="6" x2="6" y2="18"></line>
                 <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
           </button>
           <strong><?=Yii::t('app','Note') ?> !</strong> <?=Yii::t('app','If the {user_fullname}\'s renewal date is greater than the current time and status is deactive all services will be active.If it is lower and status is ative end-of-day services will be deactivated.',['user_fullname'=>$model->fullname]) ?> 
        </div>
       <?php endif ?>


        <?= Html::submitButton(Yii::t("app","Change"),['class'=>'btn btn-primary btn-debit']) ?>	
	<?php else: ?>
		<h5><?=Yii::t('app','Ödəniş günü dəyişilməsi üçün {user_fullname} adlı abunəçinin statusu aktiv və ya deaktiv olmalıdır',['user_fullname'=>$model['fullname']]) ?></h5>
	<?php endif ?>
   
 </div>

    
  <?php ActiveForm::end(); ?>

<?php 
$this->registerJs('

$("#users-paid_time_type").on("change",function(){
     let today = new Date();
     let day = String(today.getDate()).padStart(2, "0");
    if(  $(this).val() == "0" ){
        $("#users-paid_day").val("1");
    }else{
        $("#users-paid_day").val(day);
    }
})

$("#users-paid_day").on("change",function(){
    if(  $("#users-paid_time_type").val() == "0" ){
        $(this).val("1");
    }
});


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
      url: "'.\yii\helpers\Url::to(["users/change-paid-day?id="]).$model['id'].'",
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


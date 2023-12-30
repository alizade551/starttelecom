<?php 
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Json;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\datetime\DateTimePicker;
?>

<div class="row">
	<div class="col-sm-12">
	 <?php $form = ActiveForm::begin(['id'=>'balck-list','options' => ['autocomplete' => 'off']]); ?>
	     <h6><?=Yii::t("app","All services will removed and customer will add black list") ?></h6>
	     <?= $form->field($model_note, 'note')->textarea()->label() ?>
	     <?= $form->field($model_note, 'user_id')->hiddenInput(['value'=>$user_id])->label(false) ?>
	     <?= $form->field($model_note, 'member_name')->hiddenInput(['value'=>Yii::$app->user->username])->label(false) ?>
	     <?= $form->field($model_note, 'time')->hiddenInput(['value'=>time()])->label(false) ?>
       <div class="form-group" >
          <?= Html::submitButton(Yii::t('app','Add black list'), ['class' =>'btn btn-danger ']) ?>
          <button  class="btn btn-secondary"  title="<?=Yii::t('app','Close') ?>" ><?=Yii::t('app','Close') ?></button>         
      </div>
  <?php ActiveForm::end(); ?>
  </div>
 </div>





<?php 
$this->registerJs('
$(".btn-secondary").on("click",function(){
  $("#modal").modal("toggle");
});

var xhr_item;
var xhr_active_item=false;
var form_item = $("form#balck-list");
form_item.on("beforeSubmit", function (e) {
if( form_item.find("button").prop("disabled")){
return false;
}
if(xhr_active_item) { xhr_item.abort(); }
     xhr_active_item=true;
     form_item.find("button").prop("disabled",true);
     xhr_item = $.ajax({
          url: "'.\yii\helpers\Url::to(["users/add-black-list?user_id="]).$user_id.'",
          type: "post",
          data: form_item.serialize(),
          success: function (response) {
              if(response.status == "success"){
                alertify.set("notifier","position", "top-right");
                alertify.success("'.Yii::t("app","User added black list").'");
					      $("#modal").modal("hide");
                  $.pjax.reload({container: "#pjax-user-info", async:false});
                  $.pjax.reload({container: "#pjax-user-item-info", async:false});
                  $.pjax.reload({container: "#pjax-inet-table", async:false});
                  $.pjax.reload({container: "#pjax-tv-table", async:false});
                  $.pjax.reload({container: "#pjax-wifi-table", async:false});
              }else{
                xhr_active_item=false;
                form_item.find("button").prop("disabled",false);
              }

          }
     });
     return false;
}); 
');

 ?>
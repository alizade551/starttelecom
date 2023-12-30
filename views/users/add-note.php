<?php 
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;


$this->title = Yii::t('app','Add a note');
?>
<div style="width: 100%;">
    <?php $form = ActiveForm::begin(['id'=>"add-note-form"]); ?>
        <?= $form->field($model, 'note')->textarea()->label() ?>
        <?= $form->field($model, 'user_id')->hiddenInput(['value'=>$userModel['id']])->label(false) ?>
        <?= $form->field($model, 'member_name')->hiddenInput(['value'=>Yii::$app->user->username])->label(false) ?>
        <?= $form->field($model, 'time')->hiddenInput(['value'=>time()])->label(false) ?>
        <?= Html::submitButton(Yii::t('app','Add'), ['class' => 'btn btn-primary',]) ?>
    <?php ActiveForm::end(); ?>
</div>

<?php 
$this->registerJs('

var xhr;
var xhr_active=false;
var form = $("form#add-note-form");
form.on("beforeSubmit", function (e) {
if( form.find("button").prop("disabled")){
return false;
}
    if(xhr_active) { xhr.abort(); }
        xhr_active=true;
     $("#order-button").prop("disabled",true);
     xhr = $.ajax({
          url: "'.\yii\helpers\Url::to(["add-note"]).'?id='.$userModel['id'].'",
          type: "post",
          data: form.serialize(),
          success: function (response) {
              if(response.status == "success"){
                $("#usersnote-note").val("");
                 $.pjax.reload({
                    url: "'.Url::to(['view']).'?id='.$userModel['id'].'",
                    container: "#pjax-note-form",
                    timeout: 5000
                });
                    alertify.set("notifier","position", "top-right");
                    alertify.success(response.message);
                    $("#modal").modal("toggle");
              }else{
                // xhr_active=false;
                // form.find("button").prop("disabled",false);
              }
          }
     });
     return false;
}); 


');

 ?>
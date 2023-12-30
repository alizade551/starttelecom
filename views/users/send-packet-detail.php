<?php 
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

?>
<?php $form = ActiveForm::begin(['id'=>'send-packet-detail','options' => ['autocomplete' => 'off']]); ?>

    <?= $form->field($model, 'user_phone')->dropDownList($phones,[ 'prompt'=>Yii::t("app","Select")]); ?>

    <?= $form->field($model, 'type')->dropDownList(\app\models\UsersMessage::getMessageType(),[ 'prompt'=>Yii::t("app","Select")]); ?>
    <?php $model->lang = $userModel['message_lang'] ?>
    <?=$form->field($model, 'lang')->dropDownList($languages,['prompt'=>Yii::t("app","Select")])->label() ?>

    <?= Html::submitButton(Yii::t("app","Send"), ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>


<?php 
$this->registerJs('

var xhr_item;
var xhr_active_item=false;
var form_item = $("form#send-packet-detail");
form_item.on("beforeSubmit", function (e) {
if( form_item.find("button").prop("disabled")){
return false;
}
if(xhr_active_item) { xhr_item.abort(); }
     xhr_active_item=true;
     form_item.find("button").prop("disabled",true);
     xhr_item = $.ajax({
          url: "'.\yii\helpers\Url::to(["/users/send-packet-detail?id="]).$packetModel['id'].'",
          type: "post",
          data: form_item.serialize(),
          success: function (response) {
              if(response.status == "error"){
                alertify.set("notifier","position", "top-right");
                alertify.error(response.message);

                xhr_active_item=false;
                form_item.find("button").prop("disabled",false);
                $.fancybox.close();
                setTimeout(() => location.reload(), 5000);
              }

          }
     });
     return false;
}); 
');

 ?>
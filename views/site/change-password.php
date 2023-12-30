<?php
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
$langUrl_ = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

$this->registerJsFile('js/snackbar.min.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerCssFile('css/snackbar.min.css', [
    'depends' => [\yii\web\JqueryAsset::className()]
]);
$this->title = Yii::t('app','Change password');

?>
 
<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
    <div class="widget-content widget-content-area">
      <?php 
        $form = ActiveForm::begin([
               'id' => 'user-change-password-form',
                'enableClientValidation' => true,
                'enableAjaxValidation' => true,
                'validationUrl' => Url::to('validate-password-form')
        ]);
      ?>
          <?= $form->field($model, 'new_password')->textInput(['maxlength' => true]) ?>
          <?= $form->field($model, 'new_repeat_password')->textInput(['maxlength' => true]) ?>
          <?=$form->field($model, 'current_password', ['enableAjaxValidation' => true]);?>
        <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Change password'), ['class' => 'btn btn-primary']) ?>
        </div>
      <?php ActiveForm::end(); ?>   
    </div>
  </div>
</div>

<?php
$url = Url::to($langUrl_."change-password");
$this->registerJs('
var xhr;
var xhr_active=false;

var form = $("form#user-change-password-form");
form.on(\'beforeSubmit\', function (e) {
$(".LoaderArea.ProductList").show();
if( form.find("button").prop("disabled")){
return false;
}
if(xhr_active) { xhr.abort(); }
      xhr_active=true;
   form.find("button").prop("disabled",true);
   xhr = $.ajax({
        url: "'.$url.'",
        type: "post",
        data: form.serialize(),
        success: function (response) {
            if(response){
              if(response.code == "success"){
                  
                  form.find("button").prop("disabled",false);
                  $("#user-new_password").val("");
                  $("#user-new_repeat_password").val("");
                  $("#user-current_password").val("");
                    $(".LoaderArea.ProductList").hide();

    Snackbar.show({
text: "<i style=\'font-size:20px\' class=\'far fa-check-circle\'></i> '.Yii::t('app','Your password was changed').'",
pos: "top-center",
showAction: false,
actionTextColor:"#fff",
backgroundColor:"#4caf50",
duration:5000,
customClass:"snackbar"


});

   

              }else{
                  xhr_active=false;
                  form.find("button").prop("disabled",false);
     
              }
            }

        }
   });
   return false;
});   
');


?>

<style type="text/css">
     .snackbar{
      height: 20px;
      line-height: 20px;
      background-color: 
     }
</style>
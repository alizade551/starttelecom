<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Services;
use kartik\select2\Select2; 

$id = Yii::$app->request->get("id");
$service_id = Yii::$app->request->get("service_id");
 ?>
 <div class="tv-form panel panel-default " style="padding: 10px;width: 100%">
<?php if (count($model_packets) != 0): ?>
	


    <?php $form = ActiveForm::begin(['options' => ['id'=>'tv-service-form']]); ?>

    <?= $form->field($model, 'user_id')->hiddenInput(['value' => $id])->label(false) ?>


    <?= $form->field($model, 'packet_id')->dropDownList(ArrayHelper::map($model_packets,'packet_id','packet.packet_name'))->label('Packet ') ?>
    <?= $form->field($model, 'card_number')->textInput(['maxlength' => true]) ?>

 


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success','data-loading-text'=>'<i class="fa fa-spinner fa-spin"></i> Please wait.']) ?>
    </div>
     <?php ActiveForm::end(); ?>

<?php else: ?>
	<h1 style="text-align: center;">Service has been added!</h1>
<?php endif ?>
</div>
<?php 

$this->registerJs('



var xhr;
var xhr_active=false;
var form = $("form#tv-service-form");
form.on("beforeSubmit", function (e) {
if( form.find("button").prop("disabled")){
return false;
}

       if(xhr_active) { xhr.abort(); }
        xhr_active=true;
     form.find("button").prop("disabled",true);

                 
             
             
     
            
     
     xhr = $.ajax({
          url: "'.\yii\helpers\Url::to(["tv-service"]).'"+"?id='.$id.'"+"&service_id='.$service_id.'",
          type: "post",
          data: form.serialize(),
          success: function (response) {

              if(response.status == "success"){

               window.location.href = response.url;
              }else{
                xhr_active=false;
                form.find("button").prop("disabled",false);
              }

          }
     });
     return false;
}); 







');

 ?>
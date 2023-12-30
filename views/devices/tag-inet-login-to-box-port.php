<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Cities */
/* @var $form yii\widgets\ActiveForm */
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

?>

<div style="max-width:500px;">
 	 	<h4 style="margin: 20px 0;"><?=Yii::t('app','Device {device} - {ponPort} pon port {boxName} box {port} port tagging inet login',
 		[
 		'device'=>$model->egonBox->device->name,
 		'ponPort'=>$model->egonBox->pon_port_number,
 		'boxName'=>$model->egonBox->box_name,
 		'port'=>$model->port_number,
 		]
 		) ?></h4>



		<?php $form = ActiveForm::begin([
		    'id'=>"egpon-taggin-inet-login-form",
		    'enableAjaxValidation' => true,
		    'validateOnSubmit'=> true,
		    'enableClientValidation'=>false,
		    'validationUrl' => $langUrl.'/devices/tag-inet-login-to-box-port-validate',
		]);?>


		<div class="form-group ">
			<label ><?=Yii::t('app','Pon-port') ?></label>
			<input type="text" disabled="disabled" class="form-control" value="<?=$model->egonBox->pon_port_number ?>" >
		</div>
		<div class="form-group ">
			<label ><?=Yii::t('app','Port') ?></label>
			<input type="text" disabled="disabled" class="form-control" value="<?=$model->port_number ?>" >
		</div>
		
		<?php if ( isset($model->userInet->login) ): ?>
			<div class="form-group ">
				<label ><?=Yii::t('app','Tagged inet login') ?></label>
				<input type="text" disabled="disabled" class="form-control" value="<?=$model->userInet->login ?>" >
			</div>
			<div class="form-group ">
				<label ><?=Yii::t('app','Customer') ?></label>
				<input type="text" disabled="disabled" class="form-control" value="<?=$model->userInet->user->fullname ?>" >
			</div>

		<?php endif ?>
	
	

		
		<?=$form->field($model, 'u_s_p_i')->dropDownList(ArrayHelper::map($userServicePacketModel,'id','inet_login'),['prompt'=>Yii::t('app','Select')])->label() ?>

		<?=$form->field($model, 'status')->dropDownList(\app\models\SwitchPorts::switchPortStatus(),['prompt'=>Yii::t('app','Select')])->label() ?>

		<div class="form-group">

		<?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success tagging-box-inet-login-port','data-port_id'=>$model['id']])?>
		</div>
	<?php ActiveForm::end();?>
</div>



<?php $this->registerJs('
var clickTaggingInetToBoxPort = false;
var xhrTaggingInetToBoxPort;
var xhrActiveTaggingInetToBoxPort=false;
var formTaggingInetToBoxPort = $("form#egpon-taggin-inet-login-form");

$("#egpon-taggin-inet-login-form").on("beforeSubmit", function (e) {
	if(!clickTaggingInetToBoxPort){
       var portId =  formTaggingInetToBoxPort.find(".tagging-box-inet-login-port").attr("data-port_id");
        clickTaggingInetToBoxPort = true;
	    if( formTaggingInetToBoxPort.find(".tagging-box-inet-login-port").prop("disabled")){
	        return false;
	    }
	    if(xhrActiveTaggingInetToBoxPort) { xhrTaggingInetToBoxPort.abort(); }
	    xhrActiveTaggingInetToBoxPort = true;
	    formTaggingInetToBoxPort.find(".tagging-box-inet-login-port").prop("disabled",true);

	    xhrTaggingInetToBoxPort = $.ajax({
	      url: "'.\yii\helpers\Url::to(["devices/tag-inet-login-to-box-port"]).'?id="+portId,
	      type: "post",
	      beforeSend:function(){
	        $(".loader").show();
	        $(".overlay").addClass("show");
	      },
	      data: formTaggingInetToBoxPort.serialize(),
	      success: function (response) {
	          $(".loader").hide();
	          $(".overlay").removeClass("show");
	        if(response.status == "error"){
	             alertify.set("notifier","position", "top-right");
	             alertify.error(response.message);
	        }          

	        if(response.status == "success"){
	         	 window.location.href = response.url;
	        }else{
	            xhrActiveTaggingInetToBoxPort=false;
	            formTaggingInetToBoxPort.find(".tagging-box-inet-login-port").prop("disabled",false);
	        }

	      }
	    }).done(function(){ clickTaggingInetToBoxPort = false; });
	    return false;


	}
}); 
 
') ?>
<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Cities */
/* @var $form yii\widgets\ActiveForm */
?>

<div style="max-width:500px;">
 	<h4><?=Yii::t('app','Device {device} {port} port setting',['device'=>$model->device->name,'port'=>$model->port_number]) ?></h4>
	<?php $form = ActiveForm::begin([
            'id'=>"switch-port",
	]);?>

		<div class="form-group ">
			<label for="switchports-port_number"><?=Yii::t('app','Port number') ?></label>
			<input type="text" disabled="disabled" class="form-control" value="<?=$model->port_number ?>" >
		</div>
		
		<?php if ( isset($model->userInet->login) ): ?>
			<div class="form-group ">
				<label ><?=Yii::t('app','Tagged login') ?></label>
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

		<?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success switch-port','data-id'=>$model['id']])?>
		</div>
	<?php ActiveForm::end();?>
</div>

<?php $this->registerJs('

$(document).on("change","#switchports-u_s_p_i",function(){
	if($(this).val() == ""){
		$("#switchports-status").val("")
	}

	if( $(this).val() != "" ){
		$("#switchports-status").val(1)
	}
});


$(document).on("change","#switchports-status",function(){
	if($(this).val() == "2"){
		$("#switchports-u_s_p_i").val("")
	}
});


var clickSwitchPort = false;

var xhrSwitchPort;
var xhrActiveSwitchPort=false;
var formSwitchPort = $("form#switch-port");

$("form#switch-port").on("beforeSubmit", function (e) {
	if(!clickSwitchPort){
		var swithId =  formSwitchPort.find(".switch-port").attr("data-id");
        clickSwitchPort = true;
	    if( formSwitchPort.find(".switch-port").prop("disabled")){
	        return false;
	    }
	    if(xhrActiveSwitchPort) { xhrSwitchPort.abort(); }
	    xhrActiveSwitchPort = true;
	    formSwitchPort.find(".switch-port").prop("disabled",true);

	    xhrSwitchPort = $.ajax({
	      url: "'.\yii\helpers\Url::to(["devices/use-port"]).'?id="+swithId,
	      type: "post",
	      beforeSend:function(){
	        $(".loader").show();
	        $(".overlay").addClass("show");
	      },
	      data: formSwitchPort.serialize(),
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
	            xhrActiveSwitchPort=false;
	            formSwitchPort.find(".switch-port").prop("disabled",false);
	        }

	      }
	    }).done(function(){ clickSwitchPort = false; });
	    return false;


	}

}); 
 
') ?>



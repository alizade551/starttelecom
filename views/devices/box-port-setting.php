<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Cities */
/* @var $form yii\widgets\ActiveForm */

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
?>
<div style="width:400px;">
 	<h4><?=Yii::t(
 		'app',
 		'Device {device} {pon_port} pon-port setting',
 		[
	 		'device'=>$model->egponPonPort->device->name,
	 		'pon_port'=>$model->egponPonPort->pon_port_number
 		]
 		); ?>
 	</h4>

<?php $form = ActiveForm::begin([
'id'=>"pon-port-box-setting-form",
    'enableAjaxValidation' => true,
    'validateOnSubmit'=> true,
    'enableClientValidation'=>false,
    'validationUrl' => $langUrl .'/devices/box-port-setting-validate',
]);?>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group ">
				<label for="switchports-port_number"><?=Yii::t('app','Box name') ?></label>
				<input type="text" disabled="disabled" class="form-control" value="<?=$model->box_name ?>" >
			</div>
		</div>
		<div class="col-sm-6">
			<?=$form->field($model, 'location_id')->dropDownList(ArrayHelper::map($coverageLocations,'id','name'),['prompt'=>Yii::t('app','Select')])->label() ?>
		</div>
	</div>
	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success pon-port-setting-btn','data-box_id'=>$model['id']])?>
	</div>
<?php ActiveForm::end();?>


<?php $this->registerJs('


var clickBoxPortSetting = false;
var xhrBoxPortSetting;
var xhrActiveBoxPortSetting=false;
var formBoxPortSetting = $("form#pon-port-box-setting-form");

$("#pon-port-box-setting-form").on("beforeSubmit", function (e) {
	if(!clickBoxPortSetting){
       var boxId =  formBoxPortSetting.find(".pon-port-setting-btn").attr("data-box_id");
        clickBoxPortSetting = true;
	    if( formBoxPortSetting.find(".pon-port-setting-btn").prop("disabled")){
	        return false;
	    }
	    if(xhrActiveBoxPortSetting) { xhrBoxPortSetting.abort(); }
	    xhrActiveBoxPortSetting = true;
	    formBoxPortSetting.find(".btn-primary").prop("disabled",true);

	    xhrBoxPortSetting = $.ajax({
	      url: "'.\yii\helpers\Url::to(["devices/box-port-setting"]).'?id="+boxId,
	      type: "post",
	      beforeSend:function(){
	        $(".loader").show();
	        $(".overlay").addClass("show");
	      },
	      data: formBoxPortSetting.serialize(),
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
	            xhrActiveBoxPortSetting=false;
	            formBoxPortSetting.find(".pon-port-setting-btn").prop("disabled",false);
	        }

	      }
	    }).done(function(){ clickBoxPortSetting = false; });
	    return false;


	}
}); 
 
') ?>
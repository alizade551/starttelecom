<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

 ?>

<div class="change-user-packet">
	<h4 style="margin-top: 15px">
		<?=Yii::t(
			"app",
			"Change {customer} {packet} packet !",
			[
				'customer'=>$model->user->fullname,
				'packet'=>$model->packet->packet_name
			]
		) ?>
	</h4>
    <?php $form = ActiveForm::begin([
            'id'=>"change-packet-forum",
            'enableAjaxValidation' => true,
            'validateOnSubmit'=> true,
            'enableClientValidation'=>false,
            'validationUrl' =>Url::to(['change-packet-validate']),
            'options' => ['autocomplete' => 'off']]);
     ?>

		<div class="form-group field-usersservicespackets-service_name required">
			<label for="usersservicespackets-service_name"><?=Yii::t('app','Service') ?></label>
			<input type="text" id="usersservicespackets-service_name" class="form-control validating" disabled="disabled" value="<?=$model->service->service_name ?>" aria-required="true">
		</div>

		<?=$form->field($model, 'service_id')->hiddenInput(['value'=>$model->service_id])->label(false) ?>
		<?=$form->field($model, 'user_id')->hiddenInput(['value'=>$model->user_id])->label(false) ?>
		<?=$form->field($model, 'u_s_p_i')->hiddenInput(['value'=>$model->id])->label(false) ?>

		<?=$form->field($model, 'packet_id')->dropDownList(
			ArrayHelper::map($packetsModel,'id','packet_name'),
			[	
				'onchange'=>'
					$(".select_loader").show();
					$.pjax.reload({
					    url: "'.Url::to(['change-packet']).'?id='.$model->id.'&service_id='.$model->service_id.'&packet_id="+$(this).val(),
					    container: "#pjax-change-packet-form",
					    timeout: 5000
					});
					$(document).on("pjax:complete", function() {
					  $(".select_loader").hide();
					});
				',
				'prompt'=>Yii::t('app','Select')
			]
		)?>

         <?php  Pjax::begin(['id'=>'pjax-change-packet-form','enablePushState'=>true]);  ?>
         	<?php if ($model->service->service_alias == "internet" ): ?>

					<?php 
					    $model->static_ip_address = ArrayHelper::toArray(
					     \app\models\IpAdresses::find()->where(['public_ip'=>$model->usersInet->static_ip])->one(), [
					    '\app\models\IpAdresses' => [
					        'id',
					        'public_ip'
					    ],
					]);
					    if (Yii::$app->request->get('packet_id')) {

							    $getPacket = \app\models\Packets::find()
							    ->where(['id'=>Yii::$app->request->get('packet_id')])
							    ->asArray()
							    ->one();
							  
					    }
					?>

					<?php $model->static_ip_address = $model->usersInet->static_ip ?>
					<?=$form->field($model, 'static_ip_address')->dropDownList(ArrayHelper::map($staticIpModel,'id','public_ip'),['prompt'=>Yii::t('app','Select')])?>
					<?php 
						if (Yii::$app->request->isPjax) {
							foreach ($form->attributes as $attribute) {
							$attribute = Json::htmlEncode($attribute);
							$this->registerJs("jQuery('form#change-packet-forum').yiiActiveForm('add', $attribute); ");
							} 
						}
					?>
					
         	<?php endif ?>
         <?php Pjax::end(); ?>  

		<?php if ( $model->service->service_alias == "voip" ): ?>
		  <?=$form->field($model, 'phone_number')->textInput(['value'=>$model->usersVoip->phone_number])->label();?>
		<?php endif ?>

		<div class="form-group">
			<?=Html::submitButton(Yii::t('app', 'Change'), ['class' => 'btn btn-primary change-packet-btn'])?>
		</div>
	<?php ActiveForm::end();?>
</div>

<style type="text/css">
	.change-user-packet {
		max-width: 500px;

	}
</style>
<?php $this->registerJs('

var clickChangingPacket = false;
var userPacketId = '.Yii::$app->request->get("id").';

var xhrChangePacket;
var xhrActiveChangePacket=false;
var formChangePacket = $("form#change-packet-forum");

$(document).on("beforeSubmit", function (e) {
	if(!clickChangingPacket){

        clickChangingPacket = true;
	    if( formChangePacket.find(".change-packet-btn").prop("disabled")){
	        return false;
	    }
	    if(xhrActiveChangePacket) { xhrChangePacket.abort(); }
	    xhrActiveChangePacket = true;
	    formChangePacket.find(".btn-primary").prop("disabled",true);

	    xhrChangePacket = $.ajax({
	      url: "'.\yii\helpers\Url::to(["request-order/change-packet"]).'?id="+userPacketId,
	      type: "post",
	      beforeSend:function(){
	        $(".loader").show();
	        $(".overlay").addClass("show");
	      },
	      data: formChangePacket.serialize(),
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
	            xhrActiveChangePacket=false;
	            formChangePacket.find(".btn-primary").prop("disabled",false);
	        }

	      }
	    }).done(function(){ clickChangingPacket = false; });
	    return false;


	}

}); 
 
') ?>
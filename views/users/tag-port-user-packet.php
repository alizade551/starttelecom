<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;



 ?>

<div style="max-width: 400px;">
	<h5><?=Yii::t("app",'{fullname} packet {packet_name} tagging port',['fullname'=>$model->user->fullname,'packet_name'=>$model->packet->packet_name]) ?></h5>
	<?php $form = ActiveForm::begin([
		'id'=>"user-packet-tag-port",
		'enableAjaxValidation' => true,
		'validateOnSubmit'=> true,
		'enableClientValidation'=>false,
		'validationUrl' => 'tag-port-user-packet-validate',
		'options' => ['autocomplete' => 'off']
		]); 
	?>
		<?= $form->field($model, 'user_id')->hiddenInput(['value' => $model->user_id])->label(false) ?>

		<?= $form->field($model, 'service_id')->hiddenInput(['value' => $model->service_id])->label(false) ?>

		<?= $form->field($model, 'packet_tags')->hiddenInput(['value' => $model->packet_id])->label(false) ?>

		<div class="form-group ">
			<label class="control-label"><?=Yii::t('app','Service') ?></label>
			<input type="text" class="form-control"  value="<?=$model->service->service_name ?>" disabled>
		</div>


		<div class="form-group ">
			<label class="control-label"><?=Yii::t('app','Packet name') ?></label>
			<input type="text" class="form-control"  value="<?=$model->packet->packet_name ?>" disabled>
		</div>

	    <?= $form->field($model, 'port_type')->dropDownList(
	        ArrayHelper::map(
	           \app\models\UsersServicesPackets::getPortType($model->user_id),
	            'device_type',
	            'device_type'
	        ),
	        [
	            'onchange'=>'
	                $(".select_loader").show();
	                $.pjax.reload({
	                    url: "'.Url::to(['tag-port-user-packet']).'?id='.$model->id.'&service_id='.$model->service_id.'&port_type="+$(this).val(),
	                    container: "#pjax-tagging-port-form-devices",
	                    timeout: 5000
	                });
	            ',

	            'prompt'=>Yii::t("app","Select")
	        ]
	    ) ?>

	    <?php  Pjax::begin(['id'=>'pjax-tagging-port-form-devices','enablePushState'=>true]);  ?>

	      <?php if ( Yii::$app->request->get('port_type')  == "switch" ): ?>

	           <?= $form->field($model, 'devices')->dropDownList(
	                ArrayHelper::map(
	                   \app\models\UsersServicesPackets::getSwitches($model->user_id),
	                    'device_id',
	                    'device_name'
	                ),
	                [
	                    'onchange'=>'
	                        $.pjax.reload({
	                            url: "'.Url::to(['tag-port-user-packet']).'?id='.$model->id .'&service_id='.$model->service_id.'&port_type='.Yii::$app->request->get('port_type').' &switchValue="+$(this).val(),
	                            container: "#pjax-tagging-port-form-switchValue",
	                            timeout: 5000
	                        });
	                    ',

	                    'prompt'=>Yii::t("app","Select")
	                ]
	            ) ?>

	            <?php 
	                foreach ($form->attributes as $attribute) {
	                    $attribute = Json::htmlEncode($attribute);
	                    $this->registerJs("jQuery('form#user-packet-tag-port').yiiActiveForm('add', $attribute);");
	                } 
	            ?>



	      <?php endif ?>


          <?php if ( Yii::$app->request->get('port_type')  == "epon"  ||  Yii::$app->request->get('port_type')  == "gpon" ): ?>
              
               <?= $form->field($model, 'devices')->dropDownList(
                    ArrayHelper::map(
                       \app\models\UsersServicesPackets::getOlt($model->user_id,Yii::$app->request->get('port_type')),
                        'device_id',
                        'device_name'
                    ),
                    [
                        'onchange'=>'
                            $.pjax.reload({
                                url: "'.Url::to(['tag-port-user-packet']).'?id='.$model->id.'&service_id='.$model->service_id.'&port_type='.Yii::$app->request->get('port_type').' &deviceValue="+$(this).val(),
                                container: "#pjax-tagging-port-form-switchValue",
                                timeout: 5000
                            });

                        ',

                        'prompt'=>Yii::t("app","Select")
                    ]
                ) ?>

          <?php endif ?>


		    <?php  Pjax::begin(['id'=>'pjax-tagging-port-form-switchValue','enablePushState'=>true]);  ?>

			    <?php if ( Yii::$app->request->get('port_type')  == "switch" || Yii::$app->request->get('switchValue') != ""  ): ?>
			           <?= $form->field($model, 'switch_port')->dropDownList(
			                ArrayHelper::map(
			                   \app\models\UsersServicesPackets::getSwitchPort(Yii::$app->request->get('switchValue')),
			                    'id',
			                    'port_number'
			                ),
			                [
			                    'prompt'=>Yii::t("app","Select")
			                ]
			            ) ?>
			        <?php 
			            foreach ($form->attributes as $attribute) {
			                $attribute = Json::htmlEncode($attribute);
			                $this->registerJs("jQuery('form#user-packet-tag-port').yiiActiveForm('add', $attribute);");
			            } 
			        ?>
			    <?php endif ?>



                <?php if (Yii::$app->request->get('port_type')  == "epon" || Yii::$app->request->get('port_type')  == "gpon" ||   Yii::$app->request->get('deviceValue') != "" ): ?>
                   
                       <?= $form->field($model, 'box')->dropDownList(
                            ArrayHelper::map(
                               \app\models\UsersServicesPackets::getOltBox(Yii::$app->request->get('deviceValue'),$model->user_id),
                                'id',
                                'box_name'
                            ),
                            [
                            'onchange'=>'
                                $.pjax.reload({
                                    url: "'.Url::to(['tag-port-user-packet']).'?id='.$model->id.'&service_id='.$model->service_id.'&port_type='.Yii::$app->request->get('port_type').'&deviceValue='.Yii::$app->request->get('deviceValue').' &box="+$(this).val(),
                                    container: "#pjax-tagging-port-form-box",
                                    timeout: 5000
                                });
                            ',


                                'prompt'=>Yii::t("app","Select")
                            ]
                        ) ?>
                    <?php 
                        foreach ($form->attributes as $attribute) {
                            $attribute = Json::htmlEncode($attribute);
                            $this->registerJs("jQuery('form#user-packet-tag-port').yiiActiveForm('add', $attribute);");
                        } 
                    ?>

                <?php endif ?>


                <?php  Pjax::begin(['id'=>'pjax-tagging-port-form-box','enablePushState'=>true]);  ?>
                  <?php if ( Yii::$app->request->get('box')  != "" ): ?>
                           <?= $form->field($model, 'box_port')->dropDownList(
                                ArrayHelper::map(
                                   \app\models\UsersServicesPackets::getOltBoxPort(Yii::$app->request->get('box')),
                                    'id',
                                    'port_number'
                                ),
                                [
                                    'prompt'=>Yii::t("app","Choose")
                                ]
                            ) ?>
                        <?php 
                            foreach ($form->attributes as $attribute) {
                                $attribute = Json::htmlEncode($attribute);
                                $this->registerJs("jQuery('form#user-packet-tag-port').yiiActiveForm('add', $attribute);");
                            } 
                        ?>                                  
                    <?php endif ?>
                  <?php Pjax::end(); ?>  





		    <?php Pjax::end(); ?>  
	    <?php Pjax::end(); ?>  



	    <div class="form-group">
	        <?= Html::submitButton(Yii::t("app","Save"), ['class' => 'btn btn-primary']) ?>
	    </div>

	<?php ActiveForm::end(); ?>
</div>



<?php 
$this->registerJs('
	var clickTaggingPort = false;

	var packetId = '.Yii::$app->request->get("id").';
	var xhrTaggingPort;
	var xhrTaggingPortActive=false;
	var formTagPort = $("form#user-packet-tag-port");

	$("form#user-packet-tag-port").on("beforeSubmit", function (e) {
	    if(!clickTaggingPort){
	        clickTaggingPort = true;
	        if( formTagPort.find(".btn-primary").prop("disabled")){
	            return false;
	        }
	        if(xhrTaggingPortActive) { xhrTaggingPort.abort(); }
	        xhrTaggingPortActive=true;
	        formTagPort.find(".btn-success").prop("disabled",true);
	            
	        xhrTaggingPort = $.ajax({
	          url: "'.\yii\helpers\Url::to(["users/tag-port-user-packet"]).'?id="+packetId,
	          type: "post",
	          beforeSend:function(){
	            $(".loader").show();
	            $(".overlay").addClass("show");
	          },
	          data: formTagPort.serialize(),
	          success: function (response) {
	              $(".loader").hide();
	              $(".overlay").removeClass("show");

	            if(response.status == "error"){
	                 alertify.set("notifier","position", "top-right");
	                 alertify.error(response.message);
	            }          

	            if(response.status == "success"){
					alertify.set("notifier","position", "top-right");
					alertify.success(response.message);
					$.fancybox.close();

	            }else{
	                xhrTaggingPortActive=false;
	                formTagPort.find(".btn-primary").prop("disabled",false);
	            }

	          }
	        }).done(function(){ clickTaggingPort = false; });
	        return false;
	    }
	}); 
');


 ?>
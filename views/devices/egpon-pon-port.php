<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Cities */
/* @var $form yii\widgets\ActiveForm */
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
 

 $this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].' ', ['depends' => [yii\web\JqueryAsset::className()]]);
?>

<div style="width:800px;">
 	<h4><?=Yii::t('app','Device {device} {pon_port} pon-port setting',['device'=>$model->device->name,'pon_port'=>$model->pon_port_number]) ?></h4>
	<?php $form = ActiveForm::begin([
            'id'=>"pon-port-form",
	]);?>


		
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group ">
					<label for="switchports-port_number"><?=Yii::t('app','Pon-port') ?></label>
					<input type="text" disabled="disabled" class="form-control" value="<?=$model->pon_port_number ?>" >
				</div>
			</div>


			<div class="col-sm-6">
				<?=$form->field($model, 'splitting')->dropDownList(\app\models\EgponPonPort::splitPonPort(),['prompt'=>Yii::t('app','Select')])->label() ?>

			</div>
			<div class="col-sm-6">
				<?php  if( $model->device->type == "epon" ) { $capacity = 64; }elseif( $model->device->type == "gpon" ){ $capacity = 128; }else{ $capacity = 256; } ?>
				<?php $splitting = ($model->splitting == null) ? "1" : $model->splitting; ?>
				<div class="form-group switch-box ">
					<label for="switchports-port_number"><?=Yii::t('app','Each box port count') ?></label>
					<input data-capacity="<?=$capacity ?>" type="text" disabled="disabled" class="form-control box-count" value="<?=$capacity/$splitting ?>" >
				</div>
			</div>

			<div class="col-sm-6">
				<?=$form->field($model, 'status')->dropDownList(\app\models\EgponPonPort::ponPortStatus(),['prompt'=>Yii::t('app','Select')])->label() ?>
			</div>
		</div>
		<div class="form-group">
			<?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success pon-port-btn','data-id'=>$model['id']])?>
		</div>
	<?php ActiveForm::end();?>
		<div class="row">
			<div class="col-sm-12">
				<div class="col-lg-12 animatedParent animateOnce z-index-50">
				    <div class="panel panel-default animated fadeInUp">
				        <div class="panel-body">
				            <div class="table-responsive">
				            	 <div style="max-height: 400px; overflow: auto;">
					                <table class="table pon-port-table">
					                    <thead> 
					                        <tr> 
					                            <th>#</th> 
					                            <th><?=Yii::t('app',"Box") ?></th> 
					                            <th><?=Yii::t('app',"Location") ?></th> 
					                            <th><?=Yii::t('app',"Capacity") ?></th> 
					                            <?php if ( User::canRoute(['/devices/box-on-map']) ): ?>
					                            	<th><?=Yii::t('app',"Cordinate") ?></th> 
					                            <?php endif ?>
					                            <?php if ( User::canRoute(['/devices/use-box-port']) ): ?>
					                            	<th><?=Yii::t('app',"View") ?></th> 
					                            <?php endif ?>


					                            <?php if ( User::canRoute(['/devices/box-port-setting']) ): ?>
					                            	<th><?=Yii::t('app',"Define") ?></th>
					                            <?php endif ?>
					                        </tr> 
					                    </thead> 
					                    <tbody> 
					                    	<?php foreach ($ponPortBoxes as $key => $box): ?>
					                    		<?php 
					                    			
					                    			$busyPorts = \app\models\EgonBoxPorts::find()->where(['egon_box_id'=>$box['id']])->andWhere(['not', ['egon_box_ports.u_s_p_i' => null]])->count();
					                    		 ?>
						                        <tr> 
						                            <td><?=$key+1 ?></td> 
						                            <td><?=$box['box_name'] ?></td> 
						                            <td class="location"><?=$location = ( $box->location_id != null ) ? $box->location->name : Yii::t('app','Location not defined'); ?></td> 
						                            <td style="text-align:center;"><?=$busyPorts ?>/<?=$capacity/$box->egponPonPort->splitting ?></td> 
						                            <?php if ( User::canRoute(['/devices/box-on-map']) ): ?>
							                            <td style="text-align:center;">
							                            	<?php if ( $box->location_id != null ): ?>
								                            	<a data-fancybox="" data-type="ajax" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/devices/box-on-map?id=<?=$box['id'] ?>" href="javascript:;" href="">
								                            		<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
								                            	</a>
							                            		
							                            	<?php else: ?>
							 									<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
							                            	<?php endif ?>
							                            </td>
						                            <?php endif ?>
						                            <?php if ( User::canRoute(['/devices/use-box-port']) ): ?>
							                            <td>
							                            	<a data-fancybox="" data-type="ajax" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/devices/use-box-port?id=<?=$box['id'] ?>" href="javascript:;" href="">
							                            		<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
							                            	</a>
							                            </td>
						                            <?php endif ?>

						       


						                            <?php if ( User::canRoute(['/devices/box-port-setting']) ): ?>
							                            <td class="change-packet">
							                                <a data-fancybox="" data-type="ajax" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/devices/box-port-setting?id=<?=$box['id'] ?>" href="javascript:;">
							                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
							                                </a>
							                            </td>
						                            <?php endif ?>


						                        </tr> 
					                    	<?php endforeach ?>
					                    </tbody> 
					                </table>
				                 </div>
				            </div>
				        </div>
				    </div>
				</div>
			</div>
		</div>
</div>

<?php $this->registerJs('




$(document).on("change","#egponponport-splitting",function(){
	var that = $(this);
	 let split_value = that.val();
	 let capacity = that.closest(".row").find(".switch-box").find("input").data("capacity");
	 if(split_value != ""){
	 	that.closest(".row").find(".switch-box").find("input").val(capacity/split_value);
	 }else{
	 	that.closest(".row").find(".switch-box").find("input").val("");
	 }
});

var clickPonPort = false;

var xhrPonPort;
var xhrActivePonPort=false;
var formPonPort = $("form#pon-port-form");

$("form#pon-port-form").on("beforeSubmit", function (e) {
	if(!clickPonPort){
		var swithId =  formPonPort.find(".pon-port-btn").attr("data-id");
        clickPonPort = true;
	    if( formPonPort.find(".pon-port-btn").prop("disabled")){
	        return false;
	    }
	    if(xhrActivePonPort) { xhrPonPort.abort(); }
	    xhrActivePonPort = true;
	    formPonPort.find(".pon-port-btn").prop("disabled",true);

	    xhrPonPort = $.ajax({
	      url: "'.\yii\helpers\Url::to(["devices/split-pon-port"]).'?id="+swithId,
	      type: "post",
	      beforeSend:function(){
	        $(".loader").show();
	        $(".overlay").addClass("show");
	      },
	      data: formPonPort.serialize(),
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
	            xhrActivePonPort=false;
	            formPonPort.find(".pon-port-btn").prop("disabled",false);
	        }
	      }
	    }).done(function(){ clickPonPort = false; });
	    return false;
	}

}); 
 
') ?>

<style type="text/css">
.pon-port-table .table td, .pon-port-table .table th {
	text-align: center !important;
}
.box{
	cursor: pointer;
	background: #9e9e9e;
	line-height: 60px;
	text-align: center;
	border: 1px solid #beb4b4;
	padding: 10px; 
}
.pon-port-table table td {text-align: center;}
</style>
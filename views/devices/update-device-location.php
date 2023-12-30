<?php 
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Json;

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

 ?>
 	<div style="width:400px;">
 		<h4><?=Yii::t('app','Update device location') ?></h4>
	    <?php $form = ActiveForm::begin([
	            'id'=>"update-device-location",
	            'enableAjaxValidation' => true,
	            'validateOnSubmit'=> true,
	            'enableClientValidation'=>false,
	            'validationUrl' => $langUrl.'/devices/add-location-validate',
	            'options' => ['autocomplete' => 'off']]);
	    ?>

		<div class="form-group ">
			<label ><?=Yii::t('app','Device') ?></label>
			<input type="text" class="form-control" value="<?=$model->device->name ?>" disabled>
		</div>

   		 <?= $form->field($model, 'device_id')->hiddenInput(['value'=>$model['device_id']])->label(false) ?>

	    <?=$form->field($model, 'city_id')->dropDownList(ArrayHelper::map(\app\models\Cities::find()->all(),'id','city_name'),[
	         'onchange'=>'
	            $.pjax.reload({
	            url: "'.Url::to(['/devices/update-device-location']).'?id='.$model->id.'&city_id="+$(this).val(),
	            container: "#pjax-update-device-location-form",
	            timeout: 5000
	            });
	        ',
	        'prompt'=>'Select City'])->label();
	    ?>
   		 <?php  Pjax::begin(['id'=>'pjax-update-device-location-form','enablePushState'=>true]);  ?>
	     <?php 
		    if (Yii::$app->request->get('city_id')) {
		               echo  $form->field($model, 'district_id')->dropDownList(
		               	ArrayHelper::map(
		               	\app\models\District::find()
		                ->where(['city_id'=>Yii::$app->request->get('city_id')])
		                // ->andWhere(['!=', 'id', $model->district_id])
		                ->all(),
		                'id',
		                'district_name'
		            ),[
		         'onchange'=>'
		            $(".select_loader").show();
		            $.pjax.reload({
		            url: "'.Url::to(['/devices/update-device-location']).'?id='.$model->id.'&city_id='.Yii::$app->request->get('city_id').'&district_id="+$(this).val(),
		            container: "#pjax-update-device-location-form-district",
		            timeout: 5000
		            });
		            $(document).on("pjax:complete", function() {
		              $(".select_loader").hide();
		            });
		        ',
		        'prompt'=>Yii::t('app','Select')]);
		    }else{
		       echo  $form->field($model, 'district_id')->dropDownList(
		       	ArrayHelper::map(
		       			\app\models\District::find()
		                ->where(['city_id'=>Yii::$app->request->get('city_id')])
		                ->andWhere(['!=', 'id', $model->district_id])
		                ->all(),
		                'id',
		                'district_name
		                '),[
		         'onchange'=>'
		            $(".select_loader").show();
		            $.pjax.reload({
		            url: "'.Url::to(['/users/update']).'?id='.$model->id.'&city_id='.Yii::$app->request->get('city_id').'&district_id='.Yii::$app->request->get('district_id').'",
		            container: "#pjax-update-device-location-form-district",
		            timeout: 5000
		            });
		            $(document).on("pjax:complete", function() {
		              $(".select_loader").hide();
		            });
		        ',
		        'prompt'=>Yii::t('app','Select')])->label();  
		    }
	     ?>
    <?php Pjax::end(); ?>  

    <?php if ($model->device->type == "switch"): ?>
	    <?php  Pjax::begin(['id'=>'pjax-update-device-location-form-district','enablePushState'=>true]);  ?>

	    <?= $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(\app\models\Locations::find()
	                ->where(['city_id'=>Yii::$app->request->get('city_id')])->andWhere(['district_id'=>Yii::$app->request->get('district_id')])->all(),'id','name')) ?>
		<?php Pjax::end(); ?>
    <?php endif ?>

    <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-primary update-location',]) ?>

<?php ActiveForm::end(); ?>
</div>


<?php $this->registerJs('

var clickUpdateLocation = false;
var userPacketId = '.Yii::$app->request->get("id").';

var xhrUpdateLocation;
var xhrActiveUpdateLocation=false;
var formUpdateLocation = $("form#update-device-location");

$("form#update-device-location").on("beforeSubmit", function (e) {
	if(!clickUpdateLocation){

        clickUpdateLocation = true;
	    if( formUpdateLocation.find(".update-location").prop("disabled")){
	        return false;
	    }
	    if(xhrActiveUpdateLocation) { xhrUpdateLocation.abort(); }
	    xhrActiveUpdateLocation = true;
	    formUpdateLocation.find(".btn-primary").prop("disabled",true);

	    xhrUpdateLocation = $.ajax({
	      url: "'.\yii\helpers\Url::to(["devices/update-device-location"]).'?id='.$model->id.'",
	      type: "post",
	      beforeSend:function(){
	        $(".loader").show();
	        $(".overlay").addClass("show");
	      },
	      data: formUpdateLocation.serialize(),
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
	            xhrActiveUpdateLocation=false;
	            formUpdateLocation.find(".btn-primary").prop("disabled",false);
	        }

	      }
	    }).done(function(){ clickUpdateLocation = false; });
	    return false;


	}

}); 
 
') ?>
<?php

use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;
use borales\extensions\phoneInput\PhoneInput;



$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

?>
<?php if ( $supportedDistrictsQuery != null && $supportedCity != null ): ?>
	<div class="alert alert-success mb-4" role="alert">
	   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert">
	         <line x1="18" y1="6" x2="6" y2="18"></line>
	         <line x1="6" y1="6" x2="18" y2="18"></line>
	      </svg>
	   </button>
	   <strong ><?=Yii::t('app','Note') ?> !</strong> <?=Yii::t('app','First packets will checking and adding. Integrated packets price is 0. You can edit packets on packets list,Then users will adding to  order list ,You can edit the integrated users later on orders list') ?> 
	</div>
	<div class="row">
	    <div class="col-sm-12">
	        <?php $form = ActiveForm::begin([
	            'id'=>"request-order-form",
	            'enableAjaxValidation' => true,
	            'validationUrl' => $langUrl .'/routers/integrate-validate',
	            'options' => ['autocomplete' => 'off']
	        ]
	        ); ?>
			<div class="row">
			      <div class="col-sm-6">
					<div class="form-group field-users-fullname required">
						<label for="users-fullname"><?=Yii::t('app','Fullname') ?></label>
						<input type="text" disabled class="form-control "  maxlength="120" value="<?=Yii::t('app','System add ppoe login as fullname') ?>" aria-required="true" >
					</div>
			      </div>
			      <div class="col-sm-6">
					<div class="form-group field-users-fullname required">
						<label for="users-fullname"><?=Yii::t('app','City') ?></label>
						<input type="text" disabled="" class="form-control " maxlength="120" value="<?=$supportedCity['city_name'] ?>" aria-required="true">
					</div>
			      </div>
			      <div class="col-sm-6">
			         <?php  Pjax::begin(['id'=>'pjax-request-form','enablePushState'=>true]);  ?>
			            <?php 
			                $allDistrictPjaxGet = ArrayHelper::map(
			                        $supportedDistrictsQuery 
			                        ->withByDistrictId()
			                        ->orderBy(['district_name'=>SORT_ASC])
			                        ->all(),
			                        'id',
			                        'district_name'
			                    );
			                echo  $form->field($model, 'district_id',['enableAjaxValidation' => true,'template' => '{label}<div class="form-select">{input}<div class="spinner-border text-success select_loader  align-self-center loader-sm "></div></div>{error}'])->dropDownList($allDistrictPjaxGet,[
			                 'onchange'=>'
			                    $(".select_loader").show();
			                    $.pjax.reload({
			                    url: "'.Url::to(['/routers/integrate']).'?id='.$routerModel['id'].'&dis_id="+$(this).val(),
			                    container: "#pjax-request-form-dis",
			                    timeout: 5000
			                    });
			                    $(document).on("pjax:complete", function() {
			                      $(".select_loader").hide();
			                    });
			                ',
			                'prompt'=>Yii::t("app","Select")]);            
			            ?>
			        <?php Pjax::end(); ?>  
			      </div>
				<div class="col-sm-6">
				    <?php  Pjax::begin(['id'=>'pjax-request-form-dis','enablePushState'=>true]);  ?>
				        <?php if (  Yii::$app->request->get('dis_id') && Yii::$app->request->isPjax ): ?>
				            <?= $form->field($model, 'location_id')->dropDownList(
				                ArrayHelper::map(
				                    \app\models\Locations::find()
				                    ->where(["district_id"=>Yii::$app->request->get("dis_id")])
				                    ->withByLocationId()
				                    ->orderBy(['name'=>SORT_ASC])
				                    ->all(),
				                    'id',
				                    'name'
				                ),['prompt'=>Yii::t("app","Select")]); 
				            ?>
				            <?php 
				                foreach ($form->attributes as $attribute) {
				                    $attribute = Json::htmlEncode($attribute);
				                    $this->registerJs("jQuery('form#request-order-form').yiiActiveForm('add', $attribute); ");
				                } 
				             ?>
				          <?php else: ?>
				            <?= $form->field($model, 'location_id')->dropDownList([]); ?>
				          <?php endif ?>
				    <?php Pjax::end(); ?>       
				<?php if ($model->isNewRecord): ?>
				    <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>
				    <?= $form->field($model, 'request_at')->hiddenInput(['value'=>time()])->label(false) ?>
				<?php endif ?>
				</div>
			</div>
	        <?= Html::submitButton( '<span class="spinner-border  mr-2 align-self-center "></span>' .Yii::t("app","Start integration"), ['class' => 'btn btn-primary']) ?>
	    <?php ActiveForm::end(); ?>
		</div>
	</div>
	<?php 

	$this->registerJs('

	$("#request-order-form").on("keyup keypress", function(e) {
	  var keyCode = e.keyCode || e.which;
	  if (keyCode === 13) { 
	    e.preventDefault();
	    return false;
	  }
	});

	var xhr;
	var xhr_active=false;
	var form = $("form#request-order-form");
	form.on("beforeSubmit", function (e) {

		if( form.find("button").prop("disabled")){
		return false;
		}
	    if(xhr_active) { xhr.abort(); }
	    xhr_active=true;
	    form.find("button").prop("disabled",true);
	   
	     xhr = $.ajax({
	          url: "'.\yii\helpers\Url::to(["routers/integrate?id="]).$routerModel['id'].'",
	          type: "post",
	          data: form.serialize(),
			  beforeSend:function(){
					form.find(".spinner-border").addClass("show");
					form.find("button").prop("disabled",true);
			   },
	          success: function (response) {
	              if(response.status == "success"){
					form.find(".spinner-border").removeClass("show");
					alertify.set("notifier","position", "top-right");
	                alertify.success(response.message);
	                 $("#modal").modal("hide");
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
<?php else: ?>
<div class="alert alert-icon-left alert-light-warning mb-4" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" data-dismiss="alert" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12" y2="17"></line></svg>
    <strong><?=Yii::t('app','Warning') ?>!</strong> <?=Yii::t('app','Please add district for router') ?>
</div>
<?php endif ?>

<style type="text/css">
.router-card-inf-container{
    display: flex;
    justify-content: space-between;
}
.spinner-border {
    width: 1rem;
    height: 1rem;
    display: none;

}
.spinner-border.show{
	display: inline-block;
}

.router-inf .badge{
	margin-left: 10px;
}
</style>
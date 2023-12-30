<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

?>


<?php 
	$form = ActiveForm::begin([
		'id'=>'contract-update-form',
		'enableClientValidation' => true, 
		'enableAjaxValidation' => true
	]);
 ?>

	    <div class="row">
			<div class="col-sm-6">
				<div class="form-group field-users-fullname required has-success">
					<label class="control-label" for="users-fullname"><?=Yii::t('app','Customer') ?></label>
					<input type="text" disabled id="users-fullname" class="form-control"  value="<?=$model->fullname ?>" >
				</div>			
			</div>
			<div class="col-sm-6">
			        <?= $form->field($model, 'contract_number')->textInput(['maxlength' => true]) ?>
			</div>
	    </div>


	<?= Html::submitButton(Yii::t("app","Update"), ['class' => 'btn btn-primary']) ?>
	<a class="btn btn-secondary" title="<?=Yii::t('app','Close') ?>" style="margin-left: 5px;" ><?=Yii::t('app','Close') ?></a>     

<?php ActiveForm::end(); ?>  
<style type="text/css">
    #contract-update-form {width: 100%;}
</style>	
<?php
$this->registerJs('
	$("#modal").on("shown.bs.modal", function () {
	    $("#users-contract_number").focus();
	})  
	$(".btn-secondary").on("click",function(){
		$("#modal").modal("toggle");
	});
')


?>
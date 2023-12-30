<?php 
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;


$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

?>
<div class="row">
	<div class="col-sm-12">
		<?php $form = ActiveForm::begin([
		    'id'=>"add-report-forum",
		    'enableAjaxValidation' => true,
		    'enableClientValidation'=>false,
		    'validationUrl' =>  $langUrl .'add-report-validate',
		    'options' => ['autocomplete' => 'off']]);
		?>
			<?=$form->field($model, 'damage_reason')->dropDownList(
				$damage_reasons,[
				   'prompt'=>Yii::t('app','Select'),
			        'onchange'=>'
			            let balance_in = $("#requestorder-balance_in").val();
			            let request_type = $(this).val();
			            let id = "'.$model->id.'";
			            let that = $(this);

                        $.pjax.reload({
                             url: "'.Url::to(['users/add-report?id='.$model->id]).'&reason="+that.val(),
                             container: "#pjax-accept-user-form",
                             timeout: 5000
                        });
			        ',
				]
			); 
			?>

			<?php  Pjax::begin(['id'=>'pjax-accept-user-form']);  ?>
		        <?php if ( Yii::$app->request->isPjax && Yii::$app->request->get("reason")  ): ?>
					<?=$form->field($model, 'message')->textarea() ?>
		            <?php 
		                foreach ($form->attributes as $attribute) {
		                $attribute = Json::htmlEncode($attribute);
		                    $this->registerJs("jQuery('form#add-report-forum').yiiActiveForm('add', $attribute);");
		                } 
		            ?>
				<?php else: ?>
					<?=$form->field($model, 'message')->textarea() ?>
		        <?php endif ?>
			<?php Pjax::end(); ?>  

			<?= Html::submitButton(Yii::t("app","Add"),['class'=>'btn btn-primary']) ?>
		<?php ActiveForm::end(); ?>
	</div>
</div>


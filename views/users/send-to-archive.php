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
		    'enableAjaxValidation' => false,
		    'enableClientValidation'=>true,
		    'options' => ['autocomplete' => 'off'
		]]);
		?>
			<?=$form->field($model, 'archive_reason')->dropDownList(
				$archive_reasons
			); 
			?>

			<?= Html::submitButton(Yii::t("app","Send to archive"),['class'=>'btn btn-primary']) ?>
		<?php ActiveForm::end(); ?>
	</div>
</div>


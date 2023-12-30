<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cities */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin();?>
	<?=$form->field($model, 'city_name')->textInput(['maxlength' => true])?>
	<?php if ($model->isNewRecord): ?>
	<div class="form-group">
	<?=Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success'])?>
	</div>
	<?php else: ?>
	<div class="form-group">
	<?=Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary'])?>
	</div>
	<?php endif?>
<?php ActiveForm::end();?>



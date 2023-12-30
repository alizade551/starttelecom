<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup $model
 * @var yii\bootstrap\ActiveForm $form
 */
?>


<?php $form = ActiveForm::begin([
	'id'=>'auth-item-group-form',
	'layout'=>'horizontal',
	'validateOnBlur' => false,

]); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'autofocus'=>$model->isNewRecord ? true:false]) ?>

<?= $form->field($model, 'code')->textInput(['maxlength' => 64]) ?>


	<?php if ( $model->isNewRecord ): ?>
		<?= Html::submitButton(
			 Yii::t('app', 'Create'),['class' => 'btn btn-primary']) ?>
	<?php else: ?>
		<?= Html::submitButton(Yii::t('app', 'Update'),['class' => 'btn btn-secondary']) ?>
	<?php endif; ?>


<?php ActiveForm::end(); ?>



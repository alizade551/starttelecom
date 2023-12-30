<?php
/**
 * @var yii\widgets\ActiveForm $form
 * @var webvimark\modules\UserManagement\models\rbacDB\Role $model
 */
use webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;
?>


	<?php $form = ActiveForm::begin([
	'id'             => 'role-form',
	'layout'         => 'horizontal',
	'validateOnBlur' => false,
]) ?>

	<?= $form->field($model, 'description')->textInput(['maxlength' => 255, 'autofocus'=>$model->isNewRecord ? true:false]) ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

<?php if ( $model->isNewRecord ): ?>
	<?= Html::submitButton(Yii::t('app', 'Add'),['class' => 'btn btn-success']) ?>
<?php else: ?>
	<?= Html::submitButton(Yii::t('app', 'Update'),['class' => 'btn btn-primary']) ?>
<?php endif; ?>

<?php ActiveForm::end() ?>

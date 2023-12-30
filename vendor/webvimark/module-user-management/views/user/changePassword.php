<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */

$this->title = Yii::t('app', 'Password update') . ' : ' . $model->username;

?>
<div class="custom-modal" style="min-width: 600px !important">
	<?php $form = ActiveForm::begin([
		'id'=>'user',
		'layout'=>'horizontal',
	]); ?>
	<?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
	<?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
	<?= Html::submitButton( Yii::t('app', 'Update'),['class' => 'btn btn-primary']) ?>
	<?php ActiveForm::end(); ?>
</div>

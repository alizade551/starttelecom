<?php
/**
 * @var yii\widgets\ActiveForm $form
 * @var webvimark\modules\UserManagement\models\rbacDB\Permission $model
 */

use webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
?>

<?php $form = ActiveForm::begin([
'id'      => 'role-form',
'layout'=>'horizontal',
'validateOnBlur' => false,
]) ?>
	<?= $form->field($model, 'description')->textInput(['maxlength' => 255, 'autofocus'=>$model->isNewRecord ? true:false]) ?>
	<?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
	<?= $form->field($model, 'group_code')
		->dropDownList(ArrayHelper::map(AuthItemGroup::find()->asArray()->all(), 'code', 'name'), ['prompt'=>'']) ?>
	<?php if ( $model->isNewRecord ): ?>
		<?= Html::submitButton( Yii::t('app', 'Create'),['class' => 'btn btn-success']) ?>
	<?php else: ?>
		<?= Html::submitButton(Yii::t('app', 'Update'),['class' => 'btn btn-primary']) ?>
	<?php endif; ?>

<?php ActiveForm::end() ?>

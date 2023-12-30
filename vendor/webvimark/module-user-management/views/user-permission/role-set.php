<?php 
use webvimark\modules\UserManagement\models\rbacDB\Role;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use webvimark\modules\UserManagement\components\GhostHtml;
use kartik\select2\Select2;



 ?>

<div class="panel-panel-default" style="min-width: 400px">
	<?= Html::beginForm(['set-roles', 'id'=>$user->id]) ?>
	<?php $roles  = []; ?>
		<?php foreach (Role::getAvailableRoles() as $aRole): ?>
			<?php $isChecked = in_array($aRole['name'], ArrayHelper::map(Role::getUserRoles($user->id), 'name', 'name')) ? 'checked' : '' ?>
			<?php if ( Yii::$app->getModule('user-management')->userCanHaveMultipleRoles ): ?>
				<?php $roles[$aRole['name']] = $aRole['name'] ?>
			<?php endif; ?>
		<?php endforeach ?>
		<?php 
		echo Select2::widget([
		    'name' => 'status',
		        'bsVersion' => '4.x',
		    'hideSearch' => true,
		    // 'data' => [1 => 'Active', 2 => 'Inactive'],
		    // 'options' => ['placeholder' => 'Select status...'],
		    // 'pluginOptions' => [
		    //     'allowClear' => true
		    // ],
		]);


		$data = [];

		echo '<div class="role-c"><label class="control-label">'.Yii::t('app','Role').'</label>';
		echo Select2::widget([
		    'name' => 'roles[]',
		    'bsVersion' => '4.x',
		    'value' => ArrayHelper::map(Role::getUserRoles($user->id), 'name', 'name'), // initial value
		    'data' => $roles,
		    'maintainOrder' => false,
		    'options' => ['placeholder' => Yii::t("app","Choose"), 'multiple' => false],
		    'pluginOptions' => [
		        'tags' => false,

		         'allowClear' => false
		    
		    ],
		]);
		echo '</div>';


		 ?>
		<?php if ( Yii::$app->user->isSuperadmin OR Yii::$app->user->id != $user->id ): ?>
			<br>
		<?= Html::submitButton(
				Yii::t('app', 'Assign'),
				['class'=>'btn btn-primary']
			) ?>
		<?php else: ?>
			<div class="alert alert-warning well-sm text-center">
				<?= Yii::t('app', 'You do not have the authority to perform this operation.') ?>
			</div>
		<?php endif; ?>
	<?= Html::endForm() ?>
</div>

<style type="text/css">
	.role-c .select2-container--krajee-bs4{display: block !important;}
</style>
<?php 
$this->registerJs("
	$('#w0').parent().hide();
")
 ?>
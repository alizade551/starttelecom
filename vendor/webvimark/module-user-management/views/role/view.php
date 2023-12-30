<?php
/**
 * @var yii\widgets\ActiveForm $form
 * @var array $childRoles
 * @var array $allRoles
 * @var array $routes
 * @var array $currentRoutes
 * @var array $permissionsByGroup
 * @var array $currentPermissions
 * @var yii\rbac\Role $role
 */

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Permission for a role : {role}',['role'=>$role->description]);

?>

<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="/">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 511 511.999" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M498.7 222.695c-.016-.011-.028-.027-.04-.039L289.805 13.81C280.902 4.902 269.066 0 256.477 0c-12.59 0-24.426 4.902-33.332 13.809L14.398 222.55c-.07.07-.144.144-.21.215-18.282 18.386-18.25 48.218.09 66.558 8.378 8.383 19.44 13.235 31.273 13.746.484.047.969.07 1.457.07h8.32v153.696c0 30.418 24.75 55.164 55.168 55.164h81.711c8.285 0 15-6.719 15-15V376.5c0-13.879 11.293-25.168 25.172-25.168h48.195c13.88 0 25.168 11.29 25.168 25.168V497c0 8.281 6.715 15 15 15h81.711c30.422 0 55.168-24.746 55.168-55.164V303.14h7.719c12.586 0 24.422-4.903 33.332-13.813 18.36-18.367 18.367-48.254.027-66.633zm-21.243 45.422a17.03 17.03 0 0 1-12.117 5.024H442.62c-8.285 0-15 6.714-15 15v168.695c0 13.875-11.289 25.164-25.168 25.164h-66.71V376.5c0-30.418-24.747-55.168-55.169-55.168H232.38c-30.422 0-55.172 24.75-55.172 55.168V482h-66.71c-13.876 0-25.169-11.29-25.169-25.164V288.14c0-8.286-6.715-15-15-15H48a13.9 13.9 0 0 0-.703-.032c-4.469-.078-8.66-1.851-11.8-4.996-6.68-6.68-6.68-17.55 0-24.234.003 0 .003-.004.007-.008l.012-.012L244.363 35.02A17.003 17.003 0 0 1 256.477 30c4.574 0 8.875 1.781 12.113 5.02l208.8 208.796.098.094c6.645 6.692 6.633 17.54-.031 24.207zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
            </a>
        </li>

        <li class="breadcrumb-item">
            <a href="javascript:void(0);">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" fill-rule="evenodd" class=""><g><path d="M10.5 20.263H2.95a.2.2 0 0 1-.2-.2v-1.45c0-.831.593-1.563 1.507-2.185 1.632-1.114 4.273-1.816 7.243-1.816.49 0 .971.02 1.441.057a.75.75 0 1 0 .118-1.495 19.38 19.38 0 0 0-1.559-.062c-3.322 0-6.263.831-8.089 2.076-1.393.95-2.161 2.157-2.161 3.424v1.451a1.7 1.7 0 0 0 1.7 1.699l7.55.001a.75.75 0 0 0 0-1.5zM11.5 1.25C8.464 1.25 6 3.714 6 6.75s2.464 5.5 5.5 5.5S17 9.786 17 6.75s-2.464-5.5-5.5-5.5zm0 1.5c2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4 1.792-4 4-4zM18.152 20.208a4.003 4.003 0 1 0-2.233-6.786 3.997 3.997 0 0 0-1.127 3.427L12.47 19.17a.75.75 0 0 0-.22.531V22c0 .414.336.75.75.75h2.299a.75.75 0 0 0 .531-.22zm-.052-1.54a.75.75 0 0 0-.723.194l-2.388 2.388H13.75v-1.239l2.388-2.388a.75.75 0 0 0 .194-.723 2.504 2.504 0 0 1 4.186-2.418 2.504 2.504 0 0 1-2.418 4.186z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M17.982 17.018a1.085 1.085 0 1 1 1.535-1.533 1.085 1.085 0 0 1-1.535 1.533z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
            </a>
        </li>
        
        <li class="breadcrumb-item" aria-current="page"><a href="/user-management/role/index"><span><?=Yii::t("app","Roles") ?></span></a></li>
        <li class="breadcrumb-item active" aria-current="page"><span><?=$this->title ?></span></li>
    </ol>
</nav>



	<div class="card custom-card rbac" style="width: 100%;padding: 10px 15px;">
		<div class="row">
		
			<div class="col-sm-12">
		

					<?php if ( Yii::$app->session->hasFlash('success') ): ?>
						<div class="alert alert-success text-center">
							<?= Yii::$app->session->getFlash('success') ?>
						</div>
					<?php endif; ?>
				<div class="custom-card">
					<div class="card-body">
						<?= Html::beginForm(['set-child-permissions', 'id'=>$role->name]) ?>

						<div class="row">
							<?php foreach ($permissionsByGroup as $groupName => $permissions): ?>
								<div class="col-sm-6">
									<fieldset>
										<h4><?= $groupName ?></h4>

										<?php foreach ($permissions as $permission): ?>
											<label>
												<?php $isChecked = in_array($permission->name, ArrayHelper::map($currentPermissions, 'name', 'name')) ? 'checked' : '' ?>
												<input type="checkbox" <?= $isChecked ?> name="child_permissions[]" value="<?= $permission->name ?>">
												<?= $permission->description ?>
											</label>

											<?= GhostHtml::a(
												'<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',
												['/user-management/permission/view', 'id'=>$permission->name],
												['target'=>'_blank']
											) ?>
											<br/>
										<?php endforeach ?>

									</fieldset>
									<br/>
								</div>


							<?php endforeach ?>
						</div>
						<?= Html::submitButton(
							 Yii::t('app', 'Save'),
							['class'=>'btn btn-primary btn']
						) ?>

						<?= Html::endForm() ?>

					</div>
				</div>
			</div>
		</div>
	</div>


<?php
$this->registerJs(<<<JS

$('.role-help-btn').off('mouseover mouseleave')
	.on('mouseover', function(){
		var _t = $(this);
		_t.popover('show');
	}).on('mouseleave', function(){
		var _t = $(this);
		_t.popover('hide');
	});
JS
);
?>

<style type="text/css">
.hide{display: none;}
.card-body{padding: 0}

fieldset {
    min-width: 0;
    padding: 0;
    margin: 0;
    border: 0;
    overflow-y: scroll;
    height: 600px;
}
</style>
<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use Yii;
/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\forms\ChangeOwnPasswordForm $model
 */

$this->title = Yii::t('app', 'Şifrənizi dəyişin');

?>
 <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing" style="padding-bottom:0">
    <div class="page-header" >
      <nav class="breadcrumb-one" aria-label="breadcrumb">
        <h2><?=$this->title ?></h2>
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="/"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></a></li>
              
              <li class="breadcrumb-item active" aria-current="page"><span><?=$this->title  ?></span></li>
          </ol>
      </nav>

    </div>
   </div>


<div class="change-own-password" style="width: 60%">

	<div class="panel panel-default" style="margin-top: 25px">
		<div class="panel-body">

			<?php if ( Yii::$app->session->hasFlash('success') ): ?>
				<div class="alert alert-success text-center">
					<?= Yii::$app->session->getFlash('success') ?>
				</div>
			<?php endif; ?>

			<div class="user-form">

				<?php $form = ActiveForm::begin([
					'id'=>'user',
					'layout'=>'horizontal',
					'validateOnBlur'=>false,
				]); ?>

				<?php if ( $model->scenario != 'restoreViaEmail' ): ?>
					<?= $form->field($model, 'current_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>

				<?php endif; ?>

				<?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>

				<?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>


				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
						<?= Html::submitButton(
							'<span class="glyphicon glyphicon-ok"></span> ' . Yii::t('app', 'Dəyiş'),
							['class' => 'btn btn-primary']
						) ?>
					</div>
				</div>

				<?php ActiveForm::end(); ?>

			</div>
		</div>
	</div>

</div>

<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use kartik\file\FileInput;
use webvimark\modules\UserManagement\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\Cities */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app','Settings');

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <?php if ( User::canRoute('/site/index') ): ?>
               <a  class="btn btn-primary" data-pjax="0" href="<?=$langUrl ?>/site/index" title=" <?=Yii::t("app","Ana səhifə") ?>">
                	<?=Yii::t("app","Ana səhifə") ?>
               </a>
            <?php endif ?>
        </div>
    </div>
</div>
    <div class="widget-content widget-content-area" >
		<?php $form = ActiveForm::begin();?>
			<div class="conatiner">
				<div class="row">
					<div class="col-sm-4">
						<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'short_name')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'currency')->dropDownList( \app\models\SiteConfig::getCurrencies(),['prompt'=>Yii::t('app','Select')] ) ?>
					</div>

					<div class="col-sm-4">
						<?=$form->field($model, 'message_lang')->dropDownList( \app\models\SiteConfig::getMessageLanguages(),['prompt'=>Yii::t('app','Select')] ) ?>
					</div>
					
					<div class="col-sm-4">
						<?=$form->field($model, 'email')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<div style="display: flex; flex-direction: row; justify-content: space-between;">
				        <?= $form->field($model, 'logo_photo')->widget(FileInput::classname(), [
					        'options' => ['accept' => 'image/*'],'pluginOptions' => [
					        'showPreview' => false,
					        'showCaption' => true,
					        'showRemove' => false,
					        'showUpload' => false,
				        ]
				        ])->label('Logo') ?>
						<img src="/uploads/logo/<?=$model->logo ?>" style="width: 120px;display: block;">
						</div>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'sms_username')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'sms_password')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'sms_numberid')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'inet_ppoe_login_start')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'wifi_ppoe_login_start')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'google_map_js_token')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'whatsapp_number_id')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'whatsapp_token')->textInput(['maxlength' => true])?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'balance_alert_cron')->dropDownList(\app\models\SiteConfig::getCronCheckBalanceStatus(),['prompt' => Yii::t('app','Select')])->label(Yii::t('app','Balance alert cron').'<a  data-toggle="tooltip" title="'.Yii::t('app','Sending a reminder message about the last use of services when Active users have no balance .You can choose send with sms , whatsapp Also you can sending message disabled service').'" > <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></a>') ?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'check_balance')->dropDownList(\app\models\SiteConfig::getCronStatus(),['prompt' => Yii::t('app','Select')])->label(Yii::t('app','Check balance cron').'<a  data-toggle="tooltip" title="'.Yii::t('app','You can enable / disable check balance cron').'" > <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></a>') ?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'check_archive')->dropDownList(\app\models\SiteConfig::getCronStatus(),['prompt' => Yii::t('app','Select')])->label(Yii::t('app','Check archive cron').'<a  data-toggle="tooltip" title="'.Yii::t('app','You can enable / disable check archive cron').'" > <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></a>') ?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'check_service_credit')->dropDownList(\app\models\SiteConfig::getCronStatus(),['prompt' => Yii::t('app','Select')])->label(Yii::t('app','Check service credit cron').'<a  data-toggle="tooltip" title="'.Yii::t('app','You can enable / disable service credit cron').'" > <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></a>') ?>
					</div>
					<div class="col-sm-4">
						<?=$form->field($model, 'half_month')->dropDownList(\app\models\SiteConfig::getCronStatus(),['prompt' => Yii::t('app','Select')])->label(Yii::t('app','Half month').'<a  data-toggle="tooltip" title="'.Yii::t('app','You can enable / disable. It works only paid type First day of month.If half month is disable then Daily price is active').'" > <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></a>') ?>
					</div>

					<div class="col-sm-4">
						<?=$form->field($model, 'expired_service')->dropDownList(\app\models\SiteConfig::getCronCheckBalanceStatus(),['prompt' => Yii::t('app','Select')])->label(Yii::t('app','Expired service info').'<a  data-toggle="tooltip" title="'.Yii::t('app','Sending a reminder message about how long the services will be active when the balance is added').'" > <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></a>') ?>
					</div>

					<div class="col-sm-4">
						<?=$form->field($model, 'paid_day_refresh')->dropDownList(\app\models\SiteConfig::getCronStatus(),['prompt' => Yii::t('app','Select')])->label(Yii::t('app','Paid refresh day').'<a  data-toggle="tooltip" title="'.Yii::t('app','if you select Enable .When the user\'s services are canceled, the payment date is recalculated from the day the services were activated. If you select Disable .When the user\'s services are canceled, the payment date is recalculated from piad day').'" > <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></a>') ?>
					</div>

				</div>
			</div>
			
			<div class="form-group">
			<?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary'])?>
			</div>
		<?php ActiveForm::end();?>
    </div>
</div>


<?php 
$this->registerJs("

$(document).ready(function(){
  $('[data-toggle=\"tooltip\"]').tooltip();
});

")
 ?>

<style type="text/css">
.btn-file,.fileinput-cancel{margin: 0}
.fileinput-cancel{display: none;}
</style>
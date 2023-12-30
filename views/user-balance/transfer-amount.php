<?php

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserBalance */
/* @var $form yii\widgets\ActiveForm */
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
?>

<div class="user-balance-form">

	<?php $form = ActiveForm::begin([
	        'id'=>'transfer-amount',
	   
	        'enableAjaxValidation' => true,
	        'validateOnSubmit'=> true,
	        'enableClientValidation'=>false,
	        'validationUrl' => $langUrl.'/user-balance/transfer-amount-validate?id='.$model->id,
	        'options' => ['autocomplete' => 'off']
	]); ?>

     <div class="form-group field-userbalance-cuser_id required has-success">
        <label class="control-label" for="userbalance-cuser_id"><?=Yii::t("app","User fullname") ?></label>
        <input type="text" id="userbalance-cuser_id" class="form-control"  aria-required="true" aria-invalid="false" disabled="disabled" value="<?=$model->user->fullname ?>">
    </div>

     <div class="form-group field-userbalance-cuser_id required has-success">
        <label class="control-label" for="userbalance-cuser_id"><?=Yii::t("app","Balance in") ?></label>
        <input type="text" id="userbalance-cuser_id" class="form-control"  aria-required="true" aria-invalid="false" disabled="disabled" value="<?=$model->balance_in ?>">
    </div>



    <?= $form->field($model, 'contract_number')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Transfer'), ['class' => 'btn btn-primary']) ?>
    </div>        

    <?php ActiveForm::end(); ?>

</div>

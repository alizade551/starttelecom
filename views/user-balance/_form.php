<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserBalance */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-balance-form">

    <?php $form = ActiveForm::begin(); ?>

     <div class="form-group field-userbalance-cuser_id required has-success">
        <label class="control-label" for="userbalance-cuser_id"><?=Yii::t("app","User fullname") ?></label>
        <input type="text" id="userbalance-cuser_id" class="form-control"  aria-required="true" aria-invalid="false" disabled="disabled" value="<?=$model->user->fullname ?>">
    </div>

    <?= $form->field($model, 'balance_in')->textInput() ?>

    <?= $form->field($model, 'balance_out')->textInput() ?>

    <?= $form->field($model, 'bonus_in')->textInput()->label(Yii::t('app','Bonus in')) ?>

    <?= $form->field($model, 'bonus_out')->textInput()->label(Yii::t('app','Bonus out')) ?>

    <?php if (!$model->isNewRecord): ?>
        <?= $form->field($model, 'user_id')->hiddenInput(['value'=>$model->user_id])->label(false) ?>
       
        <?= $form->field($model, 'item_usage_id')->hiddenInput(['value'=>$model->item_usage_id])->label(false) ?>

        <?= $form->field($model, 'created_at')->hiddenInput(['value'=>$model->created_at])->label(false) ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-primary']) ?>
        </div>        
    <?php endif ?>
    <?php ActiveForm::end(); ?>

</div>

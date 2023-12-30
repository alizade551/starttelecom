<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StoreCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="store-category-form">

    <?php $form = ActiveForm::begin([
        'id'=>"add-store-category",
        'options' => ['autocomplete' => 'off']

    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit_type')->dropDownList(\app\models\ItemCategory::getUnites(),['prompt' => Yii::t('app','Select')]) ?>

    <?= $form->field($model, 'mac_address_validation')->checkbox(); ?>

    <?= $form->field($model, 'position')->hiddenInput(['value'=>0])->label(false) ?>

    <?php if ( $model->isNewRecord ): ?>
        <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>               
    <?php endif ?>

    <div class="form-group">
        <?php if ( $model->isNewRecord ): ?>
            <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
        <?php else: ?>
            <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
        <?php endif ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

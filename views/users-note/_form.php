<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UsersNote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-note-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'member_name')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 10]) ?>

    <?= $form->field($model, 'time')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

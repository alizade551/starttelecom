<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CgnIpAddress */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cgn-ip-address-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ip_address_id')->textInput() ?>

    <?= $form->field($model, 'internal_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'port_range')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inet_login')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Currencies */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currencies-form">

    <?php $form = ActiveForm::begin(['id'=>'currency-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php if ( $model->isNewRecord ): ?>
          <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>
    <?php endif ?>

    <div class="form-group">
         <?= Html::submitButton( $model->isNewRecord ? Yii::t('app','Create') :  Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MessageLang */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-lang-form">

    <?php $form = ActiveForm::begin(['id'=>'message-lang-form']); ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'published')->dropDownList([ '0'=> Yii::t('app','Deactive'), '1'=>Yii::t('app','Active'), ], ['prompt' => '']) ?>

    <div class="form-group">
       <?php if ( $model->isNewRecord ): ?>
            <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
       <?php else: ?>
            <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
       <?php endif ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

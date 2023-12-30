<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Language */
/* @var $form yii\widgets\ActiveForm */
?>

  

<?php $form = ActiveForm::begin([
'id'=>'language',
'layout'         => 'horizontal',
'validateOnBlur' => false,
]); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'published')->checkbox() ?>


    <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') :  Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>


<?php ActiveForm::end(); ?>



<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Items */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="items-form">

    <?php $form = ActiveForm::begin(['id'=>'item-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'category_id')->dropDownList(ArrayHelper::map(\app\models\ItemCategory::find()->asArray()->all(),'id','name'),['prompt'=>Yii::t('app','Select')]) ?>

    <div class="form-group">
        <?php if ( $model->isNewRecord ): ?>
            <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>
            <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
        <?php else: ?>
            <?= $form->field($model, 'updated_at')->hiddenInput(['value'=>time()])->label(false) ?>
            <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
        <?php endif ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

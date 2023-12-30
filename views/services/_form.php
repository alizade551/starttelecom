<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Services */
/* @var $form yii\widgets\ActiveForm */
?>



    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'service_name')->textInput(['maxlength' => true]) ?>
<?php if ($model->isNewRecord): ?>
      <?= $form->field($model, 'service_alias')->textInput(['maxlength' => true]) ?>
      <?= $form->field($model, 'created_at')->hiddenInput(['value' => time()])->label(false) ?>
	  <?= $form->field($model, 'updated_at')->hiddenInput(['value' => time()])->label(false) ?>
<?php else: ?>
	 <?= $form->field($model, 'service_alias')->hiddenInput(['value' => $model->service_alias])->label(false) ?>
     <?= $form->field($model, 'updated_at')->hiddenInput(['value' => time()])->label(false) ?>
<?php endif ?>
   
<?php if ($model->isNewRecord): ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t("app","Add"), ['class' => 'btn btn-primary']) ?>
    </div>
<?php else: ?>
	    <div class="form-group">
        <?= Html::submitButton(Yii::t("app","Update"), ['class' => 'btn btn-secondary']) ?>
    </div>
    <?php endif ?>
    <?php ActiveForm::end(); ?>



<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="widget-content widget-content-area">
    <?php $form = ActiveForm::begin(['id' => 'form-add-contact']); ?>

    <?php if ($model->isNewRecord): ?>
    <?= $form->field($model, 'seria')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'start_int')->textInput() ?>
    <?= $form->field($model, 'end_int')->textInput() ?>
    <?= $form->field($model, 'status')->hiddenInput(['value'=>0])->label(false) ?>
    <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissable">
             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
             <h4><i class="icon fa fa-check"></i><?=Yii::t('app','Error message!') ?></h4>
             <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t("app","Create"), ['class' => 'btn btn-success']) ?>
    </div>
    <?php else: ?>
    <?= $form->field($model, 'code')->textInput(['maxlength' => true])->label(Yii::t("app","Recipet code")) ?>
    <?= $form->field($model, 'status')->dropDownList([0=>Yii::t("app","Free"),1=>Yii::t("app","Busy")]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t("app","Update"), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php endif ?>

    <?php ActiveForm::end(); ?>
</div>

<style type="text/css">
.custom-alert-error {
    color: #ff0018;
    background-color: #fdba45;
    text-align: center;
    font-size: 16px;
    position: absolute;
    left: calc(50% - 100px);
    top: 70px;
    width: 200px;
    height: 40px;
    line-height: 40px;
    border-radius: 5px;
}
</style>
<?php

use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model app\models\ItemStock */
/* @var $form yii\widgets\ActiveForm */

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
$validationUrl = ( !$model->isNewRecord ) ? $langUrl .'/item-stock/update-validate?id='.$model->id : $langUrl .'/item-stock/create-validate';
?>

<div class="item-stock-form">

<?php $form = ActiveForm::begin([
    'id'=>"create-item-stock",
    'enableAjaxValidation' => true,
    'validationUrl' => $validationUrl ,
    'options' => ['autocomplete' => 'off']
]
); ?>

    <?= $form->field($model, 'item_id')->hiddenInput(['value'=>$model->item_id])->label(false) ?>

    <?= $form->field($model, 'warehouse_id')->dropDownList(ArrayHelper::map($warehouses,'id','name'),['prompt'=>Yii::t('app','Select')]) ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?php if ( $model->isNewRecord ): ?>
        <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
        </div>
    <?php else: ?>
        <?= $form->field($model, 'updated_at')->hiddenInput(['value'=>time()])->label(false) ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>
    <?php endif ?>


    <?php ActiveForm::end(); ?>

</div>

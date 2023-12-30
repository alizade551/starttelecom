<?php 

use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

 ?>
<?php $form = ActiveForm::begin([
    'id'=>"add-stock",
    'enableAjaxValidation' => true,
    'validationUrl' => $langUrl .'/items/add-stock-validate',
    'options' => ['autocomplete' => 'off']
]
); ?>


<?= $form->field($model, 'item_id')->hiddenInput(['value' => $itemModel->id])->label(false) ?>
<?= $form->field($model, 'created_at')->hiddenInput(['value' => time()])->label(false) ?>
<?= $form->field($model, 'warehouse_id')->dropDownList( ArrayHelper::map($warehouses,'id','name' ),['prompt' => Yii::t('app','Select')])->label() ?>

<?= $form->field($model, 'price')->textInput()->label() ?>
<?= $form->field($model, 'quantity')->textInput()->label(Yii::t('app','Quantity')." (".\app\models\ItemCategory::getUnites()[$itemModel->category->unit_type].")") ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app','Add'), ['class' =>'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>
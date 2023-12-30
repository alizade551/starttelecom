<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use kartik\select2\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\PersonalActivty */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="personal-activty-form">

    <?php $form = ActiveForm::begin(['id'=>'personal-activty-form','options' => ['autocomplete' => 'off']]); ?>
    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'created_at')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <label class="control-label" ><?=Yii::t('app','Customer') ?> </label>
        <input type="text"  class="form-control"  value="<?=$model->user->fullname ?>" aria-required="true" disabled="disabled">
    </div>

    <div class="form-group">
        <label class="control-label"><?=Yii::t('app','Activty type') ?> </label>
        <input type="text" class="form-control"  value="<?=\app\models\PersonalActivty::type()[$model->type] ?>" aria-required="true" disabled="disabled">
    </div>

    <div class="form-group">
        <label class="control-label"><?=Yii::t('app','Created at') ?> </label>
        <input type="text" class="form-control"  value="<?=date('d/m/Y H:i:s',$model['created_at']); ?>" aria-required="true" disabled="disabled">
    </div>
    <?php 
        $personal_data = \yii\helpers\ArrayHelper::map(
            \webvimark\modules\UserManagement\models\User::find()
            ->where(['personal' => '1'])->all(), 'id', 'fullname'
        );
         $model->members =\yii\helpers\ArrayHelper::map(
            \webvimark\modules\UserManagement\models\User::find()
            ->where(['personal' => '1'])
            ->andWhere(['id' => $defaultPersonal])
            ->all(), 
            'id', 
            'id'
         );
            
    ?>
    <?=$form->field($model, 'members')
    ->widget(Select2::classname(), [
        'maintainOrder' => true,
        'bsVersion' => '4.x',
        'data'=>$personal_data,
            'options' => ['placeholder' => Yii::t('app','Personal fullname'), 'multiple' => true],
            'pluginOptions' => [
                'initialize' => true,
                'allowClear' => true,
                'minimumInputLength' => 3,
                'enableClientValidation' => true,
                'language' => ['errorLoading' => new JsExpression("function () { return 'Please wait'; }"),],
                'ajax' => [
                            'url' => \yii\helpers\Url::to(['personal-list']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(city) { return city.text; }'),
                'templateSelection' => new JsExpression('function (city) { return city.text; }'),
        ],
    ])?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


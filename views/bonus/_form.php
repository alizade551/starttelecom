<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use \kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Bonus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bonus-form">
    <?php $form = ActiveForm::begin([
        'id'=>'bonus-form',
        'layout' => 'horizontal',
        'validateOnBlur' => false,
        'enableClientValidation' => true, 
        'enableAjaxValidation' => true
    ]); ?>
        <?= $form->field($model, 'name')->textInput() ?>

        <?= $form->field($model, 'month_count')->textInput() ?>

        <?= $form->field($model, 'factor')->textInput() ?>

        <?php 
        if ( !$model->isNewRecord ) {
              $model->packets = ArrayHelper::map(\app\models\BonusExceptPackets::find()->where(['bonus_id'=>$model->id])->all(),'packet_id','packet_id');
        }

        echo $form->field($model, 'packets')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(\app\models\Packets::find()->all(),'id','packet_name'),
            'options' => ['placeholder' => Yii::t('app','Select'), 'multiple' => true],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', ' '],
                'maximumInputLength' => 10
            ],
        ])->label();

         ?>

        <?= $form->field($model, 'published')->dropDownList([ '0'=>Yii::t('app','Deactive') , '1'=> Yii::t('app','Active') ], ['prompt' => '']) ?>

        <?php if ( $model->isNewRecord ): ?>
        <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>
        <?php endif ?>
        <div class="form-group">
            <?php if ( $model->isNewRecord ): ?>
                <?= Html::submitButton(Yii::t('app','Create'), ['class' => 'btn btn-success']) ?>
            <?php else: ?>
                <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-secondary']) ?>
            <?php endif ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>

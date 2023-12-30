<?php


use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use \kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model app\models\UserDamages */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-damages-form" style="width:100%">

    <?php $form = ActiveForm::begin([
        'id'=>'damage-form',
        'validateOnBlur' => false,
    ]); ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group field-userdamages-member_fullname required">
                <label for="userdamages-member_fullname"><?=Yii::t("app","Member fullname") ?></label>
                <input type="text" id="userdamages-member_fullname" class="form-control"  value="<?=$model->member->fullname ?>" aria-required="true" disabled>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group field-userdamages-user_fullanme required">
                <label for="userdamages-user_fullanme"><?=Yii::t("app","Customer") ?></label>
                <input type="text" id="userdamages-user_fullanme" class="form-control"  value="<?=$model->user->fullname ?>" aria-required="true" disabled>
                <div class="invalid-feedback"></div>
            </div>
        </div>


        <div class="col-sm-6">
            <?php 
                $model->personals = ArrayHelper::map(\app\models\DamagePersonal::find()->where(['damage_id'=>$model->id])->all(),'personal_id','personal_id');
                echo $form->field($model, 'personals')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map($allPersonal,'id','fullname'),
                    'options' => ['placeholder' => Yii::t('app','Add a personal')],
                    'pluginOptions' => [
                        'allowClear' => true,
                         'multiple'=>true
                    ],
                ]);
             ?>

        </div>
        <div class="col-sm-6">
            <div class="form-group field-userdamages-damage_reason required">
                <label for="userdamages-damage_reason"><?=Yii::t("app","Reported reason") ?></label>
                <input type="text" id="userdamages-damage_reason" class="form-control"  value="<?=$model->damage_reason ?>" aria-required="true" disabled>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        
        <div class="col-sm-6">
            <?= $form->field($model, 'damage_result')->textarea(['rows' => 6]) ?>
        </div>

        <div class="col-sm-6">
            <div class="form-group field-userdamages-message">
                <label for="userdamages-message"><?=Yii::t('app','More detail') ?></label>
                <textarea id="userdamages-message" class="form-control"  rows="6" disabled="disabled" ><?=$model->message ?></textarea>
            </div>
        </div>

         <div class="col-sm-12">
            <?= $form->field($model, 'status')->dropDownList(ArrayHelper::merge([''=>Yii::t('app','Select')],\app\models\UserDamages::getStatus()))->label() ?>
        </div>  


    </div>

    <div class="form-group">
        <?php if ( !$model->isNewRecord ): ?>
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
        <?php else: ?>
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?php endif ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style type="text/css">
.select2-container--krajee-bs3 .select2-selection--multiple .select2-search--inline .select2-search__field {
    width: 150px !important;
}
</style>
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use \app\widgets\fileuploader\Fileuploader;

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
?>
<div class="container">
    <h2><?=Yii::t('app','Upload photos') ?></h2>
    <div class="col-sm-6">
        <?php $form = ActiveForm::begin(); ?>
            <div class="form-group">
                <label class="control-label"><?=Yii::t("app","Photos")?></label>
                <div class="form-input">
                <?=Fileuploader::widget([
                'url'=>$langUrl .'/users/photo-upload',    
                'photos'=>$model->userPhotos,
                'name'=>'Users[photos]',
                ])?>
                </div>
            </div>
      
        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary',]) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

  



    





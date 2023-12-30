<?php
use yii\helpers\Html;
use webvimark\modules\UserManagement\models\User;

$this->title = Yii::t('app', 'Update district: {districtName}!', [
    'districtName' => $model->district_name,
]);
?>

<div class="district-update">
    <div class="widget widget-content-area mb-3">
        <div class="widget-one">
            <div class="actions-container" style="display: flex; justify-content: space-between;">
                <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
                <?php if ( User::canRoute(["/district/index"]) ): ?>
                   <a class="btn btn-primary" data-pjax="0" href="/district/index">
                    <?=Yii::t("app","Districts") ?>
                   </a>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="widget-content widget-content-area">
       <?=$this->render('_form', ['model' => $model,'siteConfig'=>$siteConfig]) ?>
    </div>
</div>

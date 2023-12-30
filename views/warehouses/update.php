<?php

use yii\helpers\Html;
use webvimark\modules\UserManagement\models\User;


/* @var $this yii\web\View */
/* @var $model app\models\Warehouses */

$this->title = Yii::t('app', 'Update warehouse : {warehouse}',['warehouse' => $model->name]);
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
?>

<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
        <div class="widget widget-content-area mb-3">
            <div class="widget-one">
                <div class="actions-container" style="display: flex; justify-content: space-between;">
                    <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
                    <?php if (User::canRoute("/warehouses/index")): ?>
                       <a title="<?=Yii::t('app','Warehouses') ?>" class="btn btn-success" data-pjax="0" href="<?=$langUrl ?>/warehouses/index">
                    <?=Yii::t("app","Warehouses") ?>
                   </a>
                    <?php endif?>
                </div>
            </div>
        </div>
        <?=$this->render('_form', ['model' => $model,'siteConfig'=>$siteConfig]) ?>
    </div>
</div>



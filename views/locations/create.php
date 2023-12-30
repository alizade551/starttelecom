<?php

use yii\helpers\Html;
use webvimark\modules\UserManagement\models\User;


/* @var $this yii\web\View */
/* @var $model app\models\Locations */

$this->title = Yii::t("app","Create a location");
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

?>

<div class="location-create">
    <div class="widget widget-content-area mb-3">
        <div class="widget-one">
            <div class="actions-container" style="display: flex; justify-content: space-between;">
                <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
                <?php if (User::canRoute("/locations/index")): ?>
                   <a title="<?=Yii::t('app','Warehouses') ?>" class="btn btn-primary" data-pjax="0" href="<?=$langUrl ?>/locations/index">
                <?=Yii::t("app","Locations") ?>
               </a>
                <?php endif?>
            </div>
        </div>
    </div>
    <div class="widget-content widget-content-area">
        <?=$this->render('_form', ['model' => $model,'siteConfig'=>$siteConfig]) ?>
    </div>
</div>

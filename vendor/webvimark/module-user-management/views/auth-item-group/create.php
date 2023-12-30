<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup $model
 */

$this->title = Yii::t('app', 'Permission group creation');

?>
<div class="col-lg-8">
     <nav class="breadcrumb-one" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item parent"><a data-menu_id="adminstration" href="javascript:void(0);"><?=Yii::t("app","Adminstration") ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=Yii::t("app","Permission") ?></li>
            <li class="breadcrumb-item active" aria-current="page"><?=Yii::t("app","Permission groups") ?></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$this->title ?></li>
        </ol>
    </nav>
    <div class="widget-content widget-content-area" style="padding: 15px;">
     <?= $this->render('_form', compact('model')) ?>
    </div>
</div>


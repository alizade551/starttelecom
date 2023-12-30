<?php
use yii\helpers\Html;
$this->title = Yii::t('app', 'Update') . ': ' . $model->name;
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

<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;
use webvimark\modules\UserManagement\models\User;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */

$this->title = Yii::t('app', 'Create a user');

?>
<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
         <?php if (User::canRoute("/user-management/user/index")): ?>
                <a class="btn btn-primary" data-pjax="0" href="/user-management/user/index">
                    <?=Yii::t("app","Users") ?>
                </a>
         <?php endif?>
        </div>
    </div>
</div>


 <div class="widget-content widget-content-area" style="padding: 15px;">
    <?= $this->render('_form', compact('model')) ?>
 </div>

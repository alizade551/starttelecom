<?php

use yii\helpers\Html;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */

$this->title = Yii::t('app','Create receiptes');
?>
<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <div class="container-actions">
                <?php if (User::canRoute("/receipt/delete-receipt-from-member")): ?>
                    <a class="btn btn-danger add-element" data-pjax="0" href="/receipt/delete-receipt-from-member" style="margin-left:10px;margin-bottom: 10px;">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        <?=Yii::t("app","Delete receipt from user") ?>
                    </a>
                <?php endif ?>

                <?php if (User::canRoute("/receipt/member-recipet")): ?>
                <a class="btn btn-primary add-element" data-pjax="0" href="/receipt/member-recipet" style="margin-left:10px;margin-bottom: 10px;">
                   <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <?=Yii::t("app","Define receipt to user") ?>
                </a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<div class="receipt-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

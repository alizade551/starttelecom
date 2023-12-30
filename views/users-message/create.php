<?php

use yii\helpers\Html;
use webvimark\modules\UserManagement\models\User;


/* @var $this yii\web\View */
/* @var $model app\models\UsersSms */

$this->title = Yii::t('app','Send message');
$this->params['breadcrumbs'][] = ['label' => 'Users Sms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if (User::canRoute("/users-message/index")): ?>
                <a class="btn btn-primary" data-pjax="0" href="/users-message/index">
                    <?=Yii::t("app","Messages") ?>
                </a>
            <?php endif?>
        </div>
    </div>
</div>

<div class="widget-content widget-content-area" >
    <?=$this->render('_form', ['model' => $model,'templates'=>$templates]) ?>
</div>


<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserBalance */

$this->title = Yii::t('app','Update operation id: {operation_id}',['operation_id'=>$model->id]);
?>
<div class="user-balance-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

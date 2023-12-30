<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */


$this->title = Yii::t('app', 'Update receipt: {receipt_code}!', ['receipt_code' => $model->code]);
?>
<div class="widget-content widget-content-area bx-top-6">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

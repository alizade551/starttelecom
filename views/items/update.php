<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Items */

$this->title = Yii::t('app', 'Update an item: {name}', ['name' => $model->name]);
?>
<div class="items-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

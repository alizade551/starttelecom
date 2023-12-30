<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Items */

$this->title = Yii::t('app', 'Create an item');
?>
<div class="items-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

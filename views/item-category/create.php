<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StoreCategory */

$this->title = Yii::t('app', 'Create a category');
?>
<div class="store-category-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StoreCategory */

$this->title = Yii::t('app', 'Update category: {name}', ['name' => $model->name]);

?>
<div class="store-category-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

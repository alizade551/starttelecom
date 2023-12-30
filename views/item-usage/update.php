<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ItemUsage */

$this->title = Yii::t('app', 'Update item usage: {name}', [
    'name' => $model->id,
]);

?>
<div class="item-usage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

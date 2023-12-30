<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Packets */

$this->title = Yii::t('app', 'Create Packets');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Packets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="packets-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

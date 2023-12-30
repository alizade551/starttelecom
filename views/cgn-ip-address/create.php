<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CgnIpAddress */

$this->title = Yii::t('app', 'Create Cgn Ip Address');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cgn Ip Addresses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cgn-ip-address-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

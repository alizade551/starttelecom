<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FailProcess */

$this->title = Yii::t('app', 'Create Fail Process');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fail Processes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fail-process-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

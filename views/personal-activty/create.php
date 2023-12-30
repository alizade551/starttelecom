<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PersonalActivty */

$this->title = Yii::t('app', 'Create Personal Activty');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Personal Activties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-activty-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UsersVoip */

$this->title = Yii::t('app', 'Create Users Voip');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users Voips'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-voip-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

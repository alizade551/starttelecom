<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MessageLang */

$this->title = Yii::t('app', 'Add a message language');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Message Langs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-lang-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

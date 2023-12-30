<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MessageTemplate */

$this->title = Yii::t('app', 'Add a message template');
?>
<div class="message-template-create" style="padding:0">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

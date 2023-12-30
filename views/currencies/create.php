<?php

use yii\helpers\Html;
$this->title = Yii::t('app', 'Create a currency');
?>
<div class="currencies-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

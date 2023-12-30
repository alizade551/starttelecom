<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PersonalActivty */

$this->title = Yii::t('app', 'Update personal an activty id: {id}', [
    'id' => $model->id,
]);
?>
<div class="personal-activty-update">
    <?= $this->render('_form', [
        'model' => $model,
        'defaultPersonal' => $defaultPersonal,
        
    ]) ?>
</div>

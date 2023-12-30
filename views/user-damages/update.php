<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserDamages */

$this->title = Yii::t('app', 'Update damages: {id}', [
    'id' => $model->id,
]);
?>
<div class="user-damages-update">

    <?= $this->render('_form', [
        'model' => $model,
        'allPersonal' => $allPersonal,
    ]) ?>

</div>

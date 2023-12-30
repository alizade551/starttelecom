<?php
use yii\helpers\Html;
$this->title = Yii::t('app', 'Update a bonus rule: {bonus}!', [
    'bonus' => $model->name,
]);
?>

<?= $this->render('_form', ['model' => $model,]) ?>


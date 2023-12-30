<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cities */

$this->title = Yii::t('app', 'Update city: {cityName}!', [
    'cityName' => $model->city_name,
]);
?>

<?= $this->render('_form', ['model' => $model,]) ?>



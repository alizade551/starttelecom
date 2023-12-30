<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Cities */

$this->title = Yii::t("app","Create a city");
?>

<?=$this->render('_form', ['model' => $model,]) ?>



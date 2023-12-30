<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Language */

$this->title = Yii::t('app','Create a language');

?>

 <?=$this->render('_form', ['model' => $model,]) ?>


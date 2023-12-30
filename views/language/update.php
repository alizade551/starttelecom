<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Language */

$this->title = Yii::t("app","Update language : {language}",['language'=>$model->name]);
?>



<?=$this->render('_form', ['model' => $model,]) ?>



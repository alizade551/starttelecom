<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\RequestOrder */

$this->title = Yii::t('app','Create an order');

?>


<div class="request-order-create" style="width:100%">
	<?= $this->render('_form', ['model' => $model,'siteConfig' => $siteConfig]) ?>
</div>



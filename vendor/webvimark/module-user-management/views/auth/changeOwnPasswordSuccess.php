<?php

use Yii;

/**
 * @var yii\web\View $this
 */

$this->title = Yii::t('app', 'Şifrəni dəyiş');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="change-own-password-success">

	<div class="alert alert-success text-center">
		<?= Yii::t('app', 'Şifrəniz dəyişildi') ?>
	</div>

</div>

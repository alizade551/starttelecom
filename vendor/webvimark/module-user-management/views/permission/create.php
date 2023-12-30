<?php
use webvimark\modules\UserManagement\UserManagementModule;
$this->title = Yii::t('app', 'Permission creation');
?>
<?= $this->render('_form', compact('model')) ?>

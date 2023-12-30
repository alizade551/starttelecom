<?php
/**
 * @var $this yii\web\View
 * @var $user webvimark\modules\UserManagement\models\User
 */
use yii\helpers\Html;

?>
<?php
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/user-management/auth/password-recovery-receive', 'token' => $user->confirmation_token]);
?>

<?=Yii::t("app","Salam") ?> <?= Html::encode($user->username) ?>, <?=Yii::t("app","Linkdən şifrənizi sıfırlaya bilərsniz.") ?>:

<?= Html::a(Yii::t("app","Şifrəni sıfırla"), $resetLink) ?>
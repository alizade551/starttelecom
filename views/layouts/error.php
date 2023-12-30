<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="page-error">
 <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700i" rel="stylesheet">   
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">


<?php $this->beginBody() ?>

 <div class="col-lg-12 layout-spacing  ">
    <div class="page-error-content animated growIn slower">
        <?php if ($this->title == "Forbidden (#403)" ): ?>
        <div class="error-code">403</div>
        <h4 class="text-center title"><?=Yii::t("app","You do not have the authority to perform this operation.") ?></h4>
        <?php else: ?>
        <div class="error-code">404</div>
        <h4 class="text-center title"><?=Yii::t("app","Page not found or you don't have permission for this page") ?></h4>
        <?php endif ?>
        <p class="text-center"><a href="/login" class="btn btn-primary"> <?=Yii::t("app","Back") ?></a></p>
    </div>
 </div>  

<style type="text/css">
/**
 * Error Page
 */

.page-error-container {
    padding: 30px 0;
    position: relative;
}

.page-error-content {
    margin: 0 auto;
    padding: 40px 0;
    width: 380px;
    max-width: 94%;
}

.error-code {
    font-size: 160px;
    text-align: center;
    line-height: 1;
    font-weight: 600;
}

.page-error-content h4 {
    margin-bottom: 30px;
    line-height: 40px;
}

</style>
<!--Load JQuery-->
<script src="js/jquery.min.js"></script>
<!-- Load CSS3 Animate It Plugin JS -->
<script src="js/plugins/css3-animate-it-plugin/css3-animate-it.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<?php
use app\assets\AppAsset;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
AppAsset::register($this);
$controller_name = Yii::$app->controller->id;
$languageModel = \app\models\Language::find()->where(['published' => '1'])->all();
$siteConfig = \app\models\SiteConfig::find()->one();
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
// Yii::$app->cache->flush();

$damageCount = \app\models\UserDamages::find()->where(['status'=>'0'])->count();
$pendingCount = \app\models\Users::find()->where(['status'=>'0'])->count();

?>

<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Sarabun" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/css/print.css" media="print">
    <?=Html::csrfMetaTags()?>
    <title><?=Html::encode($this->title)?></title>
    <?php $this->head()?>
</head>
<body class="dashboard-analytics">
<?php $this->beginBody()?>

<div style="position:absolute;left: 50%;top: 50%;  -ms-transform: translate(-50%, -50%);transform: translate(-50%, -50%);">
    
<h2 >All services blocked temporary</h2>
    <a style="display:block;text-align: center;font-size: 18px;" href = "mailto: admin@netbox.az">Send mail</a>
</div>


</body>
<style type="text/css">
    body{
            color: #ffffff;
    height: 100%;
    font-size: 0.875rem;
    background: #060818;
    overflow-x: hidden;
    overflow-y: auto;
    font-family: 'Quicksand', sans-serif;
    }
</style>
<?php $this->endBody()?>

</html>
<?php $this->endPage()?>
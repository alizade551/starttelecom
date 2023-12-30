<?php
use app\assets\AppAssetLight;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
AppAssetLight::register($this);
$controller_name = Yii::$app->controller->id;
$languageModel = \app\models\Language::find()->where(['published' => '1'])->all();
$siteConfig = \app\models\SiteConfig::find()->one();
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
// Yii::$app->cache->flush();

$damageCount = \app\models\UserDamages::find()->where(['status'=>'0'])->count();
$pendingCount = \app\models\Users::find()->where(['status'=>'0'])->count();

$cookies = Yii::$app->request->cookies;

if (($cookie = $cookies->get('sideBar')) !== null) {
    $sideBar = $cookies->get('sideBar')->value;
}else{
    $sideBar = "false";
}
if ( $sideBar == "false" ) {
    $extraActiveClass = "mini-recent-submenu";
}else{
    $extraActiveClass = "show";
}
?>

<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>" class="<?=( $sideBar == "false" ) ? "sidebar-noneoverflow" : "" ?>">
<head>
    <meta charset="<?=Yii::$app->charset?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Sarabun" rel="stylesheet">
    <?=Html::csrfMetaTags()?>
    <title><?=Html::encode($this->title)?></title>
    <?php $this->head()?>
</head>
<body class="<?=( $sideBar == "false" ) ? "sidebar-noneoverflow" : "" ?>">
    <!--  BEGIN NAVBAR  -->
    <div class="header-container fixed-top">
        <header class="header navbar navbar-expand-sm">
            <ul class="navbar-item theme-brand flex-row  text-center">
                <li class="nav-item side-menu-icon">
                    <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                    </a>
                </li>

                <li class="nav-item theme-text">
                   <div>
                       <img src="/img/light.svg">
                   </div>
                </li>
            </ul>
            <ul class="navbar-item flex-row ml-md-0 ml-auto">
                <li class="nav-item align-self-center search-animated">
                        <a style="font-size: 18px !important; color: #e0e6ed !important; padding: 0; text-transform: uppercase; font-weight: 700; margin-left: -15px; margin-top: 20px;" href="javascript:void(0)" class="nav-link d-none d-md-block"> <?=$siteConfig['name'] ?> </a>
                </li>
            </ul>


            <ul class="navbar-item flex-row ml-md-auto" style="margin-right:5px">
                <div class="toggle-switch" style="display: block; line-height: 0px; margin-top: 5px; margin-right: 5px;">
                    <label class="switch s-icons s-outline  s-outline-secondary">
                        <input type="checkbox" class="theme-shifter" checked>
                        <span class="slider round">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                        </span>
                    </label>
                </div>




                <li class="nav-item dropdown language-dropdown more-dropdown">

                    <?php
                        $default_lang = '';
                        if (Yii::$app->language == "en") {
                            $default_lang = 'us';
                        } else {
                            $default_lang = Yii::$app->language;
                        }
                    ?>
                    <div class="dropdown  custom-dropdown-icon">
                        <a class="nav-link dropdown-toggle" href="#" id="customDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <img style="display: block;" width="20px" height="20px" src="https://flagicons.lipis.dev/flags/4x3/<?=$default_lang?>.svg" class="flag-width" alt="flag">
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="customDropdown">
                        <?php foreach ($languageModel as $key => $lang): ?>
                            <?php if (Yii::$app->language == $lang->alias) {continue;}?>
                                <?php $f_lang_alias = $lang->alias;if ($lang->alias == "en") {$f_lang_alias = "us";}?>

                            <a class="dropdown-item" data-img-value="flag-de" data-value="German" href="<?=Url::current(['language' => $lang->alias])?>">
                            <img width="20px" height="20px" src="https://flagicons.lipis.dev/flags/4x3/<?=$f_lang_alias?>.svg" class="flag-width" alt="flag"> <?=$lang->name?></a>
                        <?php endforeach?>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown message-dropdown">
                    <?php $notify = \app\models\Users::CalcNotify();?>
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="messageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span class="badge badge-primary">
                           <?=$notify['notf_count']?>
                        </span>
                    </a>
                    <div class="dropdown-menu position-absolute" aria-labelledby="messageDropdown">
                    <ul class="accordion-tabs">
                        <li class="tab-head-cont">
                            <a href="#" ><?=Yii::t('app', 'Contract')?>
                            <span class="badge badge-primary"><?=$notify['contractNumberCount']?></span>
                        </a>
                            <section>
                            <?php if ( $notify['contractNumberCount'] > 0 ): ?>
                            <div class="slimscroll-noti">
                                <?php foreach ($notify['contractNumber'] as $key => $value): ?>
                                <a class="dropdown-item" href="/users/view?id=<?=$value['id']?>" >
                                    <div class="">
                                        <div class="media">
                                            <div class="media-body">
                                                <div class="">
                                                    <h5 class="usr-name"> <?=$key + 1?>.<?=$value['fullname']?></h5>
                                                    <p class="msg-title"><?=Yii::t('app', 'Contract number is empty!')?> <?=Yii::t('app', 'Please fill it for online payments')?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach?>
                            <?php if ( $notify['contractNumberCount'] > 10 ): ?>
                            <a id="all-contract" href="/site/empty-contracts"><?=Yii::t('app','See all') ?></a>
                            <?php endif ?>
                            </div>
                            <?php else: ?>
                                <h4 style="text-align: center;margin-bottom: 15px" class="noti-detail-des" ><?=Yii::t('app', 'No contract number notification')?></h4>
                            <?php endif?>

                            </section>
                        </li>
                        <li class="tab-head-cont">
                            <a href="#" ><?=Yii::t('app', 'Temporary')?>
                            <span class="badge badge-danger"><?=$notify['permittedCount']?></span>
                            </a>
                            <section>
                            <?php if ( $notify['permittedCount'] > 0 ): ?>
                            <div class="slimscroll-noti">
                                <?php foreach ($notify['permitted'] as $key => $value): ?>
                                <a class="dropdown-item" href="/users/view?id=<?=$value['id']?>" >
                                    <div class="">
                                        <div class="media">
                                            <div class="media-body">
                                                <div class="">
                                                    <h5 class="usr-name"> <?=$key + 1?>.<?=$value['fullname'] ?></h5>
                                                    <p class="msg-title"><?=Yii::t('app', '{customer} is using temporary internet  service with special permission',['customer'=>$value['fullname']])?> </p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </a>
                            <?php endforeach?>
                           <?php if ( $notify['permittedCount'] > 10 ): ?>
                            <a id="all-permitted" href="/site/permitted"><?=Yii::t('app','See all') ?></a>
                           <?php endif ?>
                            </div>
                            <?php else: ?>
                                <h4 style="text-align: center;margin-bottom: 15px" class="noti-detail-des" ><?=Yii::t('app', 'No temporary service notification')?></h4>
                            <?php endif?>

                            </section>
                        </li>
                        <li class="tab-head-cont">
                            <a href="#" ><?=Yii::t('app', 'Credit')?>
                            <span class="badge badge-info"><?=$notify['creditCount']?></span>
                            </a>
                            <section>
                            <?php if ( $notify['creditCount'] > 0 ): ?>
                            <div class="slimscroll-noti">
                                <?php foreach ($notify['creditModel'] as $key => $value): ?>
                                <a class="dropdown-item" href="/users/view?id=<?=$value['id']?>" >
                                    <div class="">
                                        <div class="media">
                                            <div class="media-body">
                                                <div class="">
                                                    <h5 class="usr-name"> <?=$key + 1?>.<?=$value['item_name']?></h5>
                                                    <p class="msg-title"><?=Yii::t('app', 'Continued Credit')?> <?=Yii::t('app', 'Not complate')?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach?>
                            <?php if ( $notify['creditCount'] > 10 ): ?>
                            <a id="all-credit" href="/all-credits"><?=Yii::t('app','See all') ?></a>
                            <?php endif ?>
                            </div>
                            <?php else: ?>
                                <h4 style="text-align: center;margin-bottom: 15px" class="noti-detail-des" ><?=Yii::t('app', 'No credits notification')?></h4>
                            <?php endif?>

                            </section>
                        </li>
                        <li class="tab-head-cont">
                            <a href="#" ><?=Yii::t('app', 'Gift')?>
                            <span class="badge badge-success"><?=$notify['giftCount']?></span>
                            </a>
                            <section>
                            <?php if ($notify['giftCount'] > 0): ?>
                            <div class="slimscroll-noti">
                                <?php foreach ($notify['giftModel'] as $key => $value): ?>
                                <a class="dropdown-item" href="/users/view?id=<?=$value['id']?>" >
                                    <div class="">
                                        <div class="media">
                                            <div class="media-body">
                                                <div class="">
                                                    <h5 class="usr-name"> <?=$key + 1?>.<?=$value['item_name']?></h5>
                                                    <p class="msg-title"><?=Yii::t('app', 'Continued gift')?>   <?=$gift_con['item_name']?> - <?=Yii::t('app', 'Not complate')?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach?>
                            </div>
                            <?php if ( $notify['giftCount'] > 10 ): ?>
                                <a id="all-gifts" href="/site/all-gifts"><?=Yii::t('app','See all') ?></a>
                            <?php endif ?>
                            <?php else: ?>
                                <h4 style="text-align: center;margin-bottom: 15px" class="noti-detail-des" ><?=Yii::t('app', 'No gifts notification')?></h4>
                            <?php endif?>
                            </section>
                        </li>
                    </ul>
                <?php
                $this->registerJs("
                $(document).ready(function () {
                    $('.accordion-tabs').children('li').first().children('a').addClass('is-active').next().addClass('is-open').show();
                    $('.accordion-tabs').on('click', 'li > a', function(event) {
                        if (!$(this).hasClass('is-active')) {
                            event.preventDefault();
                            $('.accordion-tabs .is-open').removeClass('is-open').hide();
                            $(this).next().toggleClass('is-open').toggle();
                            $('.accordion-tabs').find('.is-active').removeClass('is-active');
                            $(this).addClass('is-active');
                        } else {
                            event.preventDefault();
                        }
                    });
                });

                ")
                ?>
                    </div>
                </li>






                <li class="nav-item dropdown user-profile-dropdown">
                    <?php if ( Yii::$app->user->photo !== ""): ?>
                        <?php $photo_url = "/uploads/users/profile/" . Yii::$app->user->photo ?>
                    <?php else: ?>
                        <?php $photo_url = "/uploads/users/avatar_dark.png"?>
                    <?php endif?>
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="<?=$photo_url ?>" alt="avatar">
                    </a>
                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                        <div class="">
                            <div class="dropdown-item">
                                <a href="/site/user-profile">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> <span> <?=Yii::t('app','Profile') ?></span>
                                </a>
                            </div>
                            <div class="dropdown-item">
                                <a href="/site/change-password">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        <span> <?=Yii::t("app","Change password") ?></span>
                                </a>
                            </div>
                            <div class="dropdown-item">
                                <a href="/site/logout">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    <span> <?=Yii::t("app", "Log out")?></span>
                                 </a>
                            </div>
                        </div>
                    </div>
                </li>





             </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->
    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container <?=( $sideBar == "false" ) ? "sidebar-closed " : "sbar-open" ?>   " id="container">
        <div class="overlay <?=( $sideBar == "false" ) ? "" : "show" ?>"></div>
        <div class="loader-overlay"></div>
        <div class="loader">
            <img src="/img/loader.svg">
        </div>
        <!--  BEGIN SIDEBAR  -->
        <div class="sidebar-wrapper sidebar-theme">
            <nav id="sidebar">
                <ul class="list-unstyled menu-categories" id="accordionExample"> 
                    <li class="menu  <?php if($controller_name."/".Yii::$app->controller->action->id == "site/index"){echo "active";} ?> ">
                        <a href="/" aria-expanded="false" class="dropdown-toggle" <?php if($controller_name."/".Yii::$app->controller->action->id == "site/index"){echo 'data-active="true" aria-expanded="true"';} ?>>
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 511 511.999" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M498.7 222.695c-.016-.011-.028-.027-.04-.039L289.805 13.81C280.902 4.902 269.066 0 256.477 0c-12.59 0-24.426 4.902-33.332 13.809L14.398 222.55c-.07.07-.144.144-.21.215-18.282 18.386-18.25 48.218.09 66.558 8.378 8.383 19.44 13.235 31.273 13.746.484.047.969.07 1.457.07h8.32v153.696c0 30.418 24.75 55.164 55.168 55.164h81.711c8.285 0 15-6.719 15-15V376.5c0-13.879 11.293-25.168 25.172-25.168h48.195c13.88 0 25.168 11.29 25.168 25.168V497c0 8.281 6.715 15 15 15h81.711c30.422 0 55.168-24.746 55.168-55.164V303.14h7.719c12.586 0 24.422-4.903 33.332-13.813 18.36-18.367 18.367-48.254.027-66.633zm-21.243 45.422a17.03 17.03 0 0 1-12.117 5.024H442.62c-8.285 0-15 6.714-15 15v168.695c0 13.875-11.289 25.164-25.168 25.164h-66.71V376.5c0-30.418-24.747-55.168-55.169-55.168H232.38c-30.422 0-55.172 24.75-55.172 55.168V482h-66.71c-13.876 0-25.169-11.29-25.169-25.164V288.14c0-8.286-6.715-15-15-15H48a13.9 13.9 0 0 0-.703-.032c-4.469-.078-8.66-1.851-11.8-4.996-6.68-6.68-6.68-17.55 0-24.234.003 0 .003-.004.007-.008l.012-.012L244.363 35.02A17.003 17.003 0 0 1 256.477 30c4.574 0 8.875 1.781 12.113 5.02l208.8 208.796.098.094c6.645 6.692 6.633 17.54-.031 24.207zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                                <span><?=Yii::t("app","Home") ?></span>
                            </div>
                        </a>
                    </li>

                    <?php if ( User::canRoute("/request-order/index") ): ?>
                    <li class="menu <?php if( $controller_name == "request-order" ){echo "active";} ?>">
                        <a href="/request-order/index" aria-expanded="false" class="dropdown-toggle" style="position: relative;" <?php if( $controller_name == "request-order"){echo 'data-active="true" aria-expanded="true"';} ?> >
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M21 5h-3V4a3.003 3.003 0 0 0-3-3H9a3.003 3.003 0 0 0-3 3v1H3a3.003 3.003 0 0 0-3 3v12a3.003 3.003 0 0 0 3 3h18a3.003 3.003 0 0 0 3-3V8a3.003 3.003 0 0 0-3-3ZM8 4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v1H8ZM3 7h18a1 1 0 0 1 1 1v1.3l-7.304 2.653A1.995 1.995 0 0 0 13 11h-2a1.995 1.995 0 0 0-1.696.953L2 9.299V8a1 1 0 0 1 1-1Zm10 6v2h-2v-2Zm8 8H3a1 1 0 0 1-1-1v-8.572l7 2.543V15a2.002 2.002 0 0 0 2 2h2a2.002 2.002 0 0 0 2-2v-1.03l7-2.542V20a1 1 0 0 1-1 1Z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                                <span><?=Yii::t("app","All orders") ?></span>
                            </div>
                            <?php if ( $pendingCount > 0 ): ?>
                            <div id="orders-count"><?=$pendingCount ?></div>
                            <?php endif ?>
                        </a>
                    </li>
                    <?php endif ?>

                    <?php if ( User::canRoute("/users/index")  ): ?>
                        <li class="menu <?php if( $controller_name == "users" ){echo "active";} ?>">
                            <a href="#customers" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"  <?php if( $controller_name == "users" || $controller_name == "online-users" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                <div class="">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M161.2 251.7c-52.7 0-96.2-43.5-97.1-96.9 0-53.7 43.5-97.2 97.1-97.2 53.5 0 97 43.5 97 97.1.1 53.5-43.4 97-97 97zm0-171.1c-40.9 0-74.1 33.3-74.1 74.1.7 40.8 33.9 74.1 74.1 74.1 40.9 0 74.1-33.2 74.1-74.1s-33.2-74.1-74.1-74.1z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M311.8 454.3H11.5c-6.3 0-11.5-5.1-11.5-11.5V400c0-89.3 72.3-162 161.2-162 43 0 83.6 16.9 114.3 47.7 3 3 6.5 6.5 10 10.9 21.6 24.9 34.4 56.3 36.9 90.4v.8c0 1.6.2 3.3.3 5.1.2 2.5.4 4.9.4 7.1v42.8c.1 6.3-5 11.5-11.3 11.5zm-288.9-23h277.4V400c0-1.6-.2-3.3-.3-5.1-.2-2.3-.4-4.6-.4-6.7-2.1-29-13.1-55.6-31.6-77-3-3.7-5.9-6.6-8.6-9.3-26.4-26.4-61.3-41-98.1-41C85 260.9 22.9 323.3 22.9 400zM382.9 239.5c-43.4 0-78.7-35.3-78.7-78.7s35.3-78.7 78.7-78.7 78.7 35.3 78.7 78.7-35.3 78.7-78.7 78.7zm0-134.5c-30.8 0-55.8 25-55.8 55.8s25 55.8 55.8 55.8 55.8-25 55.8-55.8-25.1-55.8-55.8-55.8z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M500.5 399.2H311c-6 0-11-4.6-11.4-10.6-2-28.2-13-55-31.8-77.5-2.9-3.4-3.5-8.3-1.5-12.3 21.5-44.8 67.3-73.7 116.5-73.7 34.6 0 67.1 13.4 91.4 37.7s37.7 56.8 37.7 91.4v33.6c.1 6.3-5 11.4-11.4 11.4zm-179.2-22.9H489v-22.2c0-28.5-11-55.2-31-75.2s-46.7-31-75.2-31c-38.2 0-73.8 21.2-92.6 54.5 16.8 22 27.4 47.3 31.1 73.9z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                                    <span><?=Yii::t("app","Customers") ?></span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                            </a>
                           
                            <ul class="collapse submenu list-unstyled <?php if( $controller_name == "users"  ){
                                echo "collapse ".$extraActiveClass;} ?>" id="customers" data-parent="#accordionExample">
                                <?php if ( User::canRoute("/users/index") ): ?>
                                    <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "users/index" ||  $controller_name."/".Yii::$app->controller->action->id == "users/view"){echo "active";} ?>">
                                        <a href="/users/index"> <?=Yii::t("app","All customers") ?> </a>
                                    </li>
                                <?php endif ?>

                                <?php if ( User::canRoute("/users/statistc") ): ?>
                                    <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "users/statistc"){echo "active";} ?>">
                                        <a href="/users/statistc"> <?=Yii::t("app","Statistic") ?> </a>
                                    </li>
                                <?php endif ?>

                            </ul>
                        </li>
                    <?php endif ?>

                    <?php if ( User::canRoute("/user-damages/index") ): ?>
                        <li class="menu <?php if( $controller_name == "active-reports" || $controller_name == "user-damages" ){echo "active";} ?>">
                            <a href="#reports" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" style="position: relative;" <?php if( $controller_name == "active-reports" || $controller_name == "user-damages" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                <div class="">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m19.25 8 2.55-3.4A1 1 0 0 0 21 3H9a1 1 0 0 0-2 0v17H6a1 1 0 0 0 0 2h4a1 1 0 0 0 0-2H9v-7h12a1 1 0 0 0 .8-1.6zm-2.05.6L19 11H9V5h10l-1.8 2.4a.999.999 0 0 0 0 1.2z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                                    <span><?=Yii::t("app","Reports") ?></span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>

                                <?php if ( $damageCount > 0 ): ?>
                                <div id="reports-count"><?=$damageCount ?></div>
                                <?php endif ?>
                            </a>
                            <ul class="collapse submenu list-unstyled <?php if( $controller_name == "user-damages" || $controller_name == "active-reports" ){echo "collapse ".$extraActiveClass;} ?>" id="reports" data-parent="#accordionExample">
                                <?php if ( User::canRoute("/active-reports/index") ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "active-reports/index"){echo "active";} ?>">
                                    <a href="/active-reports/index"> <?=Yii::t("app","Active reports") ?> </a>
                                </li>
                                <?php endif ?>
                                <?php if ( User::canRoute("/user-damages/index") ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "user-damages/index"){echo "active";} ?>">
                                    <a href="/user-damages/index"> <?=Yii::t("app","Sloved reports") ?> </a>
                                </li> 
                                <?php endif ?>
                            </ul>
                        </li>
                    <?php endif ?>

                    <?php if (  User::canRoute("/routers/index") || User::canRoute("/packets/index") || User::canRoute("/devices/index") || User::canRoute("/cgn-ip-address/index") ): ?>
                        <li class="menu <?php if(  $controller_name == "routers" || $controller_name == "packets" || $controller_name == "devices" || $controller_name == "users-voip" || $controller_name == "ip-adresses" || $controller_name == "fail-process" || $controller_name == "cgn-ip-address" ){echo "active";} ?> " >

                            <?php if ( User::canRoute("/routers/index") || User::canRoute("/packets/index") || User::canRoute("/ip-adresses/index") ||  User::canRoute("/users-voip/index") || User::canRoute("/devices/index") || User::canRoute("/fail-process/index")  || User::canRoute("/cgn-ip-address/index") ): ?>
                                <a style="position: relative;" href="#network" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" <?php if( $controller_name == "routers" || $controller_name == "packets" || $controller_name == "devices" || $controller_name == "users-voip" || $controller_name == "ip-adresses" || $controller_name == "fail-process" || $controller_name == "cgn-ip-address" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                    <div class="">
                                       <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M256 6C117.979 6 6 117.98 6 256s111.979 250 250 250c138.02 0 250-111.979 250-250S394.02 6 256 6zm180.424 392.633c-22.507-13.043-47.881-22.603-73.141-29.25C370.897 337.622 375.23 302.496 375.9 266h109.879c-2.103 48.273-19.388 94.754-49.355 132.633zm-360.847 0C45.61 360.754 28.325 314.273 26.221 266H136.1c.67 36.496 5.003 71.622 12.617 103.383-25.446 6.819-50.169 15.92-73.14 29.25zm-.001-285.266c22.427 12.976 47.584 22.52 73.141 29.251-7.615 31.76-11.947 66.886-12.617 103.382H26.221c2.104-48.274 19.389-94.754 49.355-132.633zM266 356.121V266h89.898c-.664 34.999-4.807 68.575-12.067 98.813-23.761-4.95-50.192-8.076-77.831-8.692zm-97.831 8.692c-7.26-30.237-11.404-63.814-12.067-98.813H246v90.121c-27.307.609-53.566 3.645-77.831 8.692zM246 155.879V246h-89.898c.663-34.999 4.807-68.575 12.067-98.812 24.353 5.044 50.306 8.077 77.831 8.691zM266 246v-90.121c26.884-.6 53.011-3.542 77.831-8.691 7.261 30.237 11.403 63.813 12.067 98.812zm0-110.126V27.267c21.479 5.362 42.433 27.722 58.691 63.194 5.281 11.522 9.935 24.061 13.938 37.406-23.94 4.882-48.499 7.464-72.629 8.007zM246 27.267v108.607c-24.646-.555-49.21-3.227-72.629-8.007 4.003-13.345 8.657-25.884 13.938-37.406C203.567 54.988 224.52 32.628 246 27.267zm0 348.859v108.607c-21.48-5.362-42.433-27.722-58.691-63.194-5.281-11.522-9.935-24.061-13.938-37.406 23.561-4.783 47.859-7.449 72.629-8.007zm20 108.607V376.126c25.926.583 50.03 3.416 72.629 8.007-4.003 13.346-8.656 25.884-13.938 37.406-16.258 35.473-37.212 57.832-58.691 63.194zM375.9 246c-.67-36.496-5.003-71.622-12.617-103.382 25.307-6.699 50.592-16.172 73.141-29.251 29.967 37.879 47.252 84.359 49.355 132.633zm47.166-148.059c-20.134 11.223-42.371 19.449-64.933 25.39-9.359-31.64-24.537-66.811-47.328-90.703 42.611 10.446 81.633 32.943 112.261 65.313zM201.193 32.628c-22.791 23.892-37.968 59.064-47.327 90.703-22.45-5.908-44.646-14.099-64.933-25.39 30.63-32.37 69.65-54.867 112.26-65.313zM88.934 414.059c20.424-11.347 42.508-19.513 64.933-25.39 9.359 31.637 24.536 66.812 47.327 90.702-42.612-10.445-81.631-32.942-112.26-65.312zm221.872 65.313c22.764-23.863 37.95-59.002 47.328-90.703 22.172 5.818 44.581 14.061 64.933 25.39-30.629 32.37-69.65 54.867-112.261 65.313z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g>
                                       </svg>
                                        <span><?=Yii::t("app","Network") ?></span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </div>

                                    <?php if ( $notify['failProcessCount']  > 0 ): ?>
                                    <div id="reports-count"><?=$notify['failProcessCount'] ?></div>
                                    <?php endif ?>
                                </a>
                            <?php endif ?>

                            <ul class="collapse submenu list-unstyled <?php if( $controller_name == "radacct-all" || $controller_name == "radpostauth" || $controller_name == "routers" || $controller_name == "packets" || $controller_name == "devices" || $controller_name == "users-voip" || $controller_name == "ip-adresses" || $controller_name == "fail-process" || $controller_name == "cgn-ip-address" ){echo "collapse ".$extraActiveClass;} ?> " id="network" data-parent="#accordionExample">
                                <?php if ( User::canRoute("/routers/index")  ): ?>
                                    <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "routers/index" || $controller_name."/".Yii::$app->controller->action->id == "routers/create" || $controller_name."/".Yii::$app->controller->action->id == "routers/update" ){echo "active";} ?>">
                                        <a href="/routers/index"> <?=Yii::t("app","Routers") ?> </a>
                                    </li>
                                <?php endif ?>

                                <?php if (  User::canRoute("/fail-process/index")  ): ?>
                                    <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "fail-process/index" ){echo "active";} ?>">
                                        <a href="/fail-process/index"> <?=Yii::t("app","Fail process") ?> </a>
                                    </li>
                                <?php endif ?>

                                <?php if (  User::canRoute("/packets/index")  ): ?>
                                    <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "packets/index" || $controller_name."/".Yii::$app->controller->action->id == "packets/detail" ){echo "active";} ?>">
                                        <a href="/packets/index"> <?=Yii::t("app","Packets") ?> </a>
                                    </li>
                                <?php endif ?>



                                <?php if ( User::canRoute("/ip-adresses/index") || User::canRoute("/cgn-ip-address/index")  ): ?>
                                    <li>
                                        <a href="#items" data-toggle="collapse" aria-expanded="<?php if( $controller_name == "ip-adresses" || $controller_name == "cgn-ip-address" ){echo "true";}else{ echo "false";} ?>" class="dropdown-toggle"> <?=Yii::t("app","Ip Adress & CG-NAT") ?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg> </a>
                                        <ul class="collapse list-unstyled sub-submenu <?php if( $controller_name == "ip-adresses" || $controller_name == "cgn-ip-address" ){echo "collapse show";} ?>" id="items" data-parent="#warehouses"> 

                                          <?php if ( User::canRoute("/ip-adresses/index") ): ?>
                                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "ip-adresses/index" ){echo "active";} ?>">
                                                    <a href="/ip-adresses/index"> <?=Yii::t("app","Ip Adresses") ?> </a>
                                                </li>
                                            <?php endif ?>

                                            <?php if ( User::canRoute("/cgn-ip-address/index") ): ?>
                                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "cgn-ip-address/index"  || $controller_name."/".Yii::$app->controller->action->id == "cgn-ip-address/define-router" ){echo "active";} ?>">
                                                    <a href="/cgn-ip-address/index"> <?=Yii::t("app","CG-NATS") ?> </a>
                                                </li>
                                            <?php endif ?>

                                        </ul>
                                    </li>
                                    
                                <?php endif ?>



                                <?php if ( User::canRoute("/users-voip/index") ): ?>
                                    <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "users-voip/index" ){echo "active";} ?>">
                                        <a href="/users-voip/index"> <?=Yii::t("app","VOIPS ") ?> </a>
                                    </li>
                                <?php endif ?>

                                <?php if ( User::canRoute("/devices/index") ): ?>
                                    <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "devices/update-cordinate" || $controller_name."/".Yii::$app->controller->action->id == "devices/detail" || $controller_name."/".Yii::$app->controller->action->id == "devices/index" ){echo "active";} ?>">
                                        <a href="/devices/index"> <?=Yii::t("app","Devices") ?> </a>
                                    </li>
                                <?php endif ?>
                            </ul>
                        </li>
                    <?php endif ?>

                    <?php if ( User::canRoute("/user-balance/index") || User::canRoute("/receipt/index") || User::canRoute("/bonus/index") || User::canRoute("/currencies/index") || User::canRoute("/user-balance/payment-calculator") || User::canRoute("/user-balance/statistc") ): ?>
                        <li class="menu <?php if( $controller_name == "user-balance" || $controller_name == "receipt" || $controller_name == "bonus" || $controller_name == "currencies" ){echo "active";} ?> ">


                            <a href="#payments" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" <?php if( $controller_name == "user-balance" || $controller_name == "receipt" || $controller_name == "bonus" || $controller_name == "currencies" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                <div class="">
                                   <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 469.341 469.341" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M448.004 236.639v-65.965c0-22.368-17.35-40.559-39.271-42.323l-61.26-107c-5.677-9.896-14.844-16.969-25.813-19.906-10.917-2.917-22.333-1.385-32.104 4.302L79.553 128.007H42.67c-23.531 0-42.667 19.135-42.667 42.667v256c0 23.531 19.135 42.667 42.667 42.667h362.667c23.531 0 42.667-19.135 42.667-42.667v-65.965c12.389-4.418 21.333-16.147 21.333-30.035v-64c0-13.888-8.944-25.617-21.333-30.035zm-64.06-108.632h-92.971l69.729-40.596 23.242 40.596zm-33.841-59.109-101.529 59.109h-42.113l133.112-77.5 10.53 18.391zm-49.808-44.714c4.823-2.823 10.458-3.573 15.844-2.135 5.448 1.458 9.99 4.979 12.813 9.906l.022.039-164.91 96.013h-42.111L300.295 24.184zm126.375 402.49c0 11.76-9.573 21.333-21.333 21.333H42.67c-11.76 0-21.333-9.573-21.333-21.333v-256c0-11.76 9.573-21.333 21.333-21.333h362.667c11.76 0 21.333 9.573 21.333 21.333v64h-64c-35.292 0-64 28.708-64 64s28.708 64 64 64h64v64zm21.334-96c0 5.885-4.781 10.667-10.667 10.667H362.67c-23.531 0-42.667-19.135-42.667-42.667 0-23.531 19.135-42.667 42.667-42.667h74.667c5.885 0 10.667 4.781 10.667 10.667v64z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M362.67 277.341c-11.76 0-21.333 9.573-21.333 21.333 0 11.76 9.573 21.333 21.333 21.333 11.76 0 21.333-9.573 21.333-21.333.001-11.76-9.572-21.333-21.333-21.333z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                                    <span><?=Yii::t("app","Payments & Bonuses") ?></span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                            </a>


                            <ul class="collapse submenu list-unstyled <?php if( $controller_name == "user-balance" || $controller_name == "bonus" || $controller_name == "currencies" || $controller_name == "receipt" ){echo "collapse ".$extraActiveClass;} ?> " id="payments" data-parent="#accordionExample">

                                <?php if ( User::canRoute("/user-balance/index") ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "user-balance/index" ){echo "active";} ?>">
                                    <a href="/user-balance/index"> <?=Yii::t('app','Transactions') ?> </a>
                                </li>
                                <?php endif ?>

                                <?php if ( User::canRoute("/receipt/index") ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "receipt/index" || $controller_name."/".Yii::$app->controller->action->id == "receipt/create" || $controller_name."/".Yii::$app->controller->action->id == "receipt/member-recipet" || $controller_name."/".Yii::$app->controller->action->id == "receipt/delete-receipt-from-member" ){echo "active";} ?>">
                                    <a href="/receipt/index"> <?=Yii::t('app','Receipts') ?> </a>
                                </li>
                                <?php endif ?>

                                <?php if ( User::canRoute("/bonus/index") ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "bonus/index" ){echo "active";} ?>">
                                    <a href="/bonus/index"> <?=Yii::t('app','Bonuses') ?> </a>
                                </li>
                                <?php endif ?>

                                <?php if ( User::canRoute("/currencies/index") ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "currencies/index" ){echo "active";} ?>">
                                    <a href="/currencies/index"> <?=Yii::t('app','Currencies') ?> </a>
                                </li>
                                <?php endif ?>                            

                                <?php if ( User::canRoute("/user-balance/payment-calculator") ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "user-balance/payment-calculator" ){echo "active";} ?>">
                                    <a href="/user-balance/payment-calculator"> <?=Yii::t('app','Payment calculator') ?> </a>
                                </li>
                                <?php endif ?> 

                                <?php if ( User::canRoute("/user-balance/statistc") ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "user-balance/statistc" ){echo "active";} ?>">
                                    <a href="/user-balance/statistc"> <?=Yii::t('app','Statistic') ?> </a>
                                </li>
                                <?php endif ?> 
                            </ul>
                        </li>
                    <?php endif ?>

                    <?php if (  User::canRoute("/items/index") || User::canRoute("/item-category/index") || User::canRoute("/item-stock/index") || User::canRoute("/item-usage/index") || User::canRoute("/warehouses/index") ): ?>
                         <li class="menu <?php if( $controller_name == "items" || $controller_name == "item-category" || $controller_name == "item-stock" || $controller_name == "item-usage" || $controller_name == "warehouses" ){echo "active";} ?> ">
                                <a href="#warehouses" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" <?php if( $controller_name == "items" || $controller_name == "item-category" || $controller_name == "item-stock" || $controller_name == "item-usage" || $controller_name == "warehouses" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 512.001 512.001" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g>
                                            <path d="m454.122 180.501 55.296-81.584a15 15 0 0 0-7.697-22.654l-181-60a14.998 14.998 0 0 0-17.2 5.918l-47.52 71.279-47.519-71.28a15 15 0 0 0-17.201-5.918l-181 60a15.001 15.001 0 0 0-7.697 22.655l55.296 81.584-55.295 81.584a15 15 0 0 0 7.697 22.655l50.719 16.812v119.949a15 15 0 0 0 10.256 14.23l179.936 59.978a14.997 14.997 0 0 0 9.6.006l179.951-59.984a15 15 0 0 0 10.257-14.23V301.552l50.72-16.813a14.997 14.997 0 0 0 9.482-9.413 14.997 14.997 0 0 0-1.785-13.242l-55.296-81.583zM322.138 48.339l151.321 50.162-43.498 64.176-150.586-50.195 42.763-64.143zM38.543 98.5l151.321-50.162 42.762 64.143-150.585 50.195L38.543 98.5zm.001 164.001 43.497-64.176 150.586 50.195-42.762 64.143-151.321-50.162zm202.457 198.188-150-50v-99.192l100.28 33.242a15 15 0 0 0 17.201-5.918l32.519-48.778v170.646zm15-236L123.435 180.5l132.566-44.188L388.567 180.5l-132.566 44.189zm165 186-150 50V290.042l32.52 48.779a14.998 14.998 0 0 0 17.2 5.918l100.28-33.242v99.192zm-98.863-98.025-42.763-64.143 150.586-50.195 43.498 64.177c-2.58.854-146.859 48.67-151.321 50.161z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g>
                                        </svg>
                                        <span><?=Yii::t("app","Warehouses & Items") ?></span>
                                    </div>

                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </div>
                                </a>
                        
                            <ul class="collapse submenu list-unstyled <?php if( $controller_name == "items" || $controller_name == "item-category" || $controller_name == "item-stock" || $controller_name == "item-usage" || $controller_name == "warehouses" ){echo "collapse ".$extraActiveClass;} ?>" id="warehouses" data-parent="#accordionExample" >

                                <?php if ( User::canRoute("/items/index") || User::canRoute("/item-category/index") || User::canRoute("/item-stock/index") || User::canRoute("/item-usage/index") ): ?>
                                    <li>
                                        <a href="#items" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"> <?=Yii::t("app","Items") ?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg> </a>
                                        <ul class="collapse list-unstyled sub-submenu <?php if( $controller_name == "items" || $controller_name == "item-category" || $controller_name == "item-stock" || $controller_name == "item-usage" ){echo "collapse show";} ?>" id="items" data-parent="#warehouses"> 

                                          <?php if ( User::canRoute("/items/index") ): ?>
                                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "items/index" ){echo "active";} ?>">
                                                    <a href="/items/index"> <?=Yii::t("app","All items") ?> </a>
                                                </li>
                                            <?php endif ?>

                                            <?php if ( User::canRoute("/item-category/index") ): ?>
                                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "item-category/index" ){echo "active";} ?>">
                                                    <a href="/item-category/index"> <?=Yii::t("app","Categories") ?> </a>
                                                </li>
                                            <?php endif ?>

                                            <?php if ( User::canRoute("/item-stock/index") ): ?>
                                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "item-stock/index" ){echo "active";} ?>">
                                                    <a href="/item-stock/index"> <?=Yii::t("app","Stock management") ?> </a>
                                                </li>
                                            <?php endif ?>

                                            <?php if ( User::canRoute("/item-usage/index") ): ?>
                                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "item-usage/index" ){echo "active";} ?>">
                                                    <a href="/item-usage/index"> <?=Yii::t("app","Used items") ?> </a>
                                                </li>
                                            <?php endif ?>

                                        </ul>
                                    </li>
                                    
                                <?php endif ?>
                              
                                <?php if ( User::canRoute("/warehouses/index") ): ?>
                                    <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "warehouses/index" || $controller_name."/".Yii::$app->controller->action->id == "warehouses/create" || $controller_name."/".Yii::$app->controller->action->id == "warehouses/update" ){echo "active";} ?>">
                                        <a href="/warehouses/index"> <?=Yii::t("app","Warehouses") ?> </a>
                                    </li>
                                <?php endif ?>
                            </ul>
                        </li>
                    <?php endif ?>

                    <?php if ( User::canRoute('/users-message/index') || User::canRoute('/message-template/index') || User::canRoute('/message-lang/index') ): ?>
                        <li class="menu <?php if( $controller_name == "users-message" || $controller_name == "message-lang" || $controller_name == "message-template" ){echo "active";} ?> ">

                            <a href="#messages" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" <?php if( $controller_name == "users-message" || $controller_name == "message-lang" || $controller_name == "message-template" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                <div class="">
                                   <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M467 76H45C20.238 76 0 96.149 0 121v270c0 24.86 20.251 45 45 45h422c24.762 0 45-20.149 45-45V121c0-24.857-20.248-45-45-45zm-6.91 30L267.624 299.094c-5.864 5.882-17.381 5.886-23.248 0L51.91 106h408.18zM30 385.485v-258.97L159.065 256 30 385.485zM51.91 406l128.334-128.752 42.885 43.025c17.574 17.631 48.175 17.624 65.743 0l42.885-43.024L460.09 406H51.91zM482 385.485 352.935 256 482 126.515v258.97z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                                    <span><?=Yii::t("app","Messages") ?></span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                            </a>

                            <ul class="collapse submenu list-unstyled <?php if( $controller_name == "users-message" || $controller_name == "message-template" || $controller_name == "message-lang" ){echo "collapse ".$extraActiveClass;}   ?>" id="messages" data-parent="#accordionExample">
                                <?php if ( User::canRoute('/users-message/index') ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "users-message/index" || $controller_name."/".Yii::$app->controller->action->id == "users-message/create" ){echo "active";} ?>">
                                    <a href="/users-message/index" > <?=Yii::t("app","Messages") ?> </a>
                                </li>
                                <?php endif ?>
                                <?php if ( User::canRoute('/message-template/index') ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "message-template/index" ){echo "active";} ?>">
                                    <a href="/message-template/index" > <?=Yii::t("app","Message templates") ?> </a>
                                </li>
                                <?php endif ?>
                                <?php if ( User::canRoute('/message-lang/index') ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "message-lang/index" ){echo "active";} ?>">
                                    <a href="/message-lang/index" > <?=Yii::t("app","Message languages") ?> </a>
                                </li>
                                <?php endif ?>
                            </ul>
                        </li>
                    <?php endif ?>


                    <?php if (  User::canRoute('/cities/index') || User::canRoute('/district/index') || User::canRoute('/locations/index') ): ?>
                        <li class="menu <?php if( $controller_name == "cities" || $controller_name == "district" || $controller_name == "locations" ){echo "active";} ?>">
                            <a href="#locations" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" <?php if( $controller_name == "cities" || $controller_name == "district" || $controller_name == "locations" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                <div class="">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 32 32" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M22.31 19.53C24.28 16.6 26 13.37 26 11a10 10 0 0 0-20 0c0 2.37 1.72 5.6 3.69 8.53C5.24 20.31 1 22.08 1 25c0 3.9 7.73 6 15 6s15-2.1 15-6c0-2.92-4.24-4.69-8.69-5.47zM16 3a8 8 0 0 1 8 8c0 2.32-2.4 6.19-4.81 9.46a71.102 71.102 0 0 1-3.19 4 80.5 80.5 0 0 1-3.19-4C10.4 17.19 8 13.32 8 11a8 8 0 0 1 8-8zm0 26c-7.94 0-13-2.37-13-4 0-1.27 3-2.94 8-3.65a72.72 72.72 0 0 0 4.17 5.18 1 1 0 0 0 .13.14 1 1 0 0 0 1.48 0 1 1 0 0 0 .13-.14c.57-.64 2.33-2.66 4.17-5.18 5 .71 8 2.38 8 3.65-.08 1.63-5.14 4-13.08 4z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M16 16a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0-8a3 3 0 1 1-3 3 3 3 0 0 1 3-3z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                                    <span><?=Yii::t("app","Locations") ?></span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                            </a>
                            <ul class="collapse submenu list-unstyled <?php if( $controller_name == "cities" || $controller_name == "district" || $controller_name == "locations" ){echo "collapse ".$extraActiveClass;}   ?>" id="locations">
                                <?php if ( User::canRoute('/cities/index') ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "cities/index" ){echo "active";} ?>">
                                    <a href="/cities/index" > <?=Yii::t("app","Cities") ?> </a>
                                </li>
                                <?php endif ?>

                                <?php if ( User::canRoute('/district/index') ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "district/index" || $controller_name."/".Yii::$app->controller->action->id == "district/create" || $controller_name."/".Yii::$app->controller->action->id == "district/update"  ){echo "active";} ?>">
                                    <a href="/district/index" > <?=Yii::t("app","Districts") ?> </a>
                                </li>
                                <?php endif ?>

                                <?php if ( User::canRoute('/locations/index') ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "locations/index" ||  $controller_name."/".Yii::$app->controller->action->id == "locations/create" || $controller_name."/".Yii::$app->controller->action->id == "locations/update" || $controller_name."/".Yii::$app->controller->action->id == "locations/map" ){echo "active";} ?>">
                                    <a href="/locations/index" > <?=Yii::t("app","Locations") ?> </a>
                                </li>
                                <?php endif ?>


                            </ul>
                        </li>
                    <?php endif ?>

                    <?php if ( User::canRoute('/logs/index') || User::canRoute('/users-history/index') || User::canRoute('/cron-logs/index') || User::canRoute('/users-note/index')): ?>
                        <li class="menu <?php if( $controller_name == "users-history" || $controller_name == "users-note" || $controller_name == "logs"  || $controller_name == "cron-logs"){echo "active";} ?>">
                            <a href="#archive" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" <?php if( $controller_name == "users-history" || $controller_name == "users-note" || $controller_name == "logs"  || $controller_name == "cron-logs" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                <div class="">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M452 120h-30V60h-30V0H268.973l-20 30H120v60H90v60H60v40.457l-60 90V512h512V280.457l-60-90zM150 60h115.027l20-30H362v30h-63.027l-20 30H150zm-30 60h175.027l20-30H392v30h-63.027l-20 30H120zm-30 60h235.027l20-30H422v120H316.187l-10 30H205.813l-10-30H90zm-30 64.543V270H43.027zM482 482H30V300h144.188l10 30h143.625l10-30H482zm-13.027-212H452v-25.457zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M180 360h152v30H180zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                                    <span><?=Yii::t("app","Archives") ?></span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                            </a>
                            <ul class="collapse submenu list-unstyled <?php if( $controller_name == "users-history" || $controller_name == "users-note" || $controller_name == "logs" || $controller_name == "cron-logs" ){echo "collapse ".$extraActiveClass;}   ?>" id="archive" data-parent="#accordionExample">
                                <?php if ( User::canRoute('/logs/index') ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "logs/index" ){echo "active";} ?>">
                                    <a href="/logs/index"> <?=Yii::t("app","Logs") ?> </a>
                                </li>
                                <?php endif ?>

                               <?php if ( User::canRoute('/users-history/index') ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "users-history/index" ){echo "active";} ?>">
                                    <a href="/users-history/index"> <?=Yii::t("app","Histories") ?> </a>
                                </li>
                                <?php endif ?>
                                <?php if ( User::canRoute('/users-note/index') ): ?>
                                <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "users-note/index" ){echo "active";} ?>">
                                    <a href="/users-note"> <?=Yii::t("app","Notes") ?> </a>
                                </li>
                                <?php endif ?>


                                <?php if ( User::canRoute('/cron-logs/index') ): ?>
                                <li  class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "cron-logs/index" ){echo "active";} ?>">
                                    <a href="/cron-logs/index"> <?=Yii::t("app","Cron logs") ?> </a>
                                </li>
                                <?php endif ?>
                            </ul>
                        </li>
                    <?php endif ?>

                    <?php if ( User::canRoute("/user-management/user") || User::canRoute("/personal-activty/index") || User::canRoute("/user-management/user-visit-log/index") || User::canRoute('/user-management/role/index') || User::canRoute('/user-management/permission/index') || User::canRoute('/language/index') ): ?>
                        <li class="menu <?php if( $controller_name == "language" || $controller_name == "user" || $controller_name == "personal-activty" || $controller_name == "user-visit-log" || $controller_name == "cron-logs" || $controller_name == "role" || $controller_name == "permission" ){echo "active";} ?>">

                      
                                <a href="#admin" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" <?php if( $controller_name == "language" || $controller_name == "user" || $controller_name == "personal-activty" || $controller_name == "user-visit-log" || $controller_name == "cron-logs" || $controller_name == "role" || $controller_name == "permission" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" fill-rule="evenodd" class=""><g><path d="M10.5 20.263H2.95a.2.2 0 0 1-.2-.2v-1.45c0-.831.593-1.563 1.507-2.185 1.632-1.114 4.273-1.816 7.243-1.816.49 0 .971.02 1.441.057a.75.75 0 1 0 .118-1.495 19.38 19.38 0 0 0-1.559-.062c-3.322 0-6.263.831-8.089 2.076-1.393.95-2.161 2.157-2.161 3.424v1.451a1.7 1.7 0 0 0 1.7 1.699l7.55.001a.75.75 0 0 0 0-1.5zM11.5 1.25C8.464 1.25 6 3.714 6 6.75s2.464 5.5 5.5 5.5S17 9.786 17 6.75s-2.464-5.5-5.5-5.5zm0 1.5c2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4 1.792-4 4-4zM18.152 20.208a4.003 4.003 0 1 0-2.233-6.786 3.997 3.997 0 0 0-1.127 3.427L12.47 19.17a.75.75 0 0 0-.22.531V22c0 .414.336.75.75.75h2.299a.75.75 0 0 0 .531-.22zm-.052-1.54a.75.75 0 0 0-.723.194l-2.388 2.388H13.75v-1.239l2.388-2.388a.75.75 0 0 0 .194-.723 2.504 2.504 0 0 1 4.186-2.418 2.504 2.504 0 0 1-2.418 4.186z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M17.982 17.018a1.085 1.085 0 1 1 1.535-1.533 1.085 1.085 0 0 1-1.535 1.533z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                                        <span><?=Yii::t("app","Adminstration") ?></span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </div>
                                </a>
                     

                            <ul class="collapse submenu list-unstyled <?php if( $controller_name == "language" || $controller_name == "user" || $controller_name == "personal-activty" || $controller_name == "user-visit-log" || $controller_name == "cron-logs" || $controller_name == "role" || $controller_name == "permission" ){echo "collapse ".$extraActiveClass;}   ?>" id="admin" data-parent="#accordionExample">
                                <li class="<?php if( $controller_name == "user" || $controller_name == "personal-activty" || $controller_name == "user-visit-log" ){echo "active";} ?>">
                                    <a href="#users" data-toggle="collapse" aria-expanded="<?php if( $controller_name == "user" || $controller_name == "personal-activty" || $controller_name == "user-visit-log" ){echo "true";}else{echo "false";} ?>" class="dropdown-toggle"> <?=Yii::t("app","Users") ?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg> </a>
                                    <ul class="collapse list-unstyled sub-submenu <?php if( $controller_name == "user" || $controller_name == "personal-activty" || $controller_name == "user-visit-log" ){echo "collapse show";} ?>" id="users" data-parent="#admin"> 

                                      <?php if ( User::canRoute("/user-management/user") ): ?>
                                            <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "user/index" || $controller_name."/".Yii::$app->controller->action->id == "user/create" || $controller_name."/".Yii::$app->controller->action->id == "user/update" ){echo "active";} ?>">
                                                <a  href="/user-management/user/"> <?=Yii::t("app","All users") ?> </a>
                                            </li>
                                        <?php endif ?>

                                        <?php if ( User::canRoute("/personal-activty/index") ): ?>
                                            <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "personal-activty/index" ){echo "active";} ?>">
                                                <a href="/personal-activty/index"> <?=Yii::t("app","Activty logs") ?> </a>
                                            </li>
                                        <?php endif ?>

                                        <?php if ( User::canRoute("/user-management/user-visit-log/index") ): ?>
                                            <li class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "user-visit-log/index" ){echo "active";} ?>">
                                                <a href="/user-management/user-visit-log/index"> <?=Yii::t("app","Visit logs") ?> </a>
                                            </li>
                                        <?php endif ?>
                                    </ul>
                                </li>

                                <?php if ( User::canRoute('/user-management/role/index') ): ?>
                                <li  class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "role/index" || $controller_name."/".Yii::$app->controller->action->id == "role/view" ){echo "active";} ?>">
                                    <a href="/user-management/role/index"> <?=Yii::t("app","Roles") ?> </a>
                                </li>
                                <?php endif ?>

                                <?php if ( User::canRoute('/user-management/permission/index') ): ?>
                                <li  class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "permission/index" || $controller_name."/".Yii::$app->controller->action->id == "permission/view" ){echo "active";} ?>">
                                    <a href="/user-management/permission/index"> <?=Yii::t("app","Permissions") ?> </a>
                                </li>
                                <?php endif ?>

                                <?php if ( User::canRoute('/language/index') ): ?>
                                <li  class="<?php if( $controller_name."/".Yii::$app->controller->action->id == "language/index" || $controller_name."/".Yii::$app->controller->action->id == "language/update" || $controller_name."/".Yii::$app->controller->action->id == "language/create" ){echo "active";} ?>">
                                    <a href="/language/index"> <?=Yii::t("app","Languages") ?> </a>
                                </li>
                                <?php endif ?>

                            </ul>
                        </li>
                    <?php endif ?>

                    <?php if (  User::canRoute('/site/config') ): ?>
                        <li class="menu  <?php if($controller_name."/".Yii::$app->controller->action->id == "site/config"){echo "active";} ?> ">
                                <a href="/site/config" aria-expanded="false" class="dropdown-toggle" <?php if( $controller_name."/".Yii::$app->controller->action->id == "site/config" ){echo 'data-active="true" aria-expanded="true"';} ?> >
                                    <div class="">
                                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 682.667 682.667" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#000000" opacity="1" data-original="#000000"></path></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0c-43.446 0-78.667-35.22-78.667-78.667 0-43.446 35.221-78.666 78.667-78.666 43.446 0 78.667 35.22 78.667 78.666C78.667-35.22 43.446 0 0 0Zm220.802-22.53-21.299-17.534c-24.296-20.001-24.296-57.204 0-77.205l21.299-17.534c7.548-6.214 9.497-16.974 4.609-25.441l-42.057-72.845c-4.889-8.467-15.182-12.159-24.337-8.729l-25.835 9.678c-29.469 11.04-61.688-7.561-66.862-38.602l-4.535-27.213c-1.607-9.643-9.951-16.712-19.727-16.712h-84.116c-9.776 0-18.12 7.069-19.727 16.712l-4.536 27.213c-5.173 31.041-37.392 49.642-66.861 38.602l-25.834-9.678c-9.156-3.43-19.449.262-24.338 8.729l-42.057 72.845c-4.888 8.467-2.939 19.227 4.609 25.441l21.3 17.534c24.295 20.001 24.295 57.204 0 77.205l-21.3 17.534c-7.548 6.214-9.497 16.974-4.609 25.441l42.057 72.845c4.889 8.467 15.182 12.159 24.338 8.729l25.834-9.678c29.469-11.04 61.688 7.561 66.861 38.602l4.536 27.213c1.607 9.643 9.951 16.711 19.727 16.711h84.116c9.776 0 18.12-7.068 19.727-16.711l4.535-27.213c5.174-31.041 37.393-49.642 66.862-38.602l25.835 9.678c9.155 3.43 19.448-.262 24.337-8.729l42.057-72.845c4.888-8.467 2.939-19.227-4.609-25.441z" style="stroke-width:40;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(256 334.666)" fill="none" stroke="#000000" stroke-width="40" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path></g></g></svg>
                                        <span><?=Yii::t("app","Settings") ?></span>
                                    </div>
                                </a>
                        </li>
                    <?php endif ?>
                </ul>
            </nav>
        </div>

                <?php
                $this->registerJs("

                    $('.sidebarCollapse').on('click',function(){
              
                        var that = $(this);
                        const isClosed =  $('body').hasClass('sidebar-noneoverflow');  
                        setTimeout(function() {
                            $.ajax({
                                url:'".$langUrl.Url::to('/site/side-bar')."',
                                method:'POST',
                                data:{isClosed}
                            });
                        }, 200);
                    });


                    $('.overlay.show').on('click',function(){
                            $.ajax({
                                url:'".$langUrl.Url::to('/site/side-bar')."',
                                method:'POST',
                                data:{isClosed:'false'}
                            });

                    })

  
                ")
                ?>

        <!--  END SIDEBAR  -->
        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <?=$content ?>
                    </div>
                </div>
            </div>
            <div class="footer-wrapper">
                <div class="footer-section f-section-1">
                    <p class="">Copyright © 2018 - <?=date("Y") ?> <a target="_blank" href="https://netbox.az/">netbox.az</a>, <?=Yii::t("app","All rights reserved") ?></p>
                </div>
            </div>
        </div>
        <!--  END CONTENT AREA  -->
    </div>
    <!--  END MAIN CONTAINER  -->

    <?php
        $this->registerJs('
            $(document).ready(function() {
                App.init();
            });


            $(".theme-shifter").on("click",function(e){
                let themeData;
                if ($(".theme-shifter").is(":checked")){
                    themeData = "light";
                }else{
                    themeData = "dark";
                }
                  $.ajax({
                    url:"' . Url::to("/change-theme") .  '",
                    type:"post",
                    data:{themeData},
                    success(){
                        console.log("ss")
                        location.reload();

                    }
                });
            })
                
           $(document).on("pjax:send", function() {
                $(".loader-overlay").addClass("show");
                $(".loader").show();
            });

            $(document).on("pjax:complete", function() {
                  $(".loader").hide();
                  $(".loader-overlay").removeClass("show");
            });
        ');
    ?>
</body>
<?php $this->endBody()?>
</html>
<?php $this->endPage()?>
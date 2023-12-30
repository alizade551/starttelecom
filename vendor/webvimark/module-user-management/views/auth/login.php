

<?php
/**
 * @var $this yii\web\View
 * @var $model webvimark\modules\UserManagement\models\forms\LoginForm
 */

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;


$langUrl = (Yii::$app->language == "az") ? "" : "/".Yii::$app->language."/";

?>

<div class="form-container">
        <div class="form-form">
            <div class="form-form-wrap">
                <div class="form-container">
                    <div class="form-content">

                        <h1 style="color: #fff; position: relative; margin-bottom: 10px;" ><?=Yii::t("app","Control Panel") ?> <span style="font-size: 11px; position: absolute; top: 33px; display: block; right: 0;"> <?=Yii::t('app','2.1.0 version') ?> </span></h1>
                        <!-- <p class="signup-link">New Here? <a href="auth_register.html">Are you want dealler? contact us</a></p> -->

                   <?php $form = ActiveForm::begin([
                        'id'      => 'login-form',
                        'enableClientScript' => false,
                        'options'=>['autocomplete'=>'off','class'=>'text-left'],
                        'validateOnBlur'=>false,
                        'fieldConfig' => [
                            'template'=>"{input}\n{error}",
                        ],
                    ]) ?>
                            <div class="form">
                                <?php 
                                 echo $form->field($model, 'username',[

                                     'template' => ' <div id="username-field" class="field-wrapper input form-group">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-at-sign"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"/></svg>
                                                                   {input}
                                                                    <p class="help-block help-block-error">{error}</p>
                                                                </div>'
                                 ])->textInput()->input('text', ['placeholder' => Yii::t("app","Username")])->label(false);
                                 ?>

                                <?php 
                                 echo $form->field($model, 'password',[
                                     'template' => ' <div id="password-field" class="field-wrapper input form-group">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                                   {input}
                                                                    <p class="help-block help-block-error">{error}</p>
                                                                </div>'
                                 ])->textInput()->input('password', ['placeholder' => Yii::t("app","Password")])->label(false);
                                 ?>

                                <div class="field-wrapper text-center keep-logged-in">
                                    <div class="field-wrapper text-center keep-logged-in">
                                        <?= $form->field($model, 'rememberMe', [
                                            'template'=>'
                                                <div class="n-chk new-checkbox checkbox-outline-primary">
                                                        <label class="new-control new-checkbox checkbox-outline-primary">
                                                            {input}
                                                            <span class="new-control-indicator"></span>'.Yii::t('app','Keep me logged in').'
                                                        </label>
                                                 </div>'
                                            ])->textInput(['class'=>"new-control-input",'type'=>'checkbox']);
                                        ?>
                                    </div>
                                </div>
                                 
                                <div class="d-sm-flex justify-content-between">
                                    <div class="field-wrapper toggle-pass">
                                        <p class="d-inline-block"><?=Yii::t("app","Show password") ?></p>
                                        <label class="switch s-primary">
                                            <input type="checkbox" id="toggle-password" class="d-none">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>

                                    <div class="field-wrapper">
                                        <button type="submit" class="btn btn-primary" value=""><?=Yii::t("app","Log in") ?></button>
                                    </div>
                                </div>                    
                            </div>
                       <?php ActiveForm::end() ?>             
                        <p  style="text-align: center;color:#fff;margin-top: 30px !important;" class="terms-conditions">
                            <span>
                              Copyright © 2018 - <?=date("Y") ?> | <?=Yii::t("app","All rights reserved") ?>
                            </span>
                        </p>
                       
                         
                    </div>                    
                </div>
            </div>
        </div>
       
        <div class="form-image">
            <div class="image-container">
                <div class="l-image">
                    <img src="/img/light.png">
                </div>
                <div class="s-image">
                    <?php $siteConfig = \app\models\SiteConfig::find()->asArray()->one() ?>
                      <img src="/uploads/logo/<?=$siteConfig['logo'] ?>">
                </div>
            </div>

              
        </div>
    </div>
<?php
$this->registerJs("
$('form input:text').first().focus();

$('#toggle-password').on('change',function(){
    if($(this).is(':checked')){
            $('#loginform-password').attr('type','text')
        }else{
            $('#loginform-password').attr('type','password')
        }
  
    })

");


$css = <<<CSS
.field-loginform-rememberme label {
    font-weight: 500;
    color: #e5e6ed;
}
.form-group {
    margin-bottom: -4px;
}
html, body { height: 100%; }
body {
    overflow: auto;
    margin: 0;
    padding: 0;
    background: #fff;
    color: black;
}

body.login-page {
    background-color: #ffffff;
    color: black;
}

.form-container {
    display: flex;
}
.form-form {
    width: 50%;
    display: flex;
    flex-direction: column;
    min-height: 100%;
    background: #2e3d8f;
}
.form-form .form-form-wrap {
    max-width: 480px;
    margin: 0 auto;
    min-width: 400px;
    min-height: 100%;
    height: 100vh;
    align-items: center;
    justify-content: center;
}
.form-form .form-container {
    align-items: center;
    display: flex;
    flex-grow: 1;
    width: 100%;
    min-height: 100%;
}
.form-form .form-container .form-content {
    display: block;
    width: 100%;
}
.form-form .form-form-wrap .user-meta { margin-bottom: 35px; }
.form-form .form-form-wrap .user-meta img {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    margin-right: 15px;
    border: 4px solid #e0e6ed;
}
.form-form .form-form-wrap .user-meta div { align-self: center; }
.form-form .form-form-wrap .user-meta p {
    font-size: 31px;
    color: #3b3f5c;
    margin-bottom: 0;
}
.form-form .form-form-wrap h1 .brand-name {
    color: #4361ee;
    font-weight: 600;
}
.form-form .form-form-wrap p.signup-link {
    font-size: 14px;
    color: #3b3f5c;
    font-weight: 700;
    margin-bottom: 50px;
}
.form-form .form-form-wrap p.signup-link a {
    color: #4361ee;
    border-bottom: 1px solid;
}
.form-form .form-form-wrap form .field-wrapper.input {
    position: relative;
    padding: 11px 0 25px 0;
    border-bottom: none;
}
.form-form .form-form-wrap form .field-wrapper.input:focus { border: 1px solid #000; }
.form-form .form-form-wrap form .field-wrapper.toggle-pass p {
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 0;
}
.form-form .form-form-wrap form .field-wrapper .logged-in-user-name {
    font-size: 37px;
    color: #3b3f5c;
}
.form-form .form-form-wrap form .field-wrapper svg {
    position: absolute;
    top: 16px;
    color: #ffffff;
    fill: rgb(27 85 226 / 0%);
}
.form-form .form-form-wrap form .field-wrapper.terms_condition { margin-bottom: 20px; }
.form-form .form-form-wrap form .field-wrapper.terms_condition label {
    font-size: 14px;
    color: #888ea8;
    padding-left: 31px;
    font-weight: 100;
}
.form-form .form-form-wrap form .field-wrapper.terms_condition a { color: #4361ee; }
.form-form .form-form-wrap form .field-wrapper input {
    display: inline-block;
    vertical-align: middle;
    border-radius: 0;
    min-width: 50px;
    max-width: 635px;
    width: 100%;
    min-height: 36px;
    background-color:#2e3d8f;
    border: none;
    -ms-transition: all 0.2s ease-in-out 0s;
    transition: all 0.2s ease-in-out 0s;
    color: #ffffff;
    font-weight: 600;
    font-size: 16px;
    border-bottom: 1px solid #e0e6ed;
    padding: 10px 0 10px 45px;
    box-shadow: none;
}
.form-form .form-form-wrap form .field-wrapper input::-webkit-input-placeholder {
    color: #bfc9d4;
    font-size: 14px;
}
.form-form .form-form-wrap form .field-wrapper input::-ms-input-placeholder {
    color: #bfc9d4;
    font-size: 14px;
}
.form-form .form-form-wrap form .field-wrapper input::-moz-placeholder {
    color: #bfc9d4;
    font-size: 14px;
}
.form-form .form-form-wrap form .field-wrapper input:focus { border-bottom: 1px solid #31aafd; box-shadow: none; }
.form-form .form-form-wrap form .field-wrapper {}
.form-form .form-form-wrap form .field-wrapper.toggle-pass {
    align-self: center;
    text-align: left;
    margin-top: 5px;
}
.form-form .form-form-wrap form .field-wrapper.toggle-pass .switch { margin-bottom: 0; vertical-align: sub; margin-left: 7px; }
.form-form .form-form-wrap form .field-wrapper button.btn { align-self: center; }
.form-form .form-form-wrap form .field-wrapper a.forgot-pass-link {
    width: 100%;
    font-weight: 700;
    color: #4361ee;
    text-align: left;
    display: block;
    letter-spacing: 1px;
    font-size: 15px;
    margin-top: 15px;
}
.form-form .form-form-wrap form .field-wrapper .n-chk .new-control-indicator { top: 1px; border: 1px solid #bfc9d4; background-color: #f1f2f3; }
.form-form .form-form-wrap form .field-wrapper .n-chk .new-control-indicator:after { top: 52%; }
.form-form .form-form-wrap form .field-wrapper.keep-logged-in { margin-top: 5px; text-align: left !important;}
.form-form .form-form-wrap form .field-wrapper.keep-logged-in label {
    font-size: 14px;
    color: #ffffff;
    padding-left: 25px;
    font-weight: 600;
}
.new-control.new-checkbox.checkbox-outline-primary>input:checked~span.new-control-indicator:after {
    border-color: #ffffff;
}
.new-control.new-checkbox.checkbox-outline-primary>input:checked~span.new-control-indicator {
    border: 2px solid #029cd8;
}

.form-form .terms-conditions {
    max-width: 480px;
    margin: 0 auto;
    color: #3b3f5c;
    font-weight: 600;
    margin-top: 60px;
    text-align: left !important
}
.form-form .terms-conditions a {
    color: #4361ee;
    font-weight: 700;
}
.form-image {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;

    position: fixed;
    right: 0;
    min-height: auto;
    height: 100vh;
    width: 50%;
}
.image-container {
    position: relative; 
    width: 400px;
    left: 50%; 
    top: 50%; 
    margin-left: -185px; 
}
.form-image .l-image{position: absolute;}
.form-image .s-image{margin-top: 16px; position: absolute; right: 0; top: 40px;}
.form-image .s-image img{width: 90px;}
@media (max-width: 991px) {
    .form-form { width: 100%; }
    .form-form .form-form-wrap { min-width: auto; }
    .form-image { display: none; }
}
@media (max-width: 575px) {
    .form-form .form-form-wrap form .field-wrapper.toggle-pass { margin-bottom: 28px; }
}
@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .form-form .form-form-wrap { width: 100%; }
    .form-form .form-container { height: 100%; }
}

/*
==================
    Switches
==================
*/

/* The switch - the box around the slider */
.switch {
    position: relative;
    display: inline-block;
    width: 35px;
    height: 18px;
}
/* Hide default HTML checkbox */
.switch input {display:none;}
/* The slider */
.switch .slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ebedf2;
  -webkit-transition: .4s;
  transition: .4s;
}
.switch .slider:before {
  position: absolute;
  content: "";
  background-color: white;
  -webkit-transition: .4s;
  -ms-transition: .4s;
  transition: .4s;
  height: 14px;
  width: 14px;
  left: 2px;
  bottom: 2px;
  box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.34);
}
.switch input:checked + .slider:before {
  -webkit-transform: translateX(17px);
  -ms-transform: translateX(17px);
  transform: translateX(17px)
}
/* Rounded Slider Switches */
.switch .slider.round { border-radius: 34px; }
.switch .slider.round:before { border-radius: 50%; }

/* Solid Switches*/

.switch.s-default .slider:before { background-color: #d3d3d3; }
.switch.s-primary .slider:before { background-color: #42d9fa; }
.switch.s-success .slider:before { background-color: #1abc9c; }
.switch.s-warning .slider:before { background-color: #e2a03f; }
.switch.s-danger .slider:before { background-color: #e7515a; }
.switch.s-secondary .slider:before { background-color: #805dca; }
.switch.s-info .slider:before { background-color: #2196f3; }
.switch.s-dark .slider:before { background-color: #3b3f5c; }
.switch input:checked + .slider:before { background-color: #fff; }

.switch.s-default input:checked + .slider { background-color: #d3d3d3; }
.switch.s-default input:focus + .slider { box-shadow: 0 0 1px #ebedf2; }
.switch.s-primary input:checked + .slider { background-color: #42d9fa; }
.switch.s-primary input:focus + .slider { box-shadow: 0 0 1px #4361ee; }
.switch.s-success input:checked + .slider { background-color: #1abc9c; }
.switch.s-success input:focus + .slider { box-shadow: 0 0 1px #1abc9c; }
.switch.s-warning input:checked + .slider { background-color: #e2a03f; }
.switch.s-warning input:focus + .slider { box-shadow: 0 0 1px #e2a03f; }
.switch.s-danger input:checked + .slider { background-color: #e7515a; }
.switch.s-danger input:focus + .slider { box-shadow: 0 0 1px #e7515a; }
.switch.s-secondary input:checked + .slider { background-color: #805dca; }
.switch.s-secondary input:focus + .slider { box-shadow: 0 0 1px #805dca; }
.switch.s-info input:checked + .slider { background-color: #2196f3; }
.switch.s-info input:focus + .slider { box-shadow: 0 0 1px #2196f3; }
.switch.s-dark input:checked + .slider { background-color: #3b3f5c; }
.switch.s-dark input:focus + .slider { box-shadow: 0 0 1px #3b3f5c; }

/* Outline Switches */
.switch.s-outline .slider {
    border: 2px solid #ebedf2;
    background-color: transparent;
    width: 36px;
    height: 19px;
}
.switch.s-outline .slider:before { height: 13px; width: 13px; }
.switch.s-outline[class*="s-outline-"] .slider:before {
    bottom: 1px;
    left: 1px;
    border: 2px solid #bfc9d4;
    background-color: #bfc9d4;
    color: #ebedf2;
    box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.25);
}
.switch.s-icons.s-outline-default { color: #d3d3d3; }
.switch.s-icons.s-outline-primary { color: #4361ee; }
.switch.s-icons.s-outline-success { color: #1abc9c; }
.switch.s-icons.s-outline-warning { color: #e2a03f; }
.switch.s-icons.s-outline-danger { color: #e7515a; }
.switch.s-icons.s-outline-secondary { color: #805dca; }
.switch.s-icons.s-outline-info { color: #2196f3; }
.switch.s-icons.s-outline-dark { color: #3b3f5c; }

.switch.s-outline-default input:checked + .slider { border: 2px solid #ebedf2; }
.switch.s-outline-default input:checked + .slider:before {
  border: 2px solid #d3d3d3;
  background-color: #d3d3d3;
  box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.25);
}
.switch.s-outline-default input:focus + .slider { box-shadow: 0 0 1px #ebedf2; }
.switch.s-outline-primary input:checked + .slider { border: 2px solid #4361ee; }
.switch.s-outline-primary input:checked + .slider:before {
  border: 2px solid #4361ee;
  background-color: #4361ee;
  box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.34);
}
.switch.s-outline-primary input:focus + .slider { box-shadow: 0 0 1px #4361ee; }
.switch.s-outline-success input:checked + .slider { border: 2px solid #1abc9c; }
.switch.s-outline-success input:checked + .slider:before {
  border: 2px solid #1abc9c;
  background-color: #1abc9c;
  box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.34);
}
.switch.s-outline-success input:focus + .slider { box-shadow: 0 0 1px #1abc9c; }
.switch.s-outline-warning input:checked + .slider { border: 2px solid #e2a03f; }
.switch.s-outline-warning input:checked + .slider:before {
  border: 2px solid #e2a03f;
  background-color: #e2a03f;
  box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.34);
}
.switch.s-outline-warning input:focus + .slider { box-shadow: 0 0 1px #e2a03f; }
.switch.s-outline-danger input:checked + .slider { border: 2px solid #e7515a; }
.switch.s-outline-danger input:checked + .slider:before {
  border: 2px solid #e7515a;
  background-color: #e7515a;
  box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.34);
}
.switch.s-outline-danger input:focus + .slider { box-shadow: 0 0 1px #e7515a; }
.switch.s-outline-secondary input:checked + .slider { border: 2px solid #805dca; }
.switch.s-outline-secondary input:checked + .slider:before {
  border: 2px solid #805dca;
  background-color: #805dca;
  box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.34);
}
.switch.s-outline-secondary input:focus + .slider { box-shadow: 0 0 1px #805dca; }
.switch.s-outline-info input:checked + .slider { border: 2px solid #2196f3; }
.switch.s-outline-info input:checked + .slider:before {
  border: 2px solid #2196f3;
  background-color: #2196f3;
  box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.34);
}
.switch.s-outline-info input:focus + .slider { box-shadow: 0 0 1px #2196f3; }
.switch.s-outline-dark input:checked + .slider { border: 2px solid #3b3f5c; }
.switch.s-outline-dark input:checked + .slider:before {
  border: 2px solid #3b3f5c;
  background-color: #3b3f5c;
  box-shadow: 0 1px 15px 1px rgba(52, 40, 104, 0.34);
}
.switch.s-outline-dark input:focus + .slider { box-shadow: 0 0 1px #3b3f5c; }


/*  Icons Switches */

.switch.s-icons {
  width: 57px;
  height: 30px;
}
.switch.s-icons .slider {
  width: 48px;
  height: 25px;
}
.switch.s-icons .slider:before {
  vertical-align: sub;
  color: #3b3f5c;
  height: 19px;
  width: 19px;
  line-height: 1.3;
  content: url('data:image/svg+xml, <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="%23e9ecef" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>');
}
.switch.s-icons input:checked + .slider:before {
  content: url('data:image/svg+xml, <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="%23fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>');
  vertical-align: sub;
  color: #fff;
  line-height: 1.4;
}
.switch.s-icons input:checked + .slider:before {
  -webkit-transform: translateX(23px);
  -ms-transform: translateX(23px);
  transform: translateX(23px);
}
.btn-primary {
    color: #fff;
    background-color: #398be3;
    border-color: #007bff;
}
CSS;

$this->registerCss($css);
?>













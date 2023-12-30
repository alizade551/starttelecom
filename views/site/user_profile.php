<?php 
use yii\bootstrap4\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Html;
use kartik\select2\Select2;
use webvimark\modules\UserManagement\models\User;

 $this->registerJsFile(Yii::$app->request->baseUrl.'/js/dropify/dropify.min.js',['depends' => [yii\web\JqueryAsset::className()]]); 

 $this->registerJsFile(Yii::$app->request->baseUrl.'/js/account-settings.js',['depends' => [yii\web\JqueryAsset::className()]]); 


$this->registerCssFile(Yii::$app->request->baseUrl."/css/dropify/dropify.min.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/dashboard/dash_2.css");
$this->title = Yii::t('app','Profile');
 ?>
    
<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
        </div>
    </div>
</div>


<div class="card custom-card">
    <div class="account-content">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12">
					<?php $form = ActiveForm::begin( ['options' => [
						'id' => 'user-profile-form',
						'class'=>'section general-info',
						'enableClientValidation' => true,
						'enctype' => 'multipart/form-data'
					]]); ?>
                        <div class="info">
              
                            <div style="clear: both;"></div>
                            <div class="row">
                                <div class="col-lg-11 mx-auto">
                                    <div class="row">
                                        <div class="col-xl-2 col-lg-12 col-md-4">
                                            <div class="upload mt-4 pr-md-4">
										 <?= $form->field($model, 'photo_file')->fileInput(['class' => 'dropify','data-default-file'=>'/uploads/users/profile/'.Yii::$app->user->photo_url.'']) ?>
                                            
                                                <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> <?=Yii::t("app","Upload photo") ?></p>
                                            </div>
                                        </div>
                                        <div class="col-xl-10 col-lg-12 col-md-8 mt-md-0 mt-4">
                                            <div class="form">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                         <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                                                    </div>                                                                    

                                                    <div class="col-sm-3">
                                                         <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>
                                                    </div>    
                                                                                                          
                                                    <div class="col-sm-3">
                                                         <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                                                    </div>     
                                                    <div class="col-sm-3">
                                                         <?= $form->field($model, 'default_theme')->dropDownList(['light' => Yii::t('app','Light'),'dark'=>Yii::t('app','Dark')]) ?>
                                                    </div>  
                                                           
                                                </div>

													<div class="form-group">
														<?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
													</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      <?php ActiveForm::end(); ?>
                    
              
            </div>
        </div>
    </div>
</div>


<style type="text/css">
.profile-head-container{
	display: flex;
    flex-direction: row;
    justify-content: space-between;
}
.section { -webkit-box-shadow: 0 4px 6px 0 rgba(85, 85, 85, 0.09019607843137255), 0 1px 20px 0 rgba(0, 0, 0, 0.08), 0px 1px 11px 0px rgba(0, 0, 0, 0.06);
    -moz-box-shadow: 0 4px 6px 0 rgba(85, 85, 85, 0.09019607843137255), 0 1px 20px 0 rgba(0, 0, 0, 0.08), 0px 1px 11px 0px rgba(0, 0, 0, 0.06);
    box-shadow: 0 4px 6px 0 rgba(85, 85, 85, 0.09019607843137255), 0 1px 20px 0 rgba(0, 0, 0, 0.08), 0px 1px 11px 0px rgba(0, 0, 0, 0.06); }

.form-control {
    padding: 8px 8px;   
}

.blockui-growl-message {
    display: none;
    text-align: left;
    padding: 15px;
    background-color: #8dbf42;
    color: #fff;
    border-radius: 3px;
}
.blockui-growl-message i {
    font-size: 20px;
}


/*
    General Infomation
*/


.general-info .info { padding: 20px; }
.general-info .save-info { padding: 20px; }
.general-info .info .form { width: 92%; }
.general-info .info .upload { border-right: 1px solid #ebedf2; }

.general-info .info .upload p i {
    font-size: 22px;
    color: #1b55e2;
    vertical-align: middle;
}



/*
    Image upload
*/
.general-info .info .dropify-wrapper {
    width: 120px;
    height: 120px;
    border: none;
    border-radius: 6px;
}
.general-info .info .dropify-wrapper .dropify-preview {
    background-color: #FFF; 
    padding: 0; 
}
.general-info .info .dropify-wrapper .dropify-clear {
    font-size: 16px;
    padding: 4px 8px;
    color: #FFF;
    border: none;
}
.general-info .info .dropify-wrapper .dropify-clear:hover { background-color: transparent; }
.general-info .info .dropify-wrapper .dropify-preview .dropify-infos .dropify-infos-inner p.dropify-infos-message { padding-top: 27px; }
.general-info .info .dropify-wrapper .dropify-preview .dropify-infos .dropify-infos-inner p.dropify-infos-message::before {
    height:20px;
    position: absolute;
    top: -1px;
    left: 45%;
    color: #fff;
    -webkit-transform: translate(-50%,0);
    transform: translate(-50%,0);
    background: transparent;
    width: 0;
    height: 0;
    font-size: 28px;
    width: 24px;
    content: " ";
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-upload-cloud'%3e%3cpolyline points='16 16 12 12 8 16'%3e%3c/polyline%3e%3cline x1='12' y1='12' x2='12' y2='21'%3e%3c/line%3e%3cpath d='M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3'%3e%3c/path%3e%3cpolyline points='16 16 12 12 8 16'%3e%3c/polyline%3e%3c/svg%3e");
    height: 20px;
}
.general-info .info .dropify-wrapper.touch-fallback { border: 1px solid #ebedf2; }
.general-info .info .dropify-wrapper.touch-fallback .dropify-preview .dropify-infos .dropify-infos-inner { padding: 0; }
.general-info .info .dropify-wrapper.touch-fallback .dropify-clear { color: #515365; }
.general-info .info .dropify-wrapper.touch-fallback .dropify-preview .dropify-infos .dropify-infos-inner p.dropify-filename { margin-top: 10px; }

.account-settings-container  { width: 100% !important; padding: 0;margin:0 }


</style>

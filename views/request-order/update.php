<?php

use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use \app\widgets\fileuploader\Fileuploader;
use borales\extensions\phoneInput\PhoneInput;


/* @var $this yii\web\View */
/* @var $model app\models\RequestOrder */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app','Order update : {fullname}',['fullname'=>$model->fullname]);
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
$validationUrl =  $langUrl.'/request-order/update-validate?id='.$model->id;

?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <div>
                <a href="<?=$langUrl ?>/request-order/index" style="margin-left: 10px;" class="btn btn-primary"><?=Yii::t("app","Back to All orders") ?></a>
            </div>
        </div>
    </div>
</div>

  <div class="card custom-card" style="padding:15px">
    <?php $form = ActiveForm::begin([
            'id'=>"order-update-form",
            'enableAjaxValidation' => true,
            'validateOnSubmit'=> true,
            'enableClientValidation'=>false,
            'validationUrl' => $validationUrl ,
            'options' => ['autocomplete' => 'off']]);
     ?>



        <div class="row">
          <div class="col-sm-6">
            <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>
          </div>
          <div class="col-sm-6">
              <?=$form->field($model, 'city_id',['template' => '{label}<div class="form-select">{input}<div class="loader select_loader"></div></div>{error}'])->dropDownList(ArrayHelper::map(\app\models\Cities::find()->all(),'id','city_name'),[
                 'onchange'=>'
                    $(".select_loader").show();
                    $.pjax.reload({
                    url: "'.Url::to(['/request-order/update']).'?id='.$model->id.'&city_id="+$(this).val(),
                    container: "#pjax-users-form",
                    timeout: 5000
                    });
                    $(document).on("pjax:complete", function() {
                      $(".select_loader").hide();
                    });
                ',
                'prompt'=>'Select City'])->label("City") ?>
          </div>
        </div>
               
        <div class="row">
          <div class="col-sm-6">
              <?= $form->field($model, 'company')->textInput(['maxlength' => true]) ?>
          </div>
          <div class="col-sm-6">
            <?php  Pjax::begin(['id'=>'pjax-users-form','enablePushState'=>true]);  ?>

            <?php 

            if (Yii::$app->request->get('city_id')) {
                       echo  $form->field($model, 'district_id')->dropDownList(ArrayHelper::map(\app\models\District::find()
                        ->where(['city_id'=>Yii::$app->request->get('city_id')])->all(),'id','district_name'),[
                 'onchange'=>'
                    $(".select_loader").show();
                    $.pjax.reload({
                    url: "'.Url::to(['/request-order/update']).'?id='.$model->id.'&city_id='.Yii::$app->request->get('city_id').'&dis_id="+$(this).val(),
                    container: "#pjax-users-form-dis",
                    timeout: 5000
                    });
                    $(document).on("pjax:complete", function() {
                      $(".select_loader").hide();
                    });
                ',
                'prompt'=>'Select District']);
            }else{
               echo  $form->field($model, 'district_id')->dropDownList(ArrayHelper::map(\app\models\District::find()
                        ->where(['city_id'=>Yii::$app->request->get('city_id')])->all(),'id','district_name'),[
                 'onchange'=>'
                    $(".select_loader").show();
                    $.pjax.reload({
                    url: "'.Url::to(['/request-order/update']).'?id='.$model->id.'&city_id='.Yii::$app->request->get('city_id').'&dis_id='.Yii::$app->request->get('dis_id').'",
                    container: "#pjax-users-form-dis",
                    timeout: 5000
                    });
                    $(document).on("pjax:complete", function() {
                      $(".select_loader").hide();
                    });
                ',
                'prompt'=>'Select District'])->label("District");  
            }

             ?>

            <?php Pjax::end(); ?>  
          </div>
        </div>

      
        <div class="row">
            <div class="col-sm-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-6">
            <?php  Pjax::begin(['id'=>'pjax-users-form-dis','enablePushState'=>true]);  ?>
            <?= $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(\app\models\Locations::find()
                        ->where(['city_id'=>Yii::$app->request->get('city_id')])->andWhere(['district_id'=>Yii::$app->request->get('dis_id')])->all(),'id','name')) ?>
            <?php Pjax::end(); ?>  
            </div>
        </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="row" style="margin-right: -10px;margin-left: -10px;">    
                    <div class="col-sm-4">  
                       <?= $form->field($model, 'message_lang')->dropDownList(
                        ArrayHelper::map(\app\models\MessageLang::find()->where(['published'=>'1'])->asArray()->all(),'alias','name')
                        ,['prompt'=>Yii::t('app','Select')]
                        ); 
                        ?>
                    </div>
                    <div class="col-sm-4">  
                        <?=$form->field($model, 'phone')->widget(PhoneInput::className(), [
                        'jsOptions' => [
                            'preferredCountries'=>['az','tr','ru','us'],
                            'formatOnDisplay' => true,
                            'separateDialCode' => false,
                            'autoHideDialCode' => true,
                            'nationalMode' => false,
                            'utilsScript'=>'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.0.2/js/utils.js'      
                        ]
                        ]); ?>
                    </div>
                    <div class="col-sm-4">  
                        <?=$form->field($model, 'extra_phone')->widget(PhoneInput::className(), [
                        'jsOptions' => [
                            'preferredCountries'=>['az','tr','ru','us'],
                            'formatOnDisplay' => true,
                            'separateDialCode' => false,
                            'autoHideDialCode' => true,
                            'nationalMode' => false,
                            'utilsScript'=>'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.0.2/js/utils.js'      
                        ]
                        ]); ?>
                    </div>
                </div>
              </div>
              <div class="col-sm-6">
                <?= $form->field($model, 'room')->textInput() ?>
              </div>
            </div>

            <div class="row">
                <?php if ( $model->password == "" ): ?>
                <div class="col-sm-4">
                    <?= $form->field($model, 'password',['inputOptions' => ['placeholder'=>Yii::t('app','Password')]])->textInput(['value' => $model->password,'class' => 'form-control'])->label() ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'paid_time_type')->dropDownList(
                        \app\models\RequestOrder::getPaidDayType()
                    ); 
                    ?>
                </div>

                <div class="col-sm-4">
                    <?= $form->field($model, 'paid_day')->dropDownList(
                        \app\models\RequestOrder::getDays(),['prompt'=>Yii::t('app','Select')]
                    ); 
                    ?>
                </div>

                <?php else: ?>
                <?= $form->field($model, 'password',['inputOptions' => ['placeholder'=>Yii::t('app','Password')]])->hiddenInput(['value' => $model->password,'class' => 'form-control'])->label(false) ?>
                <div class="col-sm-6">
                    <?= $form->field($model, 'paid_time_type')->dropDownList(
                        \app\models\RequestOrder::getPaidDayType()
                    ); 
                    ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'paid_day')->dropDownList(
                        \app\models\RequestOrder::getDays(),['prompt'=>Yii::t('app','Select')]
                    ); 
                    ?>
                </div>
                <?php endif ?>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label"><?=Yii::t("app","Photos")?></label>
                        <div class="form-input">
                            <?=Fileuploader::widget([
                                'url'=>$langUrl.'/request-order/photo-upload',    
                                  'photos'=>$model->userPhotos,
                                  'name'=>'RequestOrder[photos]',
                            ])?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
            <?php 

                if ( count(  explode(",",$model->selected_services) ) == 1) {
                    $model->selected_service_form = $model->selected_services;
                }else{
                    $model->selected_service_form = explode(',', $model->selected_services);
                }
             ?>
              <div class="col-sm-6">
                <?= $form->field($model, 'selected_service_form')->checkboxList(ArrayHelper::map(app\models\Services::find()->all(), 'id', 'service_name'),[
                    'item' => function($index, $label, $name, $checked, $value) {
                        $checked = $checked ? 'checked' : '';
                        return "<div class='n-chk'><label class=\"new-control new-checkbox checkbox-success\" for='checkbox-".$index."'> <input class='new-control-input' id='checkbox-".$index."' type='checkbox' {$checked} name='{$name}' value='{$value}'> <span class='new-control-indicator'></span>  {$label}</label></div>";
                    }
                ]) ?> 
                 <?= $form->field($model, 'request_at')->hiddenInput(['value'=>time()])->label(false) ?>
              </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-primary',]) ?>
                           <a href="/request-order/index" style="margin-left: 10px;" class="btn btn-warning"><?=Yii::t('app','Back to All orders') ?></a>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

   
 </div>


<?php 

$this->registerJs('


    var xhr;
    var xhr_active=false;
    var form = $("form#order-update-form");
    form.on("beforeSubmit", function (e) {
    if( form.find("button").prop("disabled")){
    return false;
    }
           if(xhr_active) { xhr.abort(); }
            xhr_active=true;
         form.find("button").prop("disabled",true);
       
         xhr = $.ajax({
              url: "'.\yii\helpers\Url::to(["request-order/update?id=".$model->id]).'",
              type: "post",
              data: form.serialize(),
              success: function (response) {
                  if(response.status == "success"){
                   window.location.href = response.url;
                  }else{
                    xhr_active=false;
                    form.find("button").prop("disabled",false);
                  }
              }
         });
         return false;
    }); 


  $(document).on("click",".add_extra_phone",function(){
      $(".field-users-extra_phone").fadeIn(500);
      $(".field-users-extra_phone_2").fadeIn(500);
      $(".remove_extra_phone").fadeIn(500);
      $(".remove_extra_phone_2").fadeIn(500);
    });

    $(document).on("click",".remove_extra_phone",function(){
         $(".field-users-extra_phone").find("input").val("");
         $(".field-users-extra_phone").fadeOut(500);
         $(this).fadeOut(500);
      });

    $(document).on("click",".remove_extra_phone_2",function(){
         $(".field-users-extra_phone_2").find("input").val("");
         $(".field-users-extra_phone_2").fadeOut(500);
         $(this).fadeOut(500);

      });


 $(document).on("keydown",".o_number",function (e) {
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||    
        (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
        (e.keyCode >= 35 && e.keyCode <= 39)) {
             return;
    }
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});





  ');
 ?>



<style type="text/css">
.field-requestorder-phone_main label,.field-requestorder-extra_phone label,.field-requestorder-extra_phone_2 label,.iti{display: block;}

#pac-input{
    padding: 10px;
    font-size: 14px;
    width: 20%;
    height: 36px;
    z-index: 9999;
    line-height: 14px;
}

.field-users-phone_main, .field-users-extra_phone,.field-users-extra_phone_2{width: 89%;float: left;}

   @media only screen and (max-width:1300px){
.field-users-phone_main, .field-users-extra_phone,.field-users-extra_phone_2{width: 81%;float: left;}
    }


.add_extra_phone,.remove_extra_phone_2,.remove_extra_phone{
    float: left;
    width: 35px;
    height: 31px;
    line-height: 31px;
    box-shadow: inset 0 0 0 1px #c8ccd0 !important;
    text-align: center;
    border-radius: 2px;
    background: #ffffff;
    color: #0ab21b;
    font-size: 24px;
    cursor: pointer;
    margin-top: 25px;
    margin-left: 10px;
}
.add_extra_phone::selection {color: none;background: none}

.remove_extra_phone, .remove_extra_phone_2 {
    color: red !important;
}
         .form-select{position: relative;}
.select_loader,.select_loader:after {
    width: 20px;
    height: 20px;
}
.loader,
.loader:after {
  border-radius: 50%;
  width: 3.5em;
  height: 3.5em;
}
.loader {
  margin: 0px auto;
  font-size: 10px;
  position: relative;
  text-indent: -9999em;
border-top: 0.25em solid rgba(0, 0, 0, 0.1);
border-right: 0.25em solid rgba(0, 0, 0, 0.1);
border-bottom: 0.25em solid rgba(0, 0, 0, 0.1);
border-left: 0.25em solid #0280e4;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation: load8 0.5s infinite linear;
  animation: load8 0.5s infinite linear;
}
@-webkit-keyframes load8 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes load8 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

.select_loader,
.select_loader:after {
    width: 20px;
    height: 20px;
}
.select_loader{
    display: none;
    margin: 0;
    position: absolute;
    background: white;
    right: 4px;
    top: 7px;
    z-index: 99;
    border-width: 2px;
}

.ball-beat>div{
    background-color:#aaa;
    border-radius:100%
}

@-webkit-keyframes ball-beat{
    50%{
        opacity:.2;
        -webkit-transform:scale(.75);
        transform:scale(.75)
    }
    100%{
        opacity:1;
        -webkit-transform:scale(1);
        transform:scale(1)
    }
}
@keyframes ball-beat{
    50%{
        opacity:.2;
        -webkit-transform:scale(.75);
        transform:scale(.75)
    }
    100%{
        opacity:1;
        -webkit-transform:scale(1);
        transform:scale(1)
    }
}
.ball-beat>div{
    width:15px;
    height:15px;
    margin:2px;
    display:inline-block;
    -webkit-animation:ball-beat .7s 0s infinite linear;
    animation:ball-beat .7s 0s infinite linear
}
.ball-beat>div:nth-child(2n-1){
    -webkit-animation-delay:-.35s!important;
    animation-delay:-.35s!important
}

.animate {
    -webkit-animation-duration: 0.3s;
    animation-duration: 0.3s;
    -webkit-animation-fill-mode: both;
    animation-fill-mode: both;
}

@-webkit-keyframes fadeInUp {
  from {
    opacity: 0;
    -webkit-transform: translateY(20px);
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}
@keyframes fadeInUp {
  from {
    opacity: 0;
    -webkit-transform: translate3d(0, 20px, 0);
    transform: translate3d(0, 20px, 0);
  }

  to {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}
.fadeInUp {
  -webkit-animation-name: fadeInUp;
  animation-name: fadeInUp;
}
#requestorder-selected_service_form{display: flex;}

</style>
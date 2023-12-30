<?php

use \app\widgets\fileuploader\Fileuploader;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;
use borales\extensions\phoneInput\PhoneInput;



$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">

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


      <div class="widget-content widget-content-area">
        <?php $form = ActiveForm::begin([
            'id'=>"request-order-form",
            'enableAjaxValidation' => true,
            'validationUrl' => $langUrl .'/request-order/create-validate',
            'options' => ['autocomplete' => 'off']
        ]
        ); ?>
    <div class="row">
      <div class="col-sm-6">
          <?= $form->field($model, 'fullname',['inputOptions' => ['placeholder'=>Yii::t('app','Fullname')]])->textInput(['maxlength' => true,'class' => 'form-control']) ?>
      </div>
      <div class="col-sm-6">
        <?= $form->field($model, 'company',['inputOptions' => ['placeholder'=>Yii::t('app','Company name'),'class' => 'form-control']])->textInput(['maxlength' => true]) ?>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-6">
        <?php 
            $allCity = ArrayHelper::map(
                \app\models\Cities::find()
                ->withByCityId()
                ->orderBy(['city_name'=>SORT_ASC])
                ->all()
                ,'id',
                'city_name'
            );
         ?>

        <?=$form->field($model, 'city_id')->dropDownList($allCity,[
         'onchange'=>'
            $(".select_loader").show();
            $.pjax.reload({
            url: "'.Url::to(['request-order/create']).'?city_id="+$(this).val(),
            container: "#pjax-request-form",
            timeout: 5000
            });
            $(document).on("pjax:complete", function() {
              $(".select_loader").hide();
            });
        ',
        'prompt'=>Yii::t("app","Select")
        ])?>
      </div>
      <div class="col-sm-6">
        <?php  Pjax::begin(['id'=>'pjax-request-form','enablePushState'=>true]);  ?>

        <?php 


        if (Yii::$app->request->get('city_id') && Yii::$app->request->isPjax ) {
            $allDistrictPjaxGet = ArrayHelper::map(
                \app\models\District::find()
                ->where(['city_id'=>Yii::$app->request->get('city_id')])
                ->withByDistrictId()
                ->orderBy(['district_name'=>SORT_ASC])
                ->all(),
                'id',
                'district_name'
            );
        echo  $form->field($model, 'district_id',['enableAjaxValidation' => true])->dropDownList($allDistrictPjaxGet,[
         'onchange'=>'
            // $(".select_loader").show();
            $.pjax.reload({
            url: "'.Url::to(['/request-order/create']).'?city_id='.Yii::$app->request->get('city_id').'&dis_id="+$(this).val(),
            container: "#pjax-request-form-dis",
            timeout: 5000
            });
            $(document).on("pjax:complete", function() {
              // $(".select_loader").hide();
            });
        ',
        'prompt'=>Yii::t("app","Select")]);
          foreach ($form->attributes as $attribute) {
              $attribute = Json::htmlEncode($attribute);
              $this->registerJs("jQuery('form#request-order-form').yiiActiveForm('add', $attribute); ");
          } 

        }else{
        echo  $form->field($model, 'district_id')->dropDownList([''=>''],['prompt'=>Yii::t("app","Select")]);
        }
        ?>
        <?php Pjax::end(); ?>  
      </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php  Pjax::begin(['id'=>'pjax-request-form-dis','enablePushState'=>true]);  ?>
                <?php if (Yii::$app->request->get('city_id') && Yii::$app->request->get('dis_id') && Yii::$app->request->isPjax): ?>
                    <?= $form->field($model, 'location_id')->dropDownList(
                        ArrayHelper::map(
                            \app\models\Locations::find()
                            ->where(['city_id'=>Yii::$app->request->get("city_id"),"district_id"=>Yii::$app->request->get("dis_id")])
                            ->withByLocationId()
                            ->orderBy(['name'=>SORT_ASC])
                            ->all(),
                            'id',
                            'name'
                        )); 
                    ?>
                    <?php 
                        foreach ($form->attributes as $attribute) {
                            $attribute = Json::htmlEncode($attribute);
                            $this->registerJs("jQuery('form#request-order-form').yiiActiveForm('add', $attribute); ");
                        } 
                     ?>
                  <?php else: ?>
                    <?= $form->field($model, 'location_id')->dropDownList(['prompt'=>Yii::t("app","Select")]); ?>
                  <?php endif ?>
            <?php Pjax::end(); ?>       
          </div>
          <div class="col-sm-6">
              <?= $form->field($model, 'room',['inputOptions' => ['placeholder'=>Yii::t('app','Room number'),'class' => 'form-control']])->textInput(['maxlength' => true]) ?>
          </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'password',['inputOptions' => ['placeholder'=>Yii::t('app','Password')]])
                ->textInput(['maxlength' => true,'class' => 'form-control']) ?>
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

    </div>
    <div class="row" >
      <div class="col-sm-12">
        <div class="row" style="margin-right: -10px;margin-left: -10px;">
            <div class="col-sm-3">  
                <?php $model->message_lang = $siteConfig['message_lang']; ?>
               <?= $form->field($model, 'message_lang')->dropDownList(
                ArrayHelper::map(\app\models\MessageLang::find()->where(['published'=>'1'])->asArray()->all(),'alias','name')
                ,['prompt'=>Yii::t('app','Select')]
                ); 
                ?>
            </div>
            <div class="col-sm-3">  
               <?= $form->field($model, 'email',['inputOptions' => ['placeholder'=>Yii::t('app','example@mail.com') ,'class' => 'form-control']])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-3">  
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
            <div class="col-sm-3">  
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

    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>
        <?= $form->field($model, 'request_at')->hiddenInput(['value'=>time()])->label(false) ?>

    <?= $form->field($model, 'selected_service_form')->checkboxList(ArrayHelper::map(app\models\Services::find()->all(), 'id', 'service_name'),[
            'item' => function($index, $label, $name, $checked, $value) {
                $checked = $checked ? 'checked' : '';
                return "<div class='n-chk'><label class=\"new-control new-checkbox checkbox-primary\" for='checkbox-".$index."'> <input class='new-control-input' id='checkbox-".$index."' type='checkbox' {$checked} name='{$name}' value='{$value}'> <span class='new-control-indicator'></span>  {$label}</label></div>";
            }
        ]) ?> 
    <?php endif ?>

    <?= $form->field($model, 'request_at')->hiddenInput(['value'=>time()])->label(false) ?>
        <?= Html::submitButton(Yii::t("app","Create"), ['class' => 'btn btn-success']) ?>
    
    <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>

<style type="text/css">
.field-requestorder-phone_main label,.field-requestorder-extra_phone label,.field-requestorder-extra_phone_2 label,.iti{display: block;}
#mapBox {
    width: 100%;
    height: 600px;
    position: relative;
    padding: 0;
    margin: 0;
}
#mapBox h2 {
    text-align: center;
    position: absolute;
    left: 50%;
    top: 50%;
    font-size: 20px;
    margin-left: -110px;
}
#pac-input{
    padding: 10px;
    font-size: 14px;
    width: 30%;
    height: 36px;
    z-index: 9999;
    line-height: 14px;
}
#requestorder-selected_service_form{display: flex;}

</style>
<?php 

$this->registerJs('

$("#requestorder-paid_time_type").on("change",function(){
     let today = new Date();
     let day = parseInt( String(today.getDate()).padStart(2, "0") );
    if(  $(this).val() == "0" ){
        $("#requestorder-paid_day").val("1");
    }else{
        $("#requestorder-paid_day").val(day);
    }
})

$("#requestorder-paid_day").on("change",function(){
    if(  $("#requestorder-paid_time_type").val() == "0" ){
        $(this).val("1");
    }
});


$("#request-order-form").on("keyup keypress", function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) { 
    e.preventDefault();
    return false;
  }
});

var xhr;
var xhr_active=false;
var form = $("form#request-order-form");
form.on("beforeSubmit", function (e) {
if( form.find("button").prop("disabled")){
return false;
}
       if(xhr_active) { xhr.abort(); }
        xhr_active=true;
     form.find("button").prop("disabled",true);
   
     xhr = $.ajax({
          url: "'.\yii\helpers\Url::to(["request-order/create"]).'",
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
');

$this->registerJs('

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


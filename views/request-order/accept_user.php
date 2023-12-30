<?php
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;
use kartik\select2\Select2;
use yii\web\JsExpression;



/* @var $this yii\web\View */
/* @var $model app\models\Devices */
/* @var $form yii\widgets\ActiveForm */

$currency = $siteConfig['currency'];
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

if ( $model->tariff != 0 ) {
    $updatedAt = \app\components\Utils::nextUpdateAtWhenRequested( $model->id, $tarif )['date'];
}else{
    $updatedAt = Yii::t("app","Data not set");
}

$span= '';
$disabled = false;
if ($model->status == 0) {
     $span = '<span class="badge badge-warning"  style="margin-right:5px">'.Yii::t('app','Pending').'</span>';
}

if ($model->status == 3) {
     $span = '<span class="badge badge-warning"  style="background-color: #795548;color:#fff;margin-right:5px">'.Yii::t('app','Archive').'</span>';
}

if ($model->second_status == '4') {
     $span .= '<span class="badge badge-info" style="margin-right:5px">'.Yii::t('app','Reconnect').'</span>';

}if ($model->second_status == '5') {
    $disabled = true;
     $span .= '<span class="badge badge-primary" style="margin-right:5px">'.Yii::t('app','New service').'</span>';
}

if ($model->status == '1') {
     $span .= ' <span class="badge badge-success" style="margin-right:5px">'.Yii::t('app','Active').'</span>';
}

?>

<?php $form = ActiveForm::begin([
    'id'=>"accept-order-forum",
    'enableAjaxValidation' => true,
    'enableClientValidation'=>true,
    'validationUrl' =>  $langUrl .'accept-order-validate',
    'options' => ['autocomplete' => 'off']]);
?>


    <ul class="conf-user">
        <li><?=Yii::t("app", "Balance")?>: <b> <?=$model->balance?> </b> <?=$currency ?></li>
        <li><?=Yii::t("app", "Tariff")?>: <b> <?=$tarif ?> </b> <?=$currency ?></li>
        <li><?=$span ?>  </li>
    </ul>

    <?php 
        if ( $model->status == 0 || $model->status == 3 || $model->second_status == 4 ) {
            $requestTypeData = app\models\RequestOrder::getOrderRequestType();
        }elseif ( $model->second_status == 5 ){
            $requestTypeData = app\models\RequestOrder::getOrderRequestType();
            unset(  $requestTypeData['3'] );
        }

     ?>
    <?= $form->field($model, 'request_type')->dropDownList(
    $requestTypeData,
       [
        'maxlength' => true,
        'class' => 'form-control',
        'prompt'=>Yii::t("app","Select"),
        'onchange'=>'

            let balance_in = $("#requestorder-balance_in").val();
            let request_type = $(this).val();
            let id = "'.$model->id.'";
            let that = $(this);

           $.ajax({
                url:"'.Url::to('accept-order-price').'",
                beforeSend:function(){
                    
                },
                method:"POST",
                data:{id,balance_in,request_type},
                success:function(res){
                    if( res.status == "0" || res.status == "3" ){
                        $("#expired-at").val(res.date);
                        $.pjax.reload({
                             url: "'.Url::to(['request-order/accept-order?id='.$model->id]).'&request_type="+that.val(),
                             container: "#pjax-accept-user-form",
                             timeout: 5000
                        });


                        if( that.val() == "1" || that.val() == "3" || that.val() == "2" ){
                            $("#requestorder-balance_in").val(0);
                            $("#removal-amount").val(0);
                            $("#requestorder-balance_in").attr("readonly","readonly");
                        }else{
                            $("#requestorder-balance_in").removeAttr("readonly");
                        }

                    }

                    if( res.status == "1" || res.status == "2" ){
                       
                        if( that.val() == "1" || that.val() == "2" ){
                            $("#requestorder-balance_in").val(0);
                            $("#removal-amount").val(0);
                            $("#requestorder-balance_in").attr("readonly","readonly");
                        }else{
                            $("#requestorder-balance_in").removeAttr("readonly");
                            $("#removal-amount").val(res.removalAmount)
                        }
                    }

                }
            });
        ',
        ]
    ) ?>

    <?php  Pjax::begin(['id'=>'pjax-accept-user-form']);  ?>
    <?php $model->request_type = Yii::$app->request->get("request_type");  ?>
        <?php if ( Yii::$app->request->isPjax && Yii::$app->request->get("request_type") && $model->request_type == "3" ): ?>
    
            <?= $form->field($model, 'temporary_day')->dropDownList(app\models\RequestOrder::getTemporaryDays(),[
                'prompt'=>Yii::t("app","Select"),
                'onchange'=>'
                    let balance_in = 0;
                    let request_type = 3;
                    let id = "'.$model->id.'";
                    let temporary_day = $(this).val();

                   $.ajax({
                        url:"'.Url::to('accept-order-price').'",
                        beforeSend:function(){
                            
                        },
                        method:"POST",
                        data:{id,balance_in,request_type,temporary_day},
                        success:function(res){
                            if( res.status == "0" || res.status == "3" ){
                                if(  request_type == "3"  ){
                                    $("#expired-at").val(res.date);
                                }
                            }
                        }
                    });

                '
            ]) ?>
            <?php 
                foreach ($form->attributes as $attribute) {
                $attribute = Json::htmlEncode($attribute);
                    $this->registerJs("jQuery('form#accept-order-forum').yiiActiveForm('add', $attribute);");
                } 
            ?>
        <?php endif ?>


    <?php Pjax::end(); ?>  

    <?php if ( $model->status == "0" ): ?>
          <?= $form->field($model, 'contract_number')->textInput(['maxlength' => true,'class' => 'form-control']) ?>
    <?php else: ?>
          <?= $form->field($model, 'contract_number')->hiddenInput(['value' => $model->contract_number])->label(false) ?>
        <div class="form-group field-itemusage-quantity">
            <label class="control-label" for="request-order-contract"><?=Yii::t('app','Contract number') ?></label>
            <input class="form-control" type="text" id="contract" disabled="" value="<?=$model->contract_number ?>">
          </div>
    <?php endif ?>


  <?=$form->field($model, 'personals')->widget(Select2::classname(), [
    'maintainOrder' => true,
    'bsVersion' => '4.x',
    'data'=>$personal_data,
     'options' => ['placeholder' => Yii::t('app','Personal fullname'), 'multiple' => true],
    'pluginOptions' => [
        'initialize' => true,
        'allowClear' => true,
        'minimumInputLength' => 1,
         'enableClientValidation' => true,
        'language' => [
            'errorLoading' => new JsExpression("function () { return 'Please wait'; }"),
        ],
        'ajax' => [
            'url' => \yii\helpers\Url::to(['personal-list']),
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {q:params.term}; }')
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression('function(city) { return city.text; }'),
        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
    ],
  ])?>
  <?= $form->field($model, 'balance_in')->textInput(['maxlength' => true,'class' => 'form-control','value'=>0,'readonly'=>true]) ?>

  <?php 
  $this->registerJs('
    $("#requestorder-balance_in").on("change",function(){

        let balance_in = $(this).val();
        let request_type = $("#requestorder-request_type").val();
        let id = "'.$model->id.'";

          $.ajax({
                url:"'.Url::to('accept-order-price').'",
                beforeSend:function(){
                    
                },
                method:"POST",
                data:{id,balance_in,request_type},
                success:function(res){
                    if( res.status == "0" || res.status == "3" ){
                        $("#expired-at").val(res.date);
                    }
                    if( request_type == "0" ){
                        if( res.status == "0" || res.status == "3" ){
                            $("#removal-amount").val(res.removalAmount);
                        }
                    }
                }
            });

    })

   ');

   ?>


  <?= $form->field($model, 'customer_id')->hiddenInput(['value' => $model->id])->label(false) ?>

  <div class="form-group field-itemusage-quantity">
    <label class="control-label" for="itemusage-quantity"><?=Yii::t("app","Removal amount") ?></label>
    <input class="form-control" type="text" id="removal-amount" disabled="" value="0">
  </div>

<?php if ( $model->status  == 0 || $model->status == 3 ): ?>
<div class="form-group field-itemusage-quantity">
    <label class="control-label" for="itemusage-quantity"><?=Yii::t("app","Expired at") ?></label>
    <input class="form-control" type="text" id="expired-at" disabled="" value="<?=$updatedAt ?>">
</div>
<?php endif ?>


    <?= Html::submitButton('<span class="spinner-border  mr-2 align-self-center "></span>'.Yii::t('app','Accept order') , ['class' => 'btn btn-success']) ?>
<?php ActiveForm::end(); ?>
        

<?php $this->registerJs('

var clickTransfer = false;
var xhrTransfer;
var xhrActiveTransfer=false;
var formTransfer = $("form#accept-order-forum");

$("form#accept-order-forum").on("beforeSubmit", function (e) {
    if(!clickTransfer){

        clickTransfer = true;
        if( formTransfer.find(".update-device").prop("disabled")){
            return false;
        }
        if(xhrActiveTransfer) { xhrTransfer.abort(); }
        xhrActiveTransfer = true;
        formTransfer.find(".btn-primary").prop("disabled",true);

        xhrTransfer = $.ajax({
          url: "'.\yii\helpers\Url::to(["/request-order/accept-order?id="]).$model->id.'",
          type: "post",
          beforeSend:function(){
            $(".loader").show();
            $(".overlay").addClass("show");
            $(".spinner-border").addClass("show");
          },
          data: formTransfer.serialize(),
          success: function (response) {
              $(".loader").hide();
              $(".overlay").removeClass("show");
              $(".spinner-border").removeClass("show");

            if(response.status == "error"){
                 alertify.set("notifier","position", "top-right");
                 alertify.error(response.message);
            }          

            if(response.status == "success"){
                $("#transfer-packet-forum").find("p").text(response.message);
                $("#modal").modal("toggle");
                alertify.set("notifier","position", "top-right");
                alertify.success(response.message);
            }else{
                xhrActiveTransfer=false;
                formTransfer.find(".add-device").prop("disabled",false);
            }

          }
        }).done(function(){ clickTransfer = false; });
        return false;


    }

}); 
 
') ?>

<style type="text/css">
.spinner-border {
    width: 1rem;
    height: 1rem;
    display: none;
}
.spinner-border.show{
    display: inline-block;
}
</style>
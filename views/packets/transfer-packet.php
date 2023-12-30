<?php
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model app\models\Devices */
/* @var $form yii\widgets\ActiveForm */
?>
<div style="width: 100%;">
<?php $form = ActiveForm::begin([
    'id'=>"transfer-packet-forum",
    'enableAjaxValidation' => true,
    'validateOnSubmit'=> true,
    'enableClientValidation'=>false,
    'validationUrl' => 'transfer-packet-validate',
    'options' => ['autocomplete' => 'off']]);
?>

<?php 
    if ( $userCount['active_user_count'] != null && $userCount['deactive_user_count'] != null && $userCount['archive_user_count'] != null && $userCount['vip_user_count'] != null  && $userCount['pending_user_count'] != null  ) {
        $active_user_count = $userCount['active_user_count'];
        $deactive_user_count = $userCount['deactive_user_count'];
        $archive_user_count = $userCount['archive_user_count'];
        $vip_user_count = $userCount['vip_user_count'];
        $pending_user_count = $userCount['pending_user_count'];

        
    }else{
        $active_user_count = 0;
        $deactive_user_count = 0;
        $archive_user_count = 0;
        $vip_user_count = 0;
        $pending_user_count = 0;
    }

 ?>

<p style="font-size: 14px; font-weight: 400;">  <?=Yii::t(
        'app',
        '{packet_name} packet have {active_user_count} active, {deactive_user_count} deactive, {archive_user_count} archive, {vip_user_count} vip, and {pending_user_count} pending users. Are you sure want to transfer users to Transmitted packet?',
        [
            'packet_name' => $model['packet_name'],
            'active_user_count' => $active_user_count,
            'deactive_user_count' => $deactive_user_count,
            'archive_user_count' => $archive_user_count,
            'vip_user_count' => $vip_user_count,
            'pending_user_count' => $pending_user_count,
        ]
    ) ?> </p>
    
 <div class="form-group field-requestorder-fullname required has-success">
    <label class="control-label" for="requestorder-fullname"><?=Yii::t('app','Current packet') ?></label>
    <input type="text" id="requestorder-fullname" class="form-control"  value="<?=$model['packet_name'] ?>" disabled>
</div>   
<?=$form->field($model, 'transfer_packet')->dropDownList( ArrayHelper::map( $allPackets,'id','packet_name' ),['prompt'=>Yii::t('app','Select')] ) ?>
<?=$form->field($model, 'query_count')->dropDownList( \app\models\Packets::getQueryCount() ,['prompt'=>Yii::t('app','Select')] ) ?>


<?= Html::submitButton('<span class="spinner-border  mr-2 align-self-center "></span>'.Yii::t('app','Transfer') , ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
        
</div>

<?php $this->registerJs('

var clickTransfer = false;
var xhrTransfer;
var xhrActiveTransfer=false;
var formTransfer = $("form#transfer-packet-forum");

$("form#transfer-packet-forum").on("beforeSubmit", function (e) {
    if(!clickTransfer){

        clickTransfer = true;
        if( formTransfer.find(".update-device").prop("disabled")){
            return false;
        }
        if(xhrActiveTransfer) { xhrTransfer.abort(); }
        xhrActiveTransfer = true;
        formTransfer.find(".btn-primary").prop("disabled",true);

        xhrTransfer = $.ajax({
          url: "'.\yii\helpers\Url::to(["/packets/transfer-packet?id="]).$model->id.'",
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
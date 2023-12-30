<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app','Clear nats');
?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <div class="container-actions">
                <?php if (User::canRoute("/cgn-ip-address/index")): ?>
                    <a class="btn btn-primary" data-pjax="0" href="/cgn-ip-address/index">
                        <?=Yii::t("app","CG-NATS") ?>
                    </a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<div class="card custom-card" style="padding: 15px;">
	 <div class="row">
		 <div class="col-lg-12">
		    <?php $form = ActiveForm::begin(['id' => 'ip-adresses-form']); ?>

			    <?=$form->field($model, 'start_ip')->textInput() ?>
			    <?=$form->field($model, 'end_ip')->textInput() ?>

			    <div class="form-group">
			        <?= Html::submitButton(Yii::t("app","Clear"), ['class' => 'btn btn-danger']) ?>
			    </div>
		    <?php ActiveForm::end(); ?>
		</div>
	</div>
</div>




<?php 
$this->registerJs('

var xhr;
var xhr_active=false;
var form = $("form#ip-adresses-form");
form.on("beforeSubmit", function (e) {
if( form.find("button").prop("disabled")){
return false;
}
       if(xhr_active) { xhr.abort(); }
        xhr_active=true;
     form.find("button").prop("disabled",true);
   
     xhr = $.ajax({
          url: "'.\yii\helpers\Url::to(["cgn-ip-address/delete-nat-from-router"]).'",
          type: "post",
          beforeSend:function(){
            form.find(".btn-primary .spinner-border").addClass("show");
            $(".loader").show();
            $(".loader-overlay").addClass("show")
          },

          data: form.serialize(),
          success: function (response) {
              if(response.status == "success"){
                form.find(".btn-primary .spinner-border").removeClass("show");
                alertify.set("notifier","position", "top-right");
                alertify.success(response.message);
                $("#modal").modal("hide");
                window.location.href = response.url
              }else{
                 form.find(".btn-primary .spinner-border").removeClass("show");
                alertify.set("notifier","position", "top-right");
                alertify.error(response.message);
                xhr_active=false;
                form.find("button").prop("disabled",false);
              }

                  $(".loader").hide();
                $(".loader-overlay").removeClass("show");
          }
     })
     return false;
}); 

');


 ?>

 <style type="text/css">
 .select2-container {
    z-index: 99 !important;
}
 </style>
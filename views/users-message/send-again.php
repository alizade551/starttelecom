<div class="send-again">
	<h5><?=Yii::t("app","Send again to {user_fullname} !",['user_fullname'=>$model->user->fullname]) ?></h5>
	<p><?=Yii::t('app','Are you sure want to send ?' ) ?></p>
	<button id="send-again" class="btn btn-success"><?=Yii::t('app','Send') ?></button>
	<button data-fancybox-close="" class="btn btn-primary" title="Close"><?=Yii::t('app','Close') ?></button>
</div>
 <style type="text/css">

 .send-again{min-width: 400px;max-width: 500px}
 #send-again{margin-right: 3px;}
 .send-again button {margin-top: 10px}
</style>     

<?php 
$this->registerJs('

var xhr_item;
var xhr_active_item=false;
var button = $("#send-again");
button.on("click", function (e) {
	if( button.prop("disabled")){
		return false;
	}

	if (xhr_active_item ) { xhr_item.abort(); }
     xhr_active_item=true;
     button.prop("disabled",true);
     xhr_item = $.ajax({
          url: "'.\yii\helpers\Url::to(["/users-message/send-again?id="]).$model->id.'",
          type: "POST",
          success: function (response) {
              if(response.status == "success"){
                alertify.set("notifier","position", "top-right");
                alertify.success(response.message);
                xhr_active_item=false;
                button.prop("disabled",false);
                $.fancybox.close();
              }else{
                alertify.set("notifier","position", "top-right");
                alertify.error(response.message);
                xhr_active_item=false;
                button.prop("disabled",false);
                $.fancybox.close();
              }
          }
     });
     return false;
}); 
');

 ?>
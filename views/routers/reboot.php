<div class="router-reboot">
	<h5><?=Yii::t("app","Reboot router {router_name} !",['router_name'=>$model['name']]) ?></h5>
	<p><?=Yii::t('app','Are you sure want to re-boot {router_name} ?',[ 'router_name'=>$model['name'] ] ) ?></p>
	<button id="router-reboot" class="btn btn-primary"><?=Yii::t('app','Re-boot') ?></button>
	<button data-fancybox-close="" class="btn btn-secondary" title="Close"><?=Yii::t('app','Close') ?></button>
</div>
 <style type="text/css">

 .router-reboot{min-width: 400px;max-width: 500px}
 #router-reboot{margin-right: 3px;}
 .router-reboot button {margin-top: 10px}
</style>     

<?php 
$this->registerJs('

var xhr_item;
var xhr_active_item=false;
var button = $("#router-reboot");
button.on("click", function (e) {
	if( button.prop("disabled")){
		return false;
	}

	if (xhr_active_item ) { xhr_item.abort(); }
     xhr_active_item=true;
     button.prop("disabled",true);
     xhr_item = $.ajax({
          url: "'.\yii\helpers\Url::to(["/routers/reboot?id="]).$model['id'].'",
          type: "post",
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
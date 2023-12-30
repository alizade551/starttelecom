<?php 
use yii\helpers\Url;

$this->title = Yii::t("app","Re-connecting");
 ?>
<div class="reconnect_user" style="display: inline-block;">
    <p> <?=Yii::t('app', 'Are you sure want to re-connect {fullname} ?', ['fullname' => $model->fullname]) ?></p>
    <button id="reconnect-button" class="btn btn-primary" data-user_id="<?=$model->id ?>"     title="<?=Yii::t('app','Re-connecting') ?>" ><?=Yii::t('app','Send user to reconnection') ?></button>
    <button  class="btn btn-secondary" style="margin-left: 5px;" title="<?=Yii::t('app','Close') ?>" ><?=Yii::t('app','Close') ?></button>	         
</div>

<?php
$this->registerjs('
$(".btn-secondary").on("click",function(){
	$("#modal").modal("toggle");
});

$("#reconnect-button").on("click",function(){
	const user_id = $(this).data("user_id");
	const text = "'.Yii::t("app","Are you sure want to do this ?").'";
  	if (confirm(text) == true) {
  		$("#user-reconnect").hide();
		$.ajax({
			url:"' . Url::to("re-connect?id=".$model->id) . '",
			method: "POST",
			data:{user_id},
			beforeSend:function(){
				$(".loader").show();
				 $(".overlay").addClass("show");
			},
			success:function(response){
				if(response.code == "success"){
					$(this).attr("disabled","disabled");
					$("#modal").modal("toggle");
					$(".c-loader").hide();
					 $(".overlay").removeClass("show");
					alertify.set("notifier","position", "top-right");
					alertify.success("' . Yii::t('app', '{user_fullname} added to reconnection list', ['user_fullname' => $model->fullname,]) . '");
			        $.pjax.reload({
			            container: "#pjax-user-info",
			            timeout: 5000,
			        }).done(function(){
						$(".loader").hide();
						 $(".overlay").removeClass("show");
			        });
				}
			}
		});		   
  	} 
});
');
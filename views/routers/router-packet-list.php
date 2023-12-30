<?php 
use yii\helpers\Url;

 ?>

<ul class="list-group list-group-icons-meta" style="max-height: 350px; overflow-y: auto;">
	<?php foreach ($allPackets as $key => $packet): ?>
		<?php  $isChecked = ($packet['status'] == '1') ? "checked" : ""; ?>
		<li class="list-group-item list-group-item-action ">
	        <div class="media">
	            <div class="media-body router-packet-body">
	                <h6 class="tx-inverse"><?=$key+1 ?>. <?=$packet['packet_name'] ?></h6>
					<div style=" display: inline-block;">   
					<label style="display:block;" ><?=Yii::t('app','Add/Remove') ?></label>                               
						<input <?=$isChecked ?> name="input_cj_bank_status" class="router_packet_status" data-packet_id="<?=$packet['id'] ?>" type="checkbox" hidden="hidden" id="packets_check_<?=$packet['id'] ?>">
						<label class="c-switch" for="packets_check_<?=$packet['id'] ?>"></label>
					</div>
	            </div>
	        </div>
	    </li>
	<?php endforeach ?>
</ul>


<style type="text/css">
	.router-packet-body{display: flex;justify-content: space-between;}
	.router-packet-body .btn{padding: 5px;} 
	.tx-inverse{line-height: 45px;}
</style>

<?php 

$this->registerJs("
	$(document).on(\"click\",\".router_packet_status\",function(){
	    var packet_id = $(this).attr(\"data-packet_id\");
	    if($(this).is(\":checked\")){
	    $(this).prop(\"checked\",true);  
	        var checked = 1;
	    }else{
	        var checked = 0;
	        $(this).prop(\"checked\",false);  
	    }
	    $.ajax({
	        url:'".Url::toRoute('routers/router-change-packet-status')."',
	        type:\"post\",
	        data:{checked:checked,packet_id:packet_id},
	        success:function(response){
				alertify.set('notifier','position', 'top-right');
	        	if(checked == 1 && response.status == 'success'){
					alertify.success(response.message);
	        	}else{
					alertify.error(response.message);
	        	}
	        }
	    });
		e.preventDefault();
		return false;
	})
");

 ?>


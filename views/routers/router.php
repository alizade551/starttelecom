<?php 
// echo "<pre>";
// print_r($model_inet);
// echo "</pre>";
use yii\helpers\Url;
$this->title = Yii::t('app','{router} router restore  data',['router'=>$model['name'] ]);
 ?>
<div class="col-lg-8">
    <nav class="breadcrumb-one" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item parent"><a data-menu_id="adminstration" href="javascript:void(0);"><?=Yii::t("app","Adminstration") ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=Yii::t("app","Routers") ?></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$this->title  ?></li>
        </ol>
    </nav>
    <div class="card component-card_1">
        <div class="card-body">
        	<div class="router-card-inf-container">
            	<div class="router-inf">
            		<h5 class="card-title"><?=$model['name'] ?> - <span><?=$model['vendor_name'] ?></span></h5>
							<h6><?=Yii::t("app","Active users") ?> <span class="badge badge-success"> <?=$active_user_packet_count ?></span></h6>
							<h6><?=Yii::t("app","Deactive users") ?> <span class="badge badge-danger"> <?=$deactive_user_packet_count ?></span></h6>
							<h6><?=Yii::t("app","VIP users") ?> <span class="badge badge-primary"> <?=$vip_user_packet_count ?></span></h6>
							<h6><?=Yii::t("app","Pending users") ?> <span class="badge badge-warning"> <?=$pending_user_packet_count ?></span></h6>
							<h6><?=Yii::t("app","Archive users") ?> <span class="badge badge-dark"> <?=$archive_user_packet_count ?></span></h6>
            		<p><?=Yii::t('app','All active,deactive and vip users will restored.Note Archive users don\'t restored!!!') ?></p>
            	</div>
        		 <div class="icon-svg">
                	<img style="width: 128px;height: 128px;" src="/img/router.png">
            	</div>
        	</div>
			<button class="btn btn-primary restore" data-router_id="<?=$model['id'] ?>">
				<span class="spinner-border  mr-2 align-self-center "></span> <span class="r-b-t"><?=Yii::t("app","Restore data") ?></span>
			</button>
        </div>
    </div>
    	
    </div>


<style type="text/css">
.router-card-inf-container{
    display: flex;
    justify-content: space-between;
}
.spinner-border {
    width: 1rem;
    height: 1rem;
    display: none;

}
.spinner-border.show{
	display: inline-block;
}

.router-inf .badge{
	margin-left: 10px;
}
</style>




<?php 
$this->registerJs("

$(document).on('click','.restore',function(e){
	const that = $(this);
	const router_id = $(this).data('router_id');
	console.log('salam')

    $.ajax({
        url:'".Url::to('/routers/router?id=')."'+router_id,
        method: 'POST',
        beforeSend:function(){
        	that.find('.spinner-border').addClass('show');
        	that.find('.r-b-t').text('".Yii::t('app','Restoring data...')."');
        	that.prop('disabled', true);

        	
        },
        success:function(res){
        	if(res.status=='success'){
	        	that.find('.spinner-border').removeClass('show');
	        	that.find('.r-b-t').text('".Yii::t('app','Restore data')."');
                alertify.set('notifier','position', 'top-right');
                alertify.success(res.message);
        		that.prop('disabled', false);


	        }

 
  
        }
    });
	$('.restore').unbind('click').bind('click', function () { });  


})




");


 ?>
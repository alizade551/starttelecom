<?php 
use webvimark\modules\UserManagement\models\User;

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

 ?>
 <div style="max-width:600px">
 	<h4 style="margin: 20px 0;"><?=Yii::t('app','Device {device} - {ponPort} pon port {boxName} box setting',
 		[
 		'device'=>$egonBoxPortsModel->egonBox->device->name,
 		'ponPort'=>$egonBoxPortsModel->egonBox->pon_port_number,
 		'boxName'=>$egonBoxPortsModel->egonBox->box_name,
 		]
 		) ?></h4>

	<div class="col-lg-12 animatedParent animateOnce z-index-50">
	    <div class="panel panel-default animated fadeInUp">
	        <div class="panel-body">
		        <div style="max-height: 400px; overflow: auto;">
		            <div class="table-responsive">
		                <table class="table">
		                    <thead> 
		                        <tr> 
		                            <th><?=Yii::t("app",'Port') ?></th> 
		                            <th><?=Yii::t("app",'Inet login') ?></th> 
		                            <th><?=Yii::t("app",'Status') ?></th> 
		                            <?php if ( User::canRoute(['/devices/tag-inet-login-to-box-port']) ): ?>
		                            	<th><?=Yii::t("app",'Update') ?></th> 
		                            <?php endif ?>
		                            <?php if ( User::canRoute(['/devices/box-port-clear']) ): ?>
		                            	<th><?=Yii::t("app",'Clear') ?></th> 
		                            <?php endif ?>
		                        </tr> 
		                    </thead> 
		                    <tbody> 
		                    	<?php foreach ($model as $portKey => $port): ?>
		                            <tr> 
		                                <td><?=$port['port_number'] ?></td> 
		                                <td class="uspi">
		                                	<?php if ($port['inet_login'] != ""): ?>
		                                		<a href="/users/view?id=<?=$port['port_user_id'] ?>" target="_blank"><?=$port['inet_login'] ?></a>
		                                	<?php else: ?>
		                                		<?=Yii::t('app','Login not defined') ?>
		                                	<?php endif ?>
		                                	
		                            	</td> 
		                                <td class="port-status">
		                                	
		                                	<?php if ($port['status'] == "0"): ?>
		                                		<span class="badge badge-success">
		                                		 <?=\app\models\EgonBoxPorts::boxPortStatus()[$port['status']] ?> 
		                                		</span>


		                                	<?php elseif($port['status'] == "1"): ?>
		                                		<span class="badge badge-warning"> 
		                                			<?=\app\models\EgonBoxPorts::boxPortStatus()[$port['status']] ?> 
		                                		</span>

		                                	<?php elseif($port['status'] == "2"): ?>
												<span class="badge badge-danger">
												 <?=\app\models\EgonBoxPorts::boxPortStatus()[$port['status']] ?> 
												</span>
		                                	<?php endif ?>

		                                </td> 
		                                <?php if ( User::canRoute(['/devices/tag-inet-login-to-box-port']) ): ?>
			                                <td class="change-packet">
			                                    <a data-fancybox="" data-type="ajax" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/devices/tag-inet-login-to-box-port?id=<?=$port['id'] ?>" href="javascript:;">
			                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
			                                    </a>
			                                </td>
		                                <?php endif ?>

		                                <?php if ( User::canRoute(['/devices/box-port-clear']) ): ?>
				                            <td class="clear-cordinate">
				                                <a href="javascript:void(0)" data-href="<?=$langUrl ?>/devices/box-port-clear?id=<?=$port['id'] ?>" class="alertify-confirm">
				                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg>
				                                </a>
				                            </td>
		                                <?php endif ?>
		                            </tr> 
		                    	<?php endforeach ?>
		                    </tbody> 
		                </table>
		            </div>
		        </div>
	        </div>
	    </div>
	</div>
 	
 </div>


 <?php 
$this->registerJs('
$(document).on("click",".alertify-confirm",function(){
  var that = $(this);
  var message  = "'.Yii::t("app","Are you sure want to clear ?").'";
      alertify.confirm( message, function (e) {
        if (e) {
           $.ajax({
               url:that.attr("data-href"),
               type:"post",
               success:function(response){
                    if(response.status == "success"){
                        that.closest("tr").find(".uspi").text("'.Yii::t("app","Login not defined").'");
                        that.closest("tr").find(".port-status").find("span").removeClass("badge-warning");
                        that.closest("tr").find(".port-status").find("span").addClass("badge-success");
                        alertify.set("notifier","position", "top-right");
                        alertify.success(response.message);
                    }else{
                         alertify.set("notifier","position", "top-right");
                         alertify.error(response.message);
                    }
               }
           });
        } 
    }).set({title:"'.Yii::t("app","Clear a box port").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});;      
    return false;
});
');


 ?>
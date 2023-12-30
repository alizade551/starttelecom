<?php 
	$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

 ?>
<div style="max-width:600px">
	<h5 style="margin-bottom:15px"><?=Yii::t('app','Inet login : {login} - {device} device port information',['device'=>$checkSwitchPort->device->name,'login'=>$model->usersInet->login]) ?></h5>
	<div class="port-container">
		<?php $count = 0; ?>
		<?php foreach ($switchPorts as $key => $port): ?>
			<?php $count++ ?>
			<div class="port">
				<?php if ( $checkSwitchPort['id'] == $port['id'] ): ?>
				<a href="javascript::void(0)" class="tag_port_confrim" 
					data-href="<?=$langUrl ?>/users/remove-port" 
					data-inetLogin="<?=$model->usersInet->login ?>" 
					data-userPacketId="<?=$port['u_s_p_i'] ?>" 
					data-port_id="<?=$checkSwitchPort['id'] ?>" 
					data-device_type="<?=$checkSwitchPort->device->type ?>">
				<?php else: ?>
				<a href="javascript::void(0)">
				<?php endif ?>
					<?php if ($port['status'] == "0"): ?>
						<span class="port-free"></span>	
					<?php endif ?>

					<?php if ($port['status'] == "1"): ?>
						<?php if ($checkSwitchPort['id'] == $port['id'] ): ?>
							<span class="port-info"></span>	
						<?php else: ?>
							<span class="port-busy"></span>	
					    <?php endif ?> 
					<?php endif ?>

					<?php if ($port['status'] == "2"): ?>
						<span class="port-broken"></span>	
					<?php endif ?>
				</a>
					<div><?=$port['port_number'] ?></div>
			</div>
			<?php if ($count == 6): ?>
				<div class="line-break"></div>
				<?php $count=0; ?>
			<?php endif ?>
		<?php endforeach ?>
	</div>
</div>


<?php 
$this->registerJs('
  $(document).on("click",".tag_port_confrim",function(){
      var that = $(this);
      var message  = "'.Yii::t("app","Are you sure want to remove ?").'";
          alertify.confirm( message, function (e) {
            if (e) {
               $.ajax({
                   url:that.attr("data-href"),
                   type:"post",
                   data:{deviceType:that.attr("data-device_type"),userPacketId:that.attr("data-userPacketId"),portId:that.attr("data-port_id"),inetLogin:that.attr("data-inetLogin")},
                   success:function(response){
                        if(response.status == "success"){
							alertify.set("notifier","position", "top-right");
							alertify.success(response.message);
							$.fancybox.close();
                        }else{
							 alertify.set("notifier","position", "top-right");
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Remove a port").'"});  
        return false;
    });

');

 ?>
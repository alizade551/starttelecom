<div class="check-connection">
	<h4><?=Yii::t("app","{service} servis {packet} packet detail",['service'=>$model->service->service_name,'packet'=>$model->packet->packet_name]) ?></h4>
    <table class="table table-striped mb-0" >
        <tbody>
        	<?php if ( $model->service->service_alias == "internet" ): ?>
	  
	             <tr>
	                <td><?=Yii::t('app','Inet login') ?></td>
	                <td><?=$model->usersInet->login ?></td>
	            </tr>
	            	<?php if ( $model->usersInet->static_ip != "" ): ?>
			             <tr>
			                <td><?=Yii::t('app','Static ip') ?></td>
			                <td><?=\app\models\IpAdresses::find()->where(['id'=>$model->usersInet->static_ip])->asArray()->one()['public_ip'] ?></td>
			            </tr>
	            	<?php endif ?>

	             <tr>
	                <td><?=Yii::t('app','Inet password') ?></td>
	                <td><?=$model->usersInet->password ?></td>
	            </tr>
	             <tr>
	                <td><?=Yii::t('app','Router name') ?></td>
	                <td><?=$model->usersInet->router->name ?></td>
	            </tr>

        	<?php endif ?>


        	<?php if ($model->service->service_alias == "tv"): ?>
	             <tr>
	                <td><?=Yii::t('app','Tv login') ?></td>
	                <td><?=$model->usersTv->card_number ?></td>
	            </tr>
        	<?php endif ?>

        	<?php if ($model->service->service_alias == "wifi"): ?>
	             <tr>
	                <td><?=Yii::t('app','Wifi login') ?></td>
	                <td><?=$model->usersWifi->login ?></td>
	            </tr>
	             <tr>
	                <td><?=Yii::t('app','Wifi password') ?></td>
	                <td><?=$model->usersWifi->password ?></td>
	            </tr>
        	<?php endif ?>

        	<?php if ($model->service->service_alias == "voip"): ?>
	             <tr>
	                <td><?=Yii::t('app','Phone number') ?></td>
	                <td><?=$model->usersVoip->phone_number ?></td>
	            </tr>
        	<?php endif ?>

        </tbody>
    </table>	

</div>
 <style type="text/css">

 .check-connection{max-width: 250px;max-width: 600px}
</style>     

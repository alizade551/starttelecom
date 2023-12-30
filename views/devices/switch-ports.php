<?php 
use webvimark\modules\UserManagement\models\User;

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

 ?>
<div class="col-lg-12 animatedParent animateOnce z-index-50">
    <div class="panel panel-default animated fadeInUp">
        <div class="panel-body">
	        <div style="max-height: 400px; overflow: auto;">
	            <div class="table-responsive">
	                <table class="table">
	                    <thead> 
	                        <tr> 
	                            <th><?=Yii::t("app",'Port number') ?></th> 
	                            <th><?=Yii::t("app",'Inet login') ?></th> 
	                            <th><?=Yii::t("app",'Status') ?></th> 
	                            <?php if ( User::canRoute(['/devices/use-port']) ): ?>
	                            	<th><?=Yii::t("app",'Update') ?></th> 
	                            <?php endif ?>
	                        </tr> 
	                    </thead> 
	                    <tbody> 
	                    	<?php foreach ($model as $portKey => $port): ?>
	                            <tr> 
	                                <td><?=$port['port_number'] ?></td> 
	                                <td>
	                                	<?php if ($port['inet_login'] != ""): ?>
	                                		<a href="/users/view?id=<?=$port['port_user_id'] ?>" target="_blank"><?=$port['inet_login'] ?></a>
	                                	<?php else: ?>
	                                		<a href="javascript::void(0)"><?=Yii::t('app','Login not defined') ?></a>
	                                	<?php endif ?>
	                                </td> 
	                                <td>
	                                	
	                                	<?php if ($port['status'] == "0"): ?>
	                                		<span class="badge badge-success">
	                                		 <?=\app\models\SwitchPorts::switchPortStatus()[$port['status']] ?> 
	                                		</span>


	                                	<?php elseif($port['status'] == "1"): ?>
	                                		<span class="badge badge-warning"> 
	                                			<?=\app\models\SwitchPorts::switchPortStatus()[$port['status']] ?> 
	                                		</span>

	                                	<?php elseif($port['status'] == "2"): ?>
											<span class="badge badge-danger">
											 <?=\app\models\SwitchPorts::switchPortStatus()[$port['status']] ?> 
											</span>
	                                	<?php endif ?>

	                                </td> 
	                                <?php if ( User::canRoute(['/devices/use-port']) ): ?>
	                                <td class="change-packet">
	                                    <a data-fancybox="" data-type="ajax" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/devices/use-port?id=<?=$port['id'] ?>" href="javascript:;">
	                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
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
<?php 
use webvimark\modules\UserManagement\models\User;


$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
 ?>
<div style="max-height: 600px; overflow: auto;">
    <div class="table-responsive">
    	<?php if ($checkLocation != null ): ?>
        <table class="table">
            <thead> 
                <tr> 
                    <th><?=Yii::t("app",'Pon port') ?></th> 
                    <th><?=Yii::t("app",'Status') ?></th> 
                    <?php if ( User::canRoute(['/devices/split-pon-port']) ): ?>
                        <th><?=Yii::t("app",'Setting') ?></th> 
                    <?php endif ?>
                </tr> 
            </thead> 
            <tbody> 
            	<?php foreach ($model as $portKey => $port): ?>
                    <tr> 
                        <td>
                        	<?=$port['pon_port_number'] ?>
                        </td> 
                        <td>
                        	<?php if ($port['status'] == "0"): ?>
                        		<span class="badge badge-success">
                        		 <?=\app\models\EgponPonPort::ponPortStatus()[$port['status']] ?> 
                        		</span>
                        	<?php elseif($port['status'] == "1"): ?>
                        		<span class="badge badge-warning"> 
                        			<?=\app\models\EgponPonPort::ponPortStatus()[$port['status']] ?> 
                        		</span>
                        	<?php elseif($port['status'] == "2"): ?>
								<span class="badge badge-danger">
								 <?=\app\models\EgponPonPort::ponPortStatus()[$port['status']] ?> 
								</span>
                        	<?php endif ?>
                        </td> 
                        <?php if ( User::canRoute(['/devices/split-pon-port']) ): ?>
                            <td class="change-packet">
                                <a data-fancybox="" data-type="ajax" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/devices/split-pon-port?id=<?=$port['id'] ?>" href="javascript:;">
     								<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                                </a>
                            </td>
                        <?php endif ?>
                    </tr> 
            	<?php endforeach ?>
            </tbody> 
        </table>
    	<?php else: ?>
    		<h5 style="margin-top:20px"><?=Yii::t('app','Please add district to {device} device before splitting pon-ports',['device'=>$deviceModel['name']]) ?></h5>
    		<style type="text/css">
				.modal-body {
					height: 120px;
				}
    		</style>
    	<?php endif ?>
    </div>
</div>
<div class="check-connection">
<?php if ($data != null): ?>

	<h3><?=Yii::t("app","{router} connection information",['router'=>$routerModel['name']]) ?></h3>
    <table class="table table-striped mb-0" >
        <tbody>
             <tr >
                <td><?=Yii::t('app','Tx - Uptime') ?></td>
                <td><?=\app\components\Utils::formatBytes($data['tx-byte'],'G',2) ?> GB </td>
            </tr>
             <tr >
                <td><?=Yii::t('app','Rx - Uptime') ?></td>
                <td><?=\app\components\Utils::formatBytes($data['rx-byte'],'G',2)  ?> GB </td>
            </tr>
             <tr >
                <td><?=Yii::t('app','Mac address') ?></td>
                <td><?=$data['mac-address']  ?></td>
            </tr>
             <tr>
                <td><?=Yii::t('app','type') ?></td>
                <td><?=$data['type'] ?></td>
            </tr>
             <tr>
                <td><?=Yii::t('app','mtu') ?></td>
                <td><?=$data['mtu'] ?></td>
            </tr>
             <tr>
                <td><?=Yii::t('app','actual-mtu') ?></td>
                <td><?=$data['actual-mtu'] ?></td>
            </tr>
             <tr>
                <td><?=Yii::t('app','l2mtu') ?></td>
                <td><?=( isset( $data['l2mtu'] ) ) ? $data['l2mtu'] : Yii::t("app","Data not set") ?> </td>
            </tr>
             <tr>
                <td><?=Yii::t('app','max-l2mtu') ?></td>
                <td><?=( isset( $data['max-l2mtu'] ) ) ? $data['max-l2mtu'] : Yii::t("app","Data not set") ?> </td>
            </tr>
          

             <tr>
                <td><?=Yii::t('app','last-link-up-time') ?></td>
                <td><?=( isset( $data['last-link-up-time'] ) ) ? $data['last-link-up-time'] : Yii::t("app","Data not set") ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','link-downs') ?></td>
                <td><?=$data['link-downs'] ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','rx-packet') ?></td>
                <td><?=\app\components\Utils::formatBytes($data['rx-packet'],'M',2) ?> MB</td>
            </tr>
             <tr>
                <td><?=Yii::t('app','tx-packet') ?></td>
                <td><?=\app\components\Utils::formatBytes($data['tx-packet'],'M',2) ?> MB</td>
            </tr>
             <tr>
                <td><?=Yii::t('app','rx-drop') ?></td>
                 <td><?= (isset ($data['rx-drop']) ) ? $data['rx-drop'] : Yii::t('app','Data not found') ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','tx-queue-drop') ?></td>
                <td><?= (isset ($data['tx-drop']) ) ? $data['tx-drop'] : Yii::t('app','Data not found') ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','rx-error') ?></td>
                  <td><?= (isset ( $data['tx-error'] ) ) ? $data['tx-error'] : Yii::t('app','Data not found') ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','tx-error') ?></td>
                <td><?= (isset ( $data['tx-error'] ) ) ? $data['tx-error'] : Yii::t('app','Data not found') ?></td>
            </tr>
             <tr>
                <td><?=Yii::t('app','fp-rx-byte') ?></td>
                <td><?=\app\components\Utils::formatBytes($data['fp-rx-byte'],'G',2) ?> GB </td>
            </tr>
             <tr>
                <td><?=Yii::t('app','fp-tx-byte') ?></td>
                <td><?=\app\components\Utils::formatBytes($data['fp-tx-byte'],'G',2) ?> GB </td>
            </tr>
             <tr>
                <td><?=Yii::t('app','fp-rx-packet') ?></td>
                <td><?=\app\components\Utils::formatBytes($data['fp-rx-packet'],'G',2) ?> GB </td>
            </tr>  

             <tr>
                <td><?=Yii::t('app','fp-tx-packet') ?></td>
                <td><?=\app\components\Utils::formatBytes($data['fp-tx-packet'],'G',2) ?> GB </td>
            </tr>  
        </tbody>
    </table>	
<?php else: ?>
   <div style="text-align:center;padding: 0 10px;">
         <h5 style="margin-bottom:10px"><?=Yii::t("app","Router not connected network!") ?></h5>
         <svg viewBox="0 0 24 24" width="64" height="64" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="1" y1="1" x2="23" y2="23"></line><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path><path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"></path><path d="M10.71 5.05A16 16 0 0 1 22.58 9"></path><path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
   </div>
<?php endif ?>
</div>
 <style type="text/css">

 .check-connection{max-width: 250px;max-width: 500px}
</style>     
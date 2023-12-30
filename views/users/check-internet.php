<div class="check-connection">
<?php if ($data != null): ?>

   <h3 style="margin-bottom:15px"><?=Yii::t("app","Router connection information") ?></h3>
    <table class="table table-striped mb-0" >
        <tbody>
             <tr>
                <td><?=Yii::t('app','Vendor name') ?></td>
                <td><?=\app\components\MikrotikQueries::getRouterNameWithMacAddress($data['mac-address'])  ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','Internal ip address') ?></td>
                <td><?=htmlspecialchars($data['address']) ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','Mac address') ?></td>
                <td><?=$data['mac-address'] ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','Address list') ?></td>
                <td><?=$data['address-list'] ?></td>
            </tr>            

             <tr>
                <td><?=Yii::t('app','Download / Upload') ?></td>
                <td><?=$data['rate-limit'] ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','Client id') ?></td>
                <td><?=$data['client-id'] ?></td>
            </tr>

             <tr>
                <td><?=Yii::t('app','Host name') ?></td>
                <td><?=$data['host-name'] ?></td>
            </tr>
            
             <tr>
                <td><?=Yii::t('app','Status') ?></td>
                <td><?=$data['status'] ?></td>
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
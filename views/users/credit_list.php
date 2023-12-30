<?php if (count($credit_model) > 0): ?>
<div class="credit-h-t ">
    <h4 style="text-align: center;margin-bottom: 15px" class="mt-0 header-title"><?=Yii::t("app","Credit history timeline") ?> </h4>
    <div class="table-responsive scrollbar-custom">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?=Yii::t('app','Amount') ?></th>
                    <th><?=Yii::t('app','Created at') ?></th>
                </tr>
            </thead>
            <tbody>
            	<?php $c=0; ?>
            	<?php foreach ($credit_model as $key => $credit_h): ?>
            	<?php $c++; ?>
                <tr>
                    <td><?=$c; ?></td>
                    <td><?=$credit_h['paid'] ?></td>
                    <td><?=date('d-M-Y H:i:s', $credit_h['paid_at']); ?></td>
                </tr>
            	<?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="credit-h-t">
    <h4 style="text-align: center;margin: 0" class="mt-0 header-title"> <?=Yii::t("app","Not found  credit history") ?></h4>
</div>
<?php endif ?>

<style type="text/css">
.credit-h-t .container{
    max-width: 400px;
    height: 250px;
    overflow-y: auto;
}
</style>
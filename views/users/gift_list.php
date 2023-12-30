<?php if (count($gift_model) > 0): ?>

<div class="credit-h-t">
    <div class="container">
        <h4 style="text-align: center;margin-bottom: 15px" class="mt-0 header-title"><?=Yii::t("app","Gift history timeline") ?></h4>
        <div class="table-responsive scrollbar-custom">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?=Yii::t("app","Created at") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $c=0; ?>
                    <?php foreach ($gift_model as $key => $gift_h): ?>
                    <?php $c++; ?>
                    <tr>
                        <td ><?=$c; ?></td>
                       
                        <td><?=date('d-M-Y H:i:s', $gift_h['created_at']); ?></td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php else: ?>
<div class="credit-h-t">
    <h4 style="text-align: center;margin: 0" class="mt-0 header-title"> Not found  gift history</h4>
</div>
<?php endif ?>

<style type="text/css">
.credit-h-t .container{
max-width: 400px;
height: 250px;
overflow-y: auto;
}
</style>
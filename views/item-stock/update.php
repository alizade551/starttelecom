<?php

use yii\helpers\Html;
?>
<div class="item-stock-update">
    <?= $this->render('_form', [
        'model' => $model,
        'warehouses' =>$warehouses
    ]) ?>
</div>

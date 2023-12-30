<?php 
use yii\bootstrap4\LinkPager;
use yii\bootstrap4\Modal;
use yii\widgets\Pjax;


$this->title = Yii::t('app','Fail processes');
 ?>
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item parent"><a  href="/"><?=Yii::t('app','Dashboard') ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?=$this->title ?></li>
    </ol>
</nav>
<?php Pjax::begin(['id'=>'empty-contract-pjax', 'timeout' => 15000,"scrollTo"=>true]) ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
    <div class="widget widget-table-three">
        <div class="widget-content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><div class="th-content th-heading">#</div></th>
                            <th><div class="th-content th-heading"><?=Yii::t('app','Process name') ?></div></th>
                            <th><div class="th-content th-heading"><?=Yii::t('app','Params') ?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php $i = $pages->offset; ?>
                    	<?php foreach ($model as $processKey => $process): ?>
                    		<?php $i ++ ?>
	                        <tr>
	                            <td><div class="td-content"><span class="pricing"><?= $i ?></span></div></td>
	                            <td><div class="td-content"><span class="discount-pricing"><a data-pjax="0" href="javascript:void(0)"><?= $process['action'] ?></a></span></div></td>
	                            <td><div class="td-content"><span class="discount-pricing">
                                <?php 

                                    $params = \array_filter( unserialize( $process['params'] ), static function ($element) {
                                        return $element !== "router_password";
                                    });
                                 ?>
	                            	<?=\app\components\Utils::failProcessText($params) ?>
	                            </span></div></td>
	                        </tr>


                    	<?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
     <?=LinkPager::widget(['pagination' => $pages]);?>
    </div>
</div>
<?php Pjax::end() ?>


<style type="text/css">
.table td, .table th {
    padding: 0px;
    vertical-align: middle;
    border-top: 1px solid #1c1c1c;
}
#empty-contract-pjax{width: 100%;}
</style>

<?php 
Modal::begin([
    'title' =>Yii::t("app","Fail processes"),
    'id' => 'modal',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],
    'size' => 'modal-md',
]);
echo "<div id='modalContent'></div>";
Modal::end();
?>
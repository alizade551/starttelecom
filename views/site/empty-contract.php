<?php 
use yii\bootstrap4\LinkPager;
use yii\bootstrap4\Modal;
use yii\widgets\Pjax;


$this->title = Yii::t('app','Empty contracts');
 ?>


<?php Pjax::begin(['id'=>'empty-contract-pjax', 'timeout' => 15000,"scrollTo"=>true]) ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item parent"><a  href="/"><?=Yii::t('app','Dashboard') ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?=$this->title ?></li>
    </ol>
</nav>
    <div class="widget widget-table-three">
        <div class="widget-content">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th><div class="th-content th-heading">#</div></th>
                            <th><div class="th-content th-heading"><?=Yii::t('app','User fullname') ?></div></th>
                            <th><div class="th-content th-heading"><?=Yii::t('app','Add a contract') ?></div></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php $i = $pages->offset; ?>
                    	<?php foreach ($model as $userKey => $user): ?>
                    		<?php $i ++ ?>
	                        <tr>
	                            <td><div class="td-content"><span class="pricing"><?= $i ?></span></div></td>
	                            <td><div class="td-content"><span class="discount-pricing"><?= $user['fullname'] ?></span></div></td>
	                            <td><div class="td-content"><span class="discount-pricing"><a data-pjax="0" class="nav-link modal-d " href="/users/contract?id=<?=$user['id'] ?>"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg></a></span></div></td>
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
}
#empty-contract-pjax{width: 100%;}

.th-content.th-heading{
    height: 40px;
    line-height: 40px;
}
</style>

<?php 
Modal::begin([
    'title' =>Yii::t("app","Add a contract"),
    'id' => 'modal',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],
    'size' => 'modal-md',
]);
echo "<div id='modalContent'></div>";
Modal::end();
?>
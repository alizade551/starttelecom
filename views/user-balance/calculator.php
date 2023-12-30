
<?php 
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\bootstrap4\LinkPager;
use yii\widgets\Pjax;

$this->registerJsFile('/js/jquery.dataTables.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 

$this->registerJsFile('/js/dataTables.bootstrap4.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 

$this->registerJsFile('/js/dataTables.buttons.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 

$this->registerJsFile('/js/buttons.bootstrap4.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 

$this->registerJsFile('/js/jszip.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 

$this->registerJsFile('/js/pdfmake.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 

$this->registerJsFile('/js/vfs_fonts.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 

$this->registerJsFile('/js/buttons.html5.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 

$this->registerJsFile('/js/buttons.print.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 

$this->registerJsFile('/js/buttons.colVis.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]); 
 



 $total = 0;
 $api_total = 0;
 $office_total = 0;
 $this->title = Yii::t("app","Payment interval - {start_end_date}",['start_end_date'=>Yii::$app->request->get('start_end_date')]);
 ?>


<link rel="stylesheet" href="/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
<link rel="stylesheet" type="text/css" href="/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/css/buttons.bootstrap4.min.css">
<?php Pjax::begin(['id'=>'payment-calc-container', 'timeout' => 15000,"scrollTo"=>true]) ?>
	
<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=Yii::t("app","Payment calculator") ?> </h4> </div>
        </div>
    </div>
</div>
<div class="card custom-card" style="padding:15px">
	<div class="row">
		<div class="col-sm-3">
			<?= Html::beginForm(['user-balance/statistic'], 'get' ,[ 'id' => 'search-form','data-pjax' => true]) ?>
			<?php 

				echo '<label class="control-label">'.Yii::t('app','Date range').'</label>';
				echo '<div class="drp-container">';
				echo DateRangePicker::widget([
				    'name'=>'start_end_date',
				    'value'=>Yii::$app->request->get('start_end_date'),
				    'presetDropdown'=>true,
				    'convertFormat'=>true,
				    'includeMonthsFilter'=>true,
				    'pluginOptions' => ['locale' => ['format' => 'Y/m/d']],
				    'options' => ['placeholder' => Yii::t('app','Select')],
			              'pluginEvents' =>[
			                        "apply.daterangepicker" => "function(e) { $('.help-block').hide(); }",
			                    ]  
				]);
				echo '<div class="c-help-block">'.Yii::t('app','Range can\'t be blank').'</div>';
				echo '</div></br>';




			 ?>
			<div class="form-group field-userbalance-payment_method">
				<label class="control-label" for="userbalance-payment_method"><?=Yii::t("app","Payment method") ?></label>
					<select id="userbalance-payment_method" class="form-control" name="payment_method">
					<option value=""><?=Yii::t('app','All') ?></option>
					<option value="0"><?=Yii::t('app','Internal') ?></option>
					<option value="1"><?=Yii::t('app','External') ?></option>
				</select>
			</div>

				<?php if (count($data) > 0): ?>

					<?php foreach ($data as $key => $payment): ?>
						<?php $total += $payment['balance_in'] ?>
						<?php
						 if ($payment['payment_method'] == 1) {
						 	$api_total += $payment['balance_in'];
						 }elseif ($payment['payment_method'] == 0) {
						 	$office_total += $payment['balance_in'];
						 }
						 ?>
					<?php endforeach ?>
				<?php endif ?>
			<?= Html::submitButton(Yii::t('app','Calculate'), ['class' => 'btn btn-primary']) ?>
			<?php 
			 if (isset(  Yii::$app->request->get("UserBalance")['payment_method'])) {
			 	if (Yii::$app->request->get("UserBalance")['payment_method'] == '1') {
			 		echo '<div style="margin-top: 20px">'.Yii::t('app','External total amount').' : '.$api_total.'  AZN</div>';
			 	}elseif(Yii::$app->request->get("UserBalance")['payment_method'] == '0'){
			 		echo '<div style="margin-top: 20px">'.Yii::t('app','İnternal total amount').' : '.$office_total.' AZN</div>';
			 	}else{
			 		echo '<div style="margin-top: 20px">'.Yii::t('app','External total amount').' : '.$api_total.' AZN</div><div style="margin-top: 10px">'.Yii::t('app','İnternal total amount').' : '.$office_total.'  AZN</div><div style="margin-top: 10px">'.Yii::t('app','Total amount').' : '.$total.'  AZN</div>';
			 	}

			 }else{
			 	echo '<div style="margin-top: 20px">'.Yii::t('app','External total amount').' : '.$api_total.' AZN</div><div style="margin-top: 10px">'.Yii::t('app','İnternal total amount').' : '.$office_total.'  AZN</div><div style="margin-top: 10px">'.Yii::t('app','Total amount').' : '.$total.'  AZN</div>';
			 }
			?>
			<?= Html::endForm() ?>
			<br/>
		</div>
		<div class="col-sm-9">
			<table id="example-table" class="table table-striped table-bordered" style="width:100%">
			    <thead>
			        <tr>
			            <th>#</th>
			            <th><?=Yii::t("app","ID") ?> </th>
			            <th><?=Yii::t("app","Customer") ?></th>
			            <th><?=Yii::t("app","Amount") ?></th>
			            <th><?=Yii::t("app","Payment method") ?></th>
			            <th><?=Yii::t("app","Recipet") ?></th>
			            <th><?=Yii::t("app","Transaction") ?></th>
			            <th><?=Yii::t("app","Paid at") ?></th>
			        </tr>
			    </thead>
			    <tbody>
			    	<?php if (count($data) > 0): ?>
			    		<?php foreach ($data as $key => $payment): ?>
			    			<?php $key++  ?>
				             <tr>
				             	<td><?=$key  ?></td>
				                <td><?=$payment['id'] ?></td>
				                <td><?=$payment['p_fullname'] ?></td>
				                <td><?=$payment['balance_in'] ?> AZN</td>
				 

				                <td>
				                	<?php 
				                		if ($payment['payment_method'] == 0) {
				                			$payment_method  = Yii::t("app","Internal");
				                		}elseif ($payment['payment_method'] == 1) {
				                			$payment_method  = Yii::t("app","External");
				                		}else{
				                			$payment_method = Yii::t("app","Unkown payment method");;
				                		}
				                		echo $payment_method;
				                	?>
				                 	
				                 </td>
												 <td><?=$payment['receipt'] ?></td>
				                 <td>
				                 	<?php if ($payment['payment_method'] == 0): ?>
				                 		<?php echo "Office"; ?>
				                 	<?php else: ?>
				                 		<?=$payment['transaction'] ?>
				                 	<?php endif ?>
				                 </td>
				                <td><?=date("d-m-Y H:i:s", $payment['created_at']) ?></td>
				           
				      
				            </tr>
			    		<?php endforeach ?>
			    	<?php endif ?>

			    </tbody>
			    <tfoot>
			        <tr>
			        	  <th>#</th>
			            <th><?=Yii::t("app","ID") ?></th>
			            <th><?=Yii::t("app","Customer") ?></th>
			            <th><?=Yii::t("app","Amount") ?></th>
			            <th><?=Yii::t("app","Payment method") ?></th>
			            <th><?=Yii::t("app","Recipet") ?></th>
			            <th><?=Yii::t("app","Transaction") ?></th>
			            <th><?=Yii::t("app","Paid at") ?></th>
			        </tr>
			    </tfoot>
			</table>
		</div>
	</div>
</div>

<?php
$this->registerJs('
    $(document).ready(function() {
        $("#example-table").DataTable({
			searching: true,
			info: false,
			buttons:false,
			responsive: true,
			dom: "Bfrtip",  
            "pagingType": "full_numbers",
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
        });
      

    });


var form = $("form#daily-balance");


  form.on(\'beforeSubmit\', function (e) {
      	if($(".range-value").val() != ""){
      		$(".c-help-block").hide();

      		return true
      	}else{
      		$(".c-help-block").show();
      	}
    
     return false;
});   
  ');
?>

<?php Pjax::end() ?>



<style type="text/css">
.c-help-block{display: none;color: red;}
#daily-balance{margin-top: 38px;}
#payment-calc-container{width: 100%;}
.table-bordered {
border: none !important;
}
.dt-buttons{margin-top: 28px}




</style>



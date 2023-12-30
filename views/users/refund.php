 <?php 
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = '';
?>

<div class="row">
	<div class="col-sm-4">
		<div class="col-sm-12 ">

				<div class="form-group field-userbalance-user_id required">
				<label class="control-label" for="userbalance-user_id"><?=Yii::t("app","User fullname") ?></label>
				<input type="text" id="userbalance-user_id" class="form-control" name="UserBalance[user_id]" value="<?=$model_user->fullname ?>" disabled="" aria-required="true">
				</div>

				<?php $form = ActiveForm::begin(['id'=>'refund-balance','options' => ['autocomplete' => 'off']]); ?>

			    <?=$form->field($model, 'balance_in')->hiddenInput(['value' => $model_user->balance])->label(false) ?>
			    <?=$form->field($model, 'user_id')->hiddenInput(['value' => $model_user->id])->label(false) ?>
			    <?=$form->field($model, 'payment_method')->hiddenInput(['value' => 0])->label(false) ?>
			    <?=$form->field($model, 'created_at')->hiddenInput(['value' => time()])->label(false) ?>
			    <?=$form->field($model, 'receipt_checkbox')->checkBox(); ?>
			    <?= Html::submitButton(Yii::t("app","Refund and print"),['class'=>'btn btn-primary btn-balance']) ?>
			  <?php ActiveForm::end(); ?>
	


		</div>

	</div>
	 <div class="col-sm-8">
	    <div class="col-sm-12">
	    <div id="invoiceholder">
		  <div id="invoice" class="effect2">
		    <div id="invoice-bot" >
		      <div class="invoice-info" >
		         <h2 class="invoice-head-title"><b>Tarix</b> : <?=date('m/d/Y H:i:s', time()) ?> </h2>
		         <h2 class="invoice-head-title"><b>Abonent</b> : <?=$model_user->fullname ?> </h2>
		         <h2 class="invoice-head-title"><b>Müqavilə nömrəsi</b> : <span style='margin-right:10px;'><?=$model_user->contract_number ?></span>
		          <h2 class="invoice-head-title"><b>Geri qaytarılan  məbləğ</b>: <span class="refund-balance"> <?=$model_user->balance ?> </span> AZN </h2>
		       </h2>
		         <h2 class="invoice-head-title"><b>Ünvan</b>: <?=$model_user->city->city_name.", ".$model_user->district->district_name.", ".$model_user->locations->name.", ".$model_user->room ?> </h2>
		      </div>
		      <div class="invoice-table">
		        <table>
		          <tr class="tabletitle">
		            <td >
		              <h2 style="text-align: center;">#</h2>
		            </td>
		            <td class="">
		              <h2>Xidmət,malın adı</h2>
		            </td>
		            <td class="Rate">
		              <h2>Qiymət</h2>
		            </td>
		          </tr>
		  <?php $total_price = 0; ?>
		  <?php $c = 0; ?>
		<?php foreach ($services_array as $key_s => $service): ?>
		  <?php foreach ($service as $key => $service_one): ?>
		    <?php $total_price += $service_one['packet_price'];  ?>
		    <?php $c++;  ?>
		        <tr >
		          <td class="tableitem">
		            <p class="itemtext" style="text-align: center;"><?=$c ?></p>
		          </td>
		          <td class="tableitem">
		            <p class="itemtext"><?=$service_one['packet_name']." (".ucwords($key_s)." )" ?></p>
		          </td>
		          <td class="tableitem">
		           <p class="itemtext"><?=$service_one['packet_price'] ?> AZN</p>
		          </td>
		       </tr>
		  <?php endforeach ?>
		<?php endforeach ?>
		        </table>
		      <div>
		         <h2 class="invoice-footer-title"><b>Əməliyyatçı</b>: <?=Yii::$app->user->fullname ?> </h2>
		         <h2 class="invoice-footer-title"><b>Geri ödenildi</b>: <?=$model_user->balance ?> AZN </h2>
		      </div>
		    </div>
		  </div>
		</div>
	  </div>
	</div>
	 </div>
</div>
<?php 
$this->registerJs('
    $("#userbalance-balance_in").on("change",function () {
      var balance_in_value = $(this).val();
      $(".refund-balance").text(balance_in_value);
    });
    var xhr;
    var xhr_active=false;
    var form = $("form#refund-balance");

    form.on("beforeSubmit", function (e) {

     var is_recipet =  $("#userbalance-receipt_checkbox").prop("checked")
    if( form.find("button").prop("disabled")){
    return false;
    }
    $("#refund-user-balance").hide()
       if(xhr_active) { xhr.abort(); }
        xhr_active=true;
     form.find("button").prop("disabled",true);
      xhr = $.ajax({
                url: "'.\yii\helpers\Url::to(["users/refund-balance?id="]).$id.'",
                type: "post",
                data: form.serialize(),
								beforeSend:function(){
										$(".loader").show();
										 $(".overlay").addClass("show");
								},
                success: function (response) {
                   if(response.status == "success"){
											$(".loader").hide();
											 $(".overlay").removeClass("show");
										  alertify.set("notifier","position", "top-right");
                      alertify.success(response.text);
                        $("#modal").modal("hide");
                        $.pjax.reload({container: "#pjax-user-info", timeout:5000}).done(function(){
                        	 $.pjax.reload({container: "#pjax-user-payment-history", timeout:5000}).done(function(){
															$(".loader").hide();
															 $(".overlay").removeClass("show");
                        	 	})
                        	})
                       
                        if(is_recipet == true){
                          document.title = "";
                          $.print("#invoice-bot"); 
                        }
                    }else{
                      xhr_active=false;
                      form.find("button").prop("disabled",false);
                    }
                }
           });
           return false;
      }); 
');
 ?>

<style type="text/css" media="print">
@media print { 
	body{
	margin-top: 30px;
	}
	table {
	margin-bottom: 10px
	}
	#invoice .invoice-head-title{font-size: 16px}
	.invoice-table   tr, .invoice-table   th,  .invoice-table td{
	 line-height: 10px;
	  height: 10px;
	  margin: 0;
	  padding: 0;
	  border:1px solid black;
	  color: black !important;
	    -webkit-print-color-adjust: exact; 
	}
	.invoice-table tr h2,.invoice-table th h2,.invoice-table td h2{  text-align: center;}
	#invoice-bot .invoice-info {margin-left: 0}
	#invoice-bot h2{
	font-size: 14px;margin: 0px;padding: 0px;
	  font-size: 13px;
	color: black;
	line-height: 15px;
	-webkit-print-color-adjust: exact; 
	}
	#invoice-bot  p{
	margin: 0;
	padding: 5px;
	 font-size: 14px;
	}
	.invoice-table{margin-top: 10px;margin-bottom: 10px}
}

</style>
<?php 
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Json;
use kartik\select2\Select2;
use yii\web\JsExpression;
use webvimark\modules\UserManagement\models\User;

use kartik\datetime\DateTimePicker;

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

 ?>
<div class="row">
	<div class="col-sm-12">

		<?php $form = ActiveForm::begin([
		 	'id'=>'add-form-usefull-count',
		    'enableAjaxValidation' => true,
		    'validationUrl' => $langUrl .'/users/add-item-to-user-validate',
		    'enableClientValidation' => false,
		    'options' => ['autocomplete' => 'off']
		]
		); ?>
		<div class="row">
	     <div class="col-sm-12">
					<?=$form->field($model, 'item_id')->widget(Select2::classname(), [
					'data'=>[''=>''],
					'maintainOrder' => true,
					'bsVersion' => '4.x',
					'options' => ['placeholder' =>  Yii::t('app','Item')],
					'pluginOptions' => [
						'initialize' => true,
					    'allowClear' => true,
					    'minimumInputLength' => 1,
					     'enableClientValidation' => true,
					    'language' => [
					        'errorLoading' => new JsExpression("function () { return 'Please wait'; }"),
					    ],
					    'ajax' => [
					        'url' => \yii\helpers\Url::to(['item-list']),
					        'dataType' => 'json',
					        'data' => new JsExpression('function(params) { return {q:params.term}; }')
					    ],
					    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
					    'templateResult' => new JsExpression('function(city) { return city.text; }'),
					    'templateSelection' => new JsExpression('function (city) { return city.text; }'),
					],

			          'pluginEvents'=>["change" => "function() { 
			              var that = $(this);

			                  $.pjax.reload({
			                  url:'".$langUrl.Url::to('/users/add-item-to-user')."?id=".Yii::$app->request->get("id")."&item_id='+that.val(),
			                  container: '#item-stock-pjax',
			                  timeout: 5000
			                  });

			           }",]


					])?>
					<?= $form->field($model, 'user_id')->hiddenInput(['value' => Yii::$app->request->get("id")])->label(false) ?>
	      </div>
	     <div class="col-sm-12">
	   			<?php  Pjax::begin(['id'=>'item-stock-pjax','enablePushState'=>true]);  ?>
						<?php 

						  $stocks = ArrayHelper::map(
						      \app\models\ItemStock::find()
						      ->where(['!=','quantity',0])
						      ->andWhere(['item_id'=>Yii::$app->request->get("item_id")])
						      ->all(),
						      'id',
						      'sku'
						  );

						echo $form->field($model, 'item_stock_id')->widget(Select2::classname(), [
						    'data' =>$stocks ,
						    'options' => ['placeholder' => Yii::t('app','Select a stock')],
						    'language' => 'en',
						    'pluginOptions' => [
						        'allowClear' => true
						    ],
						    'pluginEvents'=>[
						    	"change" => "function(e) {
						    		let stockId = $(this).val();
										$.ajax({
									      url: '".\yii\helpers\Url::to(["get-item-stock-price"])."',
									      type: 'post',
									      data: {id:stockId},
									      beforeSend:function(){
									          $('.loader').show();
									          $('.overlay').addClass('show');
									      },
									      success: function (response) {
									  				if( response.status == 'success' ){
									  					$('#stockPrice').val(response.price);
										          $('.loader').hide();
										          $('.overlay').removeClass('show');
									  				}
									      }
									 });
							     
							   }",
						 		]
						]);

						?>

	   			<?php if ( Yii::$app->request->get("item_id") != "" &&  Yii::$app->request->isPjax ): ?>
	   				<?php 
	   						$itemModel = \app\models\Items::find()->where(['id'=> Yii::$app->request->get("item_id")])->one();
	   				 ?>
	   					<?php if ( $itemModel != null && $itemModel->category->mac_address_validation == "1" ): ?>
	   				 		<?= $form->field($model, 'mac_address')->textInput()->label() ?>


							<div class="form-group field-itemusage-quantity required">
								<label for="itemusage-quantity"><?=Yii::t('app','Quantity') ?></label>
								<input disabled type="text" id="itemusage-quantity" class="form-control is-valid" aria-required="true" aria-invalid="false" value="1">
							</div>

		       	   			<?= $form->field($model, 'quantity')->hiddenInput(['value'=>1])->label(false) ?>
		
						<?php else: ?>
	   						<?= $form->field($model, 'mac_address')->hiddenInput()->label(false) ?>

		       	   			<?= $form->field($model, 'quantity')->textInput()->label() ?>
				       	   	<?php 
				       	   		$this->registerJs("
									$('#itemusage-quantity').on('change',function(){
										let quantity = $(this).val();
										let price = $('#stockPrice').val();
										let total =  parseFloat( quantity * parseFloat(price).toFixed(2) ).toFixed(2); 
										$('#total-price').text(total)
									});
				       	   		");

				       	   	 ?>
	   				
	   					<?php endif ?>

					<?php 
						foreach ($form->attributes as $attribute) {
							$attribute = Json::htmlEncode($attribute);
							$this->registerJs("jQuery('form#add-form-usefull-count').yiiActiveForm('add', $attribute); ");
						} 

					?>
	   			<?php endif ?>


          <?php Pjax::end(); ?>   
					<?= $form->field($model, 'user_id')->hiddenInput(['value' => Yii::$app->request->get("id")])->label(false) ?>
	      </div>


	 

				<div class="col-sm-6">
					<div class="form-group field-itemusage-quantity">
						<label class="control-label" for="itemusage-quantity"><?=Yii::t('app','Price') ?></label>
						<input class="form-control" type="text" id="stockPrice" disabled value="<?=Yii::t('app','Select item stock') ?>">
					</div>		       
				</div> 

		    <div class="col-sm-6">
		    	<?php $model->personals = $first_connection ?>
		 
					    <?=$form->field($model, 'personals')->widget(Select2::classname(), [
					        'maintainOrder' => true,
									'bsVersion' => '4.x',
					        'data'=>$personal_data,
					         'options' => ['placeholder' => Yii::t('app','Personal fullname'), 'multiple' => true],
						    'pluginOptions' => [
						    	'initialize' => true,
						        'allowClear' => true,
						        'minimumInputLength' => 1,
						         'enableClientValidation' => true,
						        'language' => [
						            'errorLoading' => new JsExpression("function () { return 'Please wait'; }"),
						        ],
						        'ajax' => [
						            'url' => \yii\helpers\Url::to(['personal-list']),
						            'dataType' => 'json',
						            'data' => new JsExpression('function(params) { return {q:params.term}; }')
						        ],
						        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
						        'templateResult' => new JsExpression('function(city) { return city.text; }'),
						        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
						    ],
					    ])?>
		      </div>
     

		    	<div class="col-sm-6">
						<?php 
						echo $form->field($model, 'created_at')->widget(DateTimePicker::classname(), [
						'options' => ['placeholder' => Yii::t('app','Device installation time')],
						'bsVersion' => '4.x',
						'pluginOptions' => [
						'autoclose' => true,
						'format' => 'dd-mm-yyyy',
						 'minView' => 2
						]
						]);
						?>
		     </div>

				<div class="col-sm-6">
					<?php  $array_status = \app\models\ItemUsage::getItemStatus();?>
					<?= $form->field($model, 'status')->dropDownList(ArrayHelper::merge([''=>Yii::t('app','Select')],$array_status)) ?>
				</div>


	       <div class="col-sm-6">
	  			 <?= $form->field($model, 'month')->dropDownList(\app\models\ItemUsage::getMonth(),['prompt'=>Yii::t('app','Select')]) ?>

	       </div>

	    </div>


		<div class="form-group" >
		    <?= Html::submitButton(Yii::t('app','Add'), ['class' =>'btn btn-success ']) ?>
		</div>
	<?php ActiveForm::end(); ?>
 	</div>

<div class="col-sm-12" >
    

    <?php if ( $model !=null ): ?>
  <table class="table table-striped mb-0">
        <thead>
           <tr>
              <th>#</th>
              <th><?=Yii::t('app','Item') ?></th>
              <th><?=Yii::t('app','Credit price per month') ?></th>
              <th><?=Yii::t('app','Status') ?></th>
              <?php if (User::canRoute('/users/user-item-delete')): ?>
                <th><?=Yii::t('app','Delete') ?></th>
              <?php endif ?>
           </tr>
        </thead>
        <tbody>
          <?php $c = 0; ?>
          <?php foreach ( $itemUsage as $key => $inf ): ?>
          <?php $c++; ?>
             <tr>
              <th scope="row"><?=$c ?></th>
              <td><?=$inf['item_name'] ?></td>
               
               <td><?= ( $inf['status'] == 6 ) ? ceil( $credit_price = ( $inf['quantity'] * $inf['price'] ) / $inf['month'] ). " " .$siteConfig['currency']  : "-";?></td>

               <td><?php
                    if ($inf['credit'] == '0' && $inf['status'] == 6 ) {
                       echo " <span class='badge badge-pill badge-success'>".app\models\ItemUsage::getItemStatus()[$inf['status']]."-success</span>";
                       if (User::canRoute('/users/credit-history')) {
                          echo "<a style='margin-left:2px' href='javascript:;' data-fancybox data-type='ajax'  data-fancybox data-type='ajax' data-src=".$langUrl.Url::to('/users/credit-history').'?user_id='.$inf['user_id'].'&item_usage_id='.$inf['id']." href='javascript:void(0);'>".Yii::t("app","History")."</a>";
                       }
                    }elseif($inf['credit'] == '1' && $inf['status'] == 6 ){
                 echo " <span class='badge badge-pill badge-warning'>".app\models\ItemUsage::getItemStatus()[$inf['status']]."</span>";
                     if (User::canRoute('/users/credit-history')) {
                      echo "<a style='margin-left:2px' href='javascript:;' data-fancybox data-type='ajax'  data-fancybox data-type='ajax' data-src=".$langUrl.Url::to('/users/credit-history').'?user_id='.$inf['user_id'].'&item_usage_id='.$inf['id']."  href='javascript:void(0);'>".Yii::t("app","History")."</a>";
                     }
                    }elseif($inf['credit'] == '2' && $inf['status'] == 4 ){
                 echo " <span class='badge badge-pill badge-warning'>".app\models\ItemUsage::getItemStatus()[$inf['status']]."</span>";
                 if (User::canRoute('/users/gift-history')) {
                    echo "<a style='margin-left:2px' href='javascript:;' data-fancybox data-type='ajax'  data-fancybox data-type='ajax' data-src=".$langUrl.Url::to('/users/gift-history').'?user_id='.$inf['user_id'].'&item_usage_id='.$inf['id']." href='javascript:void(0);'>".Yii::t("app","History")."</a>";
                 }
                    }elseif($inf['credit'] == '3' && $inf['status'] == 4 ){
                        echo " <span class='badge badge-pill badge-success'>".app\models\ItemUsage::getItemStatus()[$inf['status']]."</span>";  
                       if (User::canRoute('/users/gift-history')) {
                          echo "<a style='margin-left:2px' href='javascript:;' data-fancybox data-type='ajax'  data-fancybox data-type='ajax' data-src=".$langUrl.Url::to('/users/gift-history').'?user_id='.$inf['user_id'].'&item_usage_id='.$inf['id']."  href='javascript:void(0);'>".Yii::t("app","History")."</a>";
                       }
                    }else{
                      echo app\models\ItemUsage::getItemStatus()[$inf['status']];
                    }


                 ?></td>
            
              
                <?php if (User::canRoute('/users/user-item-delete')): ?>
                <td>
                <a data-fancybox data-src="#hidden-content-item-delete-<?=$inf['id'] ?>" href="javascript:void(0)">  <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
                <?php if (User::canRoute('/users/service-delete')): ?>
                <div style="display: none;" id="hidden-content-item-delete-<?=$inf['id'] ?>">
                        <div class="fcc">
                          <h2 ><b><?=Yii::t("app","Delete an item") ?> </b></h2>
                          <p ><?=Yii::t("app","Are you sure want delete {item} ?", ['item' => $inf['item_name']]) ?> </p>
                          <button class="btn btn-primary item-delete" data-item_usage_id="<?=$inf['id'] ?>" data-item_id="<?=$inf['item_id'] ?>" data-item-user_id="<?=$inf['user_id'] ?>" title="<?=Yii::t('app','Delete') ?>" ><?=Yii::t('app','Delete') ?></button>
                          <button data-fancybox-close="" class="btn btn-secondary"  title="<?=Yii::t('app','Close') ?>" ><?=Yii::t('app','Close') ?></button>           
                        </div>
                </div> 
            <?php endif ?> 
                </td>  
                <?php endif ?>
           </tr>
          <?php endforeach ?>
        </tbody>
     </table>
    <?php else: ?>
      <h5 style="text-align: left;"><?=Yii::t('app','Customer doesnt have any item') ?></h5>
    <?php endif ?>
  </div>
</div>

<?php 
$this->registerJs('

$(".add-item").on("click",function(){
     $.fancybox.close();
});


$(document).on("click",".item-delete",function(){

    var user_id = $(this).attr("data-item-user_id");
    var item_usage_id = $(this).attr("data-item_usage_id");
    var item_id = $(this).attr("data-item_id");

    var that = $(this);
   $.ajax({
    url:"'.$langUrl.Url::to('/request-user-item-delete').'",
    beforeSend:function(){
      $(".loader").show();
      $(".overlay").addClass("show");

    },
    method:"POST",
    data:{user_id:user_id,item_usage_id:item_usage_id,item_id:item_id},
    success:function(res){
       if(res.status == "success"){

                $.fancybox.close();
                $(".loader").hide();
                $(".overlay").removeClass("show"); 
                alertify.set("notifier","position", "top-right");
                alertify.success(res.message);

                setTimeout(()=>{
                    window.location.href=res.url;
                },1000);
      

       }else{
                $(".loader").hide();
                $(".overlay").removeClass("show"); 
                alertify.set("notifier","position", "top-right");
                alertify.error(res.message);
       }
    }
    });
});
')


 ?>



 </div>


<style type="text/css">
.select2-search__field {
	width: 100% !important;
}
.total-price-container{
    display: flex;
    width: 140px;
    justify-content: space-between;
    margin-bottom: 5px;
}
.total-price-container p {color: red; font-weight: 700;}
.field-storeitemcount-month{display: none;}

.modal-dialog {
	width: 960px !important;
}
.select2-container--krajee .select2-selection--multiple .select2-selection__rendered {
    white-space: break-spaces;
}
</style>

<?php 
$this->registerJs('
$(".btn-secondary").on("click",function(){
    $("#modal").modal("toggle");
});

$("#itemusage-month").on("change",function(){
    if(  $("#itemusage-status").val() != "4" &&  $("#itemusage-status").val() != "6" ){
    	console.log($("#itemusage-status").val() != "4" ||  $("#itemusage-status").val() != "6")
        $(this).val("");
    }
});
$("#itemusage-status").on("change",function(){
    if(  $(this).val != "4" &&  $(this).val != "6" ){
    	
        $("#itemusage-month").val("");
    }
});

var xhr_item;
var xhr_active_item=false;
var form_item = $("form#add-form-usefull-count");
form_item.on("beforeSubmit", function (e) {
	if( form_item.find("button").prop("disabled")){
	return false;
}
if(xhr_active_item) { xhr_item.abort(); }
xhr_active_item=true;

 form_item.find("button").prop("disabled",true);
 xhr_item = $.ajax({
      url: "'.\yii\helpers\Url::to(["users/add-item-to-user?id="]).Yii::$app->request->get("id").'",
      type: "post",
      beforeSend:function(){
          $(".loader").show();
          $(".overlay").addClass("show");

      },
      data: form_item.serialize(),
      success: function (response) {
          if(response.status == "success"){
          	$(".loader").show();
          	$(".overlay").removeClass("show");
			alertify.set("notifier","position", "top-right");
			alertify.success("'.Yii::t("app","Item has beeen added").'");
			$("#modal").modal("hide");
			$(".loader").hide();
			$(".overlay").removeClass("show"); 
			setTimeout(()=>{
				window.location.href=response.url;
			},1000);
          }else{
			$(".loader").hide();
			$(".overlay").removeClass("show");
            xhr_active_item=false;
            form_item.find("button").prop("disabled",false);
          }
      }
 });
 return false;
}); 
');

 ?>
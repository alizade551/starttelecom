<?php 
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Json;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\widgets\Pjax;
use kartik\datetime\DateTimePicker;


$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
 ?>

<?php $form = ActiveForm::begin([
 	'id'=>'use-item-form',
    'enableAjaxValidation' => true,
    'validationUrl' => $langUrl .'/items/use-item-validate',
    'enableClientValidation' => false,
    'options' => ['autocomplete' => 'off']
]
); ?>


	<div class="form-group field-items-name">
		<label class="control-label" ><?=Yii::t('app','Item name') ?></label>
		<input type="text" class="form-control" disabled  value="<?=$itemModel['name'] ?>" maxlength="255">
	</div> 
	<?= $form->field($model, 'item_id')->hiddenInput(['value'=>$itemModel['id']])->label(false) ?>

	<?=$form->field($model, 'item_stock_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map($itemStock,'id','sku'),
                'options' => ['placeholder' => Yii::t('app','Select')],
                'language' => 'en',
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]);

	?>


	<?php if ( $itemModel->category->mac_address_validation == "1" ): ?>
		<?= $form->field($model, 'mac_address')->textInput() ?>	
		<div class="form-group field-itemusage-quantity required">
			<label for="itemusage-quantity"><?=Yii::t('app','Quantity') ?></label>
			<input disabled type="text" value="1" id="itemusage-quantity" class="form-control is-valid" aria-required="true" aria-invalid="false">
		</div>
		<?= $form->field($model, 'quantity')->hiddenInput(['value'=>1])->label(false) ?>	
	<?php else: ?>
		<?= $form->field($model, 'quantity')->textInput() ?>		
		<?= $form->field($model, 'mac_address')->hiddenInput()->label(false) ?>	
	<?php endif ?>
	
	<?php 
		echo $form->field($model, 'created_at')->widget(DateTimePicker::classname(), [
		'options' => ['placeholder' => Yii::t('app','Installation time')],
		'bsVersion' => '4.x',
		'pluginOptions' => [
			'autoclose' => true,
			'format' => 'dd-mm-yyyy',
			'minView' => 2
		]
		]);
	?>

	<?= $form->field($model, 'status')->dropDownList(ArrayHelper::merge([''=>Yii::t('app','Select')],\app\models\ItemUsage::getItemCompanyStatus())) ?>

    <?=$form->field($model, 'personals')->widget(Select2::classname(), [
        'maintainOrder' => true,
        	'bsVersion' => '4.x',
        'options' => ['placeholder' => Yii::t('app','Personal fullname'),'multiple'=>true],
	    'pluginOptions' => [
	        'allowClear' => true,
	        'minimumInputLength' => 3,
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



	

	<?=$form->field($model, 'city',['template' => '{label}<div class="form-select">{input}<div class="loader select_loader"></div></div>{error}'])->dropDownList(ArrayHelper::map(\app\models\Cities::find()->all(),'id','city_name'),[
	 'onchange'=>'
	    $(".select_loader").show();
	    $.pjax.reload({
	    url: "'.Url::to(['/items/use-item']).'?id='.Yii::$app->request->get('id').'&city_id="+$(this).val(),
	    container: "#pjax-users-form",
	    timeout: 5000
	    });
	    $(document).on("pjax:complete", function() {
	      $(".select_loader").hide();
	    });
	',
	'prompt'=>'Select City'])->label(Yii::t('app','City')) ?>

    <?php  Pjax::begin(['id'=>'pjax-users-form','enablePushState'=>true]);  ?>

    <?php 
    if (Yii::$app->request->get('city_id') && Yii::$app->request->isPjax) {
               echo  $form->field($model, 'district')->dropDownList(ArrayHelper::map(\app\models\District::find()
                ->where(['city_id'=>Yii::$app->request->get('city_id')])->all(),'id','district_name'),[
         'onchange'=>'
            $(".select_loader").show();
            $.pjax.reload({
            url: "'.Url::to(['/items/use-item']).'?id='.Yii::$app->request->get('id').'&city_id='.Yii::$app->request->get('city_id').'&dis_id="+$(this).val(),
            container: "#pjax-use-form-loc",
            timeout: 5000
            });
            $(document).on("pjax:complete", function() {
              $(".select_loader").hide();
            });
        ',
        'prompt'=>Yii::t('app','Select')]);

			foreach ($form->attributes as $attribute) {
			    $attribute = Json::htmlEncode($attribute);
			    $this->registerJs("jQuery('form#use-item-form').yiiActiveForm('add', $attribute); ");
			} 

    }else{
		echo $form->field($model, 'district',['template' => '{label}<div class="form-select">{input}<div class="loader select_loader"></div></div>{error}'])->dropDownList([''=>''],[
		 'onchange'=>'
		    $(".select_loader").show();
		    $.pjax.reload({
		    url: "'.Url::to(['/items/use-item']).'?id='.Yii::$app->request->get('id').'&city_id="+$(this).val(),
		    container: "#pjax-use-form-loc",
		    timeout: 5000
		    });
		    $(document).on("pjax:complete", function() {
		      $(".select_loader").hide();
		    });
		',
		'prompt'=>Yii::t('app','Select')])->label(Yii::t('app','District'));
    }
     ?>

    <?php Pjax::end(); ?>  

    <?php  Pjax::begin(['id'=>'pjax-use-form-loc','enablePushState'=>true]);  ?>

    <?php if (Yii::$app->request->get('dis_id') && Yii::$app->request->isPjax ): ?>
     <?= $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(\app\models\Locations::find()
      ->where(['city_id'=>Yii::$app->request->get('city_id')])->andWhere(['district_id'=>Yii::$app->request->get('dis_id')])->all(),'id','name')) ?>
		<?php 

				foreach ($form->attributes as $attribute) {
					    $attribute = Json::htmlEncode($attribute);
					    $this->registerJs("jQuery('form#use-item-form').yiiActiveForm('add', $attribute); ");
					} 

		 ?>

	<?php else: ?>
	
      <?= $form->field($model, 'location_id')->dropDownList([''=>''],['prompt'=>Yii::t('app','Select')]); ?>  


    <?php endif ?>
    <?php Pjax::end(); ?>  
<div class="form-group">
    <?= Html::submitButton(Yii::t('app','Use'), ['class' =>'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>





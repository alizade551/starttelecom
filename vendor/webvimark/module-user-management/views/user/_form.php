<?php

use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\Html;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Json;
use kartik\date\DatePicker;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 * @var yii\bootstrap\ActiveForm $form
 */


$this->registerJsFile(Yii::$app->request->baseUrl.'/js/dropify/dropify.min.js',['depends' => [yii\web\JqueryAsset::className()]]); 
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/account-settings.js',['depends' => [yii\web\JqueryAsset::className()]]); 
$this->registerCssFile(Yii::$app->request->baseUrl."/css/dropify/dropify.min.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/dashboard/dash_2.css");
?>



<?php $form = ActiveForm::begin([
	'id'=>'user',
	'layout'=>'horizontal',
	'validateOnBlur' => false,
]); ?>


<div class="row">
	<div class="col-sm-12">
		<?php if ($model->isNewRecord): ?>
		<?= $form->field($model, 'photo_file')->fileInput(['class' => 'dropify']) ?>
		<?php else: ?>
		<?= $form->field($model, 'photo_file')->fileInput(['class' => 'dropify','data-default-file'=>'/uploads/users/profile/'.$model->photo_url.'']) ?>
		<?php endif ?>

		<?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>

		<?= $form->field($model, 'fullname')->textInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>

		<?= $form->field($model, 'personal')->dropDownList(ArrayHelper::merge([''=>Yii::t('app','Select')],User::personalStatus())) ?>

		
		<?= $form->field($model, 'email')->textInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>

	    <?= $form->field($model, 'birthday')->widget(DatePicker::classname(), [
	        'options' => ['placeholder' => Yii::t('app','Enter birth date ...')],
	        'pluginOptions' => [
	            'autoclose'=>true,
	              'format' => 'dd/mm/yyyy'
	        ]
	    ]);
	    ?>

	    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

	    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

		<?php if ($model->isNewRecord): ?>
		<?php 
				 $model->city_id =\yii\helpers\ArrayHelper::map(
				 	\app\models\Cities::find()
				 	->where(['id' => explode(",", $model->city_id)])
				 	->all(), 
				 	'id', 
				 	'id'
				 );
				echo $form->field($model, 'city_id')->widget(Select2::classname(), [
					'bsVersion' => '4.x',
				    'data' => ArrayHelper::map(
				    	\app\models\Cities::find()
				    	->all(),
				    	'id',
				    	'city_name'
				    ),
				    'options' => ['placeholder' => Yii::t('app','Select a city')],
				    'language' => 'en',
				    'pluginOptions' => [
				        'allowClear' => true,
				        'multiple'=>true
				    ],
				    'pluginEvents'=>["change" => "function() { 
				        var that = $(this);
				            $.pjax.reload({
				            url:'".Url::to('create')."?city_id='+that.val(),
				            container: '#district-form-pjax',
				            timeout: 5000
				            });

				     }",]
				]);

			 ?>
		<?php  Pjax::begin(['id'=>'district-form-pjax','enablePushState'=>true]);  ?>
		<?php 
		$model->district_id =\yii\helpers\ArrayHelper::map(
			\app\models\District::find()
			->where(['id' => explode(",", $model->district_id)])
			->all(), 
			'id', 
			'id'
		);
		if (Yii::$app->request->get('city_id') && Yii::$app->request->isPjax) {
			echo $form->field($model, 'district_id')->widget(
				Select2::classname(), [
				'bsVersion' => '4.x',
			    'data' => ArrayHelper::map(
			    	\app\models\District::find()
			    	->where(['city_id'=> explode(",",Yii::$app->request->get('city_id'))])
			    	->all(),
			    	'id',
			    	'district_name'
			    ),
			    'value'=>$model->district_id,
			    'options' => ['placeholder' => Yii::t('app','Select a district')],
			    'language' => 'en',
			    'pluginOptions' => [
			        'allowClear' => true,
			        'multiple'=>true

			    ],
			    'pluginEvents'=>["change" => "function() {
					var that = $(this);
					$.pjax.reload({
						url:'".Url::to('create')."?city_id=".Yii::$app->request->get('city_id')."&district_id='+that.val(),
						container: '#location-form-pjax',
						timeout: 5000
					});
			     }",]
			]);
			foreach ($form->attributes as $attribute) {
			  $attribute = Json::htmlEncode($attribute);
			  $this->registerJs("jQuery('form#user').yiiActiveForm('add', $attribute); ");
			} 
		}else{
			echo $form->field($model, 'district_id')->widget(Select2::classname(), [
				'bsVersion' => '4.x',
				'data' => ArrayHelper::map(
					\app\models\District::find()
					->where(['city_id'=> $model->city_id])
					->all(),
					'id',
					'district_name'
				),
				'value'=>$model->district_id,
				'options' => ['placeholder' => Yii::t('app','Select a district')],
				'language' => 'en',
				'pluginOptions' => [
				    'allowClear' => true,
				    'multiple'=>true
				],
			// 'pluginEvents'=>["change" => "function() { console.log('change'); }",]
			]);
		}
		 ?>
		<?php Pjax::end(); ?>       

		<?php  Pjax::begin(['id'=>'location-form-pjax','enablePushState'=>true]);  ?>
		<?php 
			if (Yii::$app->request->get('city_id') && Yii::$app->request->get('district_id') && Yii::$app->request->isPjax) {
				echo $form->field($model, 'location_id')->widget(Select2::classname(), [
					'bsVersion' => '4.x',
				    'data' => ArrayHelper::map(
				    	\app\models\Locations::find()
				    	->where([
				    		'city_id'=> explode(",",Yii::$app->request->get('city_id')),
				    		'district_id'=>explode(",",Yii::$app->request->get('district_id'))]
				    	)
				    	->all(),
				    	'id',
				    	'name'
				    ),
				    'value'=>$model->district_id,
				    'options' => ['placeholder' => Yii::t('app','Select a district')],
				    'language' => 'en',
				    'pluginOptions' => [
				        'allowClear' => true,
				        'multiple'=>true

				    ],
				    'pluginEvents'=>["change" => "function() {
						var that = $(this);
				
				     }",]
				]);
			          foreach ($form->attributes as $attribute) {
			              $attribute = Json::htmlEncode($attribute);
			              $this->registerJs("jQuery('form#user').yiiActiveForm('add', $attribute); ");
			          } 

				}else{
					echo $form->field($model, 'location_id')->widget(Select2::classname(), [
					'bsVersion' => '4.x',
				    'data' => ArrayHelper::map(
				    	\app\models\District::find()
				    	->where(['city_id'=> $model->city_id])
				    	->all(),
				    	'id',
				    	'district_name'
				    ),
				    'value'=>$model->district_id,
				    'options' => ['placeholder' => Yii::t('app','Select a location')],
				    'language' => 'en',
				    'pluginOptions' => [
				        'allowClear' => true,
				        'multiple'=>true

				    ],
				    // 'pluginEvents'=>["change" => "function() { console.log('change'); }",]
				]);
			}
		 ?>
		<?php Pjax::end(); ?>  
		<?php // end new record ?>     
		<?php else: ?>
			<?php 
				if ($member_location !== null) {
					$model->city_id =\yii\helpers\ArrayHelper::map(
						\app\models\Cities::find()
						->where(['id' => explode(",", $member_location->city_id)])
						->all(), 
						'id', 
						'id'
					);
				}
				echo $form->field($model, 'city_id')->widget(Select2::classname(), [
					'bsVersion' => '4.x',
				    'data' => ArrayHelper::map(
				    	\app\models\Cities::find()->all(),
				    	'id',
				    	'city_name'
				    ),
				    'options' => ['placeholder' => Yii::t('app','Select a city')],
				    'language' => 'en',
				    'pluginOptions' => [
				    	
				        'allowClear' => true,
				        'multiple'=>true
				    ],
				    'pluginEvents'=>[
				    	"change" => "function() { 
				        	var that = $(this);
				            $.pjax.reload({
				            url:'".Url::to('update')."?id=".$model->id."&city_id='+that.val(),
				            container: '#district-form-pjax',
				            timeout: 5000
				            });
				     	}
				     ",
				 ]
				]);

			 ?>
		<?php  Pjax::begin(['id'=>'district-form-pjax','enablePushState'=>true]);  ?>
		<?php
			if ($member_location !== null && !Yii::$app->request->isPjax) {
				$model->district_id =\yii\helpers\ArrayHelper::map(\app\models\District::find()->where(['id' => explode(",", $member_location->district_id)])->all(), 'id', 'id');
			}

		if (Yii::$app->request->get('city_id') && Yii::$app->request->isPjax) {
			echo $form->field($model, 'district_id')->widget(
				Select2::classname(), [
				'bsVersion' => '4.x',
			    'data' => ArrayHelper::map(\app\models\District::find()->where(['city_id'=> explode(",",Yii::$app->request->get('city_id'))])->all(),'id','district_name'),

			    'options' => ['placeholder' => Yii::t('app','Select a district')],
			    'language' => 'en',
			    'pluginOptions' => [
			        'allowClear' => true,
			        'multiple'=>true
			    ],
			    'pluginEvents'=>[
			    	"change" => "function() {
						var that = $(this);
						$.pjax.reload({
							url:'".Url::to('update')."?id=".$model->id."&city_id=".Yii::$app->request->get('city_id')."&district_id='+that.val(),
							container: '#location-form-pjax',
							timeout: 5000
						});
			     	}",
			 ]
			]);
			foreach ($form->attributes as $attribute) {
			  $attribute = Json::htmlEncode($attribute);
			  $this->registerJs("jQuery('form#user').yiiActiveForm('add', $attribute); ");
			} 
		}else{
			echo $form->field($model, 'district_id')->widget(Select2::classname(), [
				'bsVersion' => '4.x',
				'data' => ArrayHelper::map(
					\app\models\District::find()
					->where(['city_id'=> $model->city_id])
					->all(),
					'id',
					'district_name'
				),
				'value'=>$model->district_id,
				'options' => ['placeholder' => Yii::t('app','Select a district')],
				'language' => 'en',
			    'pluginOptions' => [
			        'allowClear' => true,
			        'multiple'=>true
			    ],

			    'pluginEvents'=>[
				    	"change" => "function() {
							var that = $(this);
							$.pjax.reload({
								url:'".Url::to('update')."?id=".$model->id."&city_id=".Yii::$app->request->get('city_id')."&district_id='+that.val(),
								container: '#location-form-pjax',
								timeout: 5000
							});
				     	}",
				 ]
			]);
		}
		 ?>
		<?php Pjax::end(); ?>       

		<?php  Pjax::begin(['id'=>'location-form-pjax','enablePushState'=>true]);  ?>
		<?php 

			if ($member_location !== null && !Yii::$app->request->isPjax) {
				$model->location_id =\yii\helpers\ArrayHelper::map(\app\models\Locations::find()->where(['id' => explode(",", $member_location->location_id)])->all(), 'id', 'id');
			}

			if (Yii::$app->request->get('city_id') && Yii::$app->request->get('district_id') && Yii::$app->request->isPjax) {
					echo $form->field($model, 'location_id')->widget(Select2::classname(), [
						'bsVersion' => '4.x',
					    'data' => ArrayHelper::map(\app\models\Locations::find()
					    	->where(['district_id'=>explode(",",Yii::$app->request->get('district_id'))])->all(),'id','name'),
					   
					    'options' => ['placeholder' => Yii::t('app','Select a location')],
					    'language' => 'en',
					    'pluginOptions' => [
					        'allowClear' => true,
					        'multiple'=>true
					    ],
					]);
					foreach ($form->attributes as $attribute) {
					  $attribute = Json::htmlEncode($attribute);
					  $this->registerJs("jQuery('form#user').yiiActiveForm('add', $attribute); ");
					} 
				}else{
					if (isset($member_location->city_id)) {
						$dataLocation = ArrayHelper::map(
							\app\models\Locations::find()
							->where(['city_id'=> explode(",",$member_location->city_id),'district_id'=> explode(",",$member_location->district_id)])
							->all(),
							'id',
							'name'
						);
					}else{
						$dataLocation = [];
					}
					echo $form->field($model, 'location_id')->widget(Select2::classname(), [
					'bsVersion' => '4.x',
				    'data' => $dataLocation,
				    'options' => ['placeholder' => Yii::t('app','Select a location')],
				    'language' => 'en',
				    'pluginOptions' => [
				        'allowClear' => true,
				        'multiple'=>true
				    ],
				]);
			}
		 ?>
		<?php Pjax::end(); ?>  
		<?php endif ?>

		<?php if ( $model->isNewRecord ): ?>
			<?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
			<?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>
		<?php endif; ?>


		<?= $form->field($model->loadDefaultValues(), 'status')->dropDownList(User::getStatusList()) ?>

		<?php if ( $model->isNewRecord ): ?>
			<?= Html::submitButton(
				 Yii::t('app', 'Add'),
				['class' => 'btn btn-primary']
			) ?>
		<?php else: ?>
			<?= Html::submitButton(
				Yii::t('app', 'Update'),
				['class' => 'btn btn-primary']
			) ?>
		<?php endif; ?>
	</div>
</div>

<?php ActiveForm::end(); ?>



<style type="text/css">
.dropify-wrapper .dropify-message span.file-icon {
    font-size: 19px;
    color: #CCC;
}	
#user .btn{
	    margin-left: -5px;
}
</style>
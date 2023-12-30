
<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
/* @var $this yii\web\View */
/* @var $model app\models\Locations */
/* @var $form yii\widgets\ActiveForm */
?>

<div style="width: 50%;">
    <?php $form = ActiveForm::begin(['id'=>'location-form']); ?>


<?php 

if (Yii::$app->request->get('city_id')) {
$cities = json_encode(Yii::$app->request->get('city_id'));
$this->registerJs("

	console.log(".$cities.")
   $.pjax.reload({
    url:'".Url::to('permisson-for-member?fancybox=true')."?id=".$id."&city_id='+".$cities.",
    container: '#location-form-pjax',
    timeout: 5000
    });
");
}




 $model->city_id =\yii\helpers\ArrayHelper::map(\app\models\Cities::find()->where(['id' => explode(",", $model->city_id)])->all(), 'id', 'id');


echo $form->field($model, 'city_id')->widget(Select2::classname(), [
	'bsVersion' => '4.x',
    'data' => ArrayHelper::map(\app\models\Cities::find()->all(),'id','city_name'),
    'options' => ['placeholder' => Yii::t('app','Select a city')],
    'language' => 'en',
    'pluginOptions' => [
    	
        'allowClear' => true,
        'multiple'=>true
    ],
    'pluginEvents'=>["change" => "function() { 
        var that = $(this);
  
            $.pjax.reload({
            url:'".Url::to('permisson-for-member')."?id=".$id."&city_id='+that.val(),
            container: '#location-form-pjax',
            timeout: 5000
            });

     }",]
]);

 ?>
<?php  Pjax::begin(['id'=>'location-form-pjax','enablePushState'=>true]);  ?>
<?php 
$model->district_id =\yii\helpers\ArrayHelper::map(\app\models\District::find()->where(['id' => explode(",", $model->district_id)])->all(), 'id', 'id');


if (Yii::$app->request->get('city_id') && Yii::$app->request->isPjax) {
echo $form->field($model, 'district_id')->widget(Select2::classname(), [
	'bsVersion' => '4.x',
    'data' => ArrayHelper::map(\app\models\District::find()->where(['city_id'=> explode(",",Yii::$app->request->get('city_id'))])->all(),'id','district_name'),
    'value'=>$model->district_id,
    'options' => ['placeholder' => Yii::t('app','Select a city')],
    'language' => 'en',
    'pluginOptions' => [
        'allowClear' => true,
        'multiple'=>true

    ],
    'pluginEvents'=>["change" => "function() { console.log('change'); }",]
]);
}else{
	echo $form->field($model, 'district_id')->widget(Select2::classname(), [
	'bsVersion' => '4.x',
    'data' => ArrayHelper::map(\app\models\District::find()->where(['city_id'=> $model->city_id])->all(),'id','district_name'),
    'value'=>$model->district_id,
    'options' => ['placeholder' => Yii::t('app','Select a city')],
    'language' => 'en',
    'pluginOptions' => [
        'allowClear' => true,
        'multiple'=>true

    ],
    'pluginEvents'=>["change" => "function() { console.log('change'); }",]
]);
}



 ?>

    <?php Pjax::end(); ?>       




<?php if ($model->isNewRecord): ?>
     <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Add'), ['class' => 'btn btn-primary']) ?>
    </div>
   
<?php else: ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-secondary']) ?>
    </div> 
<?php endif ?>

    <?php ActiveForm::end(); ?>


</div>
<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Json;

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

?>

<div class="users-sms-form">
    <?php  Pjax::begin(['id'=>'message-form-pjax-template-text','enablePushState'=>true]);  ?>
        <?php if ( Yii::$app->request->get('type') == "sms" ): ?>
            <div class="alert alert-warning mb-4" role="alert">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert">
                     <line x1="18" y1="6" x2="6" y2="18"></line>
                     <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
               </button>
               <strong><?=Yii::t('app','Note') ?> !</strong> <?=Yii::t('app','If you write Custom sms message, template message will canceled') ?> 
            </div>
        <?php endif ?>
     <?php Pjax::end(); ?> 
    <?php $form = ActiveForm::begin([
            'id'=>'message-form',
            'layout'=>'horizontal',
            'enableAjaxValidation' => true,
            'validateOnSubmit'=> true,
            'enableClientValidation'=>false,
            'validationUrl' => $langUrl.'/users-message/create-validate',
            'options' => ['autocomplete' => 'off']
        ]); 
    ?>

        <?php
            echo  $form->field($model, 'type')->dropDownList(\app\models\UsersMessage::getMessageType(),[
             'onchange'=>"
                $.pjax.reload({
                    url:'".Url::to('create')."?cities=".Yii::$app->request->get('cities')."&districts=".Yii::$app->request->get('districts')."&template=".Yii::$app->request->get("template")."&type='+$(this).val(),
                        container: '#message-form-pjax-type',
                        timeout: 5000,
                        async:false
                });

                $.pjax.reload({
                        container: '#message-form-pjax-template-text',
                        timeout: 5000,
                        async:false
                });
            ",
            'prompt'=>Yii::t("app","Select")]);
       
        ?>

         <?=$form->field($model, 'template')->dropDownList(
            ArrayHelper::map( $templates,'name','name') ,
            [
                'prompt'=>Yii::t("app","Select"),
                'onchange'=>"
                    $.pjax.reload({
                    url:'".Url::to('create')."?cities=".Yii::$app->request->get('cities')."&districts=".Yii::$app->request->get('districts')."&type=".Yii::$app->request->get('type')."&template='+$(this).val(),
                    container: '#message-form-pjax-template',
                    timeout: 5000
                    });
                ",
            ]
     )?>

        <?php  Pjax::begin(['id'=>'message-form-pjax-template','enablePushState'=>true]);  ?>

        <?php if ( Yii::$app->request->get('template') == "maintenance_alert" ): ?>
            <?php $params = explode(",",\app\models\MessageTemplate::find()->where(['name'=>'maintenance_alert'])->asArray()->one()['params']) ?>
       
            <?php foreach ($params as $paramKey => $param ): ?>
                  <?= $form->field($model, 'dynamic_param[]')->textInput(['maxlength' => true])->label($param) ?>
            <?php endforeach ?>
        <?php endif ?>
        <?php Pjax::end(); ?> 

        <?php  Pjax::begin(['id'=>'message-form-pjax-type','enablePushState'=>true]);  ?>
            <?php if ( Yii::$app->request->get('type') == "sms" ): ?>
                 <?=$form->field($model, 'text')->textarea(['rows' => 6])->label('Custom sms message') ?>
            <?php endif ?>

        <?php Pjax::end(); ?>  

        <?php 
        $data = ['1' => Yii::t('app','Active'), '2' => Yii::t('app','Deactive'), '3' => Yii::t('app','Archive'), '7' =>Yii::t('app','VIP')];
            echo $form->field($model, 'user_status')->widget(Select2::classname(), [
            'data' => $data,
            'bsVersion' => '4.x',
            'language' => Yii::$app->language,
            'options' => ['placeholder' =>Yii::t('app','Select'), 'multiple' => true],
            'pluginOptions' => [
                'allowClear' => true,
            ],
            ]);


        ?>

        <?php 

        echo $form->field($model, 'cities')->widget(Select2::classname(), [
            'bsVersion' => '4.x',
            'data' => ArrayHelper::map(
                \app\models\Cities::find()
                ->all(),
                'id',
                'city_name'
            ),
            'options' => ['placeholder' => Yii::t('app','Select')],
            'language' => 'en',
            'pluginOptions' => [
                'allowClear' => true,
                'multiple'=>true
            ],
            'pluginEvents'=>["change" => "function() { 
                var that = $(this);
                    $.pjax.reload({
                    url:'".Url::to('create')."?type=".Yii::$app->request->get('type')."&template=".Yii::$app->request->get("template")."&cities='+that.val(),
                    container: '#district-form-pjax',
                    timeout: 5000
                    });

             }",]
        ]);

        ?>

        <?php  Pjax::begin(['id'=>'district-form-pjax','enablePushState'=>true]);  ?>
        <?php 

        if (Yii::$app->request->get('cities') && Yii::$app->request->isPjax) {
            echo $form->field($model, 'districts')->widget(
                Select2::classname(), [
                'bsVersion' => '4.x',
                'data' => ArrayHelper::map(
                    \app\models\District::find()
                    ->where(['city_id'=> explode(",",Yii::$app->request->get('cities'))])
                    ->all(),
                    'id',
                    'district_name'
                ),
                'value'=>$model->districts,
                'options' => ['placeholder' => Yii::t('app','Select')],
                'language' => 'en',
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple'=>true

                ],
                'pluginEvents'=>["change" => "function() {
                    var that = $(this);
                    $.pjax.reload({
                        url:'".Url::to('create')."?type=".Yii::$app->request->get('type')."&template=".Yii::$app->request->get("template")."&cities=".Yii::$app->request->get('cities')."&districts='+that.val(),
                        container: '#location-form-pjax',
                        timeout: 5000
                    });
                 }",]
            ]);
            foreach ($form->attributes as $attribute) {
              $attribute = Json::htmlEncode($attribute);
              $this->registerJs("jQuery('form#user-sms').yiiActiveForm('add', $attribute); ");
            } 
        }else{
            echo $form->field($model, 'districts')->widget(Select2::classname(), [
                'bsVersion' => '4.x',
                'data' => ArrayHelper::map(
                    \app\models\District::find()
                    ->where(['city_id'=> $model->cities])
                    ->all(),
                    'id',
                    'district_name'
                ),
                'value'=>$model->districts,
                'options' => ['placeholder' => Yii::t('app','Select')],
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
            if (Yii::$app->request->get('cities') && Yii::$app->request->get('districts') && Yii::$app->request->isPjax) {
                echo $form->field($model, 'locations')->widget(Select2::classname(), [
                    'bsVersion' => '4.x',
                    'data' => ArrayHelper::map(
                        \app\models\Locations::find()
                        ->where([
                            'city_id'=> explode(",",Yii::$app->request->get('cities')),
                            'district_id'=>explode(",",Yii::$app->request->get('districts'))]
                        )
                        ->all(),
                        'id',
                        'name'
                    ),
                    'value'=>$model->districts,
                    'options' => ['placeholder' => Yii::t('app','Select')],
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
                          $this->registerJs("jQuery('form#user-sms').yiiActiveForm('add', $attribute); ");
                      } 

                }else{
                    echo $form->field($model, 'locations')->widget(Select2::classname(), [
                    'bsVersion' => '4.x',
                    'data' => ArrayHelper::map(
                        \app\models\District::find()
                        ->where(['city_id'=> $model->cities])
                        ->all(),
                        'id',
                        'district_name'
                    ),
                    'value'=>$model->locations,
                    'options' => ['placeholder' => Yii::t('app','Select')],
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

<?=$form->field($model, 'message_time')->hiddenInput(['value' => time()])->label(false)?>

<div class="form-group">
<?=Html::submitButton(Yii::t('app','Send message'), ['class' => 'btn btn-success'])?>
</div>

<?php ActiveForm::end();?>
</div>

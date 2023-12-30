<?php
use yii\helpers\ArrayHelper;
use app\models\Services;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Json;


/* @var $this yii\web\View */
/* @var $model app\models\Packets */
/* @var $form yii\widgets\ActiveForm */
if ($model->isNewRecord) {
    $this->title = Yii::t("app","Add a packet");
}else{
    $this->title = Yii::t("app","Update - {packet_name}",['packet_name'=>$model->packet_name]);
}
?>

<div class="widget-content " style="padding: 0;width: 100%;">
    <?php $form = ActiveForm::begin([
      'id'=>'packet-form',
      'enableClientValidation' => false,
      'enableAjaxValidation' => true,
      'validationUrl' => Url::toRoute('validate-create-packet')
     ]); ?>
     <div class="row">
         <div class="col-sm-12">
            <?php 
                if ($model->isNewRecord) {
                    $items = ArrayHelper::map(Services::find()->all(), 'id','service_name');
                    $options = [];
                    foreach (Services::find()->all() as $s) {
                        $options[$s->id] = ['data-service' => $s->service_alias, 'class' => 'form-control'];
                    }
                    echo $form->field($model, 'service_id')->dropDownList($items,[
                         'options' => $options,
                         'onchange'=>'
                            const option = $("option:selected", this).attr("data-service");
                            $(".select_loader").show();
                            $.pjax.reload({
                            url: "'.Url::to(['packets/create']).'?service="+option,
                            container: "#pjax-packet-form",
                            timeout: 5000
                            });
                        ',
                        'prompt'=>Yii::t("app","Select")
                    ]);
                }else{
                    $items = ArrayHelper::map(Services::find()->where(['id'=>$model->service_id])->all(), 'id','service_name');
                    $options = [];
              

                    foreach (Services::find()->where(['id'=>$model->service_id])->all() as $s) {
                        $options[$s->id] = ['data-service' => $s->service_alias, 'class' => 'form-control'];
                    }

                    if (Yii::$app->request->get('id')) {
                        $model->service_id = $model->service->id;
                    }
                    echo $form->field($model, 'service_id')->dropDownList($items,[
                         'options' => $options,
                         'onchange'=>'
                            const option = $("option:selected", this).attr("data-service");
                            $(".select_loader").show();
                            $.pjax.reload({
                            url: "'.Url::to(['packets/update']).'?id='.$model->id.'&service="+option,
                            container: "#pjax-packet-form",
                            timeout: 5000
                            });
                        ',
                        'prompt'=>Yii::t("app","Select")
                    ]);
                }
            ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'packet_name')->textInput(['maxlength' => true]) ?>
        </div>       


       <div class="col-sm-12">
            <?= $form->field($model, 'packet_price')->textInput() ?>
        </div>  


    <?php  Pjax::begin(['id'=>'pjax-packet-form','enablePushState'=>true]);  ?>
        <div class="row">
        <?php if (Yii::$app->request->get('service') == "internet"): ?>
            <div class="col-sm-12">
                <?= $form->field($model, 'download')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-12">
                <?= $form->field($model, 'upload')->textInput(['maxlength' => true]) ?>
            </div>
   
            <?php 
                if (Yii::$app->request->isPjax) {
                      foreach ($form->attributes as $attribute) {
                        $attribute = Json::htmlEncode($attribute);
                        $this->registerJs("jQuery('form#packet-form').yiiActiveForm('add', $attribute); ");
                    } 
                }
             ?>
        <?php endif ?>
            
        </div>

    
    <?php  Pjax::end();  ?>





    </div>
        <div class="form-group">
            <?php if ($model->isNewRecord): ?>
                <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false); ?>
                <?= Html::submitButton( Yii::t('app','Create a packet'), ['class' => 'btn btn-success']) ?>
            <?php else: ?>
                 <?= Html::submitButton( Yii::t('app','Update'), ['class' => 'btn btn-primary']) ?>
            <?php endif ?>
        </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>

<style type="text/css">
    #pjax-packet-form {
        width: 100%;
        margin-left: 15px;
        margin-right: 15px;
    }
</style>
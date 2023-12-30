<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
 ?>
<?php $form = ActiveForm::begin(['id'=>'new-service-form']); ?>

<div class="col-sm-12">
    <div class="row">
        <?php $model->selected_services =false; ?>
        <?= $form->field($model, 'selected_services')->checkboxList(ArrayHelper::map(app\models\Services::find()->all(), 'id', 'service_name'),[
        'item' => function($index, $label, $name, $checked, $value) {
        $checked = $checked ? 'checked' : '';
        return "<div class='n-chk'><label class=\"new-control new-checkbox checkbox-success\" for='checkbox-".$index."'> <input class='new-control-input' id='checkbox-".$index."' type='checkbox' {$checked} name='{$name}' value='{$value}'> <span class='new-control-indicator'></span>  {$label}</label></div>";
        }
        ]) ?> 
    </div>
</div>
<?= Html::submitButton(Yii::t("app","Send request"), ['class' => 'btn btn-primary']) ?>
<a class="btn btn-secondary" title="<?=Yii::t('app','Close') ?>" style="margin-left: 5px;" ><?=Yii::t('app','Close') ?></a>         

<?php ActiveForm::end(); ?>


		
<style type="text/css">
    #new-service-form #users-selected_services{display: flex;}
</style>					

<?php 
$this->registerJs('
    $(".btn-secondary").on("click",function(){
        $("#modal").modal("toggle");
    });
');

 ?>
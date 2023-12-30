<?php
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model app\models\Devices */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app','Create a device');
$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].'&libraries=places', ['depends' => [yii\web\JqueryAsset::className()]]);

?>



<?php $form = ActiveForm::begin([
        'id'=>"add-device-forum",
        'enableAjaxValidation' => true,
        'validateOnSubmit'=> true,
        'enableClientValidation'=>false,
        'validationUrl' => 'add-device-validate',
        'options' => ['autocomplete' => 'off']]);
    ?>
    <div class="row">
      <div class="col-sm-6">
            <?= $form->field($model, 'vendor_name')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-sm-6">
        <?= $form->field($model, 'type')->dropDownList(
        [ 'switch' => 'Switch', 'epon' => 'Epon', 'gpon' => 'Gpon','xpon' => 'Xpon' ], 
        [   
        'onchange'=>'
            $.pjax.reload({
                url: "'.Url::to(['/devices/create']).'?type="+$(this).val(),
                container: "#pjax-add-device-form",
                timeout: 5000
            });
            $(document).on("pjax:complete", function() {
              $(".select_loader").hide();
            });
        ',
        'prompt'=>Yii::t('app','Select')
        ]
        ) ?>
      </div>
      <div class="col-sm-6">
        <?php  Pjax::begin(['id'=>'pjax-add-device-form','enablePushState'=>true]);  ?>
            <?php if ( Yii::$app->request->get('type') == "switch" ): ?>
                <?= $form->field($model, 'port_count')->dropDownList(
                    ArrayHelper::merge([''=>Yii::t('app','Select')],\app\models\Devices::getPortCount())
                ) ?>


                <?php 
                    if (Yii::$app->request->isPjax) {
                        foreach ($form->attributes as $attribute) {
                        $attribute = Json::htmlEncode($attribute);
                        $this->registerJs("jQuery('form#add-device-forum').yiiActiveForm('add', $attribute); ");
                        } 
                    }
                ?>
                <?php elseif( Yii::$app->request->get('type') == "epon" || Yii::$app->request->get('type') == "gpon" || Yii::$app->request->get('type') == "xpon" ): ?>
                <?= $form->field($model, 'pon_port_count')->dropDownList(
                    ArrayHelper::merge([''=>Yii::t('app','Select')],\app\models\Devices::getPonPortCount())
                ) ?>
                <?php 

                    if (Yii::$app->request->isPjax) {
                        foreach ($form->attributes as $attribute) {
                        $attribute = Json::htmlEncode($attribute);
                        $this->registerJs("jQuery('form#add-device-forum').yiiActiveForm('add', $attribute); ");
                        } 
                    }
                ?>
                <?php else: ?>
                    <div class="form-group field-devices-description required">
                        <label for="devices-description"><?=Yii::t('app','Please select device type') ?></label>
                        <input type="text"  class="form-control is-invalid"   aria-required="true" aria-invalid="true" disabled="disabled">
                    </div>
                <?php endif ?>
            <?php Pjax::end(); ?>  
      </div>
      <div class="col-sm-6">
            <?= $form->field($model, 'ip_address')->textInput() ?>  
      </div>

      <div class="col-sm-6">
      <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
      </div>
    </div>
    <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success add-device']) ?>
    </div>
<?php ActiveForm::end(); ?>
 
    


<style type="text/css">

    #add-device-forum{width: 100%;}

.widget-content{
    padding: 15px;
    width: 100%;
}
      #pac-input{
    padding: 10px;
    font-size: 14px;
    width: 20%;
    height: 36px;
    z-index: 9999;
    line-height: 14px;
      }
</style>

<?php $this->registerJs('

var clickAddDevice = false;
var xhrAddDevice;
var xhrActiveChangePacket=false;
var formAddDevice = $("form#add-device-forum");

$("form#add-device-forum").on("beforeSubmit", function (e) {
    if(!clickAddDevice){

        clickAddDevice = true;
        if( formAddDevice.find(".add-device").prop("disabled")){
            return false;
        }
        if(xhrActiveChangePacket) { xhrAddDevice.abort(); }
        xhrActiveChangePacket = true;
        formAddDevice.find(".btn-primary").prop("disabled",true);

        xhrAddDevice = $.ajax({
          url: "'.\yii\helpers\Url::to(["devices/create"]).'",
          type: "post",
          beforeSend:function(){
            $(".loader").show();
            $(".overlay").addClass("show");
          },
          data: formAddDevice.serialize(),
          success: function (response) {
              $(".loader").hide();
              $(".overlay").removeClass("show");


            if(response.status == "error"){
                 alertify.set("notifier","position", "top-right");
                 alertify.error(response.message);
            }          

            if(response.status == "success"){
                 window.location.href = response.url;
            }else{
                xhrActiveChangePacket=false;
                formAddDevice.find(".add-device").prop("disabled",false);
            }

          }
        }).done(function(){ clickAddDevice = false; });
        return false;


    }

}); 
 
') ?>
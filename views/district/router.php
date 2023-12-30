<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\radius\Nas;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Location */
/* @var $form yii\widgets\ActiveForm */
?>

 
<?php $form = ActiveForm::begin(['id'=>'add-router-form']); ?>
    <?php 
	    $data = ArrayHelper::map(Nas::find()->all(),'id','nasname');
	    echo $form->field($model, 'nas_id')->widget(Select2::classname(), [
	        'data' => $data,
	        'language' => 'en',
	        'options' => ['placeholder' => Yii::t('app','Select')],
	        'pluginOptions' => [
	            'allowClear' => true
	        ],
	    ]);
     ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t("app","Define"), ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>


<?php 
$this->registerJs('

    var xhr;
    var xhr_active=false;
    var form = $("form#add-router-form");
    form.on("beforeSubmit", function (e) {
     
    if( form.find("button").prop("disabled")){
    return false;
    }
       if(xhr_active) { xhr.abort(); }
        xhr_active=true;
     form.find("button").prop("disabled",true);
      xhr = $.ajax({
                url: "'.\yii\helpers\Url::to(["/district/add-router?id="]).Yii::$app->request->get("id").'",
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
                        alertify.success( response.message);
                        $("#modal").modal("hide");

			             $.pjax.reload({
			                container: "#district-grid-pjax",
			                timeout: 5000
			            }).done(function(){
			                $(".loader").hide();
			                $(".overlay").hide();
			            })

                       
                    }else{
                      $(".loader").hide();
                      $(".overlay").removeClass("show");
                      alertify.set("notifier","position", "top-right");
                      alertify.error(response.message);
                      xhr_active=false;
                      form.find("button").prop("disabled",false);
                    }
                }
           });
           return false;
      }); 
');
 ?>



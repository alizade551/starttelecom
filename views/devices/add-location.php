<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Json;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Devices */
/* @var $form yii\widgets\ActiveForm */
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

?>
<div class="devices-form">
<div class="alert alert-success mb-4" role="alert">
   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert">
         <line x1="18" y1="6" x2="6" y2="18"></line>
         <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
   </button>
   <strong><?=Yii::t('app','Reminding!') ?></strong> <?=Yii::t('app','Only the districts on the device that support user registration are visible in District dropdown list') ?> 
</div>

    <?php $form = ActiveForm::begin([
            'id'=>"add-location-to-device",
            'enableAjaxValidation' => true,
            'validateOnSubmit'=> true,
            'enableClientValidation'=>false,
            'validationUrl' => $langUrl.'/devices/add-location-validate',
            'options' => ['autocomplete' => 'off']]);
     ?>

    <?= $form->field($model, 'device_id')->hiddenInput(['value' => $deviceModel['id']])->label(false) ?>
    <?php 
        $allCity = ArrayHelper::map(
            \app\models\Cities::find()
            ->withByCityId()
            ->orderBy(['city_name'=>SORT_ASC])
            ->all()
            ,'id',
            'city_name'
        );
     ?>
     <div class="row">
         <div class="col-sm-6">
            	<?=$form->field($model, 'city_id',)->dropDownList($allCity,[
            	         'onchange'=>'
            	            $.pjax.reload({
            	            url: "'.Url::to(['/devices/add-location']).'?id='.$deviceModel["id"].'&city_id="+$(this).val(),
            	            container: "#pjax-add-location-to-device-city",
            	            timeout: 5000
            	            });
            	        ',
            	        'prompt'=>Yii::t("app","Select")
            	    ])
            	?>
             
         </div>
         <div class="col-sm-6">
            <?php  Pjax::begin(['id'=>'pjax-add-location-to-device-city','enablePushState'=>true]);  ?>

            <?php 


            if (Yii::$app->request->get('city_id') && Yii::$app->request->isPjax ) {
                    $allDistrictPjaxGet = ArrayHelper::map(
                        \app\models\District::find()
                        ->where(['city_id'=>Yii::$app->request->get('city_id')])
                        ->andWhere(['device_registration'=>'1'])
                        ->withByDistrictId()
                        ->orderBy(['district_name'=>SORT_ASC])
                        ->all(),
                        'id',
                        'district_name'
                    );
                if ( $deviceModel['type'] == "switch" ) {
                    echo  $form->field($model, 'district_id',['enableAjaxValidation' => true])
                    ->dropDownList(
                        $allDistrictPjaxGet,
                        [
                             'onchange'=>'
                                $(".select_loader").show();
                                $.pjax.reload({
                                url: "'.Url::to(['/devices/add-location']).'?id='.$deviceModel['id'].'&city_id='.Yii::$app->request->get('city_id').'&district_id="+$(this).val(),
                                container: "#pjax-add-location-to-device-distrcit",
                                timeout: 5000
                                });
                                $(document).on("pjax:complete", function() {
                                  $(".select_loader").hide();
                                });
                            ',
                            'prompt'=>Yii::t("app","Select")
                        ]
                    );
                }else{
                    echo  $form->field($model, 'district_id',['enableAjaxValidation' => true])
                    ->dropDownList(
                        $allDistrictPjaxGet
                    );
                }
                  foreach ($form->attributes as $attribute) {
                      $attribute = Json::htmlEncode($attribute);
                      $this->registerJs("jQuery('form#add-location-to-device').yiiActiveForm('add', $attribute); ");
                  } 

            }else{
                echo  $form->field($model, 'district_id')->dropDownList([''=>''],['prompt'=>Yii::t("app","Select")]);
            }
        ?>

            <?php Pjax::end(); ?>               
         </div>
         <div class="col-sm-6">
            <?php if ($deviceModel['type'] == "switch"): ?>
                    <?php  Pjax::begin(['id'=>'pjax-add-location-to-device-distrcit','enablePushState'=>true]);  ?>
                        <?php 
                            if (Yii::$app->request->get('city_id') && Yii::$app->request->get('district_id') && Yii::$app->request->isPjax) {
                                echo $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(
                                        \app\models\Locations::find()
                                        ->where([
                                            'city_id'=> explode(",",Yii::$app->request->get('city_id')),
                                            'district_id'=>explode(",",Yii::$app->request->get('district_id'))]
                                        )
                                        ->all(),
                                        'id',
                                        'name'
                                    ));
                                      foreach ($form->attributes as $attribute) {
                                          $attribute = Json::htmlEncode($attribute);
                                          $this->registerJs("jQuery('form#add-location-to-device').yiiActiveForm('add', $attribute); ");
                                      } 

                                }else{
                                    echo $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(
                                        \app\models\Locations::find()
                                        ->where([
                                            'city_id'=> explode(",",Yii::$app->request->get('city_id')),
                                            'district_id'=>explode(",",Yii::$app->request->get('district_id'))]
                                        )
                                        ->all(),
                                        'id',
                                        'name'
                                    ),['prompt'=>Yii::t('app','Select')]);
                            }
                         ?>
                    <?php Pjax::end(); ?>   
            <?php endif ?>
         </div>
     </div>







    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success add-location','data-id'=>$deviceModel["id"]]) ?>
    </div>

    <?php ActiveForm::end(); ?>

            <div class="col-lg-12 animatedParent animateOnce z-index-50">
                <div class="panel panel-default animated fadeInUp">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead> 
                                    <tr> 
                                        <th>#</th> 
                                        <th><?=Yii::t('app','Device name') ?></th> 
                                        <th><?=Yii::t('app','Device type') ?></th> 
                                        <th><?=Yii::t('app','City') ?></th> 
                                        <th><?=Yii::t('app','District') ?></th> 
                                        <th><?=Yii::t('app','Location') ?></th> 
                                        <?php if ( User::canRoute(['/devices/update-device-location']) ): ?>
                                        <th><?=Yii::t('app','Update') ?></th> 
                                        <?php endif ?>
                                        <th><?=Yii::t('app','Delete') ?></th> 
                                    </tr> 
                                </thead> 
                                <tbody> 
                                    <?php $c = 1;?>
                                    <?php foreach ($deviceLocationsModel as $key => $deviceLocation): ?>
                                    <tr> 
                                        <td><?=$c++; ?></td> 
                                        <td><?=$deviceLocation['device_name'] ?></td> 
                                        <td><?=$deviceLocation['device_type'] ?></td> 
                                        <td><?=$deviceLocation['device_city'] ?></td> 
                                        <td><?=$deviceLocation['device_district'] ?></td> 
                                        <td>
                                            <?php if ($deviceLocation['device_location'] !="" ): ?>
                                                <?=$deviceLocation['device_location'] ?>
                                            <?php else: ?>
                                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                                            <?php endif ?>

                                        </td> 
      

 
                                        <?php if ( User::canRoute(['/devices/update-device-location']) ): ?>
                                            <td class="change-packet">
                                                <a data-fancybox="" data-type="ajax" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/devices/update-device-location?id=<?=$deviceLocation['id'] ?>&city_id=<?=$deviceLocation['city_id'] ?>&district_id=<?=$deviceLocation['district_id'] ?>&location_id=<?=$deviceLocation['location_id'] ?>" href="javascript:;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                                </a>
                                            </td>
                                        <?php endif ?>

                                        <?php if ( User::canRoute(['/devices/delete-device-from-location']) ): ?>
                                            <td>
                                                <a class="device-deleted-from-location"  data-href="<?=$langUrl ?>/devices/delete-device-from-location?id=<?=$deviceLocation['id'] ?>" href="javascript:void(0)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                </a>
                                            </td> 
                                        <?php endif ?>
                                    </tr> 
            
                                <?php endforeach ?>
                                </tbody> 
                            </table>
                        </div>
                    </div>
                </div>
            </div>



</div>

<?php $this->registerJs('

var clickAddLocation = false;

var xhrAddLocation;
var xhrActiveAddLocation=false;
var formAddLocation = $("form#add-location-to-device");

$("form#add-location-to-device").on("beforeSubmit", function (e) {
	if(!clickAddLocation){
       var deviceId =  formAddLocation.find(".add-location").attr("data-id");
        clickAddLocation = true;
	    if( formAddLocation.find(".add-location").prop("disabled")){
	        return false;
	    }
	    if(xhrActiveAddLocation) { xhrAddLocation.abort(); }
	    xhrActiveAddLocation = true;
	    formAddLocation.find(".btn-primary").prop("disabled",true);

	    xhrAddLocation = $.ajax({
	      url: "'.\yii\helpers\Url::to(["devices/add-location"]).'?id="+deviceId,
	      type: "post",
	      beforeSend:function(){
	        $(".loader").show();
	        $(".overlay").addClass("show");
	      },
	      data: formAddLocation.serialize(),
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
	            xhrActiveAddLocation=false;
	            formAddLocation.find(".btn-primary").prop("disabled",false);
	        }

	      }
	    }).done(function(){ clickAddLocation = false; });
	    return false;


	}

}); 
 
') ?>


<?php 
$this->registerJs('
  $(document).on("click",".device-deleted-from-location",function(){
      var that = $(this);
      var message  = "'.Yii::t("app","Are you sure want to delete this ?").'";
          alertify.confirm( message, function (e) {
            if (e) {
               $.ajax({
                   url:that.attr("data-href"),
                   type:"post",
                   success:function(response){
                        if(response.status == "success"){
                             that.closest("tr").fadeOut("slow");
                             alertify.set("notifier","position", "top-right");
                             alertify.success(response.message);
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error(response.message);
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a defined location").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});;   
        return false;
    });

');

 ?>
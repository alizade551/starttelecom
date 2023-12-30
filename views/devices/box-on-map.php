<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Cities */
/* @var $form yii\widgets\ActiveForm */

$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].' ', ['depends' => [yii\web\JqueryAsset::className()]]);

?>
<?php if ( isset( $model->location->cordinate ) ): ?>
	<div style="width:800px;">
	 	<h4><?=Yii::t(
	 		'app',
	 		'Device {device} {box_name} box cordinate',
	 		[
		 		'device'=>$model->egponPonPort->device->name,
		 		'box_name'=>$model->box_name
	 		]
	 		); ?>
	 	</h4>

		<?php $form = ActiveForm::begin([
			'id'=>"box-on-map-form",
		    'enableAjaxValidation' => true,
		    'validateOnSubmit'=> true,
		    'enableClientValidation'=>false,
		    'validationUrl' => $langUrl .'/devices/box-on-map-validate',
		]);?>
			<div class="row">
				<div class="col-sm-12">
					<?= $form->field($model, 'cordinate',['inputOptions' => ['placeholder'=>Yii::t('app','Cordinate')]])->textInput(['maxlength' => true,'class' => 'form-control']) ?>
					<div id='map'>
						<h2><?=Yii::t('app','Allow location') ?></h2>
					</div>
				</div>
			</div>
			<div class="form-group">
				<?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success pon-port-setting-btn','data-box_id'=>$model['id']])?>
			</div>
		<?php ActiveForm::end();?>
	</div>
	<style type="text/css">
	#map {
	    width: 100%;
	    height: 400px;
	    position: relative;
	    padding: 0;
	    margin: 20px 0;
	}

	#map h2 {
	    text-align: center;
	    position: absolute;
	    left: 50%;
	    top: 50%;
	    font-size: 20px;
	    margin-left: -110px;
	}
	</style>


	<?php 
	$cordinates =  ( $model['cordinate']  !== null ) ?  explode(",",$model['cordinate']) : null;
	$locationCordinate =  ( $model->location->cordinate  !== null ) ?  explode( ",",$model->location->cordinate ) : null;


	if ( $cordinates != null ) {
	    $latitude = $cordinates[0];
	    $longitude = $cordinates[1];
	}else{
	    $latitude = $locationCordinate[0];
	    $longitude = $locationCordinate[1];
	}

	$this->registerJs("
		var defaultCordiante = {
		    lat: ".$latitude.",
		    lng: ".$longitude."
		};


		initMap( defaultCordiante )


		function initMap( cordinates ){
			var map = new google.maps.Map(document.getElementById('map'), {
			    zoom: 20,
			    center: cordinates,
			    mapTypeId: google.maps.MapTypeId.HYBRID
			});

			var myMarker = new google.maps.Marker({
			    position: cordinates,
			    draggable: true
			});

			
			  google.maps.event.addListener(myMarker, 'dragend', function (evt) {
			        $('#egponbox-cordinate').val( evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
			        var point = myMarker.getPosition();
			         map.setCenter(point); // setCenter takes a LatLng object
			         map.panTo(point);
			    });


			    google.maps.event.addListener(map,'idle',function(){
			      if( !this.get('dragging') && this.get('oldCenter') && this.get('oldCenter')!==this.getCenter() ) {
			        //do what you want to
			          myMarker.setPosition(this.getCenter());

			      }
			      if(!this.get('dragging')){
			       // this.set('oldCenter',this.getCenter())
			      }

			    });

			    google.maps.event.addListener(map,'dragstart',function(){
			      this.set('dragging',true);          
			    });

			    google.maps.event.addListener(map,'dragend',function(){
			      this.set('dragging',false);
			      google.maps.event.trigger(this,'idle',{});
			    });

			    map.setCenter(myMarker.position);
			    myMarker.setMap(map);
			    map.setCenter(cordinates);
		}
	");

	 ?>


	<?php $this->registerJs('

	var clickBoxPortSetting = false;
	var xhrBoxPortSetting;
	var xhrActiveBoxPortSetting=false;
	var formBoxPortSetting = $("form#box-on-map-form");

	$("#box-on-map-form").on("beforeSubmit", function (e) {
		if(!clickBoxPortSetting){
	       var boxId =  formBoxPortSetting.find(".pon-port-setting-btn").attr("data-box_id");
	        clickBoxPortSetting = true;
		    if( formBoxPortSetting.find(".pon-port-setting-btn").prop("disabled")){
		        return false;
		    }
		    if(xhrActiveBoxPortSetting) { xhrBoxPortSetting.abort(); }
		    xhrActiveBoxPortSetting = true;
		    formBoxPortSetting.find(".btn-primary").prop("disabled",true);

		    xhrBoxPortSetting = $.ajax({
		      url: "'.\yii\helpers\Url::to(["devices/box-on-map"]).'?id="+boxId,
		      type: "post",
		      beforeSend:function(){
		        $(".loader").show();
		        $(".overlay").addClass("show");
		      },
		      data: formBoxPortSetting.serialize(),
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
		            xhrActiveBoxPortSetting=false;
		            formBoxPortSetting.find(".pon-port-setting-btn").prop("disabled",false);
		        }

		      }
		    }).done(function(){ clickBoxPortSetting = false; });
		    return false;


		}
	}); 
	 
	') ?>



<?php else: ?>
	<div style="width:400px">
		<h6 style="text-align:center;"><a href="/locations/update?id=<?=$model->location->id ?>"><?=Yii::t('app','Please add location cordinate') ?></a></h6>
	</div>
<?php endif ?>



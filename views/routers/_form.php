<?php
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model app\models\Routers */
/* @var $form yii\widgets\ActiveForm */

 $this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].'&libraries=places', ['depends' => [yii\web\JqueryAsset::className()]]);
?>

<div class="widget-content widget-content-area">

<?php $form = ActiveForm::begin(['id'=>'router-form']); ?>
    <div class="row">
        <div class="col-sm-6">
             <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>



        <div class="col-sm-6">
            <?= $form->field($model, 'vendor_name')->textInput(['maxlength' => true]) ?>
        </div>  

        <?php if ( $model->isNewRecord ): ?>
            <div class="col-sm-6">
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

                    <?=$form->field($model, 'city_id')->dropDownList($allCity,[
                     'onchange'=>'
                        $.pjax.reload({
                        url: "'.Url::to(['routers/create']).'?city_id="+$(this).val(),
                        container: "#pjax-router-form",
                        timeout: 5000
                        });
                        $(document).on("pjax:complete", function() {
                          $(".select_loader").hide();
                        });
                    ',
                    'prompt'=>Yii::t("app","Select")
                ])?>
            </div>

            <div class="col-sm-6">
                <?php  Pjax::begin(['id'=>'pjax-router-form','enablePushState'=>true]);  ?>
                    <?php 
                        if (Yii::$app->request->get('city_id') && Yii::$app->request->isPjax ) {
                                $allDistrictPjaxGet = ArrayHelper::map(
                                    \app\models\District::find()
                                    ->where(['city_id'=>Yii::$app->request->get('city_id')])
                                    ->withByDistrictId()
                                    ->orderBy(['district_name'=>SORT_ASC])
                                    ->all(),
                                    'id',
                                    'district_name'
                                );
                            echo  $form->field($model, 'district_id',['enableAjaxValidation' => true,'template' => '{label}<div class="form-select">{input}<div class="spinner-border text-success select_loader  align-self-center loader-sm "></div></div>{error}'])->dropDownList($allDistrictPjaxGet,[
                             'onchange'=>'
                        
                                $.pjax.reload({
                                url: "'.Url::to(['/routers/create']).'?city_id='.Yii::$app->request->get('city_id').'&dis_id="+$(this).val(),
                                container: "#pjax-router-form-dis",
                                timeout: 5000
                                });
                       
                            ',
                            'prompt'=>Yii::t("app","Select")]);
                              foreach ($form->attributes as $attribute) {
                                  $attribute = Json::htmlEncode($attribute);
                                  $this->registerJs("jQuery('form#router-form').yiiActiveForm('add', $attribute); ");
                              } 

                        }else{
                            echo  $form->field($model, 'district_id')->dropDownList([''=>''],['prompt'=>Yii::t("app","Select")]);
                        }
                    ?>
                <?php Pjax::end(); ?>
            </div>

            <div class="col-sm-6">
                <?php  Pjax::begin(['id'=>'pjax-router-form-dis','enablePushState'=>true]);  ?>
                  <?php if (Yii::$app->request->get('city_id') && Yii::$app->request->get('dis_id') && Yii::$app->request->isPjax): ?>
                        <?= $form->field($model, 'location_id')->dropDownList(
                            ArrayHelper::map(
                                \app\models\Locations::find()
                                ->where(['city_id'=>Yii::$app->request->get("city_id"),"district_id"=>Yii::$app->request->get("dis_id")])
                                ->withByLocationId()
                                ->orderBy(['name'=>SORT_ASC])
                                ->all(),
                                'id',
                                'name'
                            ),['prompt'=>Yii::t("app","Select")]
                    ); 
                    ?>
                            <?php 
                                foreach ($form->attributes as $attribute) {
                                    $attribute = Json::htmlEncode($attribute);
                                    $this->registerJs("jQuery('form#router-form').yiiActiveForm('add', $attribute); ");
                                } 
                             ?>
                  <?php else: ?>
                        <?= $form->field($model, 'location_id')->dropDownList(['prompt'=>Yii::t("app","Select")]); ?>

                  <?php endif ?>
                <?php Pjax::end(); ?>  
            </div>

        <?php else: ?>

        <div class="col-sm-6">
          <?=$form->field($model, 'city_id')->dropDownList(ArrayHelper::map(\app\models\Cities::find()->all(),'id','city_name'),[
             'onchange'=>'
                
                $.pjax.reload({
                url: "'.Url::to(['/routers/update']).'?id='.$model->id.'&type='.Yii::$app->request->get('type').'&city_id="+$(this).val(),
                container: "#pjax-users-form",
                timeout: 5000
                });
          
            ',
            'prompt'=>'Select City'])->label("City") ?>
        </div>
        <div class="col-sm-6">
            <?php  Pjax::begin(['id'=>'pjax-users-form','enablePushState'=>true]);  ?>

            <?php 

            if (Yii::$app->request->get('city_id')) {
                 echo  $form->field($model, 'district_id')->dropDownList(ArrayHelper::map(\app\models\District::find()
                        ->where(['city_id'=>Yii::$app->request->get('city_id')])->all(),'id','district_name'),[
                 'onchange'=>'
                    $.pjax.reload({
                    url: "'.Url::to(['/routers/update']).'?id='.$model->id.'&type='.Yii::$app->request->get('type').'&city_id='.Yii::$app->request->get('city_id').'&district_id="+$(this).val(),
                    container: "#pjax-users-form-dis",
                    timeout: 5000
                    });

                ',
                'prompt'=>'Select District']);
            }else{
               echo  $form->field($model, 'district_id')->dropDownList(ArrayHelper::map(\app\models\District::find()
                        ->where(['city_id'=>Yii::$app->request->get('city_id')])->all(),'id','district_name'),[
                 'onchange'=>'
                    $(".select_loader").show();
                    $.pjax.reload({
                    url: "'.Url::to(['/routers/update']).'?id='.$model->id.'&type='.Yii::$app->request->get('type').'&city_id='.Yii::$app->request->get('city_id').'&district_id='.Yii::$app->request->get('district_id').'",
                    container: "#pjax-users-form-dis",
                    timeout: 5000
                    });
                    $(document).on("pjax:complete", function() {
                      $(".select_loader").hide();
                    });
                ',
                'prompt'=>'Select District'])->label("District");  
            }

             ?>

            <?php Pjax::end(); ?>  
        </div>
          <div class="col-sm-6">
            <?php  Pjax::begin(['id'=>'pjax-users-form-dis','enablePushState'=>true]);  ?>
                <?= $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(\app\models\Locations::find()
                            ->where(['city_id'=>Yii::$app->request->get('city_id')])->andWhere(['district_id'=>Yii::$app->request->get('district_id')])->all(),'id','name'),['prompt'=>Yii::t('app','Select')]) ?>
            <?php Pjax::end(); ?>  
          </div>
        <?php endif ?>

        <div class="col-sm-6">
             <?= $form->field($model, 'ip_pool_var')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'interface')->textInput(['maxlength' => true]) ?>
        </div>  
 
        <div class="col-sm-6">
            <?= $form->field($model, 'nas')->textInput(['maxlength' => true]) ?>
        </div> 
        <div class="col-sm-6">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <div class="router-password-container" >
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
                <div  id="show-router-password">
                    <?=Yii::t('app','Show password') ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
             <?php $routerList = ( $model->isNewRecord ) ? \app\models\Routers::find()->where(['parent'=>0])->asArray()->all() : \app\models\Routers::find()->where(['parent'=>0])->andWhere(['!=', 'id', Yii::$app->request->get('id')])->asArray()->all() ?>
              <?= $form->field($model, 'parent')->dropDownList(
               ArrayHelper::merge(['0'=>Yii::t('app','Mother router')], ArrayHelper::map($routerList,'id','name')),
                ['prompt' => Yii::t('app','Select')]
            ) ?>
       
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'cordinate',['inputOptions' => ['placeholder'=>Yii::t('app','Cordinate')]])->textInput(['maxlength' => true,'class' => 'form-control']) ?>
                <div id='map'>
                    <h2><?=Yii::t('app','Allow location') ?></h2>
                </div>
                <div class="searchbox">
                  <input id="pac-input" class="controls " type="text" placeholder="<?=Yii::t("app","Search...") ?>"/>  
                </div>
        </div>
    </div>
   
    <?php if ($model->isNewRecord): ?>
        
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php else: ?>
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>  
    <?php endif ?>

    <?php ActiveForm::end(); ?>

</div>
<?php 

$this->registerJs('

$("#show-router-password").on("click",(e)  => {
	let password_input = $("#routers-password");
	let text = $("#show-router-password");
	let password_input_type = $("#routers-password").attr("type");

	if(password_input_type == "password"){
		password_input.attr("type","text")
		text.text("'.Yii::t('app','Hide password').'")
	}else{
		password_input.attr("type","password");
		text.text("'.Yii::t('app','Show password').'")
	}
});


');


 ?>

 <style type="text/css">
 	.router-password-container{
 		position: relative;
 	}
 	.router-password-container #show-router-password{
		color: #000000bf; position: absolute; right: 35px; top: 37px; cursor: pointer; text-align: center; z-index: 9;
 	}
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
if ( $cordinates != null ) {
    $latitude = explode(",",$model['cordinate'])[0];
    $longitude = explode(",",$model['cordinate'])[1];
}else{
    $latitude = 'null';
    $longitude = 'null';
}

$this->registerJs("
var defaultLocation = {
    lat: ".$latitude.",
    lng: ".$longitude."
};
var searchBox
var placeMarkers = [];


getLocation();


function getLocation() {
    if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(geoSuccess, geoError);
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

function geoError(error) {
    console.log(error)
    alert('Geocoder failed.');
}

function geoSuccess(position) {
     const pos = {
        lat: position.coords.latitude,
        lng: position.coords.longitude,
      };

      if( defaultLocation.lat == null && defaultLocation.lng == null ){
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 20,
                center: new google.maps.LatLng(pos.lat,pos.lng),
                mapTypeId: google.maps.MapTypeId.HYBRID
            });

            var myMarker = new google.maps.Marker({
                position: new google.maps.LatLng(pos.lat,pos.lng),
                draggable: true
            });

            $('#nas-cordinate').val( pos.lat.toFixed(6) + ',' + pos.lng.toFixed(6));
              google.maps.event.addListener(myMarker, 'dragend', function (evt) {
                    $('#nas-cordinate').val( evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
                    var point = myMarker.getPosition();
                     map.setCenter(point); // setCenter takes a LatLng object
                     map.panTo(point);
                });


                google.maps.event.addListener(map,'idle',function(){
                  if(!this.get('dragging') && this.get('oldCenter') && this.get('oldCenter')!==this.getCenter()) {
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


            //~ initSearch();
            // Create the search box and link it to the UI element.
            input = (document.getElementById('pac-input'));
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);
            //

            searchBox = new google.maps.places.SearchBox( //var
                /** @type {HTMLInputElement} */
                (input));
            // Listen for the event fired when the user selects an item from the
            // pick list. Retrieve the matching places for that item.
            google.maps.event.addListener(searchBox, 'places_changed', function() {
                var places = searchBox.getPlaces();
                if (places.length == 0) {
                    return;
                }
                for (var i = 0, marker; marker = placeMarkers[i]; i++) {
                    marker.setMap(null);
                }
                // For each place, get the icon, place name, and location.
                placeMarkers = [];
                var bounds = new google.maps.LatLngBounds();
                for (var i = 0, place; place = places[i]; i++) {
                    var image = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };
                    // Create a marker for each place.
             
                bounds.extend(place.geometry.location);
            
                $('#nas-cordinate').val( place.geometry.location.lat().toFixed(6) + ',' + place.geometry.location.lng().toFixed(6));
                var point = myMarker.getPosition();
                 map.setCenter(point); // setCenter takes a LatLng object
                 map.panTo(point);
                 myMarker.setPosition(place.geometry.location);

                }
                map.fitBounds(bounds);
        });
        // Bias the SearchBox results towards places that are within the bounds of the
        // current map's viewport.
        google.maps.event.addListener(map, 'bounds_changed', function() {
        var bounds = map.getBounds();
        searchBox.setBounds(bounds);

        }); //////////////////////




                map.setCenter(myMarker.position);
                myMarker.setMap(map);
                map.setCenter(pos);
      }else{
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 20,
                center: new google.maps.LatLng(defaultLocation.lat,defaultLocation.lng),
                mapTypeId: google.maps.MapTypeId.HYBRID
            });

            var myMarker = new google.maps.Marker({
                position: new google.maps.LatLng(defaultLocation.lat,defaultLocation.lng),
                draggable: true
            });

            $('#nas-cordinate').val( defaultLocation.lat.toFixed(6) + ',' + defaultLocation.lng.toFixed(6));
            google.maps.event.addListener(myMarker, 'dragend', function (evt) {
                $('#nas-cordinate').val( evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
                var point = myMarker.getPosition();
                 map.setCenter(point); // setCenter takes a LatLng object
                 map.panTo(point);
            });


            google.maps.event.addListener(map,'idle',function(){
              if(!this.get('dragging') && this.get('oldCenter') && this.get('oldCenter')!==this.getCenter()) {
                //do what you want to
                  myMarker.setPosition(this.getCenter());

              }
              if(!this.get('dragging')){
               // this.set('oldCenter',this.getCenter())
              }

            });

            //~ initSearch();
            // Create the search box and link it to the UI element.
            input = (document.getElementById('pac-input'));
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);
            //

            searchBox = new google.maps.places.SearchBox( //var
                /** @type {HTMLInputElement} */
                (input));
            // Listen for the event fired when the user selects an item from the
            // pick list. Retrieve the matching places for that item.
            google.maps.event.addListener(searchBox, 'places_changed', function() {
                var places = searchBox.getPlaces();
                if (places.length == 0) {
                    return;
                }
                for (var i = 0, marker; marker = placeMarkers[i]; i++) {
                    marker.setMap(null);
                }
                // For each place, get the icon, place name, and location.
                placeMarkers = [];
                var bounds = new google.maps.LatLngBounds();
                for (var i = 0, place; place = places[i]; i++) {
                    var image = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };
                    // Create a marker for each place.
             
                    bounds.extend(place.geometry.location);
                console.log(place.geometry.location)
                $('#nas-cordinate').val( place.geometry.location.lat().toFixed(6) + ',' + place.geometry.location.lng().toFixed(6));
                var point = myMarker.getPosition();
                 map.setCenter(point); // setCenter takes a LatLng object
                 map.panTo(point);
                 myMarker.setPosition(place.geometry.location);

                }
                map.fitBounds(bounds);
        });
        // Bias the SearchBox results towards places that are within the bounds of the
        // current map's viewport.
        google.maps.event.addListener(map, 'bounds_changed', function() {
        var bounds = map.getBounds();
        searchBox.setBounds(bounds);

        }); //////////////////////



            google.maps.event.addListener(map,'dragstart',function(){
              this.set('dragging',true);          
            });

            google.maps.event.addListener(map,'dragend',function(){
              this.set('dragging',false);
              google.maps.event.trigger(this,'idle',{});
            });

            map.setCenter(myMarker.position);
            myMarker.setMap(map);
            map.setCenter(defaultLocation);
      }


}

$('#location-form').on('keyup keypress', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) { 
    e.preventDefault();
    return false;
  }
});
");

 ?>
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
#pac-input{
    padding: 10px;
    font-size: 14px;
    width: 20%;
    height: 36px;
    z-index: 9999;
    line-height: 14px;
}
</style>

<?php

use yii\helpers\ArrayHelper;
use app\models\Cities;
use app\models\Routers;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
/* @var $this yii\web\View */
/* @var $model app\models\Location */
/* @var $form yii\widgets\ActiveForm */
 $this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].'&libraries=places', ['depends' => [yii\web\JqueryAsset::className()]]);
?>

 
<?php $form = ActiveForm::begin(['id'=>'district-form']); ?>
    <?php 
    $data = ArrayHelper::map(
    Cities::find()
    ->all()
    ,'id',
    'city_name'
    );
        
    echo $form->field($model, 'city_id')->widget(Select2::classname(), [
        'data' => $data,
        'language' => 'en',
        'options' => ['placeholder' => Yii::t('app','Select')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
     ?>
    <?= $form->field($model, 'district_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'device_registration')->dropDownList(\app\models\District::getDistrictUserRegistrationOnDeviceStatus(),['prompt'=>Yii::t("app","Select")]) ?>

    <?=$form->field($model, 'cordinate',['inputOptions' => ['placeholder'=>Yii::t('app','Cordinate')]])->textInput(['maxlength' => true,'class' => 'form-control']) ?>
    <div id='map'>
        <h2><?=Yii::t('app','Allow location') ?></h2>
    </div>
    <div class="searchbox">
      <input id="pac-input" class="controls " type="text" placeholder="<?=Yii::t("app","Search...") ?>"/>  
    </div>

    <?php if ($model->isNewRecord): ?>
         <div class="form-group">
            <?= $form->field($model, 'created_at')->hiddenInput(['value' => time() ])->label(false) ?>
            <?= Html::submitButton(Yii::t('app','Create'), ['class' => 'btn btn-success']) ?>
        </div>
    <?php else: ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-primary']) ?>
        </div> 
    <?php endif ?>
<?php ActiveForm::end(); ?>


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
var searchBox
var placeMarkers = [];

var defaultLocation = {
    lat: ".$latitude.",
    lng: ".$longitude."
};

    getLocation();
$('#modal').on('shown.bs.modal', function () {
});

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
                zoom: 15,
                center: new google.maps.LatLng(pos.lat,pos.lng),
                mapTypeId: google.maps.MapTypeId.HYBRID
            });

            var myMarker = new google.maps.Marker({
                position: new google.maps.LatLng(pos.lat,pos.lng),
                draggable: true
            });

            $('#district-cordinate').val( pos.lat.toFixed(6) + ',' + pos.lng.toFixed(6));
              google.maps.event.addListener(myMarker, 'dragend', function (evt) {
                    $('#district-cordinate').val( evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
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
                console.log(place.geometry.location)
                $('#district-cordinate').val( place.geometry.location.lat().toFixed(6) + ',' + place.geometry.location.lng().toFixed(6));
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
      }else{
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: new google.maps.LatLng(defaultLocation.lat,defaultLocation.lng),
                mapTypeId: google.maps.MapTypeId.HYBRID
            });

            var myMarker = new google.maps.Marker({
                position: new google.maps.LatLng(defaultLocation.lat,defaultLocation.lng),
                draggable: true
            });

            $('#district-cordinate').val( defaultLocation.lat.toFixed(6) + ',' + defaultLocation.lng.toFixed(6));
            google.maps.event.addListener(myMarker, 'dragend', function (evt) {
                $('#district-cordinate').val( evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
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
                $('#district-cordinate').val( place.geometry.location.lat().toFixed(6) + ',' + place.geometry.location.lng().toFixed(6));
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

$('#district-form').on('keyup keypress', function(e) {
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
#district-form{width: :100%;}

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
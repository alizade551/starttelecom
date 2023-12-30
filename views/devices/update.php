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
$this->title = Yii::t('app','Update : {device_name}',['device_name'=>$model->name]);

?>

<?php $form = ActiveForm::begin([
        'id'=>"update-device-forum",
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
                            url: "'.Url::to(['/devices/update?id=']).$model->id.'&city_id='.Yii::$app->request->get('city_id').'&dis_id='.Yii::$app->request->get('dis_id').'&type="+$(this).val(),
                            container: "#pjax-update-device-form",
                            timeout: 5000
                        });
                        $(document).on("pjax:complete", function() {
                          $(".select_loader").hide();
                        });
                    ',
                   
                ]
            ) ?>
        </div>
        <div class="col-sm-6">
            <?php  Pjax::begin(['id'=>'pjax-update-device-form','enablePushState'=>true]);  ?>

                <?php if (Yii::$app->request->get('type') == "switch"): ?>
                    <?= $form->field($model, 'port_count')->dropDownList(
                        ArrayHelper::merge([''=>Yii::t('app','Select')],\app\models\Devices::getPortCount())
                    ) ?>
                   

                    <?php 
                        if (Yii::$app->request->isPjax) {
                            foreach ($form->attributes as $attribute) {
                            $attribute = Json::htmlEncode($attribute);
                            $this->registerJs("jQuery('form#update-device-forum').yiiActiveForm('add', $attribute); ");
                            } 
                        }
                    ?>
                <?php endif ?>

                <?php if (Yii::$app->request->get('type') == "epon" || Yii::$app->request->get('type') == "gpon"  || Yii::$app->request->get('type') == "xpon"  ): ?>
                    <?= $form->field($model, 'pon_port_count')->dropDownList(
                        ArrayHelper::merge([''=>Yii::t('app','Select')],\app\models\Devices::getPonPortCount())
                    ) ?>
                    <?php 
                    
                        if (Yii::$app->request->isPjax) {
                            foreach ($form->attributes as $attribute) {
                            $attribute = Json::htmlEncode($attribute);
                            $this->registerJs("jQuery('form#update-device-forum').yiiActiveForm('add', $attribute); ");
                            } 
                        }
                    ?>

                <?php endif ?>
            <?php Pjax::end(); ?>  
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'ip_address')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary update-device']) ?>
    </div>
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

            $('#devices-cordinate').val( pos.lat.toFixed(6) + ',' + pos.lng.toFixed(6));
              google.maps.event.addListener(myMarker, 'dragend', function (evt) {
                    $('#devices-cordinate').val( evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
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
            
                $('#devices-cordinate').val( place.geometry.location.lat().toFixed(6) + ',' + place.geometry.location.lng().toFixed(6));
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

            $('#devices-cordinate').val( defaultLocation.lat.toFixed(6) + ',' + defaultLocation.lng.toFixed(6));
            google.maps.event.addListener(myMarker, 'dragend', function (evt) {
                $('#devices-cordinate').val( evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
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
                $('#devices-cordinate').val( place.geometry.location.lat().toFixed(6) + ',' + place.geometry.location.lng().toFixed(6));
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

$('#update-device-forum').on('keyup keypress', function(e) {
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




<?php $this->registerJs('

var clickUpdateDevice = false;
var xhrUpdateDevice;
var xhrActiveUpdateDevice=false;
var formUpdateDevice = $("form#update-device-forum");

$("form#update-device-forum").on("beforeSubmit", function (e) {
    if(!clickUpdateDevice){

        clickUpdateDevice = true;
        if( formUpdateDevice.find(".update-device").prop("disabled")){
            return false;
        }
        if(xhrActiveUpdateDevice) { xhrUpdateDevice.abort(); }
        xhrActiveUpdateDevice = true;
        formUpdateDevice.find(".btn-primary").prop("disabled",true);

        xhrUpdateDevice = $.ajax({
          url: "'.\yii\helpers\Url::to(["devices/update?id="]).$model->id.'",
          type: "post",
          beforeSend:function(){
            $(".loader").show();
            $(".overlay").addClass("show");
          },
          data: formUpdateDevice.serialize(),
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
                xhrActiveUpdateDevice=false;
                formUpdateDevice.find(".add-device").prop("disabled",false);
            }

          }
        }).done(function(){ clickUpdateDevice = false; });
        return false;


    }

}); 
 
') ?>
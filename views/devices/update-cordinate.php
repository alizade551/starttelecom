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
$this->title = Yii::t('app','Update cordinates : {device_name}',['device_name'=>$model->name]);
$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].'&libraries=places', ['depends' => [yii\web\JqueryAsset::className()]]);

?>
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item parent">
            <a href="/">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 511 511.999" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M498.7 222.695c-.016-.011-.028-.027-.04-.039L289.805 13.81C280.902 4.902 269.066 0 256.477 0c-12.59 0-24.426 4.902-33.332 13.809L14.398 222.55c-.07.07-.144.144-.21.215-18.282 18.386-18.25 48.218.09 66.558 8.378 8.383 19.44 13.235 31.273 13.746.484.047.969.07 1.457.07h8.32v153.696c0 30.418 24.75 55.164 55.168 55.164h81.711c8.285 0 15-6.719 15-15V376.5c0-13.879 11.293-25.168 25.172-25.168h48.195c13.88 0 25.168 11.29 25.168 25.168V497c0 8.281 6.715 15 15 15h81.711c30.422 0 55.168-24.746 55.168-55.164V303.14h7.719c12.586 0 24.422-4.903 33.332-13.813 18.36-18.367 18.367-48.254.027-66.633zm-21.243 45.422a17.03 17.03 0 0 1-12.117 5.024H442.62c-8.285 0-15 6.714-15 15v168.695c0 13.875-11.289 25.164-25.168 25.164h-66.71V376.5c0-30.418-24.747-55.168-55.169-55.168H232.38c-30.422 0-55.172 24.75-55.172 55.168V482h-66.71c-13.876 0-25.169-11.29-25.169-25.164V288.14c0-8.286-6.715-15-15-15H48a13.9 13.9 0 0 0-.703-.032c-4.469-.078-8.66-1.851-11.8-4.996-6.68-6.68-6.68-17.55 0-24.234.003 0 .003-.004.007-.008l.012-.012L244.363 35.02A17.003 17.003 0 0 1 256.477 30c4.574 0 8.875 1.781 12.113 5.02l208.8 208.796.098.094c6.645 6.692 6.633 17.54-.031 24.207zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
            </a>
        </li>
        <li class="breadcrumb-item parent" aria-current="page">
           <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M256 6C117.979 6 6 117.98 6 256s111.979 250 250 250c138.02 0 250-111.979 250-250S394.02 6 256 6zm180.424 392.633c-22.507-13.043-47.881-22.603-73.141-29.25C370.897 337.622 375.23 302.496 375.9 266h109.879c-2.103 48.273-19.388 94.754-49.355 132.633zm-360.847 0C45.61 360.754 28.325 314.273 26.221 266H136.1c.67 36.496 5.003 71.622 12.617 103.383-25.446 6.819-50.169 15.92-73.14 29.25zm-.001-285.266c22.427 12.976 47.584 22.52 73.141 29.251-7.615 31.76-11.947 66.886-12.617 103.382H26.221c2.104-48.274 19.389-94.754 49.355-132.633zM266 356.121V266h89.898c-.664 34.999-4.807 68.575-12.067 98.813-23.761-4.95-50.192-8.076-77.831-8.692zm-97.831 8.692c-7.26-30.237-11.404-63.814-12.067-98.813H246v90.121c-27.307.609-53.566 3.645-77.831 8.692zM246 155.879V246h-89.898c.663-34.999 4.807-68.575 12.067-98.812 24.353 5.044 50.306 8.077 77.831 8.691zM266 246v-90.121c26.884-.6 53.011-3.542 77.831-8.691 7.261 30.237 11.403 63.813 12.067 98.812zm0-110.126V27.267c21.479 5.362 42.433 27.722 58.691 63.194 5.281 11.522 9.935 24.061 13.938 37.406-23.94 4.882-48.499 7.464-72.629 8.007zM246 27.267v108.607c-24.646-.555-49.21-3.227-72.629-8.007 4.003-13.345 8.657-25.884 13.938-37.406C203.567 54.988 224.52 32.628 246 27.267zm0 348.859v108.607c-21.48-5.362-42.433-27.722-58.691-63.194-5.281-11.522-9.935-24.061-13.938-37.406 23.561-4.783 47.859-7.449 72.629-8.007zm20 108.607V376.126c25.926.583 50.03 3.416 72.629 8.007-4.003 13.346-8.656 25.884-13.938 37.406-16.258 35.473-37.212 57.832-58.691 63.194zM375.9 246c-.67-36.496-5.003-71.622-12.617-103.382 25.307-6.699 50.592-16.172 73.141-29.251 29.967 37.879 47.252 84.359 49.355 132.633zm47.166-148.059c-20.134 11.223-42.371 19.449-64.933 25.39-9.359-31.64-24.537-66.811-47.328-90.703 42.611 10.446 81.633 32.943 112.261 65.313zM201.193 32.628c-22.791 23.892-37.968 59.064-47.327 90.703-22.45-5.908-44.646-14.099-64.933-25.39 30.63-32.37 69.65-54.867 112.26-65.313zM88.934 414.059c20.424-11.347 42.508-19.513 64.933-25.39 9.359 31.637 24.536 66.812 47.327 90.702-42.612-10.445-81.631-32.942-112.26-65.312zm221.872 65.313c22.764-23.863 37.95-59.002 47.328-90.703 22.172 5.818 44.581 14.061 64.933 25.39-30.629 32.37-69.65 54.867-112.261 65.313z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g>
          </svg>
        
        </li>

        <li class="breadcrumb-item parent" aria-current="page">
          <a href="/devices/index"> <?=Yii::t("app","Devices") ?></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><?=$this->title ?></li>
     </ol>
</nav>
<div class="row">
    <div class="col-sm-12">
        
        <div class="widget-content widget-content-area" style="padding: 15px;">
            <?php $form = ActiveForm::begin([
                    'id'=>"update-cordinate-forum",
                    'enableAjaxValidation' => true,
                    'validateOnSubmit'=> true,
                    'enableClientValidation'=>false,
                    'validationUrl' => 'update-cordinate-validate',
                    'options' => ['autocomplete' => 'off']]);
             ?>
            <div class="row">
                    <div class="col-sm-6">
                        <?=$form->field($model, 'city_id')->dropDownList(ArrayHelper::map(\app\models\Cities::find()->all(),'id','city_name'),[
                        'onchange'=>'
                            $.pjax.reload({
                                url: "'.Url::to(['/devices/update-cordinate']).'?id='.$model->id.'&city_id="+$(this).val(),
                                container: "#pjax-users-form",
                                timeout: 5000
                            });
                        ','prompt'=>'Select'])->label() ?>
                    </div>
                    <div class="col-sm-6">
                        <?php  Pjax::begin(['id'=>'pjax-users-form','enablePushState'=>true]);  ?>
                            <?php 
                                if (Yii::$app->request->get('city_id')) {
                                     echo  $form->field($model, 'district_id')->dropDownList(ArrayHelper::map(\app\models\District::find()
                                            ->where(['city_id'=>Yii::$app->request->get('city_id')])->all(),'id','district_name'),[
                                     'onchange'=>'
                                        $.pjax.reload({
                                        url: "'.Url::to(['/devices/update-cordinate']).'?id='.$model->id.'&city_id='.Yii::$app->request->get('city_id').'&dis_id="+$(this).val(),
                                        container: "#pjax-users-form-dis",
                                        timeout: 5000
                                        });

                                    ',
                                    'prompt'=>Yii::t('app','Select')]);
                                }else{
                                   echo  $form->field($model, 'district_id')->dropDownList(ArrayHelper::map(\app\models\District::find()
                                            ->where(['city_id'=>Yii::$app->request->get('city_id')])->all(),'id','district_name'),[
                                     'onchange'=>'
                                        $.pjax.reload({
                                            url: "'.Url::to(['/devices/cordinate-cordinate']).'?id='.$model->id.'&city_id='.Yii::$app->request->get('city_id').'&dis_id='.Yii::$app->request->get('dis_id').'",
                                            container: "#pjax-users-form-dis",
                                            timeout: 5000
                                        });
                                    ',
                                    'prompt'=>Yii::t('app','Select')])->label();  
                                }
                             ?>
                        <?php Pjax::end(); ?>  
                    </div>
                    <div class="col-sm-6">
                        <?php  Pjax::begin(['id'=>'pjax-users-form-dis','enablePushState'=>true]);  ?>
                            <?= $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(\app\models\Locations::find()
                                        ->where(['city_id'=>Yii::$app->request->get('city_id')])->andWhere(['district_id'=>Yii::$app->request->get('dis_id')])->all(),'id','name'),['prompt'=>Yii::t('app','Select')]) ?>
                        <?php Pjax::end(); ?>  
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'cordinate',['inputOptions' => ['placeholder'=>Yii::t('app','Cordinate')]])->textInput(['maxlength' => true,'class' => 'form-control'])->label(Yii::t('app','Cordinate')) ?>
                    </div>
                    <div class="col-sm-12">
                        <div id='map'>
                            <h2><?=Yii::t('app','Allow location') ?></h2>
                        </div>
                        <div class="searchbox">
                          <input id="pac-input" class="controls " type="text" placeholder="<?=Yii::t("app","Search...") ?>"/>  
                        </div>
                        
                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary update-device']) ?>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>

        </div>
    </div>
</div>


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

$('#update-cordinate-forum').on('keyup keypress', function(e) {
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
var formUpdateDevice = $("form#update-cordinate-forum");

$("form#update-cordinate-forum").on("beforeSubmit", function (e) {
    if(!clickUpdateDevice){

        clickUpdateDevice = true;
        if( formUpdateDevice.find(".update-device").prop("disabled")){
            return false;
        }
        if(xhrActiveUpdateDevice) { xhrUpdateDevice.abort(); }
        xhrActiveUpdateDevice = true;
        formUpdateDevice.find(".btn-primary").prop("disabled",true);

        xhrUpdateDevice = $.ajax({
          url: "'.\yii\helpers\Url::to(["devices/update-cordinate?id="]).$model->id.'",
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
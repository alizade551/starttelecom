<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;


$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].'', ['depends' => [yii\web\JqueryAsset::className()]]);
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

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
</style>

<?php $form = ActiveForm::begin([
        'id'=>'cordinate-update-form',
        'layout' => 'horizontal',
        'enableAjaxValidation' => true,
        'validateOnSubmit'=> true,
        'enableClientValidation'=>false,
        'validationUrl' => $langUrl.'/users/add-cordinate-validate?id='.$model->id,
        'options' => ['autocomplete' => 'off']
]); ?>

 
    <?= $form->field($model, 'cordinate',['inputOptions' => ['placeholder'=>Yii::t('app','Cordinate')]])->textInput(['maxlength' => true,'class' => 'form-control']) ?>
    <div class="form-group">
        <a id="get-current-cordinate"  class="btn btn-primary"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg> <?=Yii::t('app','Get current cordinate') ?> </a>       
    </div>
    <div id='map'>
        <h2><?=Yii::t('app','Allow location') ?></h2>
    </div>

    <?php if ( $model->cordinate == null ): ?>
         <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Add'), ['class' => 'btn btn-success']) ?>
        </div>
    <?php else: ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Update'), ['class' => 'btn btn-primary']) ?>
        </div> 
    <?php endif ?>

<?php ActiveForm::end(); ?>

<?php 
$cordinates =  ( $model['cordinate']  !== null ) ?  explode(",",$model['cordinate']) : null;
$districtCordinates =  ( $districtModel['cordinate']  !== null ) ?  explode(",",$districtModel['cordinate']) : null;
if ( $cordinates != null ) {
    $latitude = explode(",",$model['cordinate'])[0];
    $longitude = explode(",",$model['cordinate'])[1];
}else{
    $latitude = 'null';
    $longitude = 'null';
}


if ( $districtCordinates != null ) {
    $districtLatitude = explode(",",$districtModel['cordinate'])[0];
    $districtLongitude = explode(",",$districtModel['cordinate'])[1];
}else{
    $districtLatitude = 'null';
    $districtLongitude = 'null';
}

$this->registerJs("
var defaultLocation = {
    lat: ".$latitude.",
    lng: ".$longitude."
};

var districtlocation = {
    lat: ".$districtLatitude.",
    lng: ".$districtLongitude."
}


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

        initMap( pos )
}

$('#modal').on('shown.bs.modal', function () {
    if( defaultLocation.lat == null && defaultLocation.lng == null ){
        initMap( districtlocation )
    }else{
        initMap( defaultLocation )
    }
});

$('#get-current-cordinate').on('click',function(){
    getLocation()
});





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
        console.log( cordinates )
        $('#users-cordinate').val( cordinates.lat.toFixed(6) + ',' + cordinates.lng.toFixed(6));
          google.maps.event.addListener(myMarker, 'dragend', function (evt) {
                $('#users-cordinate').val( evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
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
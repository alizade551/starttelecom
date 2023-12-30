<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
/* @var $this yii\web\View */
/* @var $model app\models\Locations */
/* @var $form yii\widgets\ActiveForm */
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
 $this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].'&libraries=places&callback=initMap', ['depends' => [yii\web\JqueryAsset::className()]]);

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


<?php $form = ActiveForm::begin([
        'id'=>'location-form',
        'enableAjaxValidation' => true,
        'validateOnSubmit'=> true,
        'enableClientValidation'=>false,
        'validationUrl' => $langUrl.'/locations/create',
        'options' => ['autocomplete' => 'off']
]); ?>


 <?php if ($model->isNewRecord): ?>
     <?php 
            $AllCities = ArrayHelper::map(
                \app\models\Cities::find()
                ->all(),
                'id',
                'city_name'
            );
        
            echo $form->field($model, 'city_id')->widget(Select2::classname(), [
                'data' => $AllCities,
                'options' => ['placeholder' => Yii::t('app','Select')],
                'language' => 'en',
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'pluginEvents'=>["change" => "function() { 
                    var that = $(this);

                        $.pjax.reload({
                        url:'".Url::to('/locations/create')."?city_id='+that.val(),
                        container: '#location-form-pjax',
                        timeout: 5000
                        });

                 }",]
            ]);
     ?>
    <?php  Pjax::begin(['id'=>'location-form-pjax','enablePushState'=>true]);  ?>
    <?php 

            $AllDistrict = ArrayHelper::map(
                \app\models\District::find()
                ->andWhere(['city_id'=>Yii::$app->request->get("city_id")])
                ->all(),
                'id',
                'district_name'
            );
            

            echo $form->field($model, 'district_id')->widget(Select2::classname(), [
                'data' =>$AllDistrict ,
                'options' => ['placeholder' => Yii::t('app','Select')],
                'language' => 'en',
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'pluginEvents'=>["change" => "function() { console.log('change'); }",]
            ]);

     ?>

            <?php Pjax::end(); ?>      
         <?php else: ?>
            <?php 
                if (Yii::$app->request->get('city_id')) {
                $this->registerJs("
                   $.pjax.reload({
                    url:'".Url::to('/locations/update')."?id=".$model->id."&city_id='+".Yii::$app->request->get('city_id').",
                    container: '#location-form-pjax',
                    timeout: 5000
                    });

                ");
            }

    
                $AllCities = ArrayHelper::map(
                    \app\models\Cities::find()
                    ->all(),
                    'id',
                    'city_name'
                );
            

            echo $form->field($model, 'city_id')->widget(Select2::classname(), [
                'data' => $AllCities,
                'options' => ['placeholder' => Yii::t('app','Select')],
                'language' => 'en',
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'pluginEvents'=>["change" => "function() { 
                    var that = $(this);
              
                        $.pjax.reload({
                            url:'".Url::to('/locations/update')."?id=".$model->id."&city_id='+that.val(),
                            container: '#location-form-pjax',
                            timeout: 5000
                        });

                 }",]
            ]);

     ?>
    <?php  Pjax::begin(['id'=>'location-form-pjax','enablePushState'=>true]);  ?>
        <?php 

                $AllDistrict = ArrayHelper::map(
                    \app\models\District::find()
                    ->andWhere(['city_id'=>Yii::$app->request->get("city_id")])
                    ->all(),
                    'id',
                    'district_name'
                );
            
            echo $form->field($model, 'district_id')->widget(Select2::classname(), [
                'data' => $AllDistrict,
                'value'=>$model->district_id,
                'options' => ['placeholder' => Yii::t('app','Select')],
                'language' => 'en',
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'pluginEvents'=>["change" => "function() { console.log('change'); }",]
            ]);

         ?>
        <?php Pjax::end(); ?>       
     <?php endif ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'cordinate',['inputOptions' => ['placeholder'=>Yii::t('app','Cordinate')]])->textInput(['maxlength' => true,'class' => 'form-control']) ?>
    <div id='map'>
        <h2><?=Yii::t('app','Allow location') ?></h2>
    </div>
    <div class="searchbox">
      <input id="pac-input" class="controls " type="text" placeholder="<?=Yii::t("app","Search...") ?>"/>  
    </div>

    <?php if ($model->isNewRecord): ?>
         <div class="form-group">
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
var defaultLocation = {
    lat: ".$latitude.",
    lng: ".$longitude."
};


if( defaultLocation.lat == null && defaultLocation.lng == null ){
    getLocation()
}else{
    initMap( defaultLocation )
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

function geoSuccess( position ) {
    const pos = {
        lat: position.coords.latitude,
        lng: position.coords.longitude,
    };
    initMap( pos )
}

function initMap ( pos ) {
    var searchBox
    var placeMarkers = [];

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 16,
        center: new google.maps.LatLng(pos.lat,pos.lng),
        mapTypeId: google.maps.MapTypeId.HYBRID
    });

    var myMarker = new google.maps.Marker({
        position: new google.maps.LatLng(pos.lat,pos.lng),
        draggable: true
    });


      google.maps.event.addListener(myMarker, 'dragend', function (evt) {
            $('#locations-cordinate').val( evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
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

    input = (document.getElementById('pac-input'));
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);


    searchBox = new google.maps.places.SearchBox( (input) );

    google.maps.event.addListener(searchBox, 'places_changed', function() {
        var places = searchBox.getPlaces();
        if (places.length == 0) {
            return;
        }
        for (var i = 0, marker; marker = placeMarkers[i]; i++) {
            marker.setMap(null);
        }

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
     
        bounds.extend(place.geometry.location);

   
        var point = myMarker.getPosition();
         map.setCenter(point); // setCenter takes a LatLng object
         map.panTo(point);
         myMarker.setPosition(place.geometry.location);

        }
        map.fitBounds(bounds);
    });
    google.maps.event.addListener(map, 'bounds_changed', function() {
            var bounds = map.getBounds();
            searchBox.setBounds(bounds);

    }); 

    map.setCenter(myMarker.position);
    myMarker.setMap(map);
    map.setCenter(pos);
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


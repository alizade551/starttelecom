<?php
use yii\helpers\Url;


$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
$this->registerJsFile('http://maps.google.com/maps/api/js?key='.$siteConfig['google_map_js_token'].'&sensor=false&v=3.21.5a&libraries=drawing&signed_in=true&libraries=places,drawing', ['depends' => [yii\web\JqueryAsset::className()]]);
?>

<div id="panel">
  	<div id="color-palette" style="display:none;"></div>
</div>
<div id="map"></div>

<?php if ( $model['cordinate'] == null ): ?>
  <h6><?=Yii::t('app','Please update district cordinate') ?></h6>
<?php else: ?>
<?php 
$locations = []; 

$centerLong = explode(",",$model['cordinate'])[0];
$centerLat  = explode(",",$model['cordinate'])[1];

?>

<?php foreach (\app\models\Locations::find()->where(['district_id'=>$model['id']])->asArray()->all() as $key => $user): ?>
  <?php 
  if ( $user['cordinate'] != null ) {

  $longitude = explode(",",$user['cordinate'])[0];
  $latitude = explode(",",$user['cordinate'])[1];

  array_push($locations, [$user['name'],$longitude,$latitude]);
  }


   ?> 
<?php endforeach ?>

<?php
if ( $model['polygon_cord'] != "" ) {
  $defaultCords = [];
  foreach (unserialize($model['polygon_cord']) as $key => $x) {
    $defaultCords[]=(array_map('floatval', explode(',', $x)));
  }
}else{
    $defaultCords = [];
}





$script = "
var drawingManager;
var selectedShape;
var colorButtons = {};
var lines = [];
var locations = ".json_encode($locations).";


var defaultCords = ".json_encode($defaultCords).";

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(initialize, geoError);
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

getLocation()

function geoError(error) {
    console.log(error)
    alert('Geocoder failed.');
}

function initialize(position) {
    const pos = {
        lat: position.coords.latitude,
        lng: position.coords.longitude,
    };

    $('#pac-input').hide()
}

function clearSelection() {
    if (selectedShape) {
        if (typeof selectedShape.setEditable == 'function') {
            selectedShape.setEditable(false);
        }
        selectedShape = null;
    }
}

function updateCurSelText(shape) {
defaultCords = []
    line = [];
    if (typeof selectedShape.getPath == 'function') {

        for (var i = 0; i < selectedShape.getPath().getLength(); i++) {
            line.push(selectedShape.getPath().getAt(i).toUrlValue());
        }
        drawingManager.setOptions({
            drawingControl: false
        });

        $.ajax({
            url: '".$langUrl.Url::to('/district/draw-coverage?id=').$model->id."',
            method: 'POST',
            data: {
                line: line
            },
            success: function(res) {
              // yazdir ekrana
            }
        });
    }
}

function setSelection(shape, isNotMarker) {
    clearSelection();
    selectedShape = shape;
    if (isNotMarker)
        shape.setEditable(true);
    updateCurSelText(shape);
}

function deleteSelectedShape() {
    if (selectedShape) {
        selectedShape.setMap(null);
    }
}




/////////////////////////////////////
var map; //= new google.maps.Map(document.getElementById('map'), {
// these must have global refs too!:
var placeMarkers = [];
var input;
var searchBox;
var curposdiv;
var curseldiv;


/////////////////////////////////////
function initialize(position) {


    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14,
        center: new google.maps.LatLng(".floatval($centerLong).",".floatval($centerLat)."),
        mapTypeId: google.maps.MapTypeId.HYBRID,
        disableDefaultUI: false,
        zoomControl: true
    });


    var infowindow = new google.maps.InfoWindow();
    var marker, i , content_=[];

    for (i = 0; i < locations.length; i++) { 

      var name_ =  locations[i][0];
      content_[i] = '<div class=\"g-d\"><ul><li><span class=\"gmk_\" >".Yii::t('app','Location')." : </span> <span class=\"gmk_value\">' +name_ +'</span> </li></ul></div>';

      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(content_[i])
          infowindow.open(map, marker);
        }
      })(marker, i));
    }


  var points = [];
  for (var i = 0; i < defaultCords.length; i++) {
    points.push({
      lat: defaultCords[i][0],
      lng: defaultCords[i][1]
    });
  }

  var poly = new google.maps.Polygon({
    paths: points,
    strokeColor: '#03a9f4',
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: '#03a9f4',
    fillOpacity: 0.35
  });
  poly.setMap(map);


    curposdiv = document.getElementById('curpos');
    curseldiv = document.getElementById('cursel');
    var polyOptions = {
        strokeWeight: 0,
        fillOpacity: 0.45,
        editable: false,
            draggable: true,

    };
    // Creates a drawing manager attached to the map that allows the user to draw
    // markers, lines, and shapes.
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        markerOptions: {
            draggable: true,
            editable: false,
        },
        polylineOptions: {
            editable: false
        },

        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
                google.maps.drawing.OverlayType.POLYGON,
            ]
        },

        polygonOptions: polyOptions,
        map: map
    });


  drawingManager.setDrawingMode(null);
  

    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {



      if (e.type != google.maps.drawing.OverlayType.MARKER) {
        var isNotMarker = (e.type != google.maps.drawing.OverlayType.MARKER);

        drawingManager.setDrawingMode(null);

        var newShape = e.overlay;
        newShape.type = e.type;
        google.maps.event.addListener(newShape, 'click', function() {

            setSelection(newShape, isNotMarker);
        });
        google.maps.event.addListener(newShape, 'drag', function() {
            updateCurSelText(newShape);
        });
        google.maps.event.addListener(newShape, 'dragend', function() {
            updateCurSelText(newShape);
        });


        google.maps.event.addListener(newShape.getPath(), 'set_at', function() {
            updateCurSelText(newShape);
        });

        google.maps.event.addListener(newShape.getPath(), 'insert_at', function() {
            updateCurSelText(newShape);
        });

        poly.setMap(null);

        setSelection(newShape, isNotMarker);
     }
    });




    // Clear the current selection when the drawing mode is changed, or when the
    // map is clicked.
    google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
    google.maps.event.addListener(map, 'click', clearSelection);
    google.maps.event.addDomListener(document.getElementById('delete-button'), 'click', deleteSelectedShape);


}
google.maps.event.addDomListener(window, 'load', initialize);
";
$this->registerJs($script);
?>
<style type="text/css">

     .widget-content-area{
        padding: 0;
        margin: 0;
        height: 100%;
        width: 100%;
      }
     #map, html, body {
        padding: 0;
        margin: 0;
        height: 100%;
        width: 100%;
      }
      #pac-input{
    padding: 10px;
    font-size: 14px;
    width: 250px;
    height: 36px;
    z-index: 9999;
    line-height: 14px;
      }
  #panel {
    width: 100%;
    font-size: 13px;
  }

      #color-palette {
        clear: both;
      }

      .color-button {
        width: 14px;
        height: 14px;
        font-size: 0;
        margin: 2px;
        float: left;
        cursor: pointer;
      }
 .badge {display: inline-block !important;}
 .map-container {width: 100%;}
 #map{width: 100%;height: 800px;}
 .g-d{padding: 20px 10px;display: block !important;}
 .g-d ul{padding: 0;margin: 0;list-style: none;}
 .gmk_,.gmk_value{color: black;}
 .gm-style-iw-d{overflow: hidden !important; }
 .gm-style .gm-style-iw-c {
    padding: 0 !important;
}
</style>
<?php endif ?>


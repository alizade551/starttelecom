<?php 
use webvimark\modules\UserManagement\models\User;

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].'', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->title = Yii::t('app','Mapping - {location}',['location'=>$location['name']]);
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
 ?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if (User::canRoute("/locations/index")): ?>
               <a title="<?=Yii::t('app','Warehouses') ?>" class="btn btn-primary" data-pjax="0" href="<?=$langUrl ?>/locations/index">
            <?=Yii::t("app","Locations") ?>
           </a>
            <?php endif?>
        </div>
    </div>
</div>
<div class="widget-content widget-content-area" style="padding: 15px;width: 100%;">

 <?php if ($location['cordinate'] != null): ?>
 <div class="map-container">
 	<div id="map"></div>
 </div>


 <style type="text/css">
 .badge {display: inline-block !important;}
 .map-container {width: 100%;}
 #map{width: 100%;height: 600px;}
 .g-d{padding: 20px 10px;}
 .g-d ul{padding: 0;margin: 0;list-style: none;}
 .gmk_,.gmk_value{color: black;}
 .gm-style-iw-d{overflow: hidden !important; }
 .gm-style .gm-style-iw-c {
    padding: 0 !important;
}
 </style>

 <?php 
$this->registerJs("

   var locations = ".json_encode($data).";



    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 16,
      center: new google.maps.LatLng(".explode(",",$location['cordinate'])[0].", ".explode(",",$location['cordinate'])[1]."),
      mapTypeId: google.maps.MapTypeId.HYBRID
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i , content_=[];

    for (i = 0; i < locations.length; i++) { 

        var name_ =  locations[i].name;
        var id_ =  locations[i].id;
     
        if( locations[i].type == 'user' ){
          content_[i] = '<div class=\"g-d\"><ul><li><span class=\"gmk_\" >".Yii::t('app','Customer')." : </span> <a href=\"/users/view?id='+id_+'\" > <span class=\"gmk_value\">' +name_ +'</span> </a> </li></ul></div>';
        }
        if( locations[i].type != 'user' ){
          content_[i] = '<div class=\"g-d\"><ul><li><span class=\"gmk_\" >".Yii::t('app','Name')." : </span>  <span class=\"gmk_value\">' +name_ +'</span>  </li></ul></div>';
        }




      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i].longitude, locations[i].latitude),
        map: map,
        icon: locations[i].icon,
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(content_[i])
          infowindow.open(map, marker);
        }
      })(marker, i));
    }

")

  ?>	
 <?php else: ?>
 	<h6 style="text-align:center;"><?=Yii::t("app","Please add cordinate of {location} location",['location'=>$location['name']]) ?></h6>
 <?php endif ?>
 </div>
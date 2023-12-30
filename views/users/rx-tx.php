<?php 
$this->title =Yii::t("app","Real time traffic monitoring for {user_fullname}",['user_fullname'=>$user_inet_model->user->fullname]);
$this->registerJsFile('https://code.highcharts.com/highcharts.js',
['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://code.highcharts.com/modules/exporting.js',
['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://code.highcharts.com/modules/export-data.js',
['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://code.highcharts.com/modules/accessibility.js',
['depends' => [\yii\web\JqueryAsset::className()]]);


$theme = explode("/", Yii::$app->getView()->theme->pathMap['@app/views'])[2];




 ?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=Yii::t("app","Real time traffic monitoring for {customer}",['customer'=>$user_inet_model->user->fullname]) ?> </h5> </div>
            <div>
                <a href="/users/view?id=<?=$user_inet_model->user->id ?>" style="margin-left: 10px;" class="btn btn-primary"><?=Yii::t("app","Back to customer {customer} page",['customer'=>$user_inet_model->user->fullname]) ?></a>
            </div>
        </div>
    </div>
</div>

 <div style="width:100%;">   
    <div class="widget-content widget-content-area " id="traffic-container" style="padding: 15px;width: 100%;">
      <div id="traffic" style="width:100%"></div>
    </div>
</div>




    
<?php 


$this->registerJs("
  var chart;
  function requestDatta(interface) {
    $.ajax({
      url: 'get-rx-tx?login=".$login."',
      datatype: 'json',
      success: function(data) {
        var midata = JSON.parse(data);
        if( midata.length > 0 ) {
          var TX=parseInt(midata[0].data);
          var RX=parseInt(midata[1].data);
          var x = (new Date()).getTime(); 
        
          chart.series[0].addPoint([x, TX], true);
          chart.series[1].addPoint([x, RX], true);




        }else{
          document.getElementById('traffic').innerHTML='- / -';
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) { 
       
      }       
    });
  } 

  $(document).ready(function() {

       const theme = '".$theme."';

    if(theme == 'dark'){
         Highcharts.setOptions({
          global: {
            useUTC: false
          },
          chart: {
            backgroundColor: '#0f1339',
          },
          credits: {
              enabled: false
          },
          legend: {
              itemStyle: {
                  color: '#fff',
                  fontWeight: 'bold'
              }
          },

            title: {
              style: {
                 color: '#fff',
               
              }
           },
            xAxis: {
              labels: {
              },
              title: {
                 style: {
                    color: '#fff',
                 }            
              }
           },
           yAxis: {
              labels: {
                 style: {
                    color: '#fff',
                 }
              },
              title: {
                 style: {
                    color: '#fff',
                 }            
              }
           },
        });     
    }else{
         Highcharts.setOptions({
            global: {
              useUTC: false
            },
            credits: {
                enabled: false
            }
          });
    }



        chart = new Highcharts.Chart({
         chart: {
        renderTo: 'traffic-container',
          animation: {
            duration: 1000,
            easing: 'easeOutBounce'
        },

        type: 'area', //line,//area
        
        events: {
          load: function () {
            setInterval(function () {
              requestDatta();
            }, 1000);
          }       
      }
     },
     title: {
      text: ''
     },
     xAxis: {
      type: 'datetime',
        tickPixelInterval: 200,
        maxZoom: 0,

     },
     yAxis: {
      minPadding: 0.2,
        maxPadding: 0.2,
        title: {
          text: '".Yii::t('app','Bit')."',
        }
     },
            series: [{
                name: '".Yii::t('app','TX - Download')."',
                data: [],
                color:'#6bfdee'
            }, {
                name: '".Yii::t('app','RX - Upload')."',
                data: [],
                color:'#c56dd3'
            }]
    });
  });
");

 ?>
<style type="text/css">
    *{-webkit-transition: all 0ms ease !important; transition: all 0ms ease!important;}

</style>
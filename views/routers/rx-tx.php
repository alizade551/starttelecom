<?php 
use webvimark\modules\UserManagement\models\User;


$this->title = Yii::t("app","Real time traffic monitoring for {routerName} router",['routerName'=>$model->name]);
$this->registerJsFile(Yii::$app->request->baseUrl.'js/highcharts.js',
['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl.'js/exporting.js',
['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl.'js/modules/export-data.js',
['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl.'js/accessibility.js',
['depends' => [\yii\web\JqueryAsset::className()]]);


$theme = explode("/", Yii::$app->getView()->theme->pathMap['@app/views'])[2];
 ?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <?php if (User::canRoute("/routers/index")): ?>
                <a class="btn btn-primary" data-pjax="0" href="/routers/index" title=" <?=Yii::t("app","Routers") ?>">
                    <?=Yii::t("app","Routers") ?>
                </a>
            <?php endif?>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-sm-6">
      <div class="panel panel-default" id="traffic-container" style="width: 100%;height: 600px;">
        <div id="traffic" style="width:100%"></div>
      </div>
  </div>
  <div class="col-sm-6">
    <div class="card custom-card" style="padding:0 10px;overflow-y: auto;max-height: 600px;">
        <div class="progress">
          <div id="dynamic" class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
            <span id="current-progress" ></span>
          </div>
        </div>
        <table id="w0" class="table table-striped table-bordered detail-view">
           <tbody>
              <tr>
                 <th><?=Yii::t('app','Board name') ?></th>
                 <td id="board-name"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','Build time') ?></th>
                 <td id="build-time"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','Uptime') ?></th>
                 <td id="uptime"><?=Yii::t('app','Loading') ?></td>
              </tr>

              <tr>
                 <th><?=Yii::t('app','CPU') ?></th>
                 <td id="cpu"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','CPU load') ?></th>
                 <td id="cpu-load"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','CPU count') ?></th>
                 <td id="cpu-count"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','CPU frequency') ?></th>
                 <td id="cpu-frequency"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','Factory software') ?></th>
                 <td id="factory-software"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','Free hdd space') ?></th>
                 <td id="free-hdd-space"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','Free memory') ?></th>
                 <td id="free-memory"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','Platform') ?></th>
                 <td id="platform"><?=Yii::t('app','Loading') ?></td>
              </tr>

              <tr>
                 <th><?=Yii::t('app','Total hdd space') ?></th>
                 <td id="total-hdd-space"><?=Yii::t('app','Loading') ?></td>
              </tr>
              <tr>
                 <th><?=Yii::t('app','Total memory') ?></th>
                 <td id="total-memory"><?=Yii::t('app','Loading') ?></td>
              </tr>
               <tr>
                 <th><?=Yii::t('app','Version') ?></th>
                 <td id="version"><?=Yii::t('app','Loading') ?></td>
              </tr>
                    <tr>
                       <th><?=Yii::t('app','Tx - Uptime') ?></th>
                       <td id="tx"><?=Yii::t('app','Loading') ?></td>
                    </tr>
                    <tr>
                       <th><?=Yii::t('app','Rx - Uptime') ?></th>
                       <td id="rx"><?=Yii::t('app','Loading') ?></td>
                    </tr>
        
                     <tr >
                        <th><?=Yii::t('app','Mac address') ?></th>
                        <td id="mac-address"><?=Yii::t('app','Loading') ?></td>
                    </tr>
                     <tr>
                        <th><?=Yii::t('app','type') ?></th>
                        <td id="type"><?=Yii::t('app','Loading') ?></td>
                    </tr>
                     <tr>
                        <th><?=Yii::t('app','mtu') ?></th>
                        <td id="mtu"><?=Yii::t('app','Loading') ?></td>
                    </tr>
                     <tr>
                        <th><?=Yii::t('app','actual-mtu') ?></th>
                        <td id="actual-mtu"><?=Yii::t('app','Loading') ?></td>
                    </tr>
                     <tr>
                        <th><?=Yii::t('app','l2mtu') ?></th>
                        <td id="l2mtu"><?=Yii::t('app','Loading') ?></td>
                    </tr>
                     <tr>
                        <th><?=Yii::t('app','max-l2mtu') ?></th>
                        <td id="max-l2mtu"><?=Yii::t('app','Loading') ?> </td>
                    </tr>
                  
                     <tr>
                        <th><?=Yii::t('app','last-link-up-time') ?></th>
                        <td id="last-link-up-time"><?=Yii::t('app','Loading') ?></td>
                    </tr>

                     <tr>
                        <th><?=Yii::t('app','link-downs') ?></th>
                        <td id="link-downs"><?=Yii::t('app','Loading') ?></td>
                    </tr>

                     <tr>
                        <th><?=Yii::t('app','rx-packet') ?></th>
                        <td id="rx-packet"><?=Yii::t('app','Loading') ?></td>
                    </tr>
                     <tr>
                        <th><?=Yii::t('app','tx-packet') ?></th>
                        <td id="tx-packet"><?=Yii::t('app','Loading') ?> </td>
                    </tr>
                     <tr>
                        <th><?=Yii::t('app','rx-drop') ?></th>
                         <td id="rx-drop"><?=Yii::t('app','Loading') ?></td>
                    </tr>

                     <tr>
                        <th><?=Yii::t('app','tx-queue-drop') ?></th>
                        <td id="tx-queue-drop"><?=Yii::t('app','Loading') ?></td>
                    </tr>

                     <tr>
                        <th><?=Yii::t('app','fp-rx-byte') ?></th>
                        <td id="fp-rx-byte"><?=Yii::t('app','Loading') ?></td>
                    </tr>
                     <tr>
                        <th><?=Yii::t('app','fp-tx-byte') ?></th>
                        <td id="fp-tx-byte"><?=Yii::t('app','Loading') ?></td>
                    </tr>
                     <tr>
                        <th><?=Yii::t('app','fp-rx-packet') ?></th>
                        <td id="fp-rx-packet"><?=Yii::t('app','Loading') ?></td>
                    </tr>  

                     <tr>
                        <th><?=Yii::t('app','fp-tx-packet') ?></th>
                        <td id="fp-tx-packet"><?=Yii::t('app','Loading') ?></td>
                    </tr>  
           </tbody>
        </table>
    </div>
  </div>
</div>

<style type="text/css">
.progress {
  margin: 10px 0;
}
.progress-bar-danger {
    background-color: red !important;
    font-size: 13px;
    padding-left: 3px;
}

</style>

<?php 
$this->registerJs("
  var getRouterTotalTrafficInterval = setInterval(function(){
    getRouterTrafficTotal()
    },2000)

  var getRouterTrafficTotal = () => {
    $.ajax({
        url: '".\yii\helpers\Url::to(["routers/router-total-traffic?id="]).$model['id']." ',
        type: 'post',
        success: function (response) {

          $('#tx').text(response['tx_byte']);
          $('#rx').text(response['rx_byte']);
          $('#mac-address').text(response['mac_address']);
          $('#type').text(response['type']);
          $('#mtu').text(response['mtu']);
          $('#actual-mtu').text(response['actual_mtu']);
          $('#l2mtu').text(response['l2mtu']);
          $('#max-l2mtu').text(response['max_l2mtu']);
          $('#last-link-up-time').text(response['last_link_up_time']);
          $('#link-downs').text(response['link_downs']);
          $('#rx-packet').text(response['rx_packet']);
          $('#tx-packet').text(response['tx_packet']);
          $('#rx-drop').text(response['rx_drop']);
          $('#tx-queue-drop').text(response['tx_queue_drop']);
          $('#fp-rx-byte').text(response['fp_rx_byte']);
          $('#fp-tx-byte').text(response['fp_tx_byte']);
          $('#fp-rx-packet').text(response['fp_rx_packet']);
          $('#fp-tx-packet').text(response['fp_tx_packet']);




        }
    }); 
  }

");



 ?>



<?php 
$this->registerJs("
  var getRouterInfoInterval = setInterval(function(){
    getRouterInfo()
    },2000)

  var getRouterInfo = () => {
    $.ajax({
        url: '".\yii\helpers\Url::to(["routers/get-router-usage?nas="]).$model['nas']."&username=".$model['username']."&password=".$model['password']." ',
        type: 'post',
        success: function (response) {

          $('#board-name').text(response['board-name']);
          $('#build-time').text(response['build-time']);
          $('#cpu').text(response['cpu']);
          $('#cpu-count').text(response['cpu-count']);
          $('#cpu-frequency').text(response['cpu-frequency']);
          $('#factory-software').text(response['factory-software']);
          $('#free-hdd-space').text(response['free-hdd-space']);
          $('#free-memory').text(response['free-memory']);
          $('#platform').text(response['platform']);
          $('#total-hdd-space').text(response['total-hdd-space']);
          $('#total-memory').text(response['total-memory']);
          $('#uptime').text(response['uptime']);
          $('#version').text(response['version']);
          $('#cpu-load').text(response['cpu-load']+' %');
        var val = response['cpu-load'];
          $('#dynamic')
          .css('width', val + '%')
          .attr('aria-valuenow', val)
          .text(val + '% usage');


        }
    }); 
  }

    $('#modal').on('hidden.bs.modal', function () {
      clearInterval(getRouterInfoInterval)
    })

");



 ?>


    
<?php 


$this->registerJs("
  var chart;
  function requestDatta(interface) {
    $.ajax({
      url: 'get-rx-tx?id=".$model->id."',
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
      text: '".Yii::t("app","Real time traffic monitoring for {routerName} router",['routerName'=>$model->name])."'
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
          text: '".Yii::t('app','Byte')."',
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
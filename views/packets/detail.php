<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\Modal;
use webvimark\modules\UserManagement\models\User;
if(!Yii::$app->user->isGuest){
    $theme = explode("/", Yii::$app->getView()->theme->pathMap['@app/views'])[2];
}

$this->registerJsFile(Yii::$app->request->baseUrl.'js/apexcharts.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerCssFile(Yii::$app->request->baseUrl."/css/apexcharts.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/modules-widgets.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/pages/dashboard.css");

$this->title = Yii::t('app','{packet_name} packet statistic',['packet_name'=>$model->packet_name]);
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
 ?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <?php if ( User::canRoute('/packets/create') ): ?>
               <a  class="btn btn-primary" data-pjax="0" href="<?=$langUrl ?>/packets/index" title=" <?=Yii::t("app","Packets") ?>">
                <?=Yii::t("app","Packets") ?>
               </a>
            <?php endif ?>
        </div>
    </div>
</div>

<?php if ( $usersCount['active'] != null || $usersCount['deactive'] != null || $usersCount['archive'] != null || $usersCount['pending'] != null || $usersCount['black'] != null ||  $usersCount['free_user'] != null ): ?>

<div class="row">
    <?php if (User::canRoute(['/packets/packet-monthly'])): ?>
        <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="card custom-card">
                <div class="card-header justify-content-between flex-wrap">
                  <div class="card-title"> <?=Yii::t("app","Money used from the system for the {packet_name} package",['packet_name'=>$model->packet_name]) ?> </div>
                    <div class="btn-group">
                        
                            <?php 
                                $years = [];
                                for ($i=2021; $i <= date("Y"); $i++) { 
                                $years[].=$i;
                                }
                            ?>
                            <select class="form-control monthly-gain-chart">
                                 <option value="" > <?=Yii::t("app","Select") ?></option>
                                <?php foreach ($years as $key => $year): ?>
                                       <option <?=(date("Y") == $year) ? "selected" : "" ?> value="<?=$year ?>"> <?=$year ?></option>
                                <?php endforeach ?>
                            </select>
                        
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="revenueMonthly"></div>
                </div>
            </div>
        </div>
    <?php 
    $this->registerJs("
        var theme = '".$theme."';
        
        let totalColor;

        if(theme == 'dark'){
            totalColor = '#fff';
        }else{
            totalColor = '#000';
        }


        const data =  ".Yii::$app->runAction('/packets/packet-monthly', ['year' =>date('Y'),'packet_id'=>$model->id]).";
        const reducedData = data.reduce((acc,el)=>{
           acc.push({'x':el.y,'y':el.a})
           return acc
        },[]); 

        let options1 = {
                chart: {
                  fontFamily: 'Quicksand, sans-serif',
                  height: 365,
                  type: 'area',
                  zoom: {
                      enabled: false
                  },
                  dropShadow: {
                    enabled: true,
                    opacity: 0.2,
                    blur: 10,
                    left: -7,
                    top: 22
                  },
                  toolbar: {
                    show: true
                  },
                  events: {
                    mounted: function(ctx, config) {
                      const highest1 = ctx.getHighestValueInSeries(0);
                      ctx.addPointAnnotation({
                        x: new Date(ctx.w.globals.seriesX[0][ctx.w.globals.series[0].indexOf(highest1)]).getTime(),
                        y: highest1,
                        label: {
                          style: {
                            cssClass: 'd-none'
                          }
                        },
                      })
                    },
                  }
                },
                colors: ['#1b55e2'],
                dataLabels: {
                    enabled: false
                },
                markers: {
                  discrete: [{
                  seriesIndex: 0,
                  dataPointIndex: 7,
                  fillColor: '#000',
                  strokeColor: '#000',
                  size: 5
                }, {
                  seriesIndex: 1,
                  dataPointIndex: 11,
                  fillColor: '#000',
                  strokeColor: '#000',
                  size: 4
                }]
                },
                stroke: {
                    show: true,
                    curve: 'smooth',
                    width: 5,
                    lineCap: 'square'
                },
                series: [{
                    name: '".Yii::t("app","Amount")."',
                    data: reducedData
                }],
                xaxis: {
                   
                  axisBorder: {
                    show: true
                  },
                  axisTicks: {
                    show: true
                  },
                  crosshairs: {
                    show: true
                  },
                  labels: {
                    offsetX: 0,
                    offsetY: 5,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Quicksand, sans-serif',
                        cssClass: 'apexcharts-xaxis-title',
                    },
                  }
                },
                yaxis: {
                  labels: {
                    formatter: function(value, index) {
                      return (value / 1000) +' K'
                    },
                    offsetX: 0,
                    offsetY: 0,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Quicksand, sans-serif',
                        cssClass: 'apexcharts-yaxis-title',
                    },
                  }
                },
                grid: {
                  borderColor: '#e0e6ed',
                  strokeDashArray: 5,
                  xaxis: {
                      lines: {
                          show: true
                      }
                  },   
                  yaxis: {
                      lines: {
                          show: true,
                      }
                  },
                  padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: -10
                  }, 
                }, 
                legend: {
                  position: 'top',
                  horizontalAlign: 'right',
                  offsetY: -50,
                  fontSize: '16px',
                  fontFamily: 'Quicksand, sans-serif',
                  markers: {
                    width: 10,
                    height: 10,
                    strokeWidth: 5,
                    strokeColor: '#fff',
                    fillColors: undefined,
                    radius: 12,
                    onClick: undefined,
                    offsetX: 0,
                    offsetY: 0
                  },    
                  itemMargin: {
                    horizontal: 0,
                    vertical: 20
                  }
                },
                tooltip: {
                  theme: 'dark',
                  marker: {
                    show: true,
                  },
                  x: {
                    show: true,
                  }
                },

                responsive: [{
                  breakpoint: 575,
                  options: {
                    legend: {
                        offsetY: -30,
                    },
                  },
                }]
            }
        
        var totalChart = new ApexCharts(
          document.querySelector('#revenueMonthly'),
          options1
        );

        totalChart.render();

        function changeData(data) {
            totalChart.updateSeries([{
            name:  '".Yii::t("app","Amount")."',
            data
          }])
        }

      $('.monthly-gain-chart').on('change', function(){
          var year = $(this).val(); 
          $.ajax({
              type: 'POST',
               url: '".$langUrl .Url::to("/packets/packet-monthly")."?packet_id=".$model->id."&year=' + year,
              data: 0,
              dataType: 'json',
              success: function(data){
               const reducedData = data.reduce((acc,el)=>{
                   acc.push({'x':el.y,'y':el.a})
                   return acc
                },[]);                
                changeData(reducedData)
              }
          });
       });

      ");
      ?>
    <?php endif ?>
    <?php if ( User::hasPermission('packet-user-activty') ): ?>
        <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="card custom-card overflow-hidden" >
                <div class="card-header justify-content-between">
                    <div class="card-title"><?=Yii::t('app','Customers activty on {packet_name} packet',['packet_name'=>$model->packet_name]) ?></div>
                </div>
                <div class="widget-content" style="margin:0 auto;">
                    <div id="chart-2" class=""></div>
                </div>
            </div>
        </div>
    <?php 
    $this->registerJs("
         const user_status = ".json_encode( $usersCount , true ).";

        var theme = '".$theme."';
      



        var options = {
              chart: {
                  type: 'donut',
                  width: 380
              },
             colors: ['#5c1ac3', '#46cd93' ,'#ef193c','#795548','#000000' ],
              dataLabels: {
                enabled: false
              },
              legend: {
                  position: 'bottom',
                  horizontalAlign: 'center',
                  fontSize: '15px',
                  markers: {
                    width: 10,
                    height: 10,
                  },
                  itemMargin: {
                    horizontal: 10,
                    vertical: 10
                  }
              },
              plotOptions: {
                pie: {
                  donut: {
                    size: '75%',
                    background: 'transparent',
                    labels: {
                      show: true,
                      name: {
                        show: true,
                        fontSize: '18',
                        color: totalColor,
                        offsetY: -10
                      },
                      value: {
                        show: true,
                        fontSize: '18',
                        color: totalColor,
                        offsetY: 16,
                        formatter: function (val) {
                          return val
                        }
                      },
                      total: {
                        show: true,
                        showAlways: true,
                        label: 'Total',
                        color: totalColor,
                        formatter: function (w) {
                          return w.globals.seriesTotals.reduce( function(a, b) {
                            return a + b
                          }, 0)
                        }
                      }
                    }
                  }
                }
              },

              series: [parseInt(user_status.free_user), parseInt(user_status.active), parseInt(user_status.deactive),parseInt(user_status.archive),parseInt(user_status.black)],
              labels: ['".Yii::t("app","VIP")."', '".Yii::t("app","Active")."', '".Yii::t("app","Deactive")."','".Yii::t("app","Archive")."','".Yii::t("app","Black")."'],
              responsive: [{
                  breakpoint: 1599,
                  options: {
                      chart: {
                          width: '450px',
                          height: '400px'
                      },
                      legend: {
                          position: 'bottom'
                      }
                  },

                  breakpoint: 1439,
                  options: {
                      chart: {
                          width: '250px',
                          height: '390px'
                      },
                      legend: {
                          position: 'bottom'
                      },
                      plotOptions: {
                        pie: {
                          donut: {
                            size: '75%',
                          }
                        }
                      }
                  },
              }]
        }


        var chart = new ApexCharts(
        document.querySelector('#chart-2'),
        options
        );
        chart.render();
    ");
    ?>










    <?php endif ?>
</div>

<?php else: ?>
    <div class=" container-fluid" style="padding:0 15px">
        <h4 style="text-align:center;"><?=Yii::t('app','No one use {packet_name} packet',['packet_name'=>$model->packet_name]) ?></h4>
    </div>
<?php endif ?>





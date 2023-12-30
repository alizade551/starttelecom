<?php 
use yii\helpers\Url;
use webvimark\modules\UserManagement\models\User;

$this->registerJsFile(Yii::$app->request->baseUrl.'js/apexcharts.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerCssFile(Yii::$app->request->baseUrl."/css/apexcharts.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/pages/dashboard.css");

$this->title = Yii::t("app","Home");
$langUrl = (Yii::$app->language == "en") ? "/" : "/".Yii::$app->language."/";
$theme = 'dark';
if(!Yii::$app->user->isGuest){
    $theme = explode("/", Yii::$app->getView()->theme->pathMap['@app/views'])[2];
}
$siteConfig = \app\models\SiteConfig::find()->asArray()->one();
$currency = $siteConfig['currency'];

 ?>


<div class="card custom-card" style="padding: 10px;">
    <h5> <?=Yii::t("app","Hi {userFullname} welcome, This page contains statistical information about you",['userFullname'=>Yii::$app->user->fullname ]) ?> </h5>
</div>

<div class="row">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 layout-spacing">
            <div class="card component-card_1">
                <div class="card-body">
                    <div class="content-card-container">
                        <div class="content-card">
                            <div class="icon-svg">
                                <img src="/img/<?=($theme == "dark") ? "at-connection_dark.png" : "at-connection.png" ?>">
                            </div>
                            <h5 style="margin-top: 15px;" class="card-title"><?=Yii::t('app','At connection') ?></h5>
                            <p class="card-text"><?=Yii::t('app','This information was you how many members connected') ?> </p>
                        </div>
                        <h2><?=( $memberActivty['activtyConnection'] == null ) ? 0 : $memberActivty['activtyConnection']; ?></h2>            
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 layout-spacing">
            <div class="card component-card_1">
                <div class="card-body">
                    <div class="content-card-container">
                        <div class="content-card">
                            <div class="icon-svg">
                                <img src="/img/<?=($theme == "dark") ? "damage-ok-dark.png" : "damage-ok.png" ?>">
                            </div>
                            <h5 style="margin-top: 15px;" class="card-title"><?=Yii::t('app','Damage done') ?></h5>
                            <p class="card-text"><?=Yii::t('app','This information was you how many damage repaired') ?> </p>
                        </div>
                        <h2><?=( $memberActivty['activtyDamageRequest'] == null ) ? 0 : $memberActivty['activtyDamageRequest']; ?></h2>            
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 layout-spacing">
            <div class="card component-card_1">
                <div class="card-body">
                    <div class="content-card-container">
                        <div class="content-card">
                            <div class="icon-svg">
                                <img src="/img/<?=($theme == "dark") ? "recovery_dark.png" : "recovery.png" ?>">
                            </div>
                            <h5 style="margin-top: 15px;" class="card-title"><?=Yii::t('app','Reconnect') ?></h5>
                            <p class="card-text"><?=Yii::t('app','This information was you how many reconnected user') ?> </p>
                        </div>
                        <h2><?=( $memberActivty['activtyReconnect'] == null ) ? 0 : $memberActivty['activtyReconnect']; ?></h2>            
                    </div>
                </div>
            </div>
        </div>


    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="card component-card_1">
            <div class="card-body">
                <div class="content-card-container">
                    <div class="content-card">
                        <div class="icon-svg">
                            <img src="/img/<?=($theme == "dark") ? "new-service-dark.png" : "new-service.png" ?>">
                        </div>
                          <h5 style="margin-top: 15px;" class="card-title"><?=Yii::t('app','New service') ?></h5>
                         <p class="card-text"><?=Yii::t('app','This information was you how many new service added on users') ?> </p>
                    </div>
                    <h2><?=( $memberActivty['activtyNewService'] == null ) ? 0 : $memberActivty['activtyNewService']; ?></h2>     
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
      <div class="card custom-card">
          <div class="card-header justify-content-between flex-wrap">
              <div class="card-title"> <?=Yii::t('app','Your connection activty') ?> </div>
               <div class="btn-group" role="group" aria-label="Basic example"> 
                      <?php 
                          $years = [];
                          for ($i=2021; $i <= date("Y"); $i++) { 
                          $years[].=$i;
                          }
                      ?>
                      <select class="form-control at-connection">
                           <option value="" > <?=Yii::t("app","Select") ?></option>
                          <?php foreach ($years as $key => $year): ?>
                                 <option value="<?=$year ?>"> <?=$year ?></option>
                          <?php endforeach ?>
                      </select>
                </div> 
          </div>
          <div class="card-body p-0">
              <div id="at-connection"></div>
          </div>
      </div>
        <?php 
        $this->registerJs("
            var theme = '".$theme."';

            const dataAtConnection =  ".Yii::$app->runAction('/site/member-at-connection', ['year' =>2023,'service_name'=>'Internet']).";

            const optionsAtConnection = {
                chart: {
                  id: 'at-connection',
                  fontFamily: 'Nunito, sans-serif',
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
                  discrete: [
                      {
                      seriesIndex: 0,
                      dataPointIndex: 7,
                      fillColor: '#000',
                      strokeColor: '#000',
                      size: 5
                      }, 
                      {
                      seriesIndex: 2,
                      dataPointIndex: 11,
                      fillColor: '#000',
                      strokeColor: '#000',
                      size: 4
                      }
                    ]
                        },
                    stroke: {
                        show: true,
                        curve: 'smooth',
                        width: 5,
                        lineCap: 'square'
                    },
                    series: dataAtConnection['yAxsis'],
                    labels: dataAtConnection['stockedAxisCategories'],
                    xaxis: {
                          axisBorder: {
                            show: false
                          },
                          axisTicks: {
                            show: false
                          },
                          crosshairs: {
                            show: true
                          },
                          labels: {
                            offsetX: 0,
                            offsetY: 5,
                            style: {
                                fontSize: '12px',
                                fontFamily: 'Nunito, sans-serif',
                                cssClass: 'apexcharts-xaxis-title',
                            },
                          }
                        },
                    yaxis: {
                      labels: {
                   
                        offsetX: 0,
                        offsetY: 0,
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Nunito, sans-serif',
                            cssClass: 'apexcharts-yaxis-title',
                        },
                      }
                    },
                    grid: {
                      borderColor: '#191e3a',
                      strokeDashArray: 5,
                      xaxis: {
                          lines: {
                              show: true
                          }
                      },   
                      yaxis: {
                          lines: {
                              show: false,
                          }
                      },
                      padding: {
                        top: 0,
                        right: 10,
                        bottom: 0,
                        left: -5
                      }, 
                    }, 
                    legend: {
                      position: 'top',
                      horizontalAlign: 'right',
                      offsetY: 0,
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
                        show: false,
                      }
                    },
                    fill: {
                        type:'gradient',
                        gradient: {
                            type: 'vertical',
                            shadeIntensity: 1,
                            inverseColors: !1,
                            opacityFrom: .19,
                            opacityTo: .05,
                            stops: [100, 100]
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




            function changeAtConnection( x,y ) {
           
              ApexCharts.exec('at-connection', 'updateOptions', {
                xaxis: {
                  categories: x
                }
              });

              ApexCharts.exec('at-connection', 'updateSeries', y);
            }


            $('.at-connection').on('change', function(){
              var year = $(this).val(); 
                  $.ajax({
                      type: 'POST',
                       url: '".$langUrl .Url::to("site/member-at-connection")."?year=' + year,
                      data: 0,
                      dataType: 'json',
                      success: function(response){
                        changeAtConnection(response.stockedAxisCategories,response.yAxsis)
                      }
                  });
            });

            var totalChart = new ApexCharts(
              document.querySelector('#at-connection'),
              optionsAtConnection
            );
            totalChart.render();
        ");
        ?>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 ">
     <div class="card custom-card">
        <div class="card-header justify-content-between flex-wrap">
             <div class="card-title"> <?=Yii::t('app','Your damage activty') ?> </div>
                <div class="btn-group" role="group" aria-label="Basic example"> 
         
               
                    <?php 
                        $years = [];
                        for ($i=2021; $i <= date("Y"); $i++) { 
                        $years[].=$i;
                        }
                    ?>
                    <select class="form-control at-damage">
                         <option value="" > <?=Yii::t("app","Select") ?></option>
                        <?php foreach ($years as $key => $year): ?>
                               <option value="<?=$year ?>"> <?=$year ?></option>
                        <?php endforeach ?>
                    </select>
                
            </div>
        </div>

         <div class="card-body p-0">
            <div id="at-damage"></div>
        </div>
    </div>
        <?php 
        $this->registerJs("
            var theme = '".$theme."';
            const dataAtDamage =  ".Yii::$app->runAction('/site/member-at-damage', ['year' =>2023,'service_name'=>'Internet']).";
            
               const optionsAtDamage = {
                chart: {
                  id: 'at-damage',
                  fontFamily: 'Nunito, sans-serif',
                  height: 365,
                  type: 'area',
                  zoom: {
                      enabled: false
                  },
                dropShadow: {
                    enabled: true,
                    opacity: 0.2,
                    blur: 10,
                    left: 0,
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
                  discrete: [
                      {
                      seriesIndex: 0,
                      dataPointIndex: 7,
                      fillColor: '#000',
                      strokeColor: '#000',
                      size: 5
                      }, 
                      {
                      seriesIndex: 2,
                      dataPointIndex: 11,
                      fillColor: '#000',
                      strokeColor: '#000',
                      size: 4
                      }
                    ]
                        },
                    stroke: {
                        show: true,
                        curve: 'smooth',
                        width: 5,
                        lineCap: 'square'
                    },
                    series: dataAtDamage['yAxsis'],
                    labels: dataAtDamage['stockedAxisCategories'],
                    xaxis: {
                          axisBorder: {
                            show: false
                          },
                          axisTicks: {
                            show: false
                          },
                          crosshairs: {
                            show: true
                          },
                          labels: {
                            offsetX: 0,
                            offsetY: 5,
                            style: {
                                fontSize: '12px',
                                fontFamily: 'Nunito, sans-serif',
                                cssClass: 'apexcharts-xaxis-title',
                            },
                          }
                        },
                    yaxis: {
                      labels: {
                   
                        offsetX: 0,
                        offsetY: 0,
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Nunito, sans-serif',
                            cssClass: 'apexcharts-yaxis-title',
                        },
                      }
                    },
                    grid: {
                      borderColor: '#191e3a',
                      strokeDashArray: 5,
                      xaxis: {
                          lines: {
                              show: true
                          }
                      },   
                      yaxis: {
                          lines: {
                              show: false,
                          }
                      },
                      padding: {
                        top: 0,
                        right: 10,
                        bottom: 0,
                        left: -5
                      }, 
                    }, 
                    legend: {
                      position: 'top',
                      horizontalAlign: 'right',
                      offsetY: 0,
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
                        show: false,
                      }
                    },
                    fill: {
                        type:'gradient',
                        gradient: {
                            type: 'vertical',
                            shadeIntensity: 1,
                            inverseColors: !1,
                            opacityFrom: .19,
                            opacityTo: .05,
                            stops: [100, 100]
                        }
                    },
                    responsive: [{
                      breakpoint: 575,
                      options: {
                        legend: {
                            offsetY: 0,
                        },
                      },
                    }]
              }

            function changeAtDamage( x,y ) {
              ApexCharts.exec('at-damage', 'updateOptions', {
                xaxis: {
                  categories: x
                }
              });
              ApexCharts.exec('at-damage', 'updateSeries', y);
            }


            $('.at-damage').on('change', function(){
              var year = $(this).val(); 
                  $.ajax({
                      type: 'POST',
                       url: '".$langUrl .Url::to("site/member-at-damage")."?year=' + year,
                      data: 0,
                      dataType: 'json',
                      success: function(response){
                        changeAtDamage(response.stockedAxisCategories,response.yAxsis)
                      }
                  });
            });

            var atDamageChart = new ApexCharts(
              document.querySelector('#at-damage'),
              optionsAtDamage
            );
            atDamageChart.render();
        ");
      ?>
   </div>    
</div>






<style type="text/css">
.content-card-container {
    display: flex;
    justify-content: space-between;
}
</style>











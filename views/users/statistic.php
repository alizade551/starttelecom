<?php 
use yii\helpers\Url;
use webvimark\modules\UserManagement\models\User;

$this->registerJsFile(Yii::$app->request->baseUrl.'js/apexcharts.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerCssFile(Yii::$app->request->baseUrl."/css/apexcharts.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/pages/dashboard.css");


$this->title = Yii::t("app","Customers statistics");
$langUrl = (Yii::$app->language == "en") ? "/" : "/".Yii::$app->language."/";
$theme = 'dark';
if(!Yii::$app->user->isGuest){
    $theme = explode("/", Yii::$app->getView()->theme->pathMap['@app/views'])[2];
}
$siteConfig = \app\models\SiteConfig::find()->asArray()->one();
$currency = $siteConfig['currency'];
 ?>
<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if (User::canRoute("/users/index")): ?>
            <div>
                <a href="<?=Yii::$app->request->referrer ?>" style="margin-left: 10px;" class="btn btn-primary"><?=Yii::t("app","Back to All customers") ?></a>
            </div>
            <?php endif?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
    	<div class="row">
	    	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 ">
		        <div class="card custom-card">
		           <div class="card-body">
		              <div class="d-flex align-items-center justify-content-between">
		                 <div>
		                    <h6 class="fw-semibold mb-3"><?=Yii::t('app','New users') ?></h6>
		                    <h2 class="fs-25 fw-semibold"><?=app\models\Users::getNewUsers() ?></h2> <span class="d-block text-danger fs-12"><p class="card-text"><?=Yii::t("app","This month has been registered user count") ?> </p></span> 
		                 </div>
		                 <div> <span class="avatar avatar-md bg-secondary"> <i class="ti ti-users fs-16"></i> </span> </div>
		              </div>
		           </div>
		        </div>
		    </div>
		   <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 ">
		        <div class="card custom-card">
		           <div class="card-body">
		              <div class="d-flex align-items-center justify-content-between">
		                 <div>
		                    <h6 class="fw-semibold mb-3"><?=Yii::t("app","Pending users") ?></h6>
		                    <h2 class="fs-25 fw-semibold"><?=$pending_users_count ?></h2> <span class="d-block text-danger fs-12"><p class="card-text"><?=Yii::t("app","Please check users from orders section") ?> </p></span> 
		                 </div>
		                 <div> <span class="avatar avatar-md bg-warning"> <i class="ti ti-users fs-16"></i> </span> </div>
		              </div>
		           </div>
		        </div>
		    </div>
		    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 ">
		        <div class="card custom-card">
		           <div class="card-body">
		              <div class="d-flex align-items-center justify-content-between">
		                 <div>
		                    <h6 class="fw-semibold mb-3"><?=Yii::t("app","Damaged users") ?></h6>
		                    <h2 class="fs-25 fw-semibold"><?=$damage_users_count ?></h2> <span class="d-block text-danger fs-12"><p class="card-text"><?=Yii::t("app","Please check users from damage section") ?> </p></span> 
		                 </div>
		                 <div> <span class="avatar avatar-md bg-danger"> <i class="ti ti-users fs-16"></i> </span> </div>
		              </div>
		           </div>
		        </div>
		    </div>
		    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 ">
		        <div class="card custom-card">
		           <div class="card-body">
		              <div class="d-flex align-items-center justify-content-between">
		                 <div>
		                    <h6 class="fw-semibold mb-3"><?=Yii::t("app","New service users") ?></h6>
		                    <h2 class="fs-25 fw-semibold"><?=$users_status['new_service_user_count'] ?></h2> <span class="d-block text-danger fs-12"><p class="card-text"><?=Yii::t("app","Please check users from orders section") ?> </p></span> 
		                 </div>
		                 <div> <span class="avatar avatar-md bg-info"> <i class="ti ti-users fs-16"></i> </span> </div>
		              </div>
		           </div>
		        </div>
		    </div>








    	</div>
    </div>
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
    	<div class="row">
	        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
	            <?php if ( User::hasPermission('dashboard-user-percentage') ): ?>
	                <div class="card custom-card overflow-hidden">
	                    <div class="card-header justify-content-between"> 
	                        <div class="card-title" style="font-size: 16px; text-align: center; width: 100%;"> <?=Yii::t("app","Customers status  count") ?> </div> 
	                    </div>
	                    <div class="widget-content" style="position: relative;">
	                        <div id="chart-2" class="" style="width:420px" >
	                        </div>
	                    </div>
	                </div>
	            <?php 
	            $this->registerJs("
	                const user_status = ".json_encode($users_status,true).";

	                var theme = '".$theme."';
	                console.log(theme)
	              
	                let totalColor;

	                if(theme == 'dark'){
	                    totalColor = '#fff';

	                }else{
	                    totalColor = '#888ea8';
	                }


	                var options = {
	                      chart: {
	                          type: 'donut',
	                          width: 370
	                      },
	                     colors: ['#5c1ac3', '#46cd93' ,'#ef193c','#795548','#000000' ],
	                      dataLabels: {
	                        enabled: false
	                      },
	                      legend: {
	                          position: 'bottom',
	                          horizontalAlign: 'center',
	                          fontSize: '15px',
	                          color: totalColor,
	                          markers: {
	                            width: 10,
	                            height: 10,
	                            color: totalColor,
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
	                                fontSize: '20',
	                                color: totalColor,
	                                offsetY: 0
	                              },
	                              value: {
	                                show: true,
	                                fontSize: '20',
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
    	</div>
    </div>
	<?php if ( User::canRoute(['/site/users-status-chart']) ): ?>
	    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
	        <div class="card custom-card">
	            <div class="card-header justify-content-between flex-wrap">
	                <div class="card-title"> <?=Yii::t("app","New users monthly") ?> </div>
	                 <div class="btn-group" role="group" aria-label="Basic example"> 
	               
	                    <?php 
	                        $years = [];
	                        for ($i=2021; $i <= date("Y"); $i++) { 
	                        $years[].=$i;
	                        }
	                    ?>
	                    <select class="form-control stacked-apex-chart-new-users">
	                         <option value="" > <?=Yii::t("app","Select") ?></option>
	                        <?php foreach ($years as $key => $year): ?>
	                               <option value="<?=$year ?>"> <?=$year ?></option>
	                        <?php endforeach ?>
	                    </select>
	             
	                </div>
	            </div>
	            <div class="card-body p-0">
	                <div id="s-new-users-stacked" class=""></div>
	            </div>
	        </div>
	    </div>
	    <?php 
	$this->registerJs("
	   var theme = '".$theme."';
	   const dataServices =  ".Yii::$app->runAction('/site/service-monthly-balance', ['year' =>date("Y"),'service_name'=>'Internet']).";
	   const getServiceMonthlyData = dataServices.reduce((acc,el)=>{
	        acc.push({'x':el.y,'y':el.a})
	       return acc
	    },[]); 
	    const newUserCuntData =  ".Yii::$app->runAction('/site/new-users-chart', ['year' =>date('Y')]).";
	    function changeStackedNewUsersChartData( x,y ) {
	      ApexCharts.exec('s-new-users-stacked', 'updateOptions', {
	        xaxis: {
	          categories: x
	        }
	      });
	      ApexCharts.exec('s-new-users-stacked', 'updateSeries', y);
	    }

	    $('.stacked-apex-chart-new-users').on('change', function(){
	      var year = $(this).val(); 
	          $.ajax({
	              type: 'POST',
	               url: '".$langUrl .Url::to("new-users-chart")."?year=' + year,
	              data: 0,
	              dataType: 'json',
	              success: function(response){
	                changeStackedNewUsersChartData(response.stockedAxisCategories,response.yAxsis)
	              }
	          });
	    });
	        
	    if( theme == 'dark' ){
	        var borderColor = '#151d68';
	    }else{
	        var borderColor = '#e9e9e9';
	    }

	    var sColStackedNewUsers = {
	        chart: {
	             id: 's-new-users-stacked',
	            height: 350,
	            type: 'bar',
	            stacked: true,
	            toolbar: {
	              show: false,
	            }
	        },
	         colors : ['#46cd93', '#ef193c','#795548','#ef4510','#5c1ac3','#000000','#ffeb3b','#2196f3'],
	        grid: {
	          borderColor: borderColor,
	            strokeDashArray: 5,
	     
	        },
	        plotOptions: {
	            bar: {
	                horizontal: false,
	            },
	        },
	        series: [
	            {
	                name: '".Yii::t("app","Amount")."',
	                data: getServiceMonthlyData
	            }
	        ],

	        series: newUserCuntData['yAxsis'],
	        xaxis: {
	            type: 'category',
	            categories: newUserCuntData['stockedAxisCategories'],
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
	            offsetX: 0,
	            offsetY: 0,
	            style: {
	                fontSize: '12px',
	                fontFamily: 'Quicksand, sans-serif',
	                cssClass: 'apexcharts-yaxis-title',
	            },
	          }
	        },
	        legend: {
	            position: 'bottom',
	            offsetY: 0,
	            itemMargin: {
	            horizontal: 25, vertical: 15
	          }

	        },
	        tooltip: {
	          theme: theme,
	          marker: {
	            show: true,
	          },
	          x: {
	            show: true,
	          }
	        },
	        fill: {
	            opacity: 1
	        },
	    }
	    var stackedApexChartNewUsers = new ApexCharts(
	        document.querySelector('#s-new-users-stacked'),
	        sColStackedNewUsers
	    );

	    stackedApexChartNewUsers.render();
	    ")

	     ?>
	<?php endif ?>
</div>
<?php 
use yii\helpers\Url;
use webvimark\modules\UserManagement\models\User;

$this->registerJsFile(Yii::$app->request->baseUrl.'js/apexcharts.min.js',
['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerCssFile(Yii::$app->request->baseUrl."/css/apexcharts.css");
$this->registerCssFile(Yii::$app->request->baseUrl."/css/pages/dashboard.css");



$this->title = Yii::t("app","Payment statistic");
$langUrl = (Yii::$app->language == "en") ? "/" : "/".Yii::$app->language."/";
$theme = 'dark';
if(!Yii::$app->user->isGuest){
    $theme = explode("/", Yii::$app->getView()->theme->pathMap['@app/views'])[2];
}
$siteConfig = \app\models\SiteConfig::find()->asArray()->one();
$currency = $siteConfig['currency'];
$lastTotalAmount = ( $lastTotalModel != null ) ? $lastTotalModel['amount'] : 0;
 ?>


<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
        </div>
    </div>
</div>


<div class="row">
	<?php if ( User::canRoute('user-balance/total-profit') ): ?>
	    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
	        <div class="card custom-card overflow-hidden">
	               <div class="card-body">
	                  <div class="d-flex align-items-top justify-content-between">
	                     <div> <span class="avatar avatar-md avatar-rounded bg-primary"> <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></span> </div>
	                     <div class="flex-fill ms-3">
	                        <div class="d-flex align-items-center justify-content-between flex-wrap">
	                           <div>
	                              <p class="text-muted mb-0"><?=Yii::t("app","Total profit") ?></p>
	                              <h4 class="fw-semibold mt-1"><?=number_format( ($lastTotalAmount ),2 ) ?> <?=$currency ?></h4>
	                           </div>
	                           <div id="crm-total-customers" style="min-height: 40px;">
	                              <div id="apexchartshijuz9nz" class="apexcharts-canvas apexchartshijuz9nz apexcharts-theme-light" style="width: 100px; height: 40px;">
	                                 <svg id="SvgjsSvg2755" width="100" height="40" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;">
	                                    <foreignObject x="0" y="0" width="100" height="40">
	                                       <div class="apexcharts-legend" xmlns="http://www.w3.org/1999/xhtml" style="max-height: 20px;"></div>
	                                    </foreignObject>
	                                    <rect id="SvgjsRect2759" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect>
	                                    <g id="SvgjsG2802" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g>
	                                    <g id="SvgjsG2757" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 0)">
	                                       <defs id="SvgjsDefs2756">
	                                          <clipPath id="gridRectMaskhijuz9nz">
	                                             <rect id="SvgjsRect2761" width="105.5" height="41.5" x="-2.75" y="-0.75" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
	                                          </clipPath>
	                                          <clipPath id="forecastMaskhijuz9nz"></clipPath>
	                                          <clipPath id="nonForecastMaskhijuz9nz"></clipPath>
	                                          <clipPath id="gridRectMarkerMaskhijuz9nz">
	                                             <rect id="SvgjsRect2762" width="104" height="44" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
	                                          </clipPath>
	                                          <linearGradient id="SvgjsLinearGradient2767" x1="0" y1="1" x2="1" y2="1">
	                                             <stop id="SvgjsStop2768" stop-opacity="0.9" stop-color="rgba(66,45,112,0.9)" offset="0"></stop>
	                                             <stop id="SvgjsStop2769" stop-opacity="0.9" stop-color="rgba(132,90,223,0.9)" offset="0.98"></stop>
	                                             <stop id="SvgjsStop2770" stop-opacity="0.9" stop-color="rgba(132,90,223,0.9)" offset="1"></stop>
	                                          </linearGradient>
	                                       </defs>
	                                       <line id="SvgjsLine2760" x1="0" y1="0" x2="0" y2="40" stroke="#b6b6b6" stroke-dasharray="3" stroke-linecap="butt" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="40" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line>
	                                       <g id="SvgjsG2772" class="apexcharts-grid">
	                                          <g id="SvgjsG2773" class="apexcharts-gridlines-horizontal" style="display: none;">
	                                             <line id="SvgjsLine2776" x1="0" y1="0" x2="100" y2="0" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2777" x1="0" y1="4" x2="100" y2="4" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2778" x1="0" y1="8" x2="100" y2="8" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2779" x1="0" y1="12" x2="100" y2="12" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2780" x1="0" y1="16" x2="100" y2="16" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2781" x1="0" y1="20" x2="100" y2="20" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2782" x1="0" y1="24" x2="100" y2="24" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2783" x1="0" y1="28" x2="100" y2="28" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2784" x1="0" y1="32" x2="100" y2="32" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2785" x1="0" y1="36" x2="100" y2="36" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                             <line id="SvgjsLine2786" x1="0" y1="40" x2="100" y2="40" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                          </g>
	                                          <g id="SvgjsG2774" class="apexcharts-gridlines-vertical" style="display: none;"></g>
	                                          <line id="SvgjsLine2788" x1="0" y1="40" x2="100" y2="40" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line>
	                                          <line id="SvgjsLine2787" x1="0" y1="1" x2="0" y2="40" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line>
	                                       </g>
	                                       <g id="SvgjsG2775" class="apexcharts-grid-borders" style="display: none;"></g>
	                                       <g id="SvgjsG2763" class="apexcharts-line-series apexcharts-plot-series">
	                                          <g id="SvgjsG2764" class="apexcharts-series" seriesName="Value" data:longestSeries="true" rel="1" data:realIndex="0">
	                                             <path id="SvgjsPath2771" d="M 0 5.217391304347828C 4.375 5.217391304347828 8.125 15.65217391304348 12.5 15.65217391304348C 16.875 15.65217391304348 20.625 6.956521739130437 25 6.956521739130437C 29.375 6.956521739130437 33.125 22.608695652173914 37.5 22.608695652173914C 41.875 22.608695652173914 45.625 7.105427357601002e-15 50 7.105427357601002e-15C 54.375 7.105427357601002e-15 58.125 5.217391304347828 62.5 5.217391304347828C 66.875 5.217391304347828 70.625 1.7391304347826164 75 1.7391304347826164C 79.375 1.7391304347826164 83.125 24.347826086956523 87.5 24.347826086956523C 91.875 24.347826086956523 95.625 19.1304347826087 100 19.1304347826087" fill="none" fill-opacity="1" stroke="url(#SvgjsLinearGradient2767)" stroke-opacity="1" stroke-linecap="butt" stroke-width="1.5" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMaskhijuz9nz)" pathTo="M 0 5.217391304347828C 4.375 5.217391304347828 8.125 15.65217391304348 12.5 15.65217391304348C 16.875 15.65217391304348 20.625 6.956521739130437 25 6.956521739130437C 29.375 6.956521739130437 33.125 22.608695652173914 37.5 22.608695652173914C 41.875 22.608695652173914 45.625 7.105427357601002e-15 50 7.105427357601002e-15C 54.375 7.105427357601002e-15 58.125 5.217391304347828 62.5 5.217391304347828C 66.875 5.217391304347828 70.625 1.7391304347826164 75 1.7391304347826164C 79.375 1.7391304347826164 83.125 24.347826086956523 87.5 24.347826086956523C 91.875 24.347826086956523 95.625 19.1304347826087 100 19.1304347826087" pathFrom="M 0 5.217391304347828C 4.375 5.217391304347828 8.125 15.65217391304348 12.5 15.65217391304348C 16.875 15.65217391304348 20.625 6.956521739130437 25 6.956521739130437C 29.375 6.956521739130437 33.125 22.608695652173914 37.5 22.608695652173914C 41.875 22.608695652173914 45.625 7.105427357601002e-15 50 7.105427357601002e-15C 54.375 7.105427357601002e-15 58.125 5.217391304347828 62.5 5.217391304347828C 66.875 5.217391304347828 70.625 1.7391304347826164 75 1.7391304347826164C 79.375 1.7391304347826164 83.125 24.347826086956523 87.5 24.347826086956523C 91.875 24.347826086956523 95.625 19.1304347826087 100 19.1304347826087" fill-rule="evenodd"></path>
	                                             <g id="SvgjsG2765" class="apexcharts-series-markers-wrap apexcharts-hidden-element-shown" data:realIndex="0"></g>
	                                          </g>
	                                          <g id="SvgjsG2766" class="apexcharts-datalabels" data:realIndex="0"></g>
	                                       </g>
	                                       <line id="SvgjsLine2789" x1="0" y1="0" x2="100" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line>
	                                       <line id="SvgjsLine2790" x1="0" y1="0" x2="100" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line>
	                                       <g id="SvgjsG2791" class="apexcharts-xaxis" transform="translate(0, 0)">
	                                          <g id="SvgjsG2792" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g>
	                                       </g>
	                                       <g id="SvgjsG2803" class="apexcharts-yaxis-annotations"></g>
	                                       <g id="SvgjsG2804" class="apexcharts-xaxis-annotations"></g>
	                                       <g id="SvgjsG2805" class="apexcharts-point-annotations"></g>
	                                    </g>
	                                 </svg>
	                              </div>
	                           </div>
	                        </div>
	                        <div class="d-flex align-items-center justify-content-between mt-1">
	                           <div> <a class="text-primary" href="/user-balance/index"><?=Yii::t("app","View All") ?><i class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a> </div>
	                           <div class="text-end">
	                              <p class="mb-0 text-success fw-semibold">+ <?=$today_balance ?> <?=$currency ?></p>
	                              <span class="text-muted op-7 fs-11"><?=Yii::t("app","Today") ?></span> 
	                              
	                           </div>
	                        </div>
	                     </div>
	                  </div>
	               </div>
	        </div>
	    </div>
    <?php endif ?>
    <?php if ( User::canRoute('user-balance/monthly-profit') ): ?>
	    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
	        <div class="card custom-card overflow-hidden">
	           <div class="card-body">
	              <div class="d-flex align-items-top justify-content-between">
	                 <div> <span class="avatar avatar-md avatar-rounded bg-secondary"> <i class="ti ti-wallet fs-16"></i> </span> </div>
	                 <div class="flex-fill ms-3">
	                    <div class="d-flex align-items-center justify-content-between flex-wrap">
	                       <div>
	                          <p class="text-muted mb-0"><?=Yii::t("app","Monthly profit") ?></p>
	                          <h4 class="fw-semibold mt-1"><?=app\models\Users::getMonthlyGainPercent()['total_balance'] ?> <?=$currency ?></h4>
	                       </div>
	                       <div id="crm-total-revenue" style="min-height: 40px;">
	                          <div id="apexcharts9d7dz6ag" class="apexcharts-canvas apexcharts9d7dz6ag apexcharts-theme-light" style="width: 100px; height: 40px;">
	                             <svg id="SvgjsSvg1070" width="100" height="40" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;">
	                                <foreignObject x="0" y="0" width="100" height="40">
	                                   <div class="apexcharts-legend" xmlns="http://www.w3.org/1999/xhtml" style="max-height: 20px;"></div>
	                                </foreignObject>
	                                <rect id="SvgjsRect1074" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect>
	                                <g id="SvgjsG1117" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g>
	                                <g id="SvgjsG1072" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 0)">
	                                   <defs id="SvgjsDefs1071">
	                                      <clipPath id="gridRectMask9d7dz6ag">
	                                         <rect id="SvgjsRect1076" width="105.5" height="41.5" x="-2.75" y="-0.75" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
	                                      </clipPath>
	                                      <clipPath id="forecastMask9d7dz6ag"></clipPath>
	                                      <clipPath id="nonForecastMask9d7dz6ag"></clipPath>
	                                      <clipPath id="gridRectMarkerMask9d7dz6ag">
	                                         <rect id="SvgjsRect1077" width="104" height="44" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
	                                      </clipPath>
	                                      <linearGradient id="SvgjsLinearGradient1082" x1="0" y1="1" x2="1" y2="1">
	                                         <stop id="SvgjsStop1083" stop-opacity="0.9" stop-color="rgba(18,92,115,0.9)" offset="0"></stop>
	                                         <stop id="SvgjsStop1084" stop-opacity="0.9" stop-color="rgba(35,183,229,0.9)" offset="0.98"></stop>
	                                         <stop id="SvgjsStop1085" stop-opacity="0.9" stop-color="rgba(35,183,229,0.9)" offset="1"></stop>
	                                      </linearGradient>
	                                   </defs>
	                                   <line id="SvgjsLine1075" x1="0" y1="0" x2="0" y2="40" stroke="#b6b6b6" stroke-dasharray="3" stroke-linecap="butt" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="40" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line>
	                                   <g id="SvgjsG1087" class="apexcharts-grid">
	                                      <g id="SvgjsG1088" class="apexcharts-gridlines-horizontal" style="display: none;">
	                                         <line id="SvgjsLine1091" x1="0" y1="0" x2="100" y2="0" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1092" x1="0" y1="4" x2="100" y2="4" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1093" x1="0" y1="8" x2="100" y2="8" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1094" x1="0" y1="12" x2="100" y2="12" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1095" x1="0" y1="16" x2="100" y2="16" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1096" x1="0" y1="20" x2="100" y2="20" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1097" x1="0" y1="24" x2="100" y2="24" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1098" x1="0" y1="28" x2="100" y2="28" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1099" x1="0" y1="32" x2="100" y2="32" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1100" x1="0" y1="36" x2="100" y2="36" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                         <line id="SvgjsLine1101" x1="0" y1="40" x2="100" y2="40" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line>
	                                      </g>
	                                      <g id="SvgjsG1089" class="apexcharts-gridlines-vertical" style="display: none;"></g>
	                                      <line id="SvgjsLine1103" x1="0" y1="40" x2="100" y2="40" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line>
	                                      <line id="SvgjsLine1102" x1="0" y1="1" x2="0" y2="40" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line>
	                                   </g>
	                                   <g id="SvgjsG1090" class="apexcharts-grid-borders" style="display: none;"></g>
	                                   <g id="SvgjsG1078" class="apexcharts-line-series apexcharts-plot-series">
	                                      <g id="SvgjsG1079" class="apexcharts-series" seriesName="Value" data:longestSeries="true" rel="1" data:realIndex="0">
	                                         <path id="SvgjsPath1086" d="M0 8C4.375 8 8.125 17.6 12.5 17.6C16.875 17.6 20.625 8 25 8C29.375 8 33.125 4.799999999999997 37.5 4.799999999999997C41.875 4.799999999999997 45.625 25.6 50 25.6C54.375 25.6 58.125 20.8 62.5 20.8C66.875 20.8 70.625 9.600000000000001 75 9.600000000000001C79.375 9.600000000000001 83.125 24 87.5 24C91.875 24 95.625 0 100 0C100 0 100 0 100 0 " fill="none" fill-opacity="1" stroke="url(#SvgjsLinearGradient1082)" stroke-opacity="1" stroke-linecap="butt" stroke-width="1.5" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMask9d7dz6ag)" pathTo="M 0 8C 4.375 8 8.125 17.6 12.5 17.6C 16.875 17.6 20.625 8 25 8C 29.375 8 33.125 4.799999999999997 37.5 4.799999999999997C 41.875 4.799999999999997 45.625 25.6 50 25.6C 54.375 25.6 58.125 20.8 62.5 20.8C 66.875 20.8 70.625 9.600000000000001 75 9.600000000000001C 79.375 9.600000000000001 83.125 24 87.5 24C 91.875 24 95.625 0 100 0" pathFrom="M -1 40 L -1 40 L 12.5 40 L 25 40 L 37.5 40 L 50 40 L 62.5 40 L 75 40 L 87.5 40 L 100 40" fill-rule="evenodd"></path>
	                                         <g id="SvgjsG1080" class="apexcharts-series-markers-wrap apexcharts-hidden-element-shown" data:realIndex="0"></g>
	                                      </g>
	                                      <g id="SvgjsG1081" class="apexcharts-datalabels" data:realIndex="0"></g>
	                                   </g>
	                                   <line id="SvgjsLine1104" x1="0" y1="0" x2="100" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line>
	                                   <line id="SvgjsLine1105" x1="0" y1="0" x2="100" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line>
	                                   <g id="SvgjsG1106" class="apexcharts-xaxis" transform="translate(0, 0)">
	                                      <g id="SvgjsG1107" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g>
	                                   </g>
	                                   <g id="SvgjsG1118" class="apexcharts-yaxis-annotations apexcharts-hidden-element-shown"></g>
	                                   <g id="SvgjsG1119" class="apexcharts-xaxis-annotations apexcharts-hidden-element-shown"></g>
	                                   <g id="SvgjsG1120" class="apexcharts-point-annotations apexcharts-hidden-element-shown"></g>
	                                </g>
	                             </svg>
	                          </div>
	                       </div>
	                    </div>
	                    <div class="d-flex align-items-center justify-content-between mt-1">
	                       <div> <a class="text-secondary" href="javascript:void(0);"><?=Yii::t("app","View All") ?><i class="ti ti-arrow-narrow-right ms-2 fw-semibold d-inline-block"></i></a> </div>
	                       <div class="text-end">
	                          <p class="mb-0 text-success fw-semibold">+ <?=$today_balance ?> <?=$currency ?></p>
	                          <span class="text-muted op-7 fs-11"><?=Yii::t("app","Today") ?></span> 
	                       </div>
	                    </div>
	                 </div>
	              </div>
	           </div>
	        </div>
	    </div>		
    <?php endif ?>
    <?php if (User::canRoute(['/site/monthly-balance'])): ?>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 ">
            <div class="card custom-card">
                <div class="card-header justify-content-between flex-wrap">
                    <div class="card-title"> <?=Yii::t("app","Increased amount for monthly services") ?> </div>
                    <div class="btn-group" role="group" aria-label="Basic example"> 
                      
                            <?php 
                                $years = [];
                                for ($i=2021; $i <= date("Y"); $i++) { 
                                $years[].=$i;
                                }
                            ?>
                            <select class="form-control monthly-gain-chart">
                                 <option value="" > <?=Yii::t("app","Select") ?></option>
                                <?php foreach ($years as $key => $year): ?>
                                       <option value="<?=$year ?>"> <?=$year ?></option>
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
        
        const data =  ".Yii::$app->runAction('/site/monthly-balance', ['year' =>date('Y'),'service_name'=>'Internet']).";
        const reducedData = data.reduce((acc,el)=>{
           acc.push({'x':el.y,'y':el.a})
           return acc
        },[]); 


        
        const options1 = {
                chart: {
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
                series: [{
                        name: '".Yii::t("app","Amount")."',
                        data: reducedData
                    }],
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
                    formatter: function(value, index) {
                      return value 
                    },
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
               url: '".$langUrl .Url::to("site-monthly-balance")."?year=' + year,
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

    <?php if (User::canRoute(['/site/service-monthly-balance'])): ?>
    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 ">
        <div class="card custom-card">
            <div class="card-header justify-content-between flex-wrap">
                <div class="card-title"> <?=Yii::t("app","The amount deducted for monthly services") ?> </div>
                <div class="btn-group" role="group" aria-label="Basic example"> 
                  <?php 

                    $services_arr = [];
         
                    for ($i=2021; $i <= date("Y"); $i++) { 
                        $services_arr["Internet"][$i] = $i;
                    }
                    for ($i=2021; $i <= date("Y"); $i++) { 
                        $services_arr["TV"][$i] = $i;
                    }  

                    for ($i=2021; $i <= date("Y"); $i++) { 
                        $services_arr["Wifi"][$i] = $i;
                    }  

                    for ($i=2021; $i <= date("Y"); $i++) { 
                        $services_arr["Items"][$i] = $i;
                    }   

                   ?>
               
                    <select class="form-control monthly-gain-chart-with-services">
                            <option value="" > <?=Yii::t("app","Select") ?></option>
                        <?php foreach ($services_arr as $key => $service_one): ?>
                            <optgroup label="<?=$key ?>">
                                <?php foreach ($service_one as $key => $s_v): ?>
                                     <option value="<?=$s_v ?>"><?=$s_v ?></option>
                                <?php endforeach ?>
                           </optgroup>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="card-body p-0">
                <div id="revenueServiceMonthly"></div>
            </div>
        </div>
    </div>
     <?php 
      $this->registerJs("

       const dataServices =  ".Yii::$app->runAction('/site/service-monthly-balance', ['year' =>date("Y"),'service_name'=>'Internet']).";

       const getServiceMonthlyData = dataServices.reduce((acc,el)=>{
            acc.push({'x':el.y,'y':el.a})
           return acc
        },[]);    

        const serviceMonthlyOptions = {
            chart: {
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
                series: [{
                        name: '".Yii::t("app","Amount")."',
                        data: getServiceMonthlyData
                    }],
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
                    formatter: function(value, index) {
                      return value
                    },
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


        var serviceMonthlyChart = new ApexCharts(
          document.querySelector('#revenueServiceMonthly'),
          serviceMonthlyOptions
        );


        serviceMonthlyChart.render();

        function changeDataServiceMonthly(data) {
            serviceMonthlyChart.updateSeries([{
            name:  '".Yii::t("app","Amount")."',
            data: data,
          }])

        }

       $('.monthly-gain-chart-with-services').on('change', function(){
          var year = $(this).val(); 
          var service_name = $('.monthly-gain-chart-with-services :selected').parent().attr('label');
          $.ajax({
              type: 'POST',
               url: '".$langUrl .Url::to("site-service-monthly-balance")."?year=' + year+'&service_name='+service_name,
              data: 0,
              dataType: 'json',
              success: function(data){
               const getServiceMonthlyDataReduced = data.reduce((acc,el)=>{
                    acc.push({'x':el.y,'y':el.a})
                   return acc
                },[]);   
                changeDataServiceMonthly(getServiceMonthlyDataReduced)  
              }
          });
        });






        ");
      ?>
    <?php endif ?>

    <?php if (User::canRoute(['/site/monthly-bonus-balance'])): ?>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 ">
            <div class="card custom-card">
                <div class="card-header justify-content-between flex-wrap">
                <div class="card-title"> <?=Yii::t("app","Increased bonus amount for monthly services") ?> </div>
                    <div class="btn-group" role="group" aria-label="Basic example"> 
                        <?php 
                            $years = [];
                            for ($i=2021; $i <= date("Y"); $i++) { 
                            $years[].=$i;
                            }
                        ?>
                        <select class="form-control monthly-bonus-balance-gain-chart">
                             <option value="" > <?=Yii::t("app","Select") ?></option>
                            <?php foreach ($years as $key => $year): ?>
                                   <option value="<?=$year ?>"> <?=$year ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div id="revenueMonthlyBonus"></div>
                </div>
            </div>
        </div>
    <?php 
    $this->registerJs("
        var theme = '".$theme."';
        
        const dataMonthlyBonusData =  ".Yii::$app->runAction('/site/monthly-bonus-balance', ['year' =>date('Y')]).";
        const reducedMonthlyBonusData = dataMonthlyBonusData.reduce((acc,el)=>{
           acc.push({'x':el.y,'y':el.a})
           return acc
        },[]); 


        const optionsMonthlyBonusData = {
            chart: {
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
                series: [{
                        name: '".Yii::t("app","Amount")."',
                        data: reducedMonthlyBonusData
                    }],
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
                    formatter: function(value, index) {
                      return value
                    },
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

        var totalChartMonthlyBonusData = new ApexCharts(
          document.querySelector('#revenueMonthlyBonus'),
          optionsMonthlyBonusData
        );

        totalChartMonthlyBonusData.render();

        function changeDataMonthlyBonusData(data) {
            totalChartMonthlyBonusData.updateSeries([{
            name:  '".Yii::t("app","Amount")."',
            data
          }])
        }

      $('.monthly-bonus-balance-gain-chart').on('change', function(){
          var year = $(this).val(); 
          $.ajax({
              type: 'POST',
               url: '".$langUrl .Url::to("site-monthly-bonus-balance")."?year=' + year,
              data: 0,
              dataType: 'json',
              success: function(data){
               const dataMonthlyBonusData = data.reduce((acc,el)=>{
                   acc.push({'x':el.y,'y':el.a})
                   return acc
                },[]);                
                changeDataMonthlyBonusData(dataMonthlyBonusData)
              }
          });
       });
      ");
      ?>
    <?php endif ?>

    <?php if ( User::canRoute(['/site/monthly-deducted-bonus-balance']) ): ?>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 ">
            <div class="card custom-card">
                <div class="card-header justify-content-between flex-wrap">
                    <div class="card-title"> <?=Yii::t("app","The amount deducted for bonus") ?> </div>
                    <div class="btn-group" role="group" aria-label="Basic example"> 
                        
                        <?php 
                            $years = [];
                            for ($i=2021; $i <= date("Y"); $i++) { 
                            $years[].=$i;
                            }
                        ?>
                        <select class="form-control monthly-bonus-balance-deducted-chart">
                             <option value="" > <?=Yii::t("app","Select") ?></option>
                            <?php foreach ($years as $key => $year): ?>
                                   <option value="<?=$year ?>"> <?=$year ?></option>
                            <?php endforeach ?>
                        </select>
                   
                    </div>
                </div>

                <div class="card-body p-0">
                    <div id="revenueDeductedBonusMonthly"></div>
                </div>
            </div>
        </div>
     <?php 
      $this->registerJs("
       const deductedBonusBalance =  ".Yii::$app->runAction('/site/monthly-deducted-bonus-balance', ['year' =>date("Y")]).";
       const getDeductedMonthlyData = deductedBonusBalance.reduce((acc,el)=>{
            acc.push({'x':el.y,'y':el.a})
           return acc
        },[]);    

        const deductedBonusBalanceOptions = {
            chart: {
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
                series: [{
                        name: '".Yii::t("app","Amount")."',
                        data: getDeductedMonthlyData
                    }],
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
                    formatter: function(value, index) {
                      return value
                    },
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
                    right: 0,
                    bottom: 0,
                    left: 0
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

        var deductedBonusMonthlyChart = new ApexCharts(
          document.querySelector('#revenueDeductedBonusMonthly'),
          deductedBonusBalanceOptions
        );

        deductedBonusMonthlyChart.render();

        function changedeductedBonusMonthlyChart(data) {
            deductedBonusMonthlyChart.updateSeries([{
            name:  '".Yii::t("app","Amount")."',
            data: data,
          }])

        }

       $('.monthly-bonus-balance-deducted-chart').on('change', function(){
          var year = $(this).val(); 
          $.ajax({
              type: 'POST',
               url: '".$langUrl .Url::to("site-monthly-deducted-bonus-balance")."?year=' + year,
              data: 0,
              dataType: 'json',
              success: function(data){
               const getServiceMonthlyDataReduced = data.reduce((acc,el)=>{
                    acc.push({'x':el.y,'y':el.a})
                   return acc
                },[]);   
                changedeductedBonusMonthlyChart(getServiceMonthlyDataReduced)  
              }
          });
        });
        ");
      ?>
    <?php endif ?>


    <?php if (User::hasPermission('last-20-user-activty ')): ?>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
            <div class="widget transactions " style="padding: 20px;">
                <div class="widget-heading">
                    <h5 class=""><?=Yii::t("app","Last 10 transactions") ?></h5>
                </div>
                <div class="widget-content transactions-container">
                    <?php foreach ($lastTransactions as $key => $transaction): ?>
                    <?php if ( $transaction['balance_in'] != 0): ?>
                        <div class="transactions-list t-info">
                            <div class="t-item">
                                <div class="t-company-name">
                                    <div class="t-icon">
                                        <div class="avatar avatar-xl">
                                            <span class="avatar-title"><?=\app\components\Utils::getUserFirstCharacter($transaction['fullname']) ?></span>
                                        </div>
                                    </div>
                                    <div class="t-name">
                                        <h4><?=$transaction['fullname'] ?></h4>
                                        <p class="meta-date"><?=date('d M Y H:i:s',$transaction['created_at']) ?></p>
                                    </div>
                                </div>
                                <div class="t-rate rate-inc">
                                    <p><span>+<?=$transaction['balance_in'] ?> <?=$currency ?></span></p>
                                </div>
                            </div>
                        </div>                
                        <?php endif ?>
                        <?php if ( $transaction['balance_out'] != 0): ?>
                        <div class="transactions-list">
                            <div class="t-item">
                                <div class="t-company-name">
                                    <div class="t-icon">
                                        <div class="avatar avatar-xl">
                                            <span class="avatar-title"><?=\app\components\Utils::getUserFirstCharacter($transaction['fullname']) ?></span>
                                        </div>
                                    </div>
                                    <div class="t-name">
                                        <h4><?=$transaction['fullname'] ?></h4>
                                        <p class="meta-date"><?=date('d M Y H:i:s',$transaction['created_at']) ?></p>
                                    </div>

                                </div>
                                <div class="t-rate rate-dec">
                                    <p><span>-<?=$transaction['balance_out']; ?> <?=$currency ?></span></p>
                                </div>
                            </div>
                        </div>
                        <?php endif ?>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    <?php endif ?>
    
</div>

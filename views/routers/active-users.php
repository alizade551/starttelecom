<?php
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\widgets\GridBulkActions;
use yii\helpers\Url;
use app\models\Cities;
use webvimark\modules\UserManagement\models\User;
use app\widgets\GridPageSize;

/*
status = 0 pending
status = 1 active
status = 2 deactive
status = 3 Archive
status = 4 Reconnectin
status = 5 New service
status = 7 VIP
status_request = 8 Damages
*/

$this->title = Yii::t("app","{router_name} router real time online user monitoring",['router_name'=>$model['name']]);
$siteConfig = \app\models\SiteConfig::find()->asArray()->one();
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";


    $content = '';
    $actions = '';



    $pageSize = GridPageSize::widget([
        'pjaxId'=>'online-grid-pjax',
        'pageName'=>'_grid_page_size_online_users'
    ]);

    $progressBar = '<div class="progress">
        <div id="auto-reload-progress-bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" >
        </div>
    </div>';


    $pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";

    $actionsContainer = "<div class='helper-action-container'>".$actions."</div>";

    $content = "<div class='helper-container'>".$pageSizeContainer.$actionsContainer."</div>".$progressBar;

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


<div class="card custom-card">
    <?php Pjax::begin(['id'=>'online-grid-pjax']); ?>

        <?= GridView::widget([
            'id'=>'users-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager'=>[
              'class'=>yii\bootstrap4\LinkPager::class
            ], 
             'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
            'columns' => [
                [
                'visible'=>User::hasPermission("customer-serial-column"), 
                'class' => 'yii\grid\SerialColumn',
                'headerOptions'=>['style'=>'width:10px;text-align:center;color:#337ab7;'],
                'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                ],
                [
                    'attribute'=>'fullname',
                    'headerOptions'=>['style'=>'width:200px;text-align:center;color:#337ab7;'],
                    'visible'=>User::hasPermission("customer-fullname-view"),
                    'label'=>Yii::t('app','User fullname'),
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off'
                    ],
                    'format'=>'raw',
                    'headerOptions'=>['style'=>'width:150px;text-align:center;color:#337ab7;'],
                    'contentOptions'=>['style'=>'width:150px;text-align:center;'],
                    'value'=>function ($model){

                      return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model['user_id'].'">'.$model['fullname'].'</a>';
                    }
                ],
                [
                    'attribute'=>'contract_number',
                    'visible'=>User::hasPermission("customer-contract-number-view"),
                    'label'=>Yii::t('app','Contract number'),
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off'
                    ],            
                    'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                    'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                    'value' => 'contract_number'
                ],
                
                [
                    'attribute'=>'login',
                    'visible'=>User::hasPermission("online-login-number-view"),
                   'headerOptions'=>['style'=>'width:10px;text-align:center;color:#337ab7;'],
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off'
                    ],            
                    'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                    'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                    'value' => 'login'
                ],

                [
                    'label'=>Yii::t('app','More info'),
                    'visible'=>User::hasPermission("online-check-usage"),
                    'format'=>'raw',
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off'
                    ],            
                    'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                    'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                    'value'=>function ( $model ) use($dataProvider , $langUrl)  {

                       // return $dataProvider->activeUsers[$model['login']]['caller-id'];
                        return '<a  style="display: block; text-align: center;" data-fancybox data-type="ajax" data-options=\'{"touch" : false}\'  data-src="'.$langUrl.'/users/check-user-internet?login='.$model['login'] .'" href="javascript:;" >
                                                  <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="22" y1="12" x2="2" y2="12"></line><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path><line x1="6" y1="16" x2="6.01" y2="16"></line><line x1="10" y1="16" x2="10.01" y2="16"></line></svg>
                                                </a>';
                    }
                ],

                [
                    'label'=>Yii::t('app','Mac address'),
                    'visible'=>User::hasPermission("online-mac-address"),
                    'format'=>'raw',
                    'headerOptions'=>['style'=>'width:10px;text-align:center;color:#337ab7;'],
                    'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                    'value'=>function ( $model ) use($dataProvider) {

                       return $dataProvider->activeUsers[$model['login']]['caller-id'];
                    }
                ],


                [
                    'label'=>Yii::t('app','Uptime'),
                    'visible'=>User::hasPermission("online-mac-address"),
                    'format'=>'raw',
                    'headerOptions'=>['style'=>'width:10px;text-align:center;color:#337ab7;'],
                    'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                    'value'=>function ( $model ) use($dataProvider) {
                        
                       return $dataProvider->activeUsers[$model['login']]['uptime'];
                    }
                ],

                [
                    'label'=>Yii::t('app','Address'),
                    'visible'=>User::hasPermission("online-address"),
                    'format'=>'raw',
                    'headerOptions'=>['style'=>'width:10px;text-align:center;color:#337ab7;'],
                    'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                    'value'=>function ( $model ) use($dataProvider) {
                        
                       return $dataProvider->activeUsers[$model['login']]['address'];
                    }
                ],



                [
                'attribute'=>'credit_status',
                'visible'=>User::hasPermission("customer-credit-status-view"),
                'label'=>Yii::t('app','Credit status'),
                'format'=>'raw',
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'credit_status',
                        \app\models\Users::getCreditStatus(),
                        ['class' => 'form-control', 'prompt' => '']
                        ),
                
                'headerOptions'=>['style'=>'width:10px;text-align:center;color:#337ab7;'],
                'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                'value'=>function ($model){
                      if($model['credit_status'] == 1){
                        $span = '<span class="badge badge-success" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Active').'</span>';
                      }elseif ($model['credit_status'] == 0) {
                         $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Deactive').'</span>';
                      }else{
                         $span = '';
                      }
        
                      return $span;
                
                }
                ],

                [
                'attribute'=>'tariff',
                'format'=>'raw',
                'visible'=>User::hasPermission("customer-tariff-view"),
                'headerOptions'=>['style'=>'width:10px;text-align:center;color:#337ab7;'],
                'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ],            
                'value'=>function ($model) use ($siteConfig) {
                    
                  return $model['tariff']." ". $siteConfig['currency'];

                }
                ],




                [
                'attribute'=>'balance',
                'visible'=>User::hasPermission("customer-balance-view"),
                'label'=>Yii::t('app','Balance'),
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ],            
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:10px;text-align:center;color:#337ab7;'],
                'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                'value'=>function ($model) use ($siteConfig) {
                    
                         
                    
                  return $model['balance']." ".$siteConfig['currency'];
                }
                ], 



                [
                'attribute'=>'status',
                'label'=>Yii::t('app','Status'),
                'visible'=>User::hasPermission("customer-status-view"),
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status',
                        \app\models\Users::getStatus(),
                        ['class' => 'form-control', 'prompt' => '']
                        ),
                'headerOptions'=>['style'=>'width:20px;text-align:center;color:#337ab7;'],
                'contentOptions'=>['style'=>'width:20px;text-align:center;'],
                'format'=>'raw',
                'value'=>function ($model){
                  if($model['user_status'] == 1){
                    $span = '<span class="badge badge-success" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Active').'</span>';
                  }elseif ($model['user_status'] == 0) {
                    $span = '<span class="badge badge-warning" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Pending').'</span>';
                  }elseif($model['user_status'] == 2 ){
                    $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Deactive').'</span>';
                  }elseif ($model['user_status'] == 3) {
                    $span = '<span style="background-color: #795548; width: 75px; display: block; margin: 0 auto; color: #fff;" class="badge ">'.Yii::t('app','Archive').'</span>';
                  }elseif ($model['user_status'] == 4) {
                    $span = '<span class="badge badge-info">'.Yii::t('app','Reconnect').'</span>';
                  
                  }elseif ($model['user_status'] == 6) {
                    $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Black list').'</span>';
                  }elseif ($model['user_status'] == 7) {
                    $span = '<span class="badge badge-primary" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','VIP').'</span>';
                  }
      


                  return $span;
                }
                ],

       
                [
                    'attribute'=>'paid_time_type',
             
                    'visible'=>User::hasPermission("customer-paid-view"),
                    'filter' => Html::activeDropDownList(
                            $searchModel,
                            'paid_time_type',
                            \app\models\RequestOrder::getPaidDayType(),
                            ['class' => 'form-control', 'prompt' => '']
                            ),
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off'
                    ],            
                    'headerOptions'=>['style'=>'width:10px;text-align:center;color:#337ab7;'],
                    'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                    'value'=>function ($model){
                      return \app\models\RequestOrder::getPaidDayType()[$model['paid_time_type']];
                    }
                ],


                [
                    'label'=>Yii::t('app','Cron will update at'),
                    'visible'=>User::hasPermission("customer-created-view"),
                    'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                    'contentOptions'=>['style'=>'width:50px;text-align:center;'],
                    'filter'=>kartik\daterange\DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'createTimeRange',
                        'convertFormat'=>true,
                        'startAttribute'=>'createTimeStart',
                        'endAttribute'=>'createTimeEnd',
                        'pluginOptions'=>[
                            'locale'=>[
                                'format'=>'Y-m-d'
                            ]
                        ]
                    ]),
                    'format'=>'raw',
                    'value'=>function($model){
                      return date('d/m/Y H:i:s',$model['updated_at']);
                    }
                ],


            ]
        ]); ?>
       
        <?php 
        Modal::begin([
            'title' => Yii::t('app','User information'),
            'id' => 'modal',
            'options' => [
                'tabindex' => false // important for Select2 to work properly
            ],
            'size' => 'modal-lg',
        ]);
        echo "<div id='modalContent'></div>";
        Modal::end();
        ?>
    <?php Pjax::end(); ?>
</div>




<?php
$this->registerJs('
var progressBarValue = 0;
var progressBarSelector = "#auto-reload-progress-bar";
var gridContainerSelector = "#radact-grid-pjax";
var progressBarInterval;

function updateProgressBar() {
    var progressBar = $(progressBarSelector);
    progressBarValue += (100 / 25);
    progressBar.css("width", progressBarValue + "%");

    if (progressBarValue > 100) {
        clearInterval(progressBarInterval);
        resetProgressBar();
        reloadGrid();
    }
}

function resetProgressBar() {
    progressBarValue = 0;
    $(progressBarSelector).css("width", "0%");
}

function reloadGrid() {
    $.pjax.reload({
        container: gridContainerSelector,
        type: "POST",
        timeout: 5000
    });
}

function startReloadInterval() {
    return setInterval(function () {
        updateProgressBar();
    }, 1000); // 1000 milliseconds = 1 second
}


resetProgressBar();
progressBarInterval = startReloadInterval();

setInterval(function () {
    resetProgressBar();
    clearInterval(progressBarInterval);
    progressBarInterval = startReloadInterval();
}, 28000); // 28000 milliseconds = 28 seconds

');
?>


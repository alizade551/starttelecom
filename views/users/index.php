<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\widgets\GridBulkActions;
use yii\helpers\Url;
use app\models\Cities;
use app\widgets\GridPageSize;
use kartik\export\ExportMenu;
use webvimark\modules\UserManagement\models\User;


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

$this->title = Yii::t('app','All customers');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'fullname',
    'contract_number',
    'phone',
    'packet_name',
    'city.city_name',
    'district.district_name',
    'location.name',
    'room',
    [
        'attribute'=>'inet_login',
        'value'=>function ( $model ){
            $html = '';
            foreach ($model->usersInets as $login) {
                $html .= "<div>". $login->login ."</div>";
            }
           return $html;
        }
    ],
    'tariff',
    'bank_status',
    'balance',
    'bonus',
    'paid_day',
    [
        'attribute'=>'paid_time_type',
        'value'=>function ( $model ){
    
           return \app\models\RequestOrder::getPaidDayType()[$model->paid_time_type];
        }
    ],
    [
        'attribute'=>'status',
        'value'=>function ( $model ){
          if($model->status == 1){
            $span = '<span class="badge badge-success" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Active').'</span>';
          }elseif ($model->status == 0) {
            $span = '<span class="badge badge-warning" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Pending').'</span>';
          }elseif($model->status == 2 ){
            $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Deactive').'</span>';
          }elseif ($model->status == 3) {
            $span = '<span style="background-color: #795548; width: 75px; display: block; margin: 0 auto; color: #fff;" class="badge ">'.Yii::t('app','Archive').'</span>';
          }elseif ($model->status == 4) {
            $span = '<span class="badge badge-info">'.Yii::t('app','Reconnect').'</span>';
          
          }elseif ($model->status == 6) {
            $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Black list').'</span>';
          }elseif ($model->status == 7) {
            $span = '<span class="badge badge-primary" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','VIP').'</span>';
          }

          return $span;
        }
    ],

    [
        'attribute'=>'updated_at',
        'value'=>function ($model, $index, $widget) {
            return date("d-m-Y", $model->updated_at);
        },
        'filterWidgetOptions' => [
            'pluginOptions'=>['format' => 'dd-mm-yyyy']
        ]
    ],

    ['class' => 'yii\grid\ActionColumn'],
];

if ( isset( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value )) {
     $gridViewVisibility = json_decode( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value ,true );

}else{
    $gridViewVisibility["serial-view"] = "true@Serial";
    $gridViewVisibility["customer_name-view"] = "true@Customer";
    $gridViewVisibility["contract_number-view"] = "true@Contract number";
    $gridViewVisibility["phone-view"] = "true@Phone";
    $gridViewVisibility["city-view"] = "true@City";
    $gridViewVisibility["district-view"] = "true@District";
    $gridViewVisibility["location-view"] = "true@Location";
    $gridViewVisibility["room-view"] = "true@Room";
    $gridViewVisibility["services-view"] = "true@Services";
    $gridViewVisibility["login-view"] = "true@Login";
    $gridViewVisibility["credit_status-view"] = "true@Credit status";
    $gridViewVisibility["tariff-view"] = "true@Tariff";
    $gridViewVisibility["bank_status-view"] = "true@Bank";
    $gridViewVisibility["balance-view"] = "true@Balance";
    $gridViewVisibility["bonus-view"] = "true@Bonus";
    $gridViewVisibility["status-view"] = "true@Status";
    $gridViewVisibility["paid_day-view"] = "true@Paid day";
    $gridViewVisibility["paid_type-view"] = "true@Paid type";
    $gridViewVisibility["updated_at-view"] = "true@Renewal date";
}


$content = '';
$actions = '';

$viewVisibility =  \app\widgets\gridViewVisibility\viewVisibility::widget(
    [
        'params'=>$gridViewVisibility,
        'url'=>Url::to('/users/grid-view-visibility'),
        'pjaxContainer'=>'#users-grid'
    ]
);

$exportMenu = ExportMenu::widget(
    [
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'clearBuffers' => true, //optional
    'filename' => 'Users_'.date('d-m-Y h:i:s'),
     'dropdownOptions' => [
        'label' => 'Export',
        'class' => 'btn btn-info btn-info',
     ],
    ]
);

    $pageSize = GridPageSize::widget([
        'pjaxId'=>'users-grid-pjax',
        'pageName'=>'_grid_page_size_users'
    ]);

    $pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";


    $actions .= $viewVisibility;
    $actionsContainer = "<div class='helper-action-container'>".$actions."</div>";

    $content = "<div class='helper-container'>".$pageSizeContainer.$actionsContainer."</div>";

?>



<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <?=$exportMenu ?>
        </div>
    </div>
</div>

<div class="card custom-card ">
    <div class="row">
        <div class="col-sm-12">
            <?php Pjax::begin(['id'=>'users-grid-pjax']); ?>
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
                            'class' => 'yii\grid\SerialColumn',
                            'headerOptions'=>['style'=>'width:1px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:1px;text-align:center;'],
                            'visible'=> ( str_contains( $gridViewVisibility["serial-view"], 'true' )  == "true" && User::hasPermission("customer-serial-column") ) ? true : false,
                        ],

                        [
                            'attribute'=>'fullname',
                            'label'=>Yii::t('app','Customer'),
                            'visible'=> ( str_contains($gridViewVisibility["customer_name-view"], 'true') == "true" && User::hasPermission("customer-fullname-view") ) ? true : false,
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],
                            'format'=>'raw',
                            'headerOptions'=>['style'=>'width:150px;text-align:center;color;'],
                            'contentOptions'=>['style'=>'width:150px;text-align:center;'],
                            'value'=>function ($model){

                              return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model->id.'">'.$model->fullname.'</a>';
                            }
                        ],

                        [
                            'attribute'=>'contract_number',
                            'visible'=> ( str_contains($gridViewVisibility["contract_number-view"] , 'true') == "true" && User::hasPermission("customer-contract-number-view") ) ? true : false,
                            'label'=>Yii::t('app','Contract number'),
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],            
                            'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                            'value' => 'contract_number'
                        ],
                        
                        [
                            'attribute'=>'phone',
                            'visible'=> ( str_contains( $gridViewVisibility["phone-view"] , 'true') == "true" && User::hasPermission("customer-phone-view") ) ? true : false,
                            'label'=>Yii::t('app','Phone'),
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],            
                            'headerOptions'=>['style'=>'width:60px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:60px;text-align:center;'],
                            'value'=>function ($model){
                                $explode_number = explode(",", $model->phone);
                                // $implode = implode(" , ",  $explode_number);
                              return $explode_number[0];
                            }
                        ],


                        [
                            'attribute'=>'city',
                            'label'=>Yii::t('app','City'),
                            'visible'=> ( str_contains( $gridViewVisibility["city-view"] , 'true') == "true" && User::hasPermission("customer-city-view") ) ? true : false,
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],            
                            'headerOptions'=>['style'=>'width:20px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:20px;text-align:center;'],
                            'value' => 'city.city_name'
                        ],


                        [
                            'attribute'=>'district',
                            'visible'=> ( str_contains( $gridViewVisibility["district-view"] , 'true') == "true" && User::hasPermission("customer-district-view") ) ? true : false,
                            'label'=>Yii::t('app','District'),
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],            
                            'headerOptions'=>['style'=>'width:80px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:80px;text-align:center;'],
                            'value' => 'district.district_name'
                        ],


                        [
                            'attribute'=>'location',
                            'visible'=> ( str_contains( $gridViewVisibility["location-view"] , 'true') == "true" && User::hasPermission("customer-location-view") ) ? true : false,
                            'label'=>Yii::t('app','Location'),
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],            
                            'headerOptions'=>['style'=>'width:110px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:110px;text-align:center;'],
                            'value' => 'locations.name'
                        ],

                        [
                            'attribute'=>'room',
                            'visible'=> ( str_contains( $gridViewVisibility["room-view"] , 'true')   == "true" && User::hasPermission("customer-room-view") ) ? true : false,
                            'label'=>Yii::t('app','Room'),
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],            
                            'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                            'value'=>function ($model){
                              return $model->room;
                            }
                        ],


                        [

                            'attribute'=>Yii::t('app','Services'),
                            'headerOptions'=>['style'=>'width:1px;text-align:center;'],
                            'visible'=> ( str_contains( $gridViewVisibility["services-view"] , 'true') == "true" && User::hasPermission("customer-services-view") ) ? true : false,
                            'format'=>'raw',
                            'filter' => Html::activeDropDownList(
                            $searchModel,
                            'services_n',
                            ArrayHelper::map(app\models\Services::find()->all(),'id','service_name'),
                            ['class' => 'form-control', 'prompt' => '']
                            ),
                            'contentOptions'=>['style'=>'width:1px;text-align:center;'],
                            'value' => function($model) {
                                $icons = [];
                                foreach ($model->serviceOne as $service_one) {
                                    if ($service_one->service_alias == "internet") {
                                        $ic = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-globe"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg> ';
                                    }elseif ($service_one->service_alias == "wifi") {
                                        $ic = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-wifi"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>';
                                    }elseif ($service_one->service_alias == "tv") {
                                        $ic = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tv"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>';

                                    }elseif ($service_one->service_alias == "voip") {
                                        $ic = '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>';

                                    }

                                    $icons[] = $ic;
                                }
                                return implode("\n", $icons);
                            },
                        ],

                        [
                            'label'=>Yii::t('app','Inet login'),
                            'visible'=> ( str_contains( $gridViewVisibility["login-view"] , 'true') == "true" && User::hasPermission("customer-inet-login-view") ) ? true : false,
                            'attribute'=>'inet_login',
                            'format'=>'raw',
                            'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                            'value'=>function ($model){
                                $html = '';
                                foreach ($model->usersInets as $login) {
                                    $html .= "<div>". $login->login ."</div>";
                                }
                               return $html;
                            }
                        ],

                        [
                            'attribute'=>'credit_status',
                            'visible'=> ( str_contains( $gridViewVisibility["credit_status-view"]  , 'true') == "true" && User::hasPermission("customer-credit-status-view") ) ? true : false,
                            'label'=>Yii::t('app','Credit status'),
                            'format'=>'raw',
                            'filter' => Html::activeDropDownList(
                                    $searchModel,
                                    'credit_status',
                                    \app\models\Users::getCreditStatus(),
                                    ['class' => 'form-control', 'prompt' => '']
                                    ),
                            
                            'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                            'value'=>function ($model){
                                  if($model->credit_status == 1){
                                    $span = '<span class="badge badge-success" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Active').'</span>';
                                  }elseif ($model->credit_status == 0) {
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
                            'visible'=> ( str_contains( $gridViewVisibility["tariff-view"] , 'true' ) == "true" && User::hasPermission("customer-tariff-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:10px;text-align:center;;'],
                            'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],            
                            'value'=>function ($model){
                                $text = '';
                                if ($model->bank_status == 1) {
                                    $text = '<span class="badge badge-success" style=" background: black; border: 1px solid #000000;">'.$model->tariff." ".$model->currency.'</span>';
                                }else{
                                     $text = $model->tariff." ". $model->currency;
                                }
                              return $text;

                            }
                        ],

                        [
                            'attribute'=>'bank_status',
                            'label'=>Yii::t("app","Bank"),
                            'visible'=> ( str_contains( $gridViewVisibility["bank_status-view"] , 'true' )  == "true" && User::hasPermission("customer-bank-view") ) ? true : false,
                            'format'=>'raw',
                            'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                            'filter' => Html::activeDropDownList(
                                    $searchModel,
                                    'bank_status',
                                    \app\models\Users::getCreditStatus(),
                                    ['class' => 'form-control', 'prompt' => '']
                                    ),
                            'value'=>function ($model){
                                $text = '';
                                if ($model->bank_status == 1) {
                                    $text = '<span class="" ><i class="fa fa-university" aria-hidden="true"></i></span>';
                                }else{
                                     $text = '-';
                                }
                              return $text;

                            }
                        ],


                        [
                            'attribute'=>'balance',
                            'visible'=> ( str_contains( $gridViewVisibility["balance-view"] , 'true' ) == "true" && User::hasPermission("customer-balance-view") ) ? true : false,
                            'label'=>Yii::t('app','Balance'),
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],   

                            'format'=>'raw',
                            'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                            'value'=>function ($model){
                                $text = '';
                                if ($model->bank_status == 1) {
                                    $text = '<span class="badge badge-success" style="background: black; border: 1px solid #000000;">'.$model->balance." ".$model->currency.'</span>';
                                }else{
                                     $text = $model->balance." ".$model->currency;
                                }
                              return $text;
                            }
                        ], 

                       [
                            'attribute'=>'bonus',
                            'visible'=> ( str_contains( $gridViewVisibility["bonus-view"] , 'true' )  == "true" && User::hasPermission("customer-bonus-view") ) ? true : false,
                            'label'=>Yii::t('app','Bonus'),  
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off'
                            ],           
                            'format'=>'raw',
                            'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                            'value'=>function ($model){
                                $text = $model->bonus." ".$model->currency;
                              return $text;
                            }
                        ], 

                        [
                            'attribute'=>'status',
                            'label'=>Yii::t('app','Status'),
                            'visible'=> ( str_contains( $gridViewVisibility["status-view"] , 'true' )  == "true" && User::hasPermission("customer-status-view") ) ? true : false,
                            'filter' => Html::activeDropDownList(
                                    $searchModel,
                                    'status',
                                    \app\models\Users::getStatus(),
                                    ['class' => 'form-control', 'prompt' => '']
                                    ),
                            'headerOptions'=>['style'=>'width:20px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:20px;text-align:center;'],
                            'format'=>'raw',
                            'value'=>function ($model){
                              if($model->status == 1){
                                $span = '<span class="badge badge-success" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Active').'</span>';
                              }elseif ($model->status == 0) {
                                $span = '<span class="badge badge-warning" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Pending').'</span>';
                              }elseif($model->status == 2 ){
                                $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Deactive').'</span>';
                              }elseif ($model->status == 3) {
                                $span = '<span style="background-color: #795548; width: 75px; display: block; margin: 0 auto; color: #fff;" class="badge ">'.Yii::t('app','Archive').'</span>';
                              }elseif ($model->status == 4) {
                                $span = '<span class="badge badge-info">'.Yii::t('app','Reconnect').'</span>';
                              
                              }elseif ($model->status == 6) {
                                $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','Black list').'</span>';
                              }elseif ($model->status == 7) {
                                $span = '<span class="badge badge-primary" style="width:75px;display:block;margin:0 auto;">'.Yii::t('app','VIP').'</span>';
                              }else{
                                $span = $model->status;
                              }
                  


                              return $span;
                            }
                        ],

                        // [
                        //     'attribute'=>'paid_day',
                        //     'label'=>Yii::t('app','Paid day'),
                        //     'visible'=> ( $gridViewVisibility["paid_day-view"] == "true" && User::hasPermission("customer-paid-day-view") ) ? true : false,
                        //     'filterInputOptions' => [
                        //         'class' => 'form-control',
                        //         'autocomplete' => 'off'
                        //     ],            
                        //     'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                        //     'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                        //     'value'=>function ($model){
                        //       return $model->paid_day;
                        //     }
                        // ],
                        // [
                        //     'attribute'=>'paid_time_type',
                        //     'visible'=> ( $gridViewVisibility["paid_type-view"] == "true" && User::hasPermission("customer-paid-type-view") ) ? true : false,
                        //     'filter' => Html::activeDropDownList(
                        //             $searchModel,
                        //             'paid_time_type',
                        //             \app\models\RequestOrder::getPaidDayType(),
                        //             ['class' => 'form-control', 'prompt' => '']
                        //             ),
                        //     'filterInputOptions' => [
                        //         'class' => 'form-control',
                        //         'autocomplete' => 'off'
                        //     ],            
                        //     'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                        //     'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                        //     'value'=>function ($model){
                        //       return \app\models\RequestOrder::getPaidDayType()[$model->paid_time_type];
                        //     }
                        // ],


                        // [
                        //     'label'=>Yii::t('app','Renewal date'),
                        //     'visible'=> ( $gridViewVisibility["updated_at-view"] == "true" &&  User::hasPermission("customer-renewal-date-view") ) ? true : false,
                        //     'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                        //     'contentOptions'=>['style'=>'width:50px;text-align:center;'],
                        //     'filter'=>kartik\daterange\DateRangePicker::widget([
                        //         'model'=>$searchModel,
                        //         'attribute'=>'createTimeRange',
                        //         'convertFormat'=>true,
                        //         'startAttribute'=>'createTimeStart',
                        //         'endAttribute'=>'createTimeEnd',
                        //         'pluginOptions'=>[
                        //             'locale'=>[
                        //                 'format'=>'Y-m-d'
                        //             ]
                        //         ]
                        //     ]),
                        //     'format'=>'raw',
                        //     'value'=>function($model){
                        //       return date('d/m/Y H:i:s',$model->updated_at);
                        //     }
                        // ],


                    ]
                ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>




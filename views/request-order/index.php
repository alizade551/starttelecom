<?php
use yii\bootstrap4\Modal;
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


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app','All orders');
$this->params['breadcrumbs'][] = $this->title;

$content = '';
$actions = '';

if ( isset( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value )) {
     $gridViewVisibility = json_decode( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value ,true );

}else{
    $gridViewVisibility["serial-view"] = "true@Serial";
    $gridViewVisibility["customer-view"] = "true@Customer";
    $gridViewVisibility["phone-view"] = "true@Phone";
    $gridViewVisibility["city-view"] = "true@City";
    $gridViewVisibility["district-view"] = "true@District";
    $gridViewVisibility["location-view"] = "true@Location";
    $gridViewVisibility["room-view"] = "true@Room";
    $gridViewVisibility["second_status-view"] = "true@Second status";
    $gridViewVisibility["created_at-view"] = "true@Created at";
}

$viewVisibility =  \app\widgets\gridViewVisibility\viewVisibility::widget(
    [
    'params'=>$gridViewVisibility,
    'url'=>Url::to('/request-order/grid-view-visibility'),
    'pjaxContainer'=>'#request-order-grid'
    ]
);

$pageSize = GridPageSize::widget([
    'pjaxId'=>'request-order-pjax',
    'pageName'=>'_grid_page_size_request_orders'
]);


$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$actions .= $viewVisibility;

$actionsContainer = "<div class='helper-action-container'>".$actions."</div>";

$content = "<div class='helper-container'>".$pageSizeContainer.$actionsContainer."</div>";

?>


<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if (User::canRoute("/request-order/create")): ?>
                <a class="btn btn-success" data-pjax="0" href="/request-order/create" title=" <?=Yii::t("app","Create an order") ?>">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <?=Yii::t("app","Create an order") ?>
                </a>
            <?php endif?>
        </div>
    </div>
</div>

<div class="card custom-card ">
    <div class="row">
        <div class="col-sm-12">
            <?php Pjax::begin(['id'=>'request-order-pjax',]); ?> 
                <?= GridView::widget([
                    'id'=>'request-order-grid',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pager'=>[
                      'class'=>yii\bootstrap4\LinkPager::class
                    ], 
                        'layout'=>' '.$content .'{items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible'=> ( str_contains( $gridViewVisibility["serial-view"], 'true')  == "true" && User::hasPermission("order-serial-column") ) ? true : false,
                                'headerOptions'=>['style'=>'width:1%;text-align:center;'],
                                'contentOptions'=>['style'=>'width:1%;text-align:center;'],
                            ],

                            [
                                'attribute'=>'fullname',
                                'label'=>Yii::t('app','Customer'),
                                'format'=>'raw',
                                'options'=>['style'=>'width:20%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:20%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:20%;text-align:center;'],
                                'visible'=> ( str_contains( $gridViewVisibility["customer-view"] , 'true') && User::hasPermission("order-fullname-view") ) ? true : false,
                                'value'=>function ($model){
                                    $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                                    return  '<a class="modal-d" title="'.Yii::t('app','About information on {customer}',['customer'=>$model->fullname]).'"  data-pjax="0" href="'.$langUrl .Url::to("/request-order/view").'?id='.$model->id.'">'.$model->fullname.'</a>';
                                }
                            ],
                            
                            [
                                'attribute'=>'phone',
                                'visible'=> ( str_contains( $gridViewVisibility["phone-view"] , 'true')  == "true" && User::hasPermission("order-phone-view") ) ? true : false,
                                'options'=>['style'=>'width:5%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:5%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:5%;text-align:center;'],
                                'value'=>function ($model){
                                  return $model->phone;
                                }
                            ],

                            [
                                'attribute'=>'city',
                                'label'=>Yii::t('app','City'),
                                'options'=>['style'=>'width:10%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                                'visible'=>( str_contains( $gridViewVisibility["city-view"] , 'true' )  == "true" && User::hasPermission("order-city-view")) ? true : false,
                                'value'=>function ($model){
                                    if (isset($model->city->city_name)) {
                                         return $model->city->city_name;
                                    }
                                }
                            ],
                            [
                                'attribute'=>'district',
                                'visible'=>( str_contains( $gridViewVisibility["district-view"] , 'true' )  == "true" && User::hasPermission("order-district-view") ) ? true : false,
                                'label'=>Yii::t('app','District'),
                                'options'=>['style'=>'width:10%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                                'value'=>function ($model){
                                    if ( isset($model->district->district_name) ) {
                                       return $model->district->district_name;
                                    }
                                }
                            ],                
                            [
                                'attribute'=>'location',
                                'visible'=>( str_contains( $gridViewVisibility["location-view"] , 'true' )  == "true" && User::hasPermission("order-location-view") ) ? true : false,
                                'label'=>Yii::t('app','Location'),
                                'options'=>['style'=>'width:10%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                                'value'=>function ($model){
                                    if  ( isset($model->locations->locations) ) {
                                         return $model->locations->name;
                                    }
                                }
                            ],

                            [
                                'attribute'=>'room',
                                'visible'=>(  str_contains( $gridViewVisibility["room-view"] , 'true' ) == "true" && User::hasPermission("order-room-view") ) ? true : false, 
                                'label'=>Yii::t('app','Room'),
                                'options'=>['style'=>'width:5%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:5%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:5%;text-align:center;'],
                                'value'=>function ($model){
                                  return $model->room;
                                }
                            ],
                           
                        [
                            'attribute'=>'second_status',
                            'visible'=>( str_contains( $gridViewVisibility["second_status-view"] , 'true' )  == "true" && User::hasPermission("order-second-status-view") ) ? true : false, 
                            'filter' => Html::activeDropDownList(
                                $searchModel,
                                'second_status',
                                \app\models\RequestOrder::getStatus(),
                                ['class' => 'form-control', 'prompt' => '']
                             ),
                            'options'=>['style'=>'width:10%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                            'format'=>'raw',
                            'value'=>function ($model){
                                   $span= '';
                                   if ($model->status == 0) {
                                        return $span = '<span class="badge badge-warning" >'.Yii::t('app','Pending').'</span>';
                                    }
                                    if ($model->second_status == '4') {
                                        return $span = '<span class="badge badge-info">'.Yii::t('app','Reconnect').'</span>';

                                    }if ($model->second_status == '5') {
                                        return $span .= '<span class="badge badge-primary" >'.Yii::t('app','New service').'</span>';
                                    }

                                    if ($model->second_status == null) {
                                        return $span .= '-';
                                    }
                                    if ($model->status == '1') {
                                        return $span .= '-';
                                    }

                               }
                        ],

                        [
                            'label'=>Yii::t('app','Created at'),
                            'visible'=>( str_contains( $gridViewVisibility["created_at-view"] , 'true' ) == "true" && User::hasPermission("order-created-at-view") ) ? true : false, 
                            'options'=>['style'=>'width:10%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:10%;text-align:center;'],
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
                              return date('d-m-Y H:i',$model->request_at);
                            }
                        ],

                            [
                                      'class' => 'yii\grid\ActionColumn',
                                      'header'=>Yii::t('app','Confrimation'),
                                      'visible'=>User::hasPermission("order-confrimation-view"),
                                      'options'=>['style'=>'width:1%;text-align:center;'],
                                      'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                                      'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                                      'template' => '{accept-order}',
                                      'buttons' => [
                                        'accept-order' => function ($url,$model){
                                          if ($model->status == 6) {
                                            return '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>';
                                          }


                                               return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>', $url, ['data' => ['pjax' => 0,'data-target'=>"#right_modal_xl"],'class'=>'modal-d','id'=>'accept-order',   'style'=>'text-align:center;display:block;','title'=>Yii::t('app','Request confirmation for {customer} customer',['customer'=>$model->fullname])]);
                                        }
                                      ],
                            ],

                            [
                                      'class' => 'yii\grid\ActionColumn',
                                      'header'=>Yii::t('app','Cancelation'),
                                      'visible'=>User::hasPermission("order-cancel-view"),
                                      'options'=>['style'=>'width:5%;text-align:center;'],
                                      'headerOptions' => ['style' => 'width:5%;text-align:center;'],
                                      'contentOptions' => ['style' => 'width:5%;text-align:center;'],
                                      'template' => '{cancel-order}',
                                      'buttons' => [
                                        'cancel-order' => function ($url,$model){
                                        $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                                        if ($model->second_status == '3') {
                                          return '<a data-pjax="0" href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a>';
                                          }


                                            if ($model->status == 0) {
                                          return  '<a  data-fancybox data-src="#hidden-content-sure-deleting-'.$model->id.'" href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a>


                                          <div class="sure_recconnect" style="display: none;width:600px" id="hidden-content-sure-deleting-'.$model->id.'">
                                                    <div class="fcc">
                                                       <h3 ><b>'.Yii::t('app','Delete request! (cancel customer!)').' </b></h3>
                                                      <p>'.Yii::t('app', 'Are you sure  want to delete {customer}\'s request ?', ['customer' => $model->fullname]).'</p>
                                                      <button class="btn btn-danger sure-deleting-button" data-req_id="'.$model->id.'"     title="Delete Request" >'.Yii::t("app","Delete from database").'</button>
                                                      <button data-fancybox-close="" class="btn btn-secondary" title="'.Yii::t("app","Close").'">'.Yii::t("app","Close").'</button>          
                                                    </div>
                                            </div>';
                                            }elseif ($model->second_status == '4') {
                                            return  '<a  data-fancybox data-src="#hidden-content-sure-reconnect-'.$model->id.'" href="javascript:void(0)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                            </a>
                                              <div class="sure_recconnect" style="display: none;" id="hidden-content-sure-reconnect-'.$model->id.'">
                                                        <div class="fcc">
                                                           <h3 ><b>'.Yii::t("app","Re-connect cancelation !").'</b></h3>
                                                          <p >'.Yii::t('app', 'Are you sure you want to archive again {customer} ?', ['customer' => $model->fullname]).'</p>
                                                          <button class="btn btn-danger sure-reconnect-button" data-req_id="'.$model->id.'"     title="Re-connecting" >'.Yii::t("app","Send to archive").'</button>
                                                          <button data-fancybox-close="" class="btn btn-secondary"  title="Close" >'.Yii::t('app','Close').'</button>           
                                                        </div>
                                                </div>';


                                            }elseif ($model->second_status == '5') {
                                                return  '<a  data-fancybox data-src="#hidden-content-sure-new_service-'.$model->id.'" href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></a>

                                                      <div class="sure_new_service" style="display: none;" id="hidden-content-sure-new_service-'.$model->id.'">
                                                                <div class="fcc">
                                                                   <h3 ><b>'.Yii::t("app","New service cancelation!").'</b></h3>
                                                                  <p>
                                                                  '.Yii::t('app', 'Are you sure you want to cancel new service to {customer} ?', ['customer' => $model->fullname]).'</p>
                                                                <p style="color:#4caf50"><b>'.Yii::t("app","Note").': </b>'.Yii::t("app","Old packets dont be change and customer be active").' </p>
                                                                  <button class="btn btn-danger sure-new_service-button" data-req_id="'.$model->id.'"     title="Re-connecting" >'.Yii::t("app","Cancel").' </button>
                                                                  <button data-fancybox-close="" class="btn btn-secondary"  title="Close" >'.Yii::t('app','Close').'</button>           
                                                                </div>
                                                        </div>';
                                            }
                                              
                                        }
                                      ],
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header'=>Yii::t('app','Service'),
                                'visible'=>User::hasPermission("order-adding-packet-view"),
                                'options'=>['style'=>'width:1%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                                'template'=>'{adding-packet}',
                                    'buttons'=>[
                                        'adding-packet'=>function($url,$model){
                                            return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>',$url,[
                                                'data'=>['pjax'=>0],
                                                'class'=>'modal-d',
                                                'id'=>'add-service',
                                                'title'=>Yii::t("app","Adding service for {customer}",['customer'=>$model['fullname']])
                                            ]); 
                                         }
                                    ]
                            ],


                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header'=>Yii::t('app','Item'),
                                'visible'=>User::hasPermission("order-adding-inventory-view"),
                                'options'=>['style'=>'width:1%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                                'template'=>'{add-item-to-user}',
                                    'buttons'=>[
                                        'add-item-to-user'=>function($url,$model){
                                            $countItem = \app\models\ItemUsage::find()->where(['user_id'=>$model->id])->count();
                                            $color = ( $countItem > 0 ) ? "green" : "red";
                                            return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>',$url.'&city_id='.$model->city_id.'&dis_id='.$model->district_id,[
                                                'data'=>['pjax'=>0],
                                                'class'=>'modal-d',
                                                'id'=>'add-item',
                                                'style'=>'color:'.$color.'',
                                                'title'=>Yii::t('app','Adding item to {customer}',['customer'=>$model['fullname']])
                                            ]); 
                                         }
                                    ]
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header'=>Yii::t('app','On map'),
                                'visible'=>User::hasPermission("order-adding-cordinate-view"),
                                'options'=>['style'=>'width:1%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                                'template'=>'{adding-cordinate}',
                                    'buttons'=>[
                                        'adding-cordinate'=>function($url,$model){
                                            $color = ( $model->cordinate != "" ) ? "green" : "red";
                                            return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',$url.'&city_id='.$model->city_id.'&dis_id='.$model->district_id,[
                                                'data'=>['pjax'=>0],
                                                'class'=>'modal-d',
                                                'id'=>'t-map',
                                                'style'=>'color:'.$color.'',
                                                'title'=>Yii::t('app','Adding coordinate to {customer}',['customer'=>$model['fullname']])
                                            ]); 
                                         }
                                    ]
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'visible'=> User::canRoute(['/request-order/update']),
                                'header'=>Yii::t('app','Update'),
                                'options'=>['style'=>'width:1%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                                'template'=>'{update}',
                                    'buttons'=>[
                                        'update'=>function($url,$model){
                                            if ($model->damage_status == '1') {
                                                return '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>';
                                            }
                                            return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',Url::to("/request-order/update").'?id='.$model->id.'&city_id='.$model->city_id.'&dis_id='.$model->district_id,[
                                                'data'=>['pjax'=>0],
                                                'title'=>$model['fullname']
                                            ]); 
                                         }
                                    ]
                            ],



                    ],
                ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>


<?php 
Modal::begin([
    'title' => Yii::t("app","Request order information"),
    'id' => 'modal',
    'class'=>'drawer right-align',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],

    'size' => 'modal-lg',
    'asDrawer' => true,

]);
echo "<div id='modalContent'></div>";
Modal::end();
?>


<?php 

$this->registerJs('
$(document).on("click","#t-map",function(){
    $("#modal").removeClass("drawer right-align")
});

$(document).on("click","#add-item, #add-service, #accept-order ",function(){
    $("#modal").addClass("drawer right-align");
});

$(document).on("click",".delete_req button",function(){
  var that = $(this);
  var req = that.attr("data-req_id");
  var url_inxdex = "'.Url::toRoute('request-order/index').'";    
  $.ajax({
    url:"'.Url::to("/request-order/delete").'?id="+req,
    success:function(res){
       window.location.href = url_inxdex;
    }
    });
  });

$(document).on("click",".sure-reconnect-button",function(){
  var that = $(this);
  var req = that.attr("data-req_id");
  var url_inxdex = "'.Url::toRoute('request-order/index').'";    

  $.ajax({
    url:"'.Url::to("/request-order/cancel-order").'?id="+req,
    success:function(res){
       window.location.href = url_inxdex;
    }

    });
  });

$(document).on("click",".sure-new_service-button",function(){
  var that = $(this);
  var req = that.attr("data-req_id");
  var url_inxdex = "'.Url::toRoute('request-order/index').'";    

  $.ajax({
    url:"'.Url::to("/request-order/cancel-order").'?id="+req,
    success:function(res){
       window.location.href = url_inxdex;
    }

    });
  });

$(document).on("click",".sure-deleting-button",function(){
  var that = $(this);
  var req = that.attr("data-req_id");
  var url_inxdex = "'.Url::toRoute('request-order/index').'";    

  $.ajax({
    url:"'.Url::to("/request-order/cancel-order").'?id="+req,
    success:function(res){
       window.location.href = url_inxdex;
    }

    });
  });

');

 ?>
 

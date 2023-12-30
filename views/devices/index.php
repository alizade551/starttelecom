<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Arrayhelper;
use app\widgets\GridBulkActions;
use app\widgets\GridPageSize;
use yii\helpers\Url;
use kartik\date\DatePicker;
use yii\bootstrap4\Modal;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DevicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Devices');
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

if ( isset( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value )) {
     $gridViewVisibility = json_decode( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value ,true );

}else{
    $gridViewVisibility["devices-serial-view"] = "true@Serial";
    $gridViewVisibility["devices-name-view"] = "true@Device name";
    $gridViewVisibility["devices-vendor_name-view"] = "true@Vendor name";
    $gridViewVisibility["devices-type-view"] = "true@Device type";
    $gridViewVisibility["devices-district-view"] = "true@District";
    $gridViewVisibility["devices-location-view"] = "true@Location";
    $gridViewVisibility["devices-port-count-view"] = "true@Port count";
    $gridViewVisibility["devices-pon-port-count-view"] = "true@Pon port count";
    $gridViewVisibility["devices-ip-address-view"] = "true@Ip address";
    $gridViewVisibility["devices-description-view"] = "true@Description";
    $gridViewVisibility["devices-published-view"] = "true@Published";
    $gridViewVisibility["devices-created-at-view"] = "true@Created at";
    $gridViewVisibility["devices-capacity-view"] = "true@Capacity";
}

    $content = '';
    $actions = '';

    $viewVisibility =  \app\widgets\gridViewVisibility\viewVisibility::widget(
        [
            'params'=>$gridViewVisibility,
            'url'=>Url::to('/devices/grid-view-visibility'),
            'pjaxContainer'=>'#devices-grid-pjax'
        ]
    );

    $pageSize = GridPageSize::widget([
        'pjaxId'=>'devices-grid-pjax',
        'pageName'=>'_grid_page_size_devices'
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
            <?php if ( User::canRoute("/devices/create") ): ?>
               <a class="btn btn-success modal-d add-element" data-pjax="0" href="<?=$langUrl ?>/devices/create" title="<?=Yii::t('app','Create a device') ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <?=Yii::t("app","Create a device") ?>
               </a>
            <?php endif?>
        </div>
    </div>
</div>



<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
            <?php Pjax::begin(['id'=>'devices-grid-pjax']); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pager'=>[
                      'class'=>yii\bootstrap4\LinkPager::class
                    ], 
                    'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',

                    'columns' => [
                        [
                        'class' => 'yii\grid\SerialColumn',
                        'visible'=> ( str_contains( $gridViewVisibility["devices-serial-view"], 'true')  == "true" && User::hasPermission("devices-serial-column-view") ) ? true : false,
                        'options'=>['style'=>'width:1%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                        ],

                        [
                            'attribute'=>'name',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-name-view"], 'true')  == "true" && User::hasPermission("devices-name-view") ) ? true : false,
                            'format'=>'raw',
                            'options'=>['style'=>'width:10%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                            'value'=>function ($model){
                            return  $model->name;
                             
                            }
                        ],

                        [
                            'attribute'=>'vendor_name',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-vendor_name-view"], 'true')  == "true" && User::hasPermission("devices-vendor-name-view") ) ? true : false,
                            'label'=>Yii::t('app','Vendor name'),
                            'format'=>'raw',
                            'options'=>['style'=>'width:10%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                            'value'=>function ($model){
                            return  $model->vendor_name;
                             
                            }
                        ],

                        [
                            'attribute'=>'type',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-type-view"], 'true')  == "true" && User::hasPermission("devices-type-view") ) ? true : false,
                            'format'=>'raw',
                            'options'=>['style'=>'width:5%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:5%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:5%;text-align:center;'],
                            'value'=>function ($model){
                            return  $model->type;
                             
                            }
                        ],
                        
                        [
                            'attribute'=>'district',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-district-view"], 'true')  == "true" && User::hasPermission("devices-district-view") ) ? true : false,
                            'label'=>Yii::t('app','District'),
                            'format'=>'raw',
                            'options'=>['style'=>'width:20%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:20%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:20%;text-align:center;'],
                            'value'=>function ($model){
                                $html = '';
                                foreach ($model->deviceLocations as $d) {
                                    $html .= "<div>". $d->district->district_name ."</div>";
                                }
                               return $html;
                             
                            }
                        ],

                        [
                            'attribute'=>'location',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-location-view"], 'true')  == "true" && User::hasPermission("devices-location-view") ) ? true : false,
                            'label'=>Yii::t('app','Location'),
                            'format'=>'raw',
                            'options'=>['style'=>'width:15%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:15%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:15%;text-align:center;'],
                            'value'=>function ($model){
                                $html = '';
                                foreach ($model->deviceLocations as $d) {
                                    if (isset($d->location)) {
                                        $html .= "<div>". $d->location->name ."</div>"; 
                                    }else{
                                        $html.= "-";
                                    }
                                }
                               return $html;
                             
                            }
                        ],

                        [
                            'attribute'=>'description',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-description-view"], 'true')  == "true" && User::hasPermission("devices-description-view") ) ? true : false,
                            'label'=>Yii::t('app','Description'),
                            'format'=>'raw',
                            'options'=>['style'=>'width:15%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:15%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:15%;text-align:center;'],
                            'value'=>function ($model){
                             return  $model->description;
                            }
                        ],


                        [
                            'label'=>Yii::t('app','Created at'),
                            'visible'=> ( str_contains( $gridViewVisibility["devices-created-at-view"], 'true')  == "true" && User::hasPermission("devices-created-at-view") ) ? true : false,
                            'options'=>['style'=>'width:15%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:15%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:15%;text-align:center;'],
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
                              return date('d/m/Y H:i:s',$model['created_at']);
                            }
                        ],

                        [
                            'attribute'=>'port_count',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-port-count-view"], 'true')  == "true" && User::hasPermission("devices-port-count-view") ) ? true : false,
                            'label'=>Yii::t('app','Port count'),
                            'format'=>'raw',
                            'options'=>['style'=>'width:5%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:5%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:5%;text-align:center;'],
                            'value'=>function ($model){
                                if ($model->type == "switch") {
                                 return  $model->port_count;
                                }
                              return  '-';
                            }
                        ],
                        [
                            'attribute'=>'pon_port_count',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-pon-port-count-view"], 'true')  == "true" && User::hasPermission("devices-pon-port-count-view") ) ? true : false,
                            'format'=>'raw',
                            'options'=>['style'=>'width:5%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:5%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:5%;text-align:center;'],
                            'value'=>function ($model){
                                if ($model->type != "switch") {
                                 return  $model->pon_port_count;
                                }
                              return  '-';
                            }
                        ],
                        [
                            'attribute'=>'ip_address',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-ip-address-view"], 'true')  == "true" && User::hasPermission("devices-ip-address-view") ) ? true : false,
                            'label'=>Yii::t('app','Ip address'),
                            'format'=>'raw',
                            'options'=>['style'=>'width:5%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:5%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:5%;text-align:center;'],
                            'value'=>function ($model){
                             return  $model->ip_address;
                            }
                        ],

                        [
                            'attribute'=>'published',
                            'visible'=> ( str_contains( $gridViewVisibility["devices-published-view"], 'true')  == "true" && User::hasPermission("devices-published-view") ) ? true : false,
                            'options'=>['style'=>'width:5%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:5%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:5%;text-align:center;'],
                            'format'=>'raw',
                            'value'=>function($model){
                                if ($model->published == 1) {
                                   $icon = '<span class="badge  badge-success"><i class="fa fa-check"></span>';
                                }else{
                                    $icon = '<span class="badge  badge-danger"><i class="fa fa-times"></span>';
                                }
                                return Html::a($icon, ['toggle-attribute','attribute'=>'published','id'=>$model->id]);
                            }
                        ],




                        [
                            'label'=>Yii::t('app','Capacity'),
                            'visible'=> ( str_contains( $gridViewVisibility["devices-capacity-view"], 'true')  == "true" && User::hasPermission("devices-capacity-view") ) ? true : false,
                            'format'=>'raw',
                            'headerOptions'=>['style'=>'width:120px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:120px;text-align:center;'],
                            'value'=>function ($model){
                                if ($model->type == "switch") {
                                    $switchBusyPortCount = \app\models\SwitchPorts::find()->where(['device_id'=>$model->id])->andWhere(['not', ['u_s_p_i' => null]])->count();
                                   return $switchBusyPortCount."/".$model->port_count;
                                }
                                    $epgonBusyPortCount = \app\models\EgonBoxPorts::find()
                                    ->leftJoin('egpon_box','egpon_box.id=egon_box_ports.egon_box_id')
                                    ->where(['device_id'=>$model->id])
                                    ->andWhere(['not', ['u_s_p_i' => null]])
                                    ->count();

                                if ($model->type == "epon") {
                                   return $epgonBusyPortCount."/". $model->pon_port_count * 64;
                                }

                                if ($model->type == "gpon") {
                                   return $epgonBusyPortCount."/". $model->pon_port_count * 128;
                                }
                                if ($model->type == "xpon") {
                                   return $epgonBusyPortCount."/". $model->pon_port_count * 256;
                                }

                            }
                        ],

                        [
                                  'class' => 'yii\grid\ActionColumn',
                                  'header'=>Yii::t('app','Define coverage'),
                                  'options'=>['style'=>'width:10px;text-align:center;'],
                                  'visible'=>User::canRoute("devices/add-location"),
                                  'headerOptions' => ['style' => 'width:120px;text-align:center;'],
                                  'contentOptions' => ['style' => 'width:120px;text-align:center;'],
                                  'template' => '{add-location}',
                                  'buttons' => [
                                    'add-location' => function ($url,$model){

                                        return Html::a('<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="2"></circle><path d="M16.24 7.76a6 6 0 0 1 0 8.49m-8.48-.01a6 6 0 0 1 0-8.49m11.31-2.82a10 10 0 0 1 0 14.14m-14.14 0a10 10 0 0 1 0-14.14"></path></svg>', $url, ['data' => ['pjax' => 0],'class'=>'modal-d','style'=>'text-align:center;display:block;','title'=>Yii::t('app','Define a location to {device} device',['device'=>$model->name])]);
                                    }
                                  ],
                        ],

                        [
                                  'class' => 'yii\grid\ActionColumn',
                                  'header'=>Yii::t('app','Port'),
                                  'options'=>['style'=>'width:10px;text-align:center;'],
                                  'visible'=>User::canRoute("ddevices/list-port"),
                                  'headerOptions' => ['style' => 'width:50px;text-align:center;'],
                                  'contentOptions' => ['style' => 'width:50px;text-align:center;'],
                                  'template' => '{list-ports}',
                                  'buttons' => [
                                    'list-ports' => function ($url,$model){
                                      if ($model->type == "gpon" || $model->type == "epon" || $model->type == "xpon" ) {
                                       return '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>';
                                      }
                                        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>', $url, ['data' => ['pjax' => 0],'class'=>'modal-d','style'=>'text-align:center;display:block;','title'=>Yii::t('app','{device} device list ports',['device'=>$model->name])]);
                                    }
                                  ],
                        ],
                        [
                                  'class' => 'yii\grid\ActionColumn',
                                  'header'=>Yii::t('app','Pon-port'),
                                  'visible'=>User::canRoute("devices/list-pon-port"),
                                  'options'=>['style'=>'width:1%;text-align:center;'],
                                  'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                                  'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                                  'template' => '{list-pon-port}',
                                  'buttons' => [
                                    'list-pon-port' => function ($url,$model){
                                      if ($model->type == "switch") {
                                       return '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>';
                                      }
                                        return Html::a('
                                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>', 
                                            $url, 
                                            [
                                                'data' => ['pjax' => 0],
                                                'class'=>'modal-d',
                                                'style'=>'text-align:center;display:block;',
                                                'title'=>Yii::t('app','{device} device pon-port setting',['device'=>$model['name']])
                                            ]
                                        );
                                    }
                                  ],
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=>User::canRoute("devices/update-cordinate"),
                            'header'=>Yii::t('app','Cordinate'),
                            'options'=>['style'=>'width:1%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                            'template'=>'{update-cordinate}',
                                'buttons'=>[
                                    'update-cordinate'=>function($url,$model){
                                        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',$url."&city_id=".$model['city_id']."&dis_id=".$model['district_id']."&type=".$model->type,[
                                            'data'=>['pjax'=>0],
                                            'title'=>$model['name']
                                        ]); 
                                     }
                                ]
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=>User::canRoute("devices/update"),
                            'header'=>Yii::t('app','Update'),
                            'options'=>['style'=>'width:1%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                            'template'=>'{update}',
                                'buttons'=>[
                                    'update'=>function($url,$model){
                                        return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url."&city_id=".$model['city_id']."&dis_id=".$model['district_id']."&type=".$model->type,[
                                            'data'=>['pjax'=>0],
                                            'class'=>'modal-d',
                                            'title'=> Yii::t('app','Update: {device_name} device',['device_name'=>$model['name']])  
                                        ]); 
                                     }
                                ]
                        ],


                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=>User::canRoute("devices/delete"),
                            'header'=>Yii::t('app','Delete'),
                            'options'=>['style'=>'width:1%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                            'template' => '{delete}',
                            'buttons' => [
                            'delete' => function($url, $model){
                                 return '<a href="javascript:void(0)" data-href="'.$url.'" class="alertify-confirm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>';
                                    }
                                ],
                        ],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
<?php 
Modal::begin([
    'title' => Yii::t("app","Device"),
    'id' => 'modal',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],
    'size' => 'modal-lg',
    'asDrawer'=>true
]);
echo "<div id='modalContent'></div>";
Modal::end();
?>

<?php 
$this->registerJs('
$(document).on("click",".alertify-confirm",function(){
  var that = $(this);
  var message  = "'.Yii::t("app","Are you sure want to delete this ?").'";
      alertify.confirm( message, function (e) {
        if (e) {
           $.ajax({
               url:that.attr("data-href"),
               type:"post",
               success:function(response){
                    if(response.status == "success"){
                        that.closest("tr").fadeOut("slow");
                        alertify.set("notifier","position", "top-right");
                        alertify.success("'.Yii::t("app","Device was deleted successfuly").'");
                    }else{
                         alertify.set("notifier","position", "top-right");
                         alertify.error(response.message);
                    }
               }
           });
        } 
    }).set({title:"'.Yii::t("app","Delete a device").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});
    return false;
});

');

 ?>
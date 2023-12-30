<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use app\widgets\GridPageSize;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ItemUsageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Used items');

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    [
        'attribute'=>'item',
        'label'=>Yii::t('app','Item'),
        'value'=>function ($model){
          return $model['item_name'];
        }       
    ],
    
    [
        'attribute'=>'item_stock',
        'label'=>Yii::t('app','Item stock'),
        'value'=>function ($model){
          return $model['item_stock_sku'];
        }
    ],


    [
        'attribute'=>'quantity',
        'label'=>Yii::t('app','Quantity'),
        'value'=>function ($model){
          return $model['quantity'];
        }
    ],


    [
        'attribute'=>'user',
        'label'=>Yii::t('app','Customer'),
        'value'=>function ($model){
            if ($model['user_id'] == "") {
               $username = '-';
            }else{
                 $username =  $model['user_fullname'];
            }
          return  $username;
        }
    ],



    [
        'attribute'=>'credit',
        'label'=>Yii::t('app','Credit'),
        'value'=>function ($model){
            if ($model['credit'] == "") {
              $credit = "-";
            }else{
                $credit = \app\models\ItemUsage::getItemCredit()[$model['credit']];
            }
          return  $credit;
        }
    ],

    [
        'attribute'=>'month',
        'label'=>Yii::t('app','Month count'),
        'value'=>function ($model){
            if ($model['month'] == 0) {
               $credit_m = '-';
            }else{
                 $credit_m = $model['month'];
            }
          return  $credit_m;
        }
    ],

    [
        'attribute'=>'mac_address',
        'label'=>Yii::t('app','Mac address'),
        'value'=>function ($model){
            if ($model['mac_address'] == "") {
               $mac_address = '-';
            }else{
                 $mac_address = $model['mac_address'];
            }
          return  $mac_address;
        }
    ],
    [
        'attribute'=>'location',
        'value'=>function ($model){
          return $model['location_name'];
        }
    ],
    [
        'attribute'=>'status',
        'value'=>function ($model){
          return  yii\helpers\ArrayHelper::merge(\app\models\ItemUsage::getItemStatus(),\app\models\ItemUsage::getItemCompanyStatus()) [$model['status']];
        }
    ],

    [
        'label'=>Yii::t('app','Created at'),
        'format'=>'raw',
        'value'=>function($model){
          return date('d/m/Y H:i:s',$model['created_at']);
        }
    ],

    ['class' => 'yii\grid\ActionColumn'],
];

$exportMenu = ExportMenu::widget(
    [
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'clearBuffers' => true, //optional
    'filename' => 'Item_Usages_'.date('d-m-Y h:i:s'),
     'dropdownOptions' => [
        'label' => 'Export',
        'class' => 'btn btn-info btn-info',
     ],
    ]
);


$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'item-usage-grid-pjax',
    'pageName'=>'_grid_page_size_item_usage'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";


?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <?=$exportMenu ?>
        </div>
    </div>
</div>

<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
             <?php Pjax::begin(['id'=>'item-usage-grid-pjax']); ?>
                <?= GridView::widget([
                    'id'=>'item-usage-grid',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pager'=>[
                      'class'=>yii\bootstrap4\LinkPager::class
                    ], 
                    'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible'=> (  User::hasPermission("item-usage-serial-column-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                        ],
                        [
                            'attribute'=>'item',
                            'label'=>Yii::t("app","Item"),
                            'visible'=> (  User::hasPermission("item-usage-item-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['item_name'];
                            }
                        ],


                        [
                            'attribute'=>'item_stock',
                            'label'=>Yii::t("app","Item stock"),  
                            'visible'=> (  User::hasPermission("item-usage-item_stock-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['item_stock_sku'];
                            }
                        ],

                        [
                            'attribute'=>'user',
                            'visible'=> (  User::hasPermission("item-usage-customer-view") ) ? true : false,
                            'label'=>Yii::t('app','Customer'),
                            'format'=>'raw',
                            'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                            'value'=>function ($model){
                                if ($model['user_id'] == "") {
                                   $username = '-';
                                }else{
                                     $username =  '<a href="/users/view?id='.$model['user_id'].'">'.$model['user_fullname'].'</a>';
                                }
                              return  $username;
                            }
                        ],

                        [
                            'attribute'=>'quantity',
                            'label'=>Yii::t("app","Quantity"),  
                            'visible'=> (  User::hasPermission("item-usage-quantity-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['quantity'];
                            }
                        ],





                        [
                            'attribute'=>'credit',
                            'visible'=> (  User::hasPermission("item-usage-credit-view") ) ? true : false,
                            'label'=>Yii::t('app','Credit'),
                            'format'=>'raw',
                             'filter'=>\app\models\ItemUsage::getItemCredit(),
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                            'value'=>function ($model){
                                if ($model['credit'] == "") {
                                  $credit = "-";
                                }else{
                                    $credit = \app\models\ItemUsage::getItemCredit()[$model['credit']];
                                }
                              return  $credit;
                            }
                        ],

                        [
                            'attribute'=>'month',
                            'visible'=> (  User::hasPermission("item-usage-month-view") ) ? true : false,
                            'label'=>Yii::t('app','Month count'),
                            'format'=>'raw',
                            'headerOptions'=>['style'=>'width:4%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:4%;text-align:center;'],
                            'value'=>function ($model){
                                if ($model['month'] == 0) {
                                   $credit_m = '-';
                                }else{
                                     $credit_m = $model['month'];
                                }
                              return  $credit_m;
                            }
                        ],

                        [
                        'attribute'=>'mac_address',
                        'label'=>Yii::t('app','Mac address'),
                        'visible'=> (  User::hasPermission("item-usage-mac_address-view") ) ? true : false,
                        'format'=>'raw',
                        'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                        'value'=>function ($model){
                            if ($model['mac_address'] == "") {
                               $mac_address = '-';
                            }else{
                                 $mac_address = $model['mac_address'];
                            }
                          return  $mac_address;
                        }
                        ],
                        [
                            'attribute'=>'location',
                            'label'=>Yii::t('app','Location'),
                            'visible'=> ( User::hasPermission("item-usage-location-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['location_name'];
                            }
                        ],
                        [
                            'attribute'=>'status',
                            'visible'=> (  User::hasPermission("item-usage-status-view") ) ? true : false,
                            'filter'=> yii\helpers\ArrayHelper::merge(\app\models\ItemUsage::getItemStatus(),\app\models\ItemUsage::getItemCompanyStatus()),
                            'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                            'value'=>function ($model){
                              return  yii\helpers\ArrayHelper::merge(\app\models\ItemUsage::getItemStatus(),\app\models\ItemUsage::getItemCompanyStatus()) [$model['status']];
                            }
                        ],

                        [
                            'label'=>Yii::t('app','Created at'),
                            'visible'=> ( User::hasPermission("item-usage-created-at-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10%;text-align:center;'],
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
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=>User::canRoute('/item-usage/delete'),
                            'options'=>['style'=>'width:20px;text-align:center;'],
                            'header'=>Yii::t('app','Delete'),
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                            'template'=>'{delete}',
                            'buttons'=>[
                            'delete' => function($url, $model){
                                 return '<a href="javascript:void(0)" data-href="'.$url.'" class="alertify-confirm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>';
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
    'title' => Yii::t('app','Item stock'),
    'id' => 'modal',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],
    'size' => 'modal-lg',
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
                             alertify.success("'.Yii::t("app","Item usage was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete an item usage").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});   
        return false;
    });

');

 ?>
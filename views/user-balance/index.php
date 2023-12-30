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
use kartik\export\ExportMenu;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\StoreItemCountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t("app","Transactions");
$siteConfig = \app\models\SiteConfig::find()->asArray()->one();

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'label'=>Yii::t('app','Customer'),
        'value'=>function( $model ){
            return $model['user_name'];
        },
    ],
    'balance_in',
    'balance_out',
    'bonus_in',
    'bonus_out',
    [
        'attribute'=>'pay_for',
        'label'=>Yii::t('app','Pay for'),  
        'value'=>function ($model){
            $payFor = '';
           if ( $model['pay_for'] == 0 ) {
                $payFor = Yii::t("app","internet");
            }elseif ( $model['pay_for'] == 1 ) {
                   $payFor = Yii::t("app","tv");
            }elseif ( $model['pay_for'] == 2 ) {
                   $payFor = Yii::t("app","wifi");
            }elseif ( $model['pay_for'] == 3 ) {
                   $payFor = Yii::t("app","item");
            }elseif ( $model['pay_for'] == 4 ) {
                   $payFor = Yii::t("app","VoIP");
            }
          return $payFor;
        }
    ],

    [
        'attribute'=>'receipt_code',
        'label'=>Yii::t('app','Receipt'),  
        'value'=>function ($model){
          return  $model['receipt_code'];
        }
    ],
    'transaction',
    [
        'attribute'=>'member',
        'label'=>Yii::t('app','Member fullname'),  
        'value'=>function ($model){
            if ($model['member_fullaname'] == null) {
                return '-';
            }
          return  $model['member_fullaname'];
        }
    ],

    [
        'attribute'=>'payment_method',
        'label'=>Yii::t('app','Payment method'),  
        'value'=>function ($model){
            if ( $model['payment_method'] == 0 ) {
                $p_m = Yii::t("app","Internal");
            }elseif ( $model['payment_method'] == 1 ) {
                $p_m = Yii::t("app","External");
            }
          return $p_m;
        }
    ],

    [
        'attribute'=>'item_name',
        'label'=>Yii::t('app','Item'),  
        'value'=>function ($model){
            if ( $model['item_usage_id'] != null ) {
                $item = $model['item_name'];
            }else{
                $item = "-";
            }
          return $item;
        }
    ],     

    [
        'label'=>Yii::t('app','Created at'),
        'value'=>function($model){
          return date('d/m/Y H:i:s',$model['created_at']);
        }
    ],

    ['class' => 'yii\grid\ActionColumn'],
];

$content = '';
$actions = '';

if ( isset( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value )) {
     $gridViewVisibility = json_decode( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value ,true );
}else{
    $gridViewVisibility["serial-view"] = "true@Serial";
    $gridViewVisibility["id-view"] = "true@ID";
    $gridViewVisibility["customer-view"] = "true@Customer";
    $gridViewVisibility["balance_in-view"] = "true@Balance in";
    $gridViewVisibility["balance_out-view"] = "true@Balance out ";
    $gridViewVisibility["bonus_in-view"] = "true@Bonus in";
    $gridViewVisibility["bonus_out-view"] = "true@Bonus out ";
    $gridViewVisibility["pay_for-view"] = "true@Pay for";
    $gridViewVisibility["receipt-view"] = "true@Receipt";
    $gridViewVisibility["transaction-view"] = "true@Transaction";
    $gridViewVisibility["operator-view"] = "true@Operator";
    $gridViewVisibility["payment_method-view"] = "true@Payment method";
    $gridViewVisibility["item-view"] = "true@Item";
    $gridViewVisibility["status-view"] = "true@Status";
    $gridViewVisibility["created_at-view"] = "true@Created at";

    
}

$viewVisibility =  \app\widgets\gridViewVisibility\viewVisibility::widget(
    [
        'params'=>$gridViewVisibility,
        'url'=>Url::to('/user-balance/grid-view-visibility'),
        'pjaxContainer'=>'#ub-grid-pjax'
    ]
);

$pageSize = GridPageSize::widget([
    'pjaxId'=>'ub-grid-pjax',
    'pageName'=>'_grid_page_size_user-balance'
]);


$exportMenu = ExportMenu::widget(
    [
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'clearBuffers' => true, //optional
    'filename' => 'Transactions_'.date('d-m-Y h:i:s'),
     'dropdownOptions' => [
        'label' => 'Export',
        'class' => 'btn btn-info btn-info'
     ],
    ]
);

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


<div class="card custom-card">
    <div class="row">


        <div class="col-sm-12">
<?php Pjax::begin(['id'=>'ub-grid-pjax']); ?>


<?= GridView::widget([
        'id'=>'ub-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager'=>[
          'class'=>yii\bootstrap4\LinkPager::class
        ], 
        'layout'=>' '.$content .'{items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
        'columns' => [

            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions'=>['style'=>'width:5px;text-align:center;'],
                'contentOptions'=>['style'=>'width:5px;text-align:center;'],
                'visible'=> ( str_contains( $gridViewVisibility["serial-view"], 'true') == "true" && User::hasPermission("transaction-serial-column") ) ? true : false,
            ],

            [
                'attribute'=>'id',
                'visible'=> ( str_contains( $gridViewVisibility["id-view"], 'true') == "true" && User::hasPermission("transaction-id-view") ) ? true : false,
                'label'=>Yii::t('app','ID'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                'value'=>function ($model){
                  return  $model['id'];
                }
            ],

            [
                'attribute'=>'user_name',
                'visible'=> ( str_contains( $gridViewVisibility["customer-view"], 'true') == "true" && User::hasPermission("transaction-user_name-view") ) ? true : false,
                'label'=>Yii::t('app','Customer'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:180px;text-align:center;'],
                'contentOptions'=>['style'=>'width:180px;text-align:center;'],
                'value'=>function ( $model ){
                    if ( $model['user_id'] == null ) {
                        return  '-';
                    }
                    return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model['user_id'].'">'.$model['user_name'].'</a>';
                 
                }
            ],
            [
                'attribute'=>'balance_in',
                'enableSorting' => true,
                'visible'=> ( str_contains( $gridViewVisibility["balance_in-view"], 'true') == "true" && User::hasPermission("transaction-balance_in-view") ) ? true : false,
                'label'=>Yii::t('app','Balance in'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                'value'=>function ($model) use ($siteConfig) {
        
                  return  $model['balance_in']." AZN";
                }
            ],
            [
                'attribute'=>'balance_out',
                'visible'=> ( str_contains( $gridViewVisibility["balance_out-view"], 'true') == "true" && User::hasPermission("transaction-balance_out-view") ) ? true : false,
                'label'=>Yii::t('app','Balance out'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                'value'=>function ($model) use ($siteConfig){
                  return  $model['balance_out']." AZN";
                }
            ],

            [
                'attribute'=>'bonus_in',
                'visible'=> ( str_contains( $gridViewVisibility["bonus_in-view"], 'true') == "true" && User::hasPermission("transaction-bonus_in-view") ) ? true : false,
                'label'=>Yii::t('app','Bonus in'),              
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                'value'=>function ($model) use ($siteConfig){
        
                  return  $model['bonus_in']." AZN";
                }
            ],


            [
                'attribute'=>'bonus_out',
                'visible'=> ( str_contains( $gridViewVisibility["bonus_out-view"], 'true') == "true" && User::hasPermission("transaction-bonus_out-view") ) ? true : false,
                'label'=>Yii::t('app','Bonus out'),                 
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                'value'=>function ($model) use ($siteConfig){
        
                  return  $model['bonus_out']." AZN";
                }
            ],


            [
                'attribute'=>'pay_for',
                'visible'=> ( str_contains( $gridViewVisibility["pay_for-view"], 'true') == "true" && User::hasPermission("transaction-pay_for-view") ) ? true : false,
                'label'=>Yii::t('app','Pay for'),  
                'filter'=>\app\models\UserBalance::getDropDownListPayFor(),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                'contentOptions'=>['style'=>'width:50px;text-align:center;'],
                'value'=>function ($model){
                    
                    $payFor = '';
                   if ( $model['pay_for'] == 0 ) {
                        $payFor = Yii::t("app","internet");
                    }elseif ( $model['pay_for'] == 1 ) {
                           $payFor = Yii::t("app","tv");
                    }elseif ( $model['pay_for'] == 2 ) {
                           $payFor = Yii::t("app","wifi");
                    }elseif ( $model['pay_for'] == 3 ) {
                           $payFor = Yii::t("app","item");
                    }elseif ( $model['pay_for'] == 4 ) {
                           $payFor = Yii::t("app","VoIP");
                    }
                  return $payFor;
                }
            ],

            [
                'attribute'=>'receipt_code',
                'visible'=> ( str_contains( $gridViewVisibility["receipt-view"], 'true') == "true" && User::hasPermission("transaction-receipt_code-view") ) ? true : false,
                'label'=>Yii::t('app','Receipt'),  
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:80px;text-align:center;'],
                'contentOptions'=>['style'=>'width:80px;text-align:center;'],
                'value'=>function ($model){
        
                  return  $model['receipt_code'];
                }
            ],

            [
                'attribute'=>'transaction',
                'visible'=> ( str_contains( $gridViewVisibility["transaction-view"], 'true') == "true" && User::hasPermission("transaction-transaction-view") ) ? true : false,
                'label'=>Yii::t('app','Transaction'),  
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                'contentOptions'=>['style'=>'width:50px;text-align:center;'],
                'value'=>function ($model){
                    if ($model['transaction'] == null) {
                      return  '-';
                    }
                  return  $model['transaction'];
                }
            ],

            [
                'attribute'=>'member',
                'visible'=> ( str_contains( $gridViewVisibility["operator-view"], 'true') == "true" && User::hasPermission("transaction-operator-view") ) ? true : false,
                'label'=>Yii::t('app','Operator'),  
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                'value'=>function ($model){
                    if ($model['member_fullaname'] == null) {
                        return '-';
                    }
                  return  $model['member_fullaname'];
                }
            ],

            [
                'attribute'=>'payment_method',
                'visible'=> ( str_contains( $gridViewVisibility["payment_method-view"], 'true') == "true" && User::hasPermission("transaction-payment_method-view") ) ? true : false,
                'label'=>Yii::t('app','Payment method'),  
                'filter'=>\app\models\UserBalance::getDropDownListPaymentMethod(),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                'value'=>function ($model){
                    if ( $model['payment_method'] == 0) {
                        $p_m = Yii::t("app","Internal");
                    }elseif ( $model['payment_method'] == 1) {
                           $p_m = Yii::t("app","External");
                    }
                  return $p_m;
                }
            ],

            [
                'attribute'=>'status',
                'visible'=> ( str_contains( $gridViewVisibility["status-view"], 'true') == "true" && User::hasPermission("transaction-status-view") ) ? true : false,
                'label'=>Yii::t('app','Status'),  
                'filter'=>\app\models\UserBalance::getDropDownListPaymentStatus(),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                'value'=>function ($model){
      
                  return app\models\UserBalance::getDropDownListPaymentStatus()[$model['status']];
                }
            ],
            
            [
                'attribute'=>'item_name',
                'visible'=> ( str_contains( $gridViewVisibility["item-view"], 'true') == "true" && User::hasPermission("transaction-item_name-view") ) ? true : false,
                'label'=>Yii::t('app','Item'),  
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                'value'=>function ($model){
                    if ( $model['item_usage_id'] != null ) {
                        $item = $model['item_name'];
                    }else{
                        $item = "-";
                    }
                  return $item;
                }
            ],        
            [
                'label'=>Yii::t('app','Created at'),
                'visible'=> ( str_contains( $gridViewVisibility["created_at-view"], 'true') == "true" && User::hasPermission("transaction-created_at-view") ) ? true : false,
                'headerOptions'=>['style'=>'width:160px;text-align:center;'],
                'contentOptions'=>['style'=>'width:160px;text-align:center;'],
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

            // [
            //     'class' => 'yii\grid\CheckboxColumn', 'options'=>['style'=>'width:1%'],
            //     'headerOptions'=>['style'=>'width:20px;text-align:center'],
            //     'contentOptions'=>['style'=>'width:20px;'],
            // ],


            // [
            //   'class' => 'yii\grid\ActionColumn',
            //   'options'=>['style'=>'width:80px;text-align:center;'],
            //   'options'=>['style'=>'width:80px;text-align:center;'],
            //   'header' => Yii::t("app","Transfer"),
            //   'visible'=> ( $gridViewVisibility["transfer-view"] == "true" && User::canRoute(["/items/add-stock"]) ) ? true : false,
            //   'headerOptions' => ['style' => 'text-align:center'],
            //   'template' => '{transfer-amount}',
            //     'buttons' => [
            //         'transfer-amount' => function ($url, $model) {
            //             if ( $model['balance_in'] >  0 ) {
            //             $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
            //                 return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="16 3 21 3 21 8"></polyline><line x1="4" y1="20" x2="21" y2="3"></line><polyline points="21 16 21 21 16 21"></polyline><line x1="15" y1="15" x2="21" y2="21"></line><line x1="4" y1="4" x2="9" y2="9"></line></svg>',$url,[
            //                     'data'=>['pjax'=>0],'class'=>'modal-d','style'=>'display:block;text-align:center','title'=>Yii::t('app','Amount transfer from {user}',['user'=>$model['user_name']])
            //                 ]);
            //             }else{
            //                 return '<div  style="text-align:center;display:block"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg></div>';
                        
            //             }
            //         }
            //       ]
            // ],

            [
                'class' => 'yii\grid\ActionColumn',
                'visible'=> (  User::canRoute(["/user-balance/update"]) ) ? true : false,
                'options'=>['style'=>'width:10px;text-align:center'],
                'header'=>Yii::t('app','Update'),
                'headerOptions'=>['style'=>'width:10px;text-align:center'],
                'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                'template'=>'{update}',
                    'buttons'=>[
                        'update'=>function($url,$model){
                            return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url,[
                                'data'=>['pjax'=>0],
                                'class'=>'modal-d',
                                'title'=>Yii::t("app","Update transaction for {customer}",['customer'=>$model['user_name']])
                            ]); 
                         }
                    ]
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'visible'=> (  User::canRoute(["/user-balance/delete"]) ) ? true : false,
                'options'=>['style'=>'width:10px;text-align:center;'],
                'header'=>Yii::t('app','Delete'),
                'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                'template' => '{delete}',
                'buttons' => [
                'delete' => function($url, $model){
                     return '<a href="javascript:void(0)" data-href="'.$url.'&user_id='.$model['user_id'].'&balance_in='.$model['balance_in'].'&balance_out='.$model['balance_out'].'&username='.$model['user_name'].'" class="alertify-confirm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>';
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
$this->registerJs('
  $(document).on("click",".alertify-confirm",function(){
      var that = $(this);
      var message  = "'.Yii::t("app","Are you sure want to delete transaction ?").'";
          alertify.confirm( message, function (e) {
            if (e) {
               $.ajax({
                   url:that.attr("data-href"),
                   type:"post",
                   success:function(response){
                        if(response.status == "success"){
                            that.closest("tr").fadeOut("slow");
                             alertify.set("notifier","position", "top-right");
                            alertify.success("'.Yii::t("app","Transaction was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a transaction").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});     
        return false;
    });

');

 ?>
 

<?php 
    Modal::begin([
        'title' => Yii::t("app","Balance operation"),
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
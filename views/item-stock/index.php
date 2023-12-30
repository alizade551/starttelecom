<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use app\widgets\GridPageSize;
use yii\helpers\Url;
use webvimark\modules\UserManagement\models\User;


/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ItemStockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stock management');
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";


$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'item-stock-grid-pjax',
    'pageName'=>'_grid_page_size_item_stock'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";


?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
        </div>
    </div>
</div>

<div class="card custom-card" >
    <div class="row">
        <div class="col-sm-12">
             <?php Pjax::begin(['id'=>'item-stock-grid-pjax']); ?>

            <?= GridView::widget([
                'id'=>'item-stock-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pager'=>[
                  'class'=>yii\bootstrap4\LinkPager::class
                ], 
                'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
                'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible'=> ( User::hasPermission("items-stock-serial-column-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                        ],

                        [
                            'attribute'=>'item',
                            'label'=>Yii::t("app","Item"),
                            'visible'=> ( User::hasPermission("items-stock-item-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['item_name'];
                            }
                        ],

                        [
                            'attribute'=>'warehouse',
                            'label'=>Yii::t("app","Warehouse"),
                            'visible'=> ( User::hasPermission("items-stock-warehouse-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['warehouse_name'];
                            }
                        ],

                        [
                            'attribute'=>'sku',
                            'visible'=> ( User::hasPermission("items-stock-sku-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['sku'];
                            }
                        ],

                        [
                            'attribute'=>'quantity',
                            'visible'=> (  User::hasPermission("items-stock-quantity-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['quantity'];
                            }
                        ],
                        [
                            'attribute'=>'price',
                            'visible'=> ( User::hasPermission("items-stock-price-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['price'];
                            }
                        ],

                        [
                            'label'=>Yii::t('app','Created at'),
                            'visible'=> (  User::hasPermission("items-stock-created-at-view") ) ? true : false,
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
                            'visible'=>User::canRoute(["/item-stock/update"]),
                            'options'=>['style'=>'width:20px;text-align:center'],
                            'header'=>Yii::t('app','Update'),
                            'headerOptions'=>['style'=>'width:1%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:1%;text-align:center;'],
                            'template'=>'{update}',
                                'buttons'=>[
                                    'update'=>function($url,$model){
                                        return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url,[
                                            'data'=>['pjax'=>0],'class'=>'modal-d','title'=>Yii::t('app', 'Update an item stock: {item_sku}', ['item_sku' => $model['sku']])
                                        ]); 
                                     }
                                ]
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=>User::canRoute(["/item-stock/delete"]),
                            'options'=>['style'=>'width:20px;text-align:center;'],
                            'header'=>Yii::t('app','Delete'),
                            'headerOptions'=>['style'=>'width:1%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:1%;text-align:center;'],
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
      var message  = "'.Yii::t("app","Are you sure want to delete ?").'";
          alertify.confirm( message, function (e) {
            if (e) {
               $.ajax({
                   url:that.attr("data-href"),
                   type:"post",
                   success:function(response){
                        if(response.status == "success"){
                            that.closest("tr").fadeOut("slow");
                             alertify.set("notifier","position", "top-right");
                             alertify.success("'.Yii::t("app","Item stock was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete an item").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});   
        return false;
    });

');

 ?>
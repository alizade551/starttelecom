<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use app\widgets\GridPageSize;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Items');
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'items-grid-pjax',
    'pageName'=>'_grid_page_size_items'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";


?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if (User::canRoute("/items/create")): ?>
               <a title="<?=Yii::t('app','Create an item') ?>" class="btn btn-success modal-d add-element" data-pjax="0" href="<?=$langUrl ?>/items/create">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <?=Yii::t("app","Create an item") ?>
               </a>
            <?php endif?>
        </div>
    </div>
</div>

<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
             <?php Pjax::begin(['id'=>'items-grid-pjax']); ?>
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
                            'visible'=> ( User::hasPermission("item-serial-column-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                        ],
                        
                        [
                            'attribute'=>'name',
                            'visible'=> (User::hasPermission("item-name-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['name'];
                            }
                        ],
                        [
                            'attribute'=>'category',
                            'visible'=> ( User::hasPermission("item-category-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['category_name'];
                            }
                        ],
                        [
                            'attribute'=>'total_stock',
                            'visible'=> ( User::hasPermission("item-total_stock-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['total_stock'];
                            }
                        ],
                        [
                            'label'=>Yii::t('app','Created at'),
                            'visible'=> ( User::hasPermission("item-created-at-view") ) ? true : false,                            
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
                          'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                          'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                          'header' => Yii::t("app","Add a stock"),
                          'visible'=>User::canRoute(["/items/add-stock"]),
                          'template' => '{add-stock}',
                            'buttons' => [
                                'add-stock' => function ($url, $model) {
                                    $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                                        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',$url,[
                                            'data'=>['pjax'=>0],'class'=>'modal-d','style'=>'display:block;text-align:center','title'=>Yii::t('app','Adding stock for {item} item forum',['item'=>$model['name']])
                                        ]);
                                }
                              ]
                        ],

                        [
                          'class' => 'yii\grid\ActionColumn',
                          'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                          'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                          'header' => Yii::t("app","Use an item"),
                          'visible'=>User::canRoute(["/items/use-item"]),
                          'template' => '{use-item}',
                            'buttons' => [
                                'use-item' => function ($url, $model) {
                                    $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                                        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>',$url,[
                                            'data'=>['pjax'=>0],'class'=>'modal-d','style'=>'display:block;text-align:center','title'=>Yii::t('app','Use {item} item forum',['item'=>$model['name']])
                                        ]);
                                }
                              ]
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'options'=>['style'=>'width:20px;text-align:center'],
                            'header'=>Yii::t('app','Update'),
                            'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                            'visible'=>User::canRoute(["/items/update"]),
                            'template'=>'{update}',
                                'buttons'=>[
                                    'update'=>function($url,$model){
                                        return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url,[
                                            'data'=>['pjax'=>0],'class'=>'modal-d','title'=>Yii::t('app', 'Update an item: {name}', ['name' => $model['name']])
                                        ]); 
                                     }
                                ]
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=>User::canRoute('/items/delete'),
                            'options'=>['style'=>'width:20px;text-align:center;'],
                            'header'=>Yii::t('app','Delete'),
                            'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:3%;text-align:center;'],
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
    'title' => Yii::t('app','Item'),
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
                             alertify.success("'.Yii::t("app","Item was deleted successfuly").'");
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
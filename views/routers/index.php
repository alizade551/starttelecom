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
/* @var $searchModel app\models\RoutersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Routers');
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";


$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'router-grid-pjax',
    'pageName'=>'_grid_page_size_bras'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";

$actionsContainer = "<div class='helper-action-container'>".$actions."</div>";

$content = "<div class='helper-container'>".$pageSizeContainer.$actionsContainer."</div>";

?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <?php if (User::canRoute("/routers/create")): ?>
                <a class="btn btn-success add-element" data-pjax="0" href="/routers/create" title=" <?=Yii::t("app","Create a router") ?>">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <?=Yii::t("app","Create a router") ?>
                </a>
            <?php endif?>
        </div>
    </div>
</div>

<div class="card custom-card ">
    <?php Pjax::begin(['id'=>'router-grid-pjax']); ?>

        <?= GridView::widget([
            'id'=>'ub-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
            'columns' => [
                [
                'class' => 'yii\grid\SerialColumn',
                'visible'=>User::hasPermission("router-serial-column-view"),
                'options'=>['style'=>'width:1%;text-align:center'],
                'headerOptions'=>['style'=>'width:1%;text-align:center;'],
                'contentOptions'=>['style'=>'width:1%;text-align:center;'],
                ],


                [
                    'attribute'=>'vendor_name',
                    'visible'=>User::hasPermission("router-vendor-name-view"),
                    'format'=>'raw',
                    'options'=>['style'=>'width:20%;text-align:center'],
                    'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                    'value'=>function ($model){
                        return  $model->vendor_name;
                    }
                ],
                 [
                    'attribute'=>'name',
                    'visible'=>User::hasPermission("router-name-view"),
                    'format'=>'raw',
                    'options'=>['style'=>'width:10%;text-align:center'],
                    'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                    'value'=>function ($model){
                    return  $model->name;
                     
                    }
                ],

                 [
                    'attribute'=>'nas',
                    'visible'=>User::hasPermission("router-nas-view"),
                    'format'=>'raw',
                    'options'=>['style'=>'width:5%;text-align:center'],
                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                    'value'=>function ($model){
                    return  $model->nas;
                     
                    }
                ],


                [
                    'label'=>Yii::t('app','Static Ip capacity'),
                    'visible'=>User::hasPermission("router-static-ip-view"),
                    'format'=>'raw',
                    'options'=>['style'=>'width:5%;text-align:center'],
                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                    'value'=>function ($model){
                        return \app\models\CgnIpAddress::staticIpAlert( $model->id, )['capacity'];

                    }
                ],

                [
                    'label'=>Yii::t('app','Dynamic Ip capacity'),
                    'visible'=>User::hasPermission("router-dynamic-ip-view"),
                    'format'=>'raw',
                    'options'=>['style'=>'width:5%;text-align:center'],
                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                    'value'=>function ($model){
                        if ( $model->parent == 0 ) {
                            return \app\models\CgnIpAddress::ipAlert( $model->id ,$model->name)['capacity'];
                        }else{
                            return '-';
                        }

                    }
                ],








                [
                  'class' => 'yii\grid\ActionColumn',
                  'header' => Yii::t("app","Logs"),
                  'visible'=>User::hasPermission("router-info-view"),
                  'options'=>['style'=>'width:2%;text-align:center'],
                  'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                  'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                  'template' => '{router-log}',
                    'buttons' => [
                        'router-log' => function ($url, $model) {
                            return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M18 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3H6a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3V6a3 3 0 0 0-3-3 3 3 0 0 0-3 3 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 3 3 0 0 0-3-3z"></path></svg>', $url, ['data' => ['pjax' => 0],'title'=>$model['name'].' - '.$model['vendor_name'],'style'=>'text-align:center;display:block;','class'=>'modal-d']) ;
                        },
                      ],

                ],

                [
                          'class' => 'yii\grid\ActionColumn',
                          'header' => Yii::t("app","Info"),
                          'visible'=>User::hasPermission("router-info-view"),
                          'options'=>['style'=>'width:2%;text-align:center'],
                          'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                          'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                          'template' => '{router-chart}',
                            'buttons' => [
                                'router-chart' => function ($url, $model) {
                                    return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>', $url, ['data' => ['pjax' => 0],'style'=>'text-align:center;display:block;']) ;
                                },
                              ],

                ],


                [
                  'class' => 'yii\grid\ActionColumn',
                  'header' => Yii::t("app","Back up"),
                  'visible'=>User::hasPermission("router-back-up-view"),
                  'options'=>['style'=>'width:2%;text-align:center'],
                  'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                  'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                  'template' => '{router}',
                    'buttons' => [
                        'router' => function ($url, $model) {
                            return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="1 4 1 10 7 10"></polyline><polyline points="23 20 23 14 17 14"></polyline><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path></svg>', $url, ['data' => ['pjax' => 0],'style'=>'text-align:center;display:block;']) ;
                        },
                      ],
                ],

                [
                  'class' => 'yii\grid\ActionColumn',
                  'options'=>['style'=>'width:2%;text-align:center'],
                  'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                  'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                  'header' => Yii::t("app","Reboot"),
                  'visible'=>User::hasPermission("router-reboot-view"),
                  'template' => '{reboot}',
                    'buttons' => [
                        'reboot' => function ($url, $model) {
                            $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                            return '<a style="text-align:center;display:block;"  data-options=\'{"touch" : false}\'    tabindex="false" data-pjax="0" data-type="ajax" data-fancybox="" href="javascript:;" data-src="'.$url.'" ><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg></a>';
                        }
                      ]
                ],



                [
                    'class' => 'yii\grid\ActionColumn',
                    'header'=>Yii::t('app','Update'),
                    'options'=>['style'=>'width:2%;text-align:center'],
                    'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                    'visible'=>User::canRoute("/routers/update"),
                    'template'=>'{update}',
                    'buttons'=>[
                        'update'=>function($url,$model){
                            return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url."&city_id=".$model['city_id']."&district_id=".$model['district_id']."&location_id=".$model['location_id'],[
                                'data'=>['pjax'=>0],
                                'title'=>Yii::t('app','Update router : {router_name}',['router_name'=>$model['name']])
                            ]); 
                         }
                    ]
                ],


                [
                    'class' => 'yii\grid\ActionColumn',
                    'visible'=>User::canRoute("/routers/delete"),
                    'header'=>Yii::t('app','Delete'),
                    'options'=>['style'=>'width:2%;text-align:center'],
                    'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                    'template'=>'{delete}',
                    'buttons'=>[
                    'delete' => function($url, $model){
                         return '<a href="javascript:void(0)" data-href="'.$url.'" class="alertify-confirm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>';
                            }
                    ]

                ]

            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php 
Modal::begin([
    'title' => Yii::t("app","Routers"),
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
      console.log()
      var message  = "'.Yii::t("app","Are you sure want to delete this ?").'";
          alertify.confirm( message, function (e) {
            if (e) {
               $.ajax({
                   url:that.attr("data-href"),
                   type:"post",
                   success:function(response){
                     that.closest("tr").fadeOut("slow");
                    alertify.set("notifier","position", "top-right");
                    alertify.success("'.Yii::t("app","Router was deleted successfuly").'");
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a router").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});;      
        return false;
    });

');

 ?>
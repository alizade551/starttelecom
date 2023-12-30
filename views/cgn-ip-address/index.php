<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\widgets\GridBulkActions;
use app\widgets\GridPageSize;
use yii\helpers\Url;
use app\models\Cities;
use app\models\Routers;
use yii\bootstrap4\Modal;
use webvimark\modules\UserManagement\models\User;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\CgnIpAddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Cgn Ip Addresses');
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";


$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'cgn-grid-pjax',
    'pageName'=>'_grid_page_size_cgn'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";
?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <div class="container-actions">
                <?php if (User::canRoute("/cgn-ip-address/delete-nat-from-router")): ?>
                    <a class="btn btn-danger" data-pjax="0" href="<?=$langUrl ?>/cgn-ip-address/delete-nat-from-router" style="margin-left:10px;margin-bottom: 10px;">
                        <?=Yii::t("app","Clear nats from BRAS") ?>
                    </a>
                <?php endif ?>
                <?php if (User::canRoute("/cgn-ip-address/define-router")): ?>
                <a class="btn btn-primary" data-pjax="0" href="<?=$langUrl ?>/cgn-ip-address/define-router" style="margin-left:10px;margin-bottom: 10px;">
                    <?=Yii::t("app","Define nats for BRAS") ?>
                </a>
                <?php endif ?>

            </div>

        </div>
    </div>
</div>


<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
            <div class=" panel panel-default " style="padding: 15px ;" >
    <?php Pjax::begin(['id'=>'cgn-grid-pjax',]); ?> 

    <?= GridView::widget([
        'id'=>'cgn-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager'=>[
            'class'=>yii\bootstrap4\LinkPager::class
        ], 
        'tableOptions' =>['class' => 'table table-striped table-bordered'],
        'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
        'columns' => [
             [
                'class' => 'yii\grid\SerialColumn',
                'visible'=>User::hasPermission("cgn-serial-column-view"),
                'headerOptions'=>['style'=>'width:1%;text-align:center;'],
                'contentOptions'=>['style'=>'width:1%;text-align:center;'],
            ],
            [
                'attribute'=>'ip_address',
                'visible'=>User::hasPermission("cgn-internal_ip-view"),
                'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                'value'=>function ($model){
                  return $model['public_ip'];
                }
            ],
            [
                'attribute'=>'internal_ip',
                'visible'=>User::hasPermission("cgn-internal_ip-view"),
                'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                'value'=>function ($model){
                  return $model['internal_ip'];
                }
            ],

            [
                'attribute'=>'port_range',
                'visible'=>User::hasPermission("cgn-port_range-view"),
                'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                'value'=>function ($model){
                  return $model['port_range'];
                }
            ],

            [
                'attribute'=>'inet_login',
                'visible'=>User::hasPermission("cgn-inet_login-view"),
                'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                'value'=>function ($model){
                  return $model['inet_login'];
                }
            ],

            [
                'attribute'=>'router',
                'visible'=>User::hasPermission("cgn-inet_login-view"),
                'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                'value'=>function ($model){
                  return $model['router_name'];
                }
            ],


            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'visible'=>User::canRoute("/bonus/delete"),
            //     'header'=>Yii::t('app','Delete'),
            //     'headerOptions'=>['style'=>'width:1%;text-align:center;'],
            //     'contentOptions'=>['style'=>'width:1%;text-align:center;'],
            //     'template' => '{delete}',
            //     'buttons' => [
            //     'delete' => function($url, $model){
            //          return '<a href="javascript:void(0)" data-href="'.$url.'" class="alertify-confirm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>';
            //             }
            //         ],
            // ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
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
                        alertify.set("notifier","position", "top-right");

                        if(response.status == "success"){
                            that.closest("tr").fadeOut("slow");
                             alertify.set("notifier","position", "top-right");
                            alertify.success("'.Yii::t("app","cgn ip was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a cgn ip address").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});    
        return false;
    });

');

 ?>
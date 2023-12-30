<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\widgets\GridBulkActions;
use app\widgets\GridPageSize;
use yii\helpers\Url;
use yii\bootstrap4\Modal;
use webvimark\modules\UserManagement\models\User;
/* @var $this yii\web\View */
/* @var $searchModel app\models\FailProcessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Fail Processes');


$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'fail-process-grid-pjax',
    'pageName'=>'_grid_page_size_fail_process'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";



?>
<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
            <div class=" panel panel-default " style="padding: 15px ;" >
               <?php Pjax::begin(['id'=>'fail-process-grid-pjax',]); ?> 
                <?= GridView::widget([
                    'id'=>'fail-process-grid',
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
                        'visible'=>User::hasPermission("fail-process-serial-column-view"),
                        'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                        ],

                        [
                        'attribute'=>'action',
                        'visible'=>User::hasPermission("fail-process-action-view"),
                        'headerOptions'=>['style'=>'width:15%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:15%;text-align:center;'],
                        'value'=>function ($model){
                          return $model['action'];
                        }
                        ],
                        [
                        'attribute'=>'member_fullname',
                        'visible'=>User::hasPermission("fail-process-member_fullname-view"),
                        'headerOptions'=>['style'=>'width:15%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:15%;text-align:center;'],
                        'value'=>function ($model){
                          return $model['member_fullname'];
                        }
                        ],


                        [
                        'attribute'=>'status',
                        'visible'=>User::hasPermission("fail-process-status-view"),
                        'label'=>Yii::t('app','Status'),
                        'filter' => Html::activeDropDownList(
                                $searchModel,
                                'status',
                                \app\models\FailProcess::getStatus(),
                                ['class' => 'form-control', 'prompt' => '']
                                ),
                        'headerOptions'=>['style'=>'width:15%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:15%;text-align:center;'],
                        'format'=>'raw',
                        'value'=>function ($model){
                          if( $model['status'] == 1 ){
                            $span = '<span class="badge badge-success" style="width:75px;display:block;margin:0 auto;">'.\app\models\FailProcess::getStatus()[$model['status']].'</span>';
                          }else {
                            $span = '<span class="badge badge-warning" style="width:75px;display:block;margin:0 auto;">'.\app\models\FailProcess::getStatus()[$model['status']].'</span>';
                          }
              


                          return $span;
                        }
                        ],


                        [
                            'label'=>Yii::t('app','Created at'),
                            'visible'=>User::hasPermission("fail-process-created-at-view"),
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
                            'options'=>['style'=>'width:10px;text-align:center'],
                            'header'=>Yii::t('app','View'),
                            'visible'=>User::canRoute(['/fail-process/view']),
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                            'template'=>'{view}',
                                'buttons'=>[
                                    'view'=>function($url,$model){
                                        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>',$url,[
                                            'data'=>['pjax'=>0],
                                            'class'=>'modal-d',
                                            'title'=>Yii::t('app','Fail process - {action}',['action'=>$model['action']]),
                                        ]); 
                                     }
                                ]
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=>User::canRoute(['/fail-process/delete']),
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
</div>

<style type="text/css">
#fail-process-grid-clear-filters-btn{margin-right: 15px;}
</style>

<?php 

Modal::begin([
    'title' => Yii::t("app","Fail process"),
    'id' => 'modal',
    'asDrawer'=>true,
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
                            alertify.success("'.Yii::t("app","Failed action was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error("'.Yii::t("app","Failed action reload page and try again...").'");
                        }
                   }
               });
            } 
        });      
        return false;
    });

');

 ?>
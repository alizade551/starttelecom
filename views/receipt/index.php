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
/* @var $searchModel app\models\search\ReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$GridBulkActions = "";
// GridBulkActions::widget([
// 'gridId'=>'receipt-grid',
// 'actions'=>[Url::to(['bulk-delete'])=>Yii::t('app', 'Delete'),],
// ]);

$this->title = Yii::t('app','Receipts');
$this->params['breadcrumbs'][] = $this->title;

$content = '';
$actions = '';


$pageSize = GridPageSize::widget([
    'pjaxId'=>'receipt-grid-pjax',
    'pageName'=>'_grid_page_size_receipt'
]);


$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
// $actions .= $viewVisibility;

$actionsContainer = "<div class='helper-action-container'>".$actions."</div>";

$content = "<div class='helper-container'>".$pageSizeContainer.$actionsContainer."</div>";


?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <div class="container-actions">
                <?php if (User::canRoute("/receipt/delete-receipt-from-member")): ?>
                    <a class="btn btn-danger add-element" data-pjax="0" href="/receipt/delete-receipt-from-member" style="margin-left:10px;margin-bottom: 10px;">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        <?=Yii::t("app","Delete receipt from user") ?>
                    </a>
                <?php endif ?>

                <?php if (User::canRoute("/receipt/member-recipet")): ?>
                <a class="btn btn-primary add-element" data-pjax="0" href="/receipt/member-recipet" style="margin-left:10px;margin-bottom: 10px;">
                   <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <?=Yii::t("app","Define receipt to user") ?>
                </a>
                <?php endif ?>

                <?php if (User::canRoute("/receipt/create")): ?>
                <a class="btn btn-success add-element" data-pjax="0" href="/receipt/create" style="margin-left:10px;margin-bottom: 10px;">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <?=Yii::t("app","Create receiptes") ?>
                </a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>


<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
            <?php Pjax::begin(['id'=>'receipt-grid-pjax']); ?>
                 <?= GridView::widget([
                        'id'=>'receipt-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pager'=>[
                          'class'=>yii\bootstrap4\LinkPager::class
                        ], 
                        'layout'=>' '.$content .'{items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
                        'columns' => [
                            [
                            'class' => 'yii\grid\SerialColumn',
                            'visible'=>User::hasPermission("receipt-serial-column-view"),
                            'options'=>['style'=>'width:1%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                            ],

                            [
                                'attribute'=>'seria',
                                'visible'=>User::hasPermission("receipt-seria-view"),
                                'format'=>'raw',
                                'options'=>['style'=>'width:5%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:5%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:5%;text-align:center;'],
                                'value'=>function ($model){
                                    if ($model->seria) {
                                        return $model->seria;
                                    }else{
                                        return '-';
                                    }
                               
                                }
                            ],
                            [
                                'attribute'=>'code',
                                'visible'=>User::hasPermission("receipt-code-view"),
                                'format'=>'raw',
                                'label'=>Yii::t('app','Recipet'),
                                'options'=>['style'=>'width:10%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                                'value'=>function ($model){
                                return $model->code;
                                }
                            ],
                            [
                                'attribute'=>'member',
                                'visible'=>User::hasPermission("receipt-member-view"),
                                'format'=>'raw',
                                'label'=>Yii::t('app','User'),
                                'options'=>['style'=>'width:10%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                                'value'=>function ($model){
                                    if ($model->member !== null) {
                                        return $model->member->fullname." (".$model->member->username.")"; 
                                    }else{
                                        return Yii::t("app","Receipt isn't defined for member"); 
                                    }
                                }
                            ],
                            [
                                'attribute'=>'status',
                                'visible'=>User::hasPermission("receipt-status-view"),
                                'format'=>'raw',
                                'options'=>['style'=>'width:10%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                                'filter'=>\app\models\Receipt::ReceiptStatus(),
                                'value'=>function($model){
                                  
                                    if ($model->status != null) {
                                        return \app\models\Receipt::ReceiptStatus()[$model->status];
                                    }else{
                                        return Yii::t('app','Status is null');
                                    }
                               }
                                
                            ],

                            [
                                'attribute'=>'type',
                                'visible'=>User::hasPermission("receipt-type-view"),
                                'format'=>'raw',
                                'options'=>['style'=>'width:10%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                                'filter'=>\app\models\Receipt::ReceiptType(),
                                'value'=>function($model){
                                  
                                    if ($model->status != null) {
                                        return \app\models\Receipt::ReceiptType()[$model->type];
                                    }else{
                                        return Yii::t('app','Status is null');
                                    }
                               }
                                
                            ],


                            [
                                'label'=>Yii::t('app','Created at'),
                                'visible'=>User::hasPermission("receipt-created_at-view"),
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
                                  return date('d/m/Y H:i:s',$model->created_at);
                                }
                            ],
                            // [
                            //     'class' => 'yii\grid\ActionColumn',
                            //     'options'=>['style'=>'width:20px;text-align:center'],
                            //     'header'=>Yii::t('app','Update'),
                            //     'headerOptions'=>['style'=>'width:20px;color:#23758f;text-align:center;color:#00b8ce'],
                            //     'contentOptions'=>['style'=>'width:20px;text-align:center;'],
                            //     'template'=>'{update}',
                            //         'buttons'=>[
                            //             'update'=>function($url,$model){
                            //                 return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url,[
                            //                     'data'=>['pjax'=>0],//pjax 0 olduqda deaktiv olur
                            //                     'class'=>'modal-d',
                            //                     'title'=>'Update'
                            //                 ]); 
                            //              }
                            //         ]
                            // ],
                            // [
                            //     'class' => 'yii\grid\CheckboxColumn', 'options'=>['style'=>'width:1%'],
                            //     'headerOptions'=>['style'=>'width:1%;text-align:center'],
                            //     'contentOptions'=>['style'=>'width:1%;'],
                            // ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'visible'=>User::canRoute(["/receipt/delete"]),
                                'header'=>Yii::t('app','Delete'),
                                'options'=>['style'=>'width:1%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                                'template'=>'{delete}',
                                    'buttons'=>[
                                        'delete'=>function($url,$model){
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
                        if(response.status == "success"){
                            that.closest("tr").fadeOut("slow");
                             alertify.set("notifier","position", "top-right");
                            alertify.success("'.Yii::t("app","Receipt was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error("'.Yii::t("app","Something went wrong reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a receipt").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});  
        return false;
    });

');

 ?>


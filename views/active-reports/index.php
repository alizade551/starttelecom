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
/* @var $searchModel app\models\search\UserDamagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Active reports');


if ( isset( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value )) {
     $gridViewVisibility = json_decode( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value ,true );

}else{
    $gridViewVisibility["serial-view"] = "true@Serial";
    $gridViewVisibility["customer-view"] = "true@Customer";
    $gridViewVisibility["user-view"] = "true@User";
    $gridViewVisibility["personal-view"] = "true@Personal";
    $gridViewVisibility["report-view"] = "true@Reported reason";
    $gridViewVisibility["report-message-view"] = "true@More detail";
    $gridViewVisibility["reason-view"] = "true@Report result";
    $gridViewVisibility["reported_at-view"] = "true@Created at";
    $gridViewVisibility["status-view"] = "true@Status";
}

    $content = '';
    $actions = '';

    $viewVisibility = \app\widgets\gridViewVisibility\viewVisibility::widget(
        [
            'params'=>$gridViewVisibility,
            'url'=>Url::to('/active-reports/grid-view-visibility'),
            'pjaxContainer'=>'#active-reports-grid-pjax'
        ]
    );

    $pageSize = GridPageSize::widget([
        'pjaxId'=>'users-grid-pjax',
        'pageName'=>'_grid_page_size_radacct_all'
    ]);

    $pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";


    $actions .= $viewVisibility;
    $actionsContainer = "<div class='helper-action-container'>".$actions."</div>";

    $content = "<div class='helper-container'>".$pageSizeContainer.$actionsContainer."</div>";

?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <?php if (User::canRoute("/active-reports/create")): ?>
                <a class="btn btn-success add-element" data-pjax="0" href="/active-reports/create" title=" <?=Yii::t("app","Create a report") ?>">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <?=Yii::t("app","Create a report") ?>
                </a>
            <?php endif?>
        </div>
    </div>
</div>



<div class="user-damages-index card custom-card " style="width:100%">

	<div class="row">
		<div class="col-sm-12">
    <?php Pjax::begin(['id'=>'active-reports-grid-pjax']); ?>

        <?= GridView::widget([
                'id'=>'active-reports-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pager'=>[
                  'class'=>yii\bootstrap4\LinkPager::class
                ], 
                'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
                'columns' => [
                    [
                    'class' => 'yii\grid\SerialColumn',
                    'visible'=> ( str_contains( $gridViewVisibility["serial-view"], 'true' ) == "true" && User::hasPermission("active-report-serial-column-view") ) ? true : false,
                    'options'=>['style'=>'width:1%;text-align:center;'],
                    'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                    'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                    ],
                    [
                        'attribute'=>'fullname',
                        'visible'=> ( str_contains( $gridViewVisibility["customer-view"], 'true' ) == "true" && User::hasPermission("active-report-customer-fullname-view") ) ? true : false,
                        'label'=>Yii::t('app','Customer'),
                        'format'=>'raw',
                        'options'=>['style'=>'width:10%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                        'value'=>function ($model){
                        return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model['user_id'].'">'.$model->user->fullname.'</a>';
                         
                        }
                    ],
                    [
                        'attribute'=>'member_fullname',
                        'visible'=> ( str_contains( $gridViewVisibility["user-view"], 'true' )  == "true" && User::hasPermission("active-report-user-fullname-view") ) ? true : false,
                        'label'=>Yii::t('app','User'),
                        'format'=>'raw',
                        'options'=>['style'=>'width:10%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                        'value'=>function ($model){
                        return $model->member->fullname;
                         
                        }
                    ],    
                    [
                        'attribute'=>'personal',
                        'visible'=> ( str_contains( $gridViewVisibility["personal-view"], 'true' )  == "true" && User::hasPermission("active-report-personal-fullname-view") ) ? true : false,
                        'label'=>Yii::t('app','Personal'),
                        'format'=>'raw',
                        'options'=>['style'=>'width:15%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:15%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:15%;text-align:center;'],
                        'value'=>function ($model){
                            $badges = '';
                            foreach ($model->damagePersonals as $key => $member) {
                                if ($model->status == 1) {
                                    $badges .= ' <span class="badge badge-pills badge-success ">'.$member->personal->fullname.'</span>';

                                }else{
                                    $badges .= ' <span class="badge badge-pills badge-warning ">'.$member->personal->fullname.'</span>';
                                }

                            }
                            return $badges;
                        }
                    ],
                    [
                        'attribute'=>'damage_reason',
                        'visible'=> ( str_contains( $gridViewVisibility["report-view"], 'true' ) == "true" && User::hasPermission("active-report-reason-view") ) ? true : false,
                        'format'=>'raw',
                        'options'=>['style'=>'width:10%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                        'value'=>function ($model){
                
                          return  \app\models\UserDamages::getDamageReason()[$model['damage_reason']];
                        }
                    ],
                    [
                        'attribute'=>'message',
                        'visible'=> ( str_contains( $gridViewVisibility["report-message-view"] , 'true' ) == "true" && User::hasPermission("active-report-message-view") ) ? true : false,
                        'format'=>'raw',
                        'options'=>['style'=>'width:10%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                        'value'=>function ($model){
                            if ( $model['message'] != "") {
                                return  $model['message'];
                            }else{
                                return "-";
                            }
                        }
                    ],

                    [
                        'attribute'=>'damage_result',
                        'visible'=> ( str_contains( $gridViewVisibility["reason-view"], 'true' ) == "true" && User::hasPermission("active-report-result-view") ) ? true : false,
                        'format'=>'raw',
                        'options'=>['style'=>'width:15%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:15%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:15%;text-align:center;'],
                        'value'=>function ($model){
                
                          return  $model['damage_result'];
                        }
                    ],


                    [
                        'label'=>Yii::t('app','Created at'),
                        'visible'=> ( str_contains( $gridViewVisibility["reported_at-view"], 'true' ) == "true" && User::hasPermission("active-report-created-view") ) ? true : false,
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
                          return date('d/m/Y H:i:s',$model['created_at']);
                        }
                    ],

                    [
	                    'attribute'=>'status',
	                    'label'=>Yii::t('app','Status'),
                        'visible'=> ( str_contains( $gridViewVisibility["status-view"], 'true' ) == "true" && User::hasPermission("active-report-status-view") ) ? true : false,
	                    'filter' => false,
                        'options'=>['style'=>'width:10%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:10%;text-align:center;'],
	                    'format'=>'raw',
	                    'value'=>function ($model){
	                      if( $model->status == 1 ){
	                        $span = '<span class="badge badge-success" style="width:75px;display:block;margin:0 auto;">'.\app\models\UserDamages::getStatus()[1].'</span>';
	                      }else{
	                        $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.\app\models\UserDamages::getStatus()[0].'</span>';
	                      }
	                      return $span;
	                    }
                    ],


                    [
                        'class' => 'yii\grid\ActionColumn',
                        'visible'=>User::canRoute(['/request-order/update']),
                        'header'=>Yii::t('app','View report'),
                        'options'=>['style'=>'width:3%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:3%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:3%;text-align:center;'],
                        'template'=>'{update}',
                            'buttons'=>[
                                'update'=>function($url,$model){
                                    return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>',$url,[
                                        'data'=>['pjax'=>0],
                                        'class'=>'modal-d',
                                        'title'=>Yii::t("app","{customer} report form",['customer'=>$model->user->fullname])
                                    ]); 
                                 }
                            ]
                    ],


                    [
                        'class' => 'yii\grid\ActionColumn',
                        'visible'=>User::canRoute(['/request-order/delete']),
                        'header'=>Yii::t('app','Delete'),
                        'options'=>['style'=>'width:3%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:3%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:3%;text-align:center;'],
                        'template' => '{delete}',
                        'buttons' => [
                        'delete' => function($url, $model){
                             return '<a href="javascript:void(0)" data-href="'.$url.'" class="alertify-confirm">
                             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                             </a>';
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
      console.log()
      var message  = "'.Yii::t("app","Are you sure want to delete report ?").'";
          alertify.confirm( message, function (e) {
            if (e) {
               $.ajax({
                   url:that.attr("data-href"),
                   type:"post",
                   success:function(response){
                        if(response.status == "success"){
                            that.closest("tr").fadeOut("slow");
                             alertify.set("notifier","position", "top-right");
                             alertify.success("'.Yii::t("app","Report was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a report").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});
        return false;
    });

');

 ?>
 

<?php 
Modal::begin([
    'title' => Yii::t("app","Damage"),
    'id' => 'modal',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],
    'size' => 'modal-lg',
    'asDrawer' => true,
]);
echo "<div id='modalContent'></div>";
Modal::end();
?>
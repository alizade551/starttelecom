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
/* @var $searchModel app\models\search\LogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Logs');

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    [
        'attribute'=>'member',
        'label'=>Yii::t('app','User'),
        'value'=>function ($model){
          return $model['member'];
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
        'label'=>Yii::t('app','Created at'),
        'format'=>'raw',
        'value'=>function($model){
          return date('d/m/Y H:i:s',$model['time']);
        }
    ],



    [
        'attribute'=>'text',
        'label'=>Yii::t('app','Text'),
        'value'=>function ($model){
          return  $model['text'];
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
        $gridViewVisibility["user-view"] = "true@User";
        $gridViewVisibility["customer-view"] = "true@Customer";
        $gridViewVisibility["text-view"] = "true@Text";
        $gridViewVisibility["created_at-view"] = "true@Created at";
    }

    $viewVisibility =  \app\widgets\gridViewVisibility\viewVisibility::widget(
        [
            'params'=>$gridViewVisibility,
            'url'=>Url::to('/logs/grid-view-visibility'),
            'pjaxContainer'=>'#logs-grid-pjax'
        ]
    );

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

    $gridBlukAction = "<div>".GridBulkActions::widget([
        'gridId'=>'logs-grid',
        'actions'=>[Url::to(['bulk-delete'])=>Yii::t('app', 'Delete'),],
        ])."</div>";

    $pageSize = GridPageSize::widget([
        'pjaxId'=>'logs-grid-pjax',
        'pageName'=>'_grid_page_size_logs'
    ]);

    $pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";


    $actions .= $viewVisibility;
    $actions .= $gridBlukAction;

    $actionsContainer = "<div class='helper-action-container'>".$actions."</div>";

    $content = "<div class='helper-container'>".$pageSizeContainer.$actionsContainer."</div></div>";



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
    <?php Pjax::begin(['id'=>'logs-grid-pjax']); ?>
    <?= GridView::widget([
        'id'=>'logs-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager'=>[
          'class'=>yii\bootstrap4\LinkPager::class
        ], 
        'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
            'columns' => [
                                [
                                    'class' => 'yii\grid\SerialColumn',
                                    'visible'=> ( str_contains( $gridViewVisibility["serial-view"], 'true' ) == "true" && User::hasPermission("logs-serial-view") ) ? true : false,
                                    'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                                    'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                                ],

                                [
                                    'attribute'=>'member',
                                    'visible'=> ( str_contains( $gridViewVisibility["user-view"], 'true' ) == "true" && User::hasPermission("logs-user-view") ) ? true : false,
                                    'label'=>Yii::t('app','User'),
                                    'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                                    'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                                    'value'=>function ($model){
                                        return $model['member'];
                                    }
                                ],

                                [
                                    'attribute'=>'user',
                                    'format'=>'raw',
                                    'visible'=> ( str_contains( $gridViewVisibility["customer-view"], 'true' ) == "true" && User::hasPermission("logs-customer-view") ) ? true : false,
                                    'label'=>Yii::t('app','Customer'),
                                    'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                                    'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                                    'value' => function($model){
                                        if (isset($model['user_fullname']) ) {
                                         return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model['user_id'].'">'.$model['user_fullname'].'</a>';
                                            // code...
                                        }else{
                                            return "-";
                                        }
                                    }
                                ],           
                           
                                [
                                    'label'=>Yii::t('app','Created at'),
                                    'visible'=> ( str_contains( $gridViewVisibility["created_at-view"], 'true' ) == "true" && User::hasPermission("logs-created_at-view") ) ? true : false,
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
                                      return date('d/m/Y H:i:s',$model['time']);
                                    }
                                ],
                                 [
                                    'attribute'=>'text',
                                    'format'=>'raw',
                                    'visible'=> ( str_contains( $gridViewVisibility["text-view"], 'true' ) == "true" && User::hasPermission("logs-text-view") ) ? true : false,
                                    'headerOptions'=>['style'=>'width:50%;text-align:center;'],
                                    'contentOptions'=>['style'=>'width:50%;text-align:center;'],
                                    'value'=>function ($model){
                                    return $model['text'];
                                    }
                                ], 



                                [
                                    'class' => 'yii\grid\CheckboxColumn',
                                    'visible'=> ( User::hasPermission("logs-check-all-view") ) ? true : false,
                                    'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                                    'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                                ],

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'visible'=> ( User::canRoute("/logs/view") ) ? true : false,
                                    'header'=>Yii::t('app','View'),
                                    'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                                    'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                                    'template'=>'{view}',
                                        'buttons'=>['view'=>function($url,$model){
                                            return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>', ['/logs/view?id='.$model['id']], ['class'=>'modal-d','data-pjax'=>"0"]);
                                        }],
             
                                ],
                                [
                                    'header'=>Yii::t('app','Delete'),
                                    'visible'=> ( User::canRoute("/logs/delete") ) ? true : false,
                                    'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                                    'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{delete}',
                                    'buttons' => [
                                    'delete' => function($url, $model){
                                     return '<a href="javascript:void(0)" data-href="'.$url.'" class="alertify-confirm"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>';
                                        }
                                    ],
                                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
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
                            alertify.success("'.Yii::t("app","Log was deleted successfuly").'");
                        }else{
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a log").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"}); 
        return false;
    });

');

 ?>

<?php 
Modal::begin([
    'title' => Yii::t('app','Logs'),
    'id' => 'modal',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],
    'size' => 'modal-lg',
    'asDrawer'=>false
]);
echo "<div id='modalContent'></div>";
Modal::end();

?>



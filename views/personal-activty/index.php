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
/* @var $searchModel app\models\search\PersonalActivtySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Activty logs ');
$this->params['breadcrumbs'][] = $this->title;


$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'persoanl-activty-grid-pjax',
    'pageName'=>'_grid_page_size_personal'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";

?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            
        </div>
    </div>
</div>

<div class="card custom-card">
   <?php Pjax::begin(['id'=>'persoanl-activty-grid-pjax']); ?>
        <?= GridView::widget([
        'id'=>'persoanl-activty-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager'=>[
          'class'=>yii\bootstrap4\LinkPager::class
        ], 
        'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
        'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                    'visible'=>User::hasPermission('view-user-activty-serial'),

                ],

                [
                    'attribute'=>'user_fullname',
                    'label'=>Yii::t('app','Customer'),
                    'visible'=>User::hasPermission('view-user-activty-customer'),
                    'format'=>'raw',
                    'headerOptions'=>['style'=>'width:30%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:30%;text-align:center;'],
                    'value'=>function ($model){
                    return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model['user_id'].'">'.$model->user->fullname.'</a>';
                     
                    }
                ],
       
                [
                    'attribute'=>'personal',
                    'label'=>Yii::t('app','User'),
                    'visible'=>User::hasPermission('view-user-activty-personal'),
                    'format'=>'raw',
                    'headerOptions'=>['style'=>'width:40%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:40%;text-align:center;'],
                    'value'=>function ($model){
                        $badges = '';
                        switch ($model->type) {
                            case '0':
                               $badge = "badge-success";
                                break;
                            case '1':
                               $badge = "badge-danger";
                                break;
                            case '2':
                               $badge = "badge-secondary";
                                break;
                            case '3':
                               $badge = "badge-warning";
                                break;
                            case '4':
                               $badge = "badge-primary";
                                break;

                            default:
                                $badge ='';
                                break;
                        }
                        foreach ($model->personalUserActivties as $key => $member) {
                           $badges .= ' <span class="badge badge-pills '.$badge.' ">'.$member->member->fullname.'</span>';

                        }
                        return $badges;
                    }
                ],

                [
                    'attribute'=>'type',
                    'label'=>Yii::t('app','Activty type'),
                    'visible'=>User::hasPermission('view-user-activty-type'),
                    'format'=>'raw',
                    'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                    'filter'=>\app\models\PersonalActivty::Type(),
                    'value'=>function($model){
                        if ($model['type'] != null) {
                                return \app\models\PersonalActivty::Type()[$model['type']];
                           }else{
                                return "-";
                        }
                   }
                    
                ],

                [
                    'label'=>Yii::t('app','Created at'),
                    'visible'=>User::hasPermission('view-user-activty-created-at'),
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
                    'visible'=>User::canRoute('/personal-activty/update'),
                    'header'=>Yii::t('app','Update'),
                    'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                    'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                    'template'=>'{update}',
                        'buttons'=>[
                            'update'=>function($url,$model){
                                return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url,[
                                    'data'=>['pjax'=>0],
                                    'class'=>'modal-d',
                                    'title'=>Yii::t('app', 'Update personal an activty id: {id}', ['id' => $model->id])
                                ]); 
                             }
                        ]
                ],


            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>Yii::t('app','Delete'),
                'visible'=>User::canRoute('/personal-activty/delete'),
                'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                'template' => '{delete}',
                'buttons' => [
                'delete' => function($url, $model){
                     return '<a href="javascript:void(0)" data-href="'.$url.'" class="alertify-confirm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>';
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
                             alertify.set("notifier","position", "top-right");
                             alertify.success("'.Yii::t("app","Activty was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete an activty").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"}); ;      
        return false;
    });

');

 ?>
 
<style type="text/css">
#persoanl-activty-grid-pjax{width: 100%;}
#persoanl-activty-grid-clear-filters-btn{
    display: block;
    margin: 10px;
}
.form-inline .form-control {
    display: inline-block;
    width: auto;
    vertical-align: middle;
}

#persoanl-activty-grid-clear-filters-btn{
    margin-right: 20px;
}
</style>

<?php 
Modal::begin([
    'title' => Yii::t("app","Personal activties"),
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
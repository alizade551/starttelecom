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

$this->title = Yii::t('app', 'Customers using VOIP service');

$content = '';
$actions = '';


$pageSize = GridPageSize::widget([
    'pjaxId'=>'voip-grid-pjax',
    'pageName'=>'_grid_page_size_voip'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";

$actionsContainer = "<div class='helper-action-container'>".$actions."</div>";

$content = "<div class='helper-container'>".$pageSizeContainer.$actionsContainer."</div>";

?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
        </div>
    </div>
</div>

<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12 text-right">
            <?php Pjax::begin(['id'=>'voip-grid-pjax']); ?>

                <?= GridView::widget([
                        'id'=>'voip-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pager'=>[
                          'class'=>yii\bootstrap4\LinkPager::class
                        ], 
                        'layout'=>'{items}<div class="row" ><div class="col-sm-8" style="margin-top: 20px; margin-bottom: 10px;">{pager}</div><div class="col-sm-4 text-right" style="margin-top: 10px; margin-bottom: 10px;">{summary}</div></div>',
                        'columns' => [
                            [
                            'class' => 'yii\grid\SerialColumn',
                            'visible'=>User::hasPermission("customer-voip-serial-column-view"),
                            'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                            ],
                            [
                                'attribute'=>'fullname',
                                'visible'=>User::hasPermission("customer-voip-fullname-view"),
                                'label'=>Yii::t('app','Customer'),
                                'format'=>'raw',
                                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                                'value'=>function ($model){
                                return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model['user_id'].'">'.$model['user_fullname'].'</a>';
                                 
                                }
                            ],
           

                            [
                                'attribute'=>'packet_name',
                                'label'=>Yii::t('app','Packet'),
                                'visible'=>User::hasPermission("customer-voip-packet_name-view"),
                                'format'=>'raw',
                                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                                'value'=>function ($model){
                        
                                  return  $model['packet_name'];
                                }
                            ],

                            [
                                'attribute'=>'phone_number',
                                'visible'=>User::hasPermission("customer-voip-phone-view"),
                                'format'=>'raw',
                                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                                'value'=>function ($model){
                        
                                  return  $model['phone_number'];
                                }
                            ],






                            [
                            'attribute'=>'status',
                            'label'=>Yii::t('app','Status'),
                            'visible'=>User::hasPermission("customer-voip-status-view"),
                            'filter' => Html::activeDropDownList(
                                    $searchModel,
                                    'status',
                                    \app\models\UsersVoip::getPacketStatus(),
                                    ['class' => 'form-control', 'prompt' => '']
                                    ),
                            'headerOptions'=>['style'=>'width:20px;text-align:center;color:#337ab7;'],
                            'contentOptions'=>['style'=>'width:20px;text-align:center;'],
                            'format'=>'raw',
                            'value'=>function ($model){
                              if( $model['status'] == 1 ){
                                $span = '<span class="badge badge-success" style="width:75px;display:block;margin:0 auto;">'.\app\models\UsersVoip::getPacketStatus()[1].'</span>';
                              }elseif( $model['status'] == 0 ){
                                $span = '<span class="badge badge-warning" style="width:75px;display:block;margin:0 auto;">'.\app\models\UsersVoip::getPacketStatus()[0].'</span>';
                              }elseif( $model['status'] == 2 ){
                                $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.\app\models\UsersVoip::getPacketStatus()[2].'</span>';
                              }elseif( $model['status'] == 3 ){
                                $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;    background-color: #795548;">'.\app\models\UsersVoip::getPacketStatus()[3].'</span>';
                              }
                              return $span;
                            }
                            ],


                        //'status',
                            [
                                'label'=>Yii::t('app','Created at'),
                                'visible'=>User::hasPermission("customer-voip-created-view"),
                                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
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
                    ],
                ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>



 
<style type="text/css">
#damages-grid-pjax{width: 100%;}

    #logs-grid-clear-filters-btn{
display: block;
    margin: 10px;
    }
.form-inline .form-control {
    display: inline-block;
    width: auto;
    vertical-align: middle;
}

#damages-grid-clear-filters-btn{
    margin-right: 20px;
}
</style>

<?php 
Modal::begin([
    'title' => Yii::t("app","Phones"),
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
    // GridView::widget([
    //     'dataProvider' => $dataProvider,
    //     'filterModel' => $searchModel,
    //     'columns' => [
    //         ['class' => 'yii\grid\SerialColumn'],

    //         'id',
    //         'user_id',
    //         'packet_id',
    //         'u_s_p_i',
    //         'phone_number',
    //         //'status',
    //         //'created_at',

    //         ['class' => 'yii\grid\ActionColumn'],
    //     ],
    // ]);

     ?>

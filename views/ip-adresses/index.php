<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use app\widgets\GridBulkActions;
use app\widgets\GridPageSize;
use yii\helpers\Url;
use webvimark\modules\UserManagement\models\User;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\IpAdressesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ip Adresses');
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

if ( isset( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value )) {
     $gridViewVisibility = json_decode( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value ,true );

}else{
    $gridViewVisibility["serial-view"] = "true@Serial";
    $gridViewVisibility["public_ip-view"] = "true@Public ip";
    $gridViewVisibility["type-view"] = "true@Type";
    $gridViewVisibility["status-view"] = "true@Status";
    $gridViewVisibility["inet-login-view"] = "true@Inet login";
    $gridViewVisibility["created_at-view"] = "true@Created at";
}

$content = '';
$actions = '';

$viewVisibility = \app\widgets\gridViewVisibility\viewVisibility::widget(
    [
        'params'=>$gridViewVisibility,
        'url'=>Url::to('/ip-adresses/grid-view-visibility'),
        'pjaxContainer'=>'#ip-adresses-grid-pjax'
    ]
);

$pageSize = GridPageSize::widget([
    'pjaxId'=>'ip-adresses-grid-pjax',
    'pageName'=>'_grid_page_size_ip_address'
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
            <?php if ( User::canRoute('/ip-adresses/create') ): ?>
               <a class="btn btn-success modal-d" data-pjax="0" href="<?=$langUrl ?>/ip-adresses/create" title="<?=Yii::t('app','Create an ip adresses range') ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <?=Yii::t("app","Create an ip adress range") ?>
               </a>
            <?php endif ?>
        </div>
    </div>
</div>


<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
            <?php Pjax::begin(['id'=>'ip-adresses-grid-pjax']); ?>
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
                            'visible'=> ( str_contains( $gridViewVisibility["serial-view"], 'true' ) == "true" && User::hasPermission("ip-address-serial-column-view") ) ? true : false,
                            'options'=>['style'=>'width:1%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                        ],

                        [
                            'attribute'=>'public_ip',
                            'visible'=> ( str_contains( $gridViewVisibility["public_ip-view"], 'true' ) == "true" && User::hasPermission("ip-address-public-ip-view") ) ? true : false,
                            'options'=>['style'=>'width:10%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                            'value'=>function ($model){
                              return $model['public_ip'];
                            }
                        ],

                        [
                            'attribute'=>'login',
                            'format'=>'raw',
                            'label'=>Yii::t('app','Inet login'),
                            'visible'=> ( str_contains( $gridViewVisibility["inet-login-view"], 'true' ) == "true" && User::hasPermission("ip-address-inet-login-view") ) ? true : false,
                            'options'=>['style'=>'width:10%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                            'value'=>function ($model){
                                if ($model['login']) {
                                    return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model['user_id'].'">'.$model['login'].'</a>';
                                }else{
                                    return Yii::t('app','not set');
                                }
                                 
                            }
                        ],


                        [
                            'label'=>Yii::t('app','Created at'),
                            'visible'=> ( str_contains( $gridViewVisibility["created_at-view"], 'true' ) == "true" && User::hasPermission("ip-address-created-at-view") ) ? true : false,
                            'options'=>['style'=>'width:20%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:20%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:20%;text-align:center;'],
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
                            'attribute'=>'type',
                            'visible'=> ( str_contains( $gridViewVisibility["type-view"], 'true' ) == "true" && User::hasPermission("ip-address-type-view") ) ? true : false,
                            'label'=>Yii::t('app','Type'),
                            'filter' => Html::activeDropDownList(
                                $searchModel,
                                'type',
                                \app\models\IpAdresses::getType(),
                                ['class' => 'form-control', 'prompt' => '']
                            ),
                            'options'=>['style'=>'width:10%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                            'format'=>'raw',
                            'value'=>function ($model){
                              if( $model['type'] == 1 ){
                                $span = '<span class="badge badge-primary" style="width:75px;display:block;margin:0 auto;">'.\app\models\IpAdresses::getType()[1].'</span>';
                              }else{
                                $span = '<span class="badge badge-danger" style="width:75px;display:block;margin:0 auto;">'.\app\models\IpAdresses::getType()[0].'</span>';
                              }
                              return $span;
                            }
                        ],

                       [
                        'attribute'=>'status',
                        'label'=>Yii::t('app','Status'),
                        'visible'=> ( str_contains( $gridViewVisibility["status-view"], 'true' ) == "true" && User::hasPermission("ip-address-status-view") ) ? true : false,
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'status',
                            \app\models\IpAdresses::getStatus(),
                            ['class' => 'form-control', 'prompt' => '']
                        ),
                        'options'=>['style'=>'width:10%;text-align:center;'],
                        'headerOptions' => ['style' => 'width:10%;text-align:center;'],
                        'contentOptions' => ['style' => 'width:10%;text-align:center;'],
                        'format'=>'raw',
                        'value'=>function ($model){
                          
                          if( $model['status'] == "1" ){
                            return '<span class="badge badge-warning" style="width:75px;display:block;margin:0 auto;">'.\app\models\IpAdresses::getStatus()[1].'</span>';
                          }else{
                             return '<span class="badge badge-success" style="width:75px;display:block;margin:0 auto;">'.\app\models\IpAdresses::getStatus()[0].'</span>';
                          }
                        
                        }
                        ],




                        [
                          'class' => 'yii\grid\ActionColumn',
                          'header'=>Yii::t('app','CGN ip'),
                          'visible'=>User::hasPermission("ip-address-cgn-ip-view"),
                          'options'=>['style'=>'width:3%;text-align:center;'],
                          'headerOptions' => ['style' => 'width:3%;text-align:center;'],
                          'contentOptions' => ['style' => 'width:3%;text-align:center;'],
                          'template' => '{cgn-ip}',
                          'buttons' => [
                            'cgn-ip' => function ($url,$model){
                                   return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>', $url, ['data' => ['pjax' => 0],'class'=>'modal-d','style'=>'text-align:center;display:block;','title'=>Yii::t('app','CGN ip splitting for {public_ip}',['public_ip'=>$model['public_ip']])]);
                            }
                          ],
                        ],




                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=>User::hasPermission("ip-address-delete-view"),
                            'header'=>Yii::t('app','Delete'),
                            'options'=>['style'=>'width:2%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:2%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:2%;text-align:center;'],
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
    'title' =>Yii::t("app","Ip addresses"),
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
                            alertify.success("'.Yii::t("app","Ip address was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error(response.message);
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t('app','Delete an ip adress').'"});;      
        return false;
    });

');

 ?>
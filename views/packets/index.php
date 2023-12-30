<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Arrayhelper;
use app\widgets\GridBulkActions;
use app\widgets\GridPageSize;
use yii\helpers\Url;
use kartik\date\DatePicker;
use yii\bootstrap4\Modal;use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PacketsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$siteConfig = \app\models\SiteConfig::find()->asArray()->one();
$this->title = Yii::t('app', 'Packets');
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

if ( isset( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value )) {
     $gridViewVisibility = json_decode( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value ,true );

}else{
    $gridViewVisibility["serial-view"] = "true@Serial";
    $gridViewVisibility["service-view"] = "true@Service";
    $gridViewVisibility["packet-view"] = "true@Packet";
    $gridViewVisibility["price-view"] = "true@Price";
}

$content = '';
$actions = '';

$viewVisibility = \app\widgets\gridViewVisibility\viewVisibility::widget(
    [
        'params'=>$gridViewVisibility,
        'url'=>Url::to('/packets/grid-view-visibility'),
        'pjaxContainer'=>'#packets-pjax'
    ]
);

$pageSize = GridPageSize::widget([
    'pjaxId'=>'packets-grid-pjax',
    'pageName'=>'_grid_page_size_packets'
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
            <?php if ( User::canRoute('/packets/create') ): ?>
               <a  class="btn btn-success modal-d add-element" data-pjax="0" href="<?=$langUrl ?>/packets/create" title=" <?=Yii::t("app","Create a packet") ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <?=Yii::t("app","Create a packet") ?>
               </a>
            <?php endif ?>
        </div>
    </div>
</div>


<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
            <?php Pjax::begin(['id'=>'packets-grid-pjax']); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pager'=>[
                      'class'=>yii\bootstrap4\LinkPager::class
                    ], 
                    'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
                    'tableOptions' =>['class' => 'table table-striped table-bordered','style'=>'width:100%'],
                    'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'visible'=> ( str_contains( $gridViewVisibility["serial-view"], 'true' ) == "true" && User::hasPermission("packets-serial-column") ) ? true : false,
                                'options'=>['style'=>'width:1%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:1%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:1%;text-align:center;'],
                            ],

                            [
                                'attribute'=>'service_name',
                                'visible'=> ( str_contains( $gridViewVisibility["service-view"], 'true' )  == "true" && User::hasPermission("packets-packet_name-view") ) ? true : false,
                                'format'=>'raw',
                                'options'=>['style'=>'width:30%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:30%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:30%;text-align:center;'],
                                'value'=>function ( $model ){
                               
                                    return  $model['service_name'];
                                 
                                }
                            ],

                            [
                                'attribute'=>'packet_name',
                                'visible'=> ( str_contains( $gridViewVisibility["packet-view"], 'true' ) == "true" && User::hasPermission("packets-packet_name-view") ) ? true : false,
                                'format'=>'raw',
                                'options'=>['style'=>'width:30%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:30%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:30%;text-align:center;'],
                                'value'=>function ( $model ){
                               
                                    return  $model['packet_name'];
                                 
                                }
                            ],

                            [
                                'attribute'=>'packet_price',
                                'visible'=> ( str_contains( $gridViewVisibility["price-view"], 'true' ) == "true" && User::hasPermission("packets-packet_price-view") ) ? true : false,
                                'format'=>'raw',
                                'options'=>['style'=>'width:30%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:30%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:30%;text-align:center;'],
                                'value'=>function ( $model ) use ($siteConfig) {
                               
                                    return  $model['packet_price']." ". $siteConfig['currency'];
                                 
                                }
                            ],

                            [
                              'class' => 'yii\grid\ActionColumn',
                              'options'=>['style'=>'width:3%;text-align:center;'],
                              'headerOptions' => ['style' => 'width:3%;text-align:center;'],
                              'contentOptions' => ['style' => 'width:3%;text-align:center;'],
                              'header' => Yii::t("app","Transfer"),
                              'visible'=>User::canRoute(["/packets/transfer-packet"]),
                              'template' => '{transfer-packet}',
                                'buttons' => [
                                    'transfer-packet' => function ($url, $model) {
                                        if ( $model['service_alias'] == "internet" ) {
                                        $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                                            return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>',$url,[
                                                'data'=>['pjax'=>0],'class'=>'modal-d','style'=>'display:block;text-align:center','title'=>Yii::t('app','Add an attribute for {groupname}',['groupname'=>$model['packet_name']])
                                            ]);
                                        }else{
                                            return '<div  style="text-align:center;display:block"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg></div>';
                                        
                                        }
                                    }
                                  ]
                            ],

                            [
                              'class' => 'yii\grid\ActionColumn',
                              'options'=>['style'=>'width:3%;text-align:center;'],
                              'headerOptions' => ['style' => 'width:3%;text-align:center;'],
                              'contentOptions' => ['style' => 'width:3%;text-align:center;'],
                              'header' => Yii::t("app","Statistic"),
                              'visible'=>User::canRoute(["/packets/detail"]),
                              'template' => '{detail}',
                                'buttons' => [
                                    'detail' => function ($url, $model) {
                                        if ( $model['service_alias'] == "internet" ) {
                                        $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                                            return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',$url,[
                                                'data'=>['pjax'=>0],'style'=>'display:block;text-align:center','title'=>Yii::t('app','Add an attribute for {groupname}',['groupname'=>$model['packet_name']])
                                            ]);
                                        }else{
                                            return '<div  style="text-align:center;display:block"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg></div>';
                                        
                                        }
                                    }
                                  ]
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'visible'=>User::canRoute(["/packets/update"]),
                                'header'=>Yii::t('app','Update'),
                                'options'=>['style'=>'width:3%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:3%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:3%;text-align:center;'],
                                'template'=>'{update}',
                                    'buttons'=>[
                                        'update'=>function($url,$model){
                                            $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                                            return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$langUrl.Url::to("/packets/update")."?id=".$model['id']."&service=".$model['service_alias'],[
                                                'data'=>['pjax'=>0],
                                                'class'=>'modal-d',
                                                'title'=>Yii::t('app','Update {packet_name} packet',['packet_name'=>$model['packet_name']])
                                            ]); 
                                         }
                                    ]
                            ],


                            [
                                'class' => 'yii\grid\ActionColumn',
                                'visible'=>User::canRoute(["/packets/delete"]),
                                'header'=>Yii::t('app','Delete'),
                                'options'=>['style'=>'width:3%;text-align:center;'],
                                'headerOptions' => ['style' => 'width:3%;text-align:center;'],
                                'contentOptions' => ['style' => 'width:3%;text-align:center;'],
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
                            alertify.success("'.Yii::t("app","Packet was deleted successfuly").'");
                        }else{
                             alertify.set("notifier","position", "top-right");
                             alertify.error(response.message);
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a packet").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});;      
        return false;
    });

');

 ?>


<?php 
Modal::begin([
    'title' => Yii::t("app","Packet"),
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
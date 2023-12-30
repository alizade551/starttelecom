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
/* @var $searchModel app\models\search\RadacctSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Online customers');

if ( isset( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value )) {
     $gridViewVisibility = json_decode( Yii::$app->request->cookies->get( Yii::$app->controller->id.'GridViewVisibility')->value ,true );

}else{

    $gridViewVisibility["online-customer-serial-view"] = "true@Serial";
    $gridViewVisibility["online-radacctid-view"] = "true@Radacctid";
    $gridViewVisibility["online-username-view"] = "true@Username";
    $gridViewVisibility["online-customer-view"] = "true@Customer";
    $gridViewVisibility["online-packet_name-view"] = "true@Packet";
    $gridViewVisibility["online-nasipaddress-view"] = "true@Nasipaddress";
    $gridViewVisibility["online-nasportid-view"] = "true@Nasportid";
    $gridViewVisibility["online-nasporttype-view"] = "true@Nasporttype";
    $gridViewVisibility["online-framedipaddress-view"] = "true@Framedipaddress";
    $gridViewVisibility["online-acctstarttime-view"] = "true@Acctstarttime";
    $gridViewVisibility["online-acctupdatetime-view"] = "true@Acctupdatetime";
    $gridViewVisibility["online-callingstationid-view"] = "true@Calledstationid";
    $gridViewVisibility["online-servicetype-view"] = "true@Servicetype";
    $gridViewVisibility["online-framedprotocol-view"] = "true@Framedprotocol";
    $gridViewVisibility["online-acctoutputoctets-view"] = "true@Acctoutputoctets";
    $gridViewVisibility["online-acctinputoctets-view"] = "true@Acctinputoctets";
    $gridViewVisibility["online-enable-disable-view"] = "true@Enable-Disable";
    $gridViewVisibility["online-acctsessiontime-view"] = "true@Acctsessiontime";

}

    $content = '';
    $actions = '';

    $viewVisibility =  \app\widgets\gridViewVisibility\viewVisibility::widget(
        [
            'params'=>$gridViewVisibility,
            'url'=>Url::to('/online-users/grid-view-visibility'),
            'pjaxContainer'=>'#users-grid'
        ]
    );

    $pageSize = GridPageSize::widget([
        'pjaxId'=>'radact-grid-pjax',
        'pageName'=>'_grid_page_size_online_users'
    ]);

    $progressBar = '<div class="progress">
        <div id="auto-reload-progress-bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" >
        </div>
    </div>';


    $pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";

    $actions .= $viewVisibility;
    $actionsContainer = "<div class='helper-action-container'>".$actions."</div>";

    $content = "<div class='helper-container'>".$pageSizeContainer.$actionsContainer."</div>".$progressBar;

?>



<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if (User::canRoute("/users/index")): ?>
            <div>
                <a href="<?=Yii::$app->request->referrer ?>" style="margin-left: 10px;" class="btn btn-primary"><?=Yii::t("app","Back to All customers") ?></a>
            </div>
            <?php endif?>
        </div>
    </div>
</div>
<div class=" card custom-card" >
    <?php Pjax::begin(['id'=>'radact-grid-pjax']); ?>
    <?= GridView::widget([
        'id'=>'radact-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager'=>[
          'class'=>yii\bootstrap4\LinkPager::class
        ], 
        'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
        'columns' => [

            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions'=>['style'=>'width:10px;text-align:center;'],
                'contentOptions'=>['style'=>'width:10px;text-align:center;'],
                'visible'=> ( str_contains( $gridViewVisibility["online-customer-serial-view"] , 'true' ) == "true" && User::hasPermission("online-customer-serial-column") ) ? true : false,
            ],            

            [
                'attribute'=>'radacctid',
                'visible'=> ( str_contains( $gridViewVisibility["online-radacctid-view"] , 'true' ) == "true" && User::hasPermission("online-radacctid-view") ) ? true : false,
                'label'=>Yii::t('app','ID'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:20px;text-align:center;'],
                'contentOptions'=>['style'=>'width:20px;text-align:center;'],
                'value'=>function ( $model ){
       
                    return  $model['radacctid'];
                 
                }
            ],
            [
                'attribute'=>'username',
                'visible'=> ( str_contains( $gridViewVisibility["online-username-view"] , 'true' ) == "true" && User::hasPermission("online-username-view") ) ? true : false,
                'label'=>Yii::t('app','Login'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:80px;text-align:center;'],
                'contentOptions'=>['style'=>'width:80px;text-align:center;'],
                'value'=>function ( $model ){
       
                    return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model['user_id'].'">'.$model['username'].'</a>';
                 
                }
            ],


            [
                'attribute'=>'fullname',
                'visible'=> ( str_contains( $gridViewVisibility["online-customer-view"], 'true' ) == "true" && User::hasPermission("online-customer-view") ) ? true : false,
                'label'=>Yii::t('app','Customer'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:150px;text-align:center;'],
                'contentOptions'=>['style'=>'width:150px;text-align:center;'],
                'value'=>function ( $model ){
       
                    return  '<a  data-pjax="0" href="'.Url::to("/users/view").'?id='.$model['user_id'].'">'.$model['fullname'].'</a>';
                 
                }
            ],
            [
                'attribute'=>'packet_name',
                'visible'=> ( str_contains( $gridViewVisibility["online-packet_name-view"], 'true' )  == "true" && User::hasPermission("online-packet_name-view") ) ? true : false,
                'label'=>Yii::t('app','Packet'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:150px;text-align:center;'],
                'contentOptions'=>['style'=>'width:150px;text-align:center;'],
                'value'=>function ( $model ){
                      $isBlocked = ($model['inet_status'] == '1') ? "" : Yii::t('app','- BLOCKED');
                    return $model['packet_name'].$isBlocked;
                 
                }
            ],

 
            [
                'attribute'=>'nasipaddress',
                'label'=>Yii::t('app','Nas'),
                'visible'=> ( str_contains( $gridViewVisibility["online-nasipaddress-view"], 'true' ) == "true" && User::hasPermission("online-nasipaddress-view") ) ? true : false,
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                'contentOptions'=>['style'=>'width:50px;text-align:center;'],
                'value'=>function ($model){
        
                  return  $model['nasipaddress'];
                }
            ],

            [
                'attribute'=>'nasportid',
                'label'=>Yii::t('app','Nas port'),
                'visible'=> ( str_contains( $gridViewVisibility["online-nasportid-view"], 'true' ) == "true" && User::hasPermission("online-nasportid-view") ) ? true : false,
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                'contentOptions'=>['style'=>'width:50px;text-align:center;'],
                'value'=>function ($model){
        
                  return  $model['nasportid'];
                }
            ],
            

            [
                'attribute'=>'nasporttype',
                'label'=>Yii::t('app','Port type'),
                'visible'=> ( str_contains( $gridViewVisibility["online-nasporttype-view"], 'true' ) == "true" && User::hasPermission("online-nasporttype-view") ) ? true : false,
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                'contentOptions'=>['style'=>'width:50px;text-align:center;'],
                'value'=>function ($model){
        
                  return  $model['nasporttype'];
                }
            ],


            [
                'attribute'=>'framedipaddress',
                'label'=>Yii::t('app','Ip address'),
                'visible'=> ( str_contains( $gridViewVisibility["online-framedipaddress-view"], 'true' )  == "true" && User::hasPermission("online-framedipaddress-view") ) ? true : false,
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:90px;text-align:center;'],
                'contentOptions'=>['style'=>'width:90px;text-align:center;'],
                'value'=>function ($model){
        
                  return  $model['framedipaddress'];
                }
            ],

            [
                'attribute'=>'acctstarttime',
                'visible'=> ( str_contains( $gridViewVisibility["online-acctstarttime-view"], 'true' )  == "true" && User::hasPermission("online-acctstarttime-view") ) ? true : false,
                'label'=>Yii::t('app','Start time'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:150px;text-align:center;'],
                'contentOptions'=>['style'=>'width:150px;text-align:center;'],
                'value'=>function ($model){
        
                  return  $model['acctstarttime'];
                }
            ],
            [
                'attribute'=>'acctupdatetime',
                'label'=>Yii::t('app','Update time'),
                'visible'=> ( str_contains( $gridViewVisibility["online-acctupdatetime-view"], 'true' )  == "true" && User::hasPermission("online-acctupdatetime-view") ) ? true : false,
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:150px;text-align:center;'],
                'contentOptions'=>['style'=>'width:150px;text-align:center;'],
                'value'=>function ($model){
        
                  return  $model['acctupdatetime'];
                }
            ],

            [
                'attribute'=>'acctsessiontime',
                'label'=>Yii::t('app','Uptime'),
                'visible'=> ( str_contains( $gridViewVisibility["online-acctsessiontime-view"], 'true' ) == "true" && User::hasPermission("online-acctsessiontime-view") ) ? true : false,
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:150px;text-align:center;'],
                'contentOptions'=>['style'=>'width:150px;text-align:center;'],
                'value'=>function ($model){
        
                  return \app\models\radius\Radacct::formatAcctSessionTime( $model['acctsessiontime'] );
                }
            ],

            [
                'attribute'=>'callingstationid',
                'visible'=> ( str_contains( $gridViewVisibility["online-callingstationid-view"], 'true' ) == "true" && User::hasPermission("online-callingstationid-view") ) ? true : false,
                'label'=>Yii::t('app','MAC'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:120px;text-align:center;'],
                'contentOptions'=>['style'=>'width:120px;text-align:center;'],
                'value'=>function ($model){
                  return  $model['callingstationid'];
                }
            ],

            [
                'attribute'=>'servicetype',
                'visible'=> ( str_contains( $gridViewVisibility["online-servicetype-view"], 'true' ) == "true" && User::hasPermission("online-servicetype-view") ) ? true : false,
                'format'=>'raw',
                'label'=>Yii::t('app','Service type'),
                'headerOptions'=>['style'=>'width:100px;text-align:center;'],
                'contentOptions'=>['style'=>'width:100px;text-align:center;'],
                'value'=>function ($model){
        
                  return  $model['servicetype'];
                }
            ],

            [
                'attribute'=>'framedprotocol',
                'visible'=> ( str_contains( $gridViewVisibility["online-framedprotocol-view"], 'true' )  == "true" && User::hasPermission("online-framedprotocol-view") ) ? true : false,
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                'contentOptions'=>['style'=>'width:50px;text-align:center;'],
                'header' => Yii::t("app","Protocol"),
                'value'=>function ($model){
        
                  return  $model['framedprotocol'];
                }
            ],

            [
                'attribute'=>'acctoutputoctets',
                'visible'=> ( str_contains( $gridViewVisibility["online-acctoutputoctets-view"], 'true' ) == "true" && User::hasPermission("online-acctoutputoctets-view") ) ? true : false,
                'label'=>Yii::t('app','Download'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                'contentOptions'=>['style'=>'width:50px;text-align:center;'],
      
                'value'=>function ($model){
        
                  return  $model['acctoutputoctets']." MB";
                }
            ],

            [
                'attribute'=>'acctinputoctets',
                'visible'=> ( str_contains( $gridViewVisibility["online-acctinputoctets-view"], 'true' ) == "true" && User::hasPermission("online-acctinputoctets-view") ) ? true : false,
                'label'=>Yii::t('app','Upload'),
                'format'=>'raw',
                'headerOptions'=>['style'=>'width:50px;text-align:center;'],
                'contentOptions'=>['style'=>'width:50px;text-align:center;'],
     
                'value'=>function ($model){
        
                  return   $model['acctinputoctets']." MB";
                }
            ],

            [
              'class' => 'yii\grid\ActionColumn',
              'options'=>['style'=>'width:50px;text-align:center;'],
              'options'=>['style'=>'width:50px;text-align:center;'],
              'contentOptions'=>['style'=>'text-align:center;'],
              'header' => Yii::t("app","Enable/Disable"),
              'visible'=> ( User::canRoute(["/online-users/packet-ajax-status"]) ) ? true : false,
              'headerOptions' => ['style' => 'text-align:center'],
              'template' => '{enable-disable}',
                'buttons' => [
                    'enable-disable' => function ($url, $model) {
                    $isChecked = ($model['inet_status'] == '1') ? "checked" : "";
                        $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                            return '<input name="input_cj" class="stat_us" data-user_id="'.$model['user_id'].'"  data-usp-id="'.$model['u_s_p_i'].'" type="checkbox" '.$isChecked.' hidden="hidden" id="packets_check'.$model['u_s_p_i'].'">
                                        <label class="c-switch" for="packets_check'.$model['u_s_p_i'].'"></label>';
                    }
                  ]
            ],
        ],
    ]); 
      ?>
</div>

<style type="text/css">
#ub-grid-pjax{width: 100%;}

    #logs-grid-clear-filters-btn{
display: block;
    margin: 10px;
    }
.form-inline .form-control {
    display: inline-block;
    width: auto;
    vertical-align: middle;
}

#ub-grid-clear-filters-btn{
    margin-right: 20px;
}
   .users-index-logs, #radact-grid-pjax{width: 100%}
</style>



<?php Pjax::end(); ?>

<?php
$this->registerJs('

var deative = \'<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="16" height="16" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path fill="#f74354" d="m63.437 10.362-9.8-9.8a1.922 1.922 0 0 0-2.717 0L32 19.484 13.079.563a1.922 1.922 0 0 0-2.717 0l-9.8 9.8c-.75.75-.75 1.966 0 2.717L19.484 32 .563 50.921c-.75.75-.75 1.966 0 2.717l9.8 9.8c.75.75 1.966.75 2.717 0L32 44.516l18.921 18.921c.75.75 1.966.75 2.717 0l9.8-9.8c.75-.75.75-1.966 0-2.717L44.516 32l18.921-18.921a1.92 1.92 0 0 0 0-2.717z" data-original="#f74354" class=""></path></g></svg>\';

var active  = \'<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="18" height="18" x="0" y="0" viewBox="0 0 367.805 367.805" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M183.903.001c101.566 0 183.902 82.336 183.902 183.902s-82.336 183.902-183.902 183.902S.001 285.469.001 183.903C-.288 82.625 81.579.29 182.856.001h1.047z" style="" fill="#3bb54a" data-original="#3bb54a" class=""></path><path d="M285.78 133.225 155.168 263.837l-73.143-72.62 29.78-29.257 43.363 42.841 100.833-100.833z" style="" fill="#d4e1f4" data-original="#d4e1f4" class=""></path></g></svg>\';




$(document).on("click",".stat_us",function(){
        that = $(this);
    var _user_id = $(this).attr("data-user_id");
    var _usp_id = $(this).attr("data-usp-id");

    if($(this).is(":checked")){
    $(this).prop("checked",true);  
    var checked = 1;
}else{
    var checked = 2;
    $(this).prop("checked",false);  
}
$.ajax({
    url:"packet-ajax-status",
    type:"post",
    data:{checked:checked,_user_id:_user_id,_usp_id:_usp_id},
    success:function(res){
        console.log(res,checked)
        if(checked == 2){
            that.parent().parent().find(".hafiz").html(deative)
        }else{
            that.parent().parent().find(".hafiz").html(active)
        }
    }
});
 e.preventDefault();
 return false;
})



   
var progressBarValue = 0;
var progressBarSelector = "#auto-reload-progress-bar";
var gridContainerSelector = "#radact-grid-pjax";
var progressBarInterval;

function updateProgressBar() {
    var progressBar = $(progressBarSelector);
    progressBarValue += (100 / 25);
    progressBar.css("width", progressBarValue + "%");

    if (progressBarValue > 100) {
        clearInterval(progressBarInterval);
        resetProgressBar();
        reloadGrid();
    }
}

function resetProgressBar() {
    progressBarValue = 0;
    $(progressBarSelector).css("width", "0%");
}

function reloadGrid() {
    $.pjax.reload({
        container: gridContainerSelector,
        type: "POST",
        timeout: 5000
    });
}

function startReloadInterval() {
    return setInterval(function () {
        updateProgressBar();
    }, 1000); // 1000 milliseconds = 1 second
}


resetProgressBar();
progressBarInterval = startReloadInterval();

// Belirli aralıklarla yeniden çalıştırma
setInterval(function () {
    resetProgressBar();
    clearInterval(progressBarInterval);
    progressBarInterval = startReloadInterval();
}, 28000); // 28000 milliseconds = 28 seconds




');
?>


<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Arrayhelper;
use app\widgets\GridBulkActions;
use app\widgets\GridPageSize;
use yii\helpers\Url;
use yii\bootstrap4\Modal;
use webvimark\modules\UserManagement\models\User;


$this->title = Yii::t('app','Messages');
$this->params['breadcrumbs'][] = $this->title;
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";


$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'sms-grid-pjax',
    'pageName'=>'_grid_page_size_sms'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";

?>


<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if (User::canRoute("/users-message/create")): ?>
                <a class="btn btn-success add-element" data-pjax="0" href="/users-message/create">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <?=Yii::t("app","Send message") ?>
                </a>
            <?php endif?>
        </div>
    </div>
</div>

<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
            <?php Pjax::begin(['id'=>'sms-grid-pjax']); ?>
                <?= GridView::widget([
                'id'=>'sms-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pager'=>[
                  'class'=>yii\bootstrap4\LinkPager::class
                ],        
                'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'visible'=> (  User::hasPermission("messages-serial-column-view") ) ? true : false,
                        'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                    ],

                    [
                        'attribute'=>'users',
                        'label'=>Yii::t('app','Customer'),
                        'visible'=> ( User::hasPermission("messages-customer-view") ) ? true : false,
                        'format'=>'raw',
                        'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                        'value'=>function ($model){
                          return  $model['user_fullname'];
                        }
                    ],


                    [
                        'attribute'=>'text',
                        'visible'=> ( User::hasPermission("messages-text-view") ) ? true : false,
                        'headerOptions'=>['style'=>'width:50%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:50%;text-align:center;'],
                        'format'=>'raw',
                        'value'=>function ($model){
                        return  $model['text'];
                        }
                    ],

                    [
                        'attribute'=>'type',
                        'visible'=> (  User::hasPermission("messages-type-view") ) ? true : false,
                        'label'=>Yii::t('app','Type'),
                        'format'=>'raw',
                        'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                        'value'=>function ($model){
                          return  \app\models\UsersMessage::getMessageType()[$model['type']] ;
                        }
                    ],

                    [
                        'attribute'=>'status',
                        'visible'=> (  User::hasPermission("messages-status-view") ) ? true : false,
                        'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:5%;text-align:center;'],
                        'format'=>'raw',
                        'value'=>function ($model){
                            if ( $model['status'] == 1 ) {
                                return '<span class="badge badge-success">'.\app\models\UsersMessage::getMessageStatus()[$model['status']].'</span>';
                            }
                            $langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
                            return '<a class="badge-danger" style="padding: 5px; border-radius: 5px;"  data-options=\'{"touch" : false}\'    tabindex="false" data-pjax="0" data-type="ajax" data-fancybox="" href="javascript:;" data-src="'.$langUrl.'/users-message/send-again?id='.$model['id'].'" ><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> '.Yii::t('app','Send again').'</a>';
                        }
                    ],

                    [
                        'label'=>Yii::t('app','Send at'),
                        'visible'=> ( User::hasPermission("messages-message_time-view") ) ? true : false,
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
                          return date('d/m/Y H:i:s',$model['message_time']);
                        }
                    ],
              
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'visible'=> ( User::canRoute(["/users-message/delete"]) ) ? true : false,
                        'header'=>Yii::t('app','Delete'),
                        'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                        'contentOptions'=>['style'=>'width:3%;text-align:center;'],
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

      var message  = "'.Yii::t("app","Are you sure want to delete this ?").'";
          alertify.confirm( message, function (e) {
            if (e) {
               $.ajax({
                   url:that.attr("data-href"),
                   type:"post",
                   success:function(response){
                        if(response.status == "success"){
                            that.closest("tr").fadeOut("slow");
                            alertify.success("'.Yii::t("app","Message was deleted successfuly").'");
                        }else{
                             alertify.error("'.Yii::t("app","Message reload page and try again...").'");
                        }
                   }
               });
            } 
         }).set({title:"'.Yii::t("app","Delete a message").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});  
        return false;
    });

');

 ?>

<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\widgets\GridBulkActions;
use app\widgets\GridPageSize;
use yii\helpers\Url;
use app\models\Cities;
use app\models\radius\Nas;
use yii\bootstrap4\Modal;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Districts');
$this->params['breadcrumbs'][] = $this->title;
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'district-grid-pjax',
    'pageName'=>'_grid_page_size_district'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";
?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if ( User::canRoute(["/district/create"]) ): ?>
               <a class="btn btn-success" data-pjax="0" href="/district/create">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <?=Yii::t("app","Create a district") ?>
               </a>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
                <?php Pjax::begin(['id'=>'district-grid-pjax',]); ?> 
                <?= GridView::widget([
                    'id'=>'district-grid',
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
                                'visible'=> ( User::hasPermission("district-serial-column-view") ) ? true : false,
                                'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                                'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                            ],
                            [
                                'attribute'=>'district_name',
                                'visible'=> (  User::hasPermission("district-name-view") ) ? true : false,
                                'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                                'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                                'value'=>function ($model){
                                  return $model->district_name;
                                }
                            ],

                            [
                                'attribute'=>'city_id',
                                'visible'=> (  User::hasPermission("districts-city_id-view") ) ? true : false,
                                'label'=>Yii::t('app','City'),
                                'headerOptions'=>['style'=>'width:20%;text-align:center;'],
                                'contentOptions'=>['style'=>'width:20%;text-align:center;'],
                                'filter' => Html::activeDropDownList(
                                $searchModel,
                                'city_id',
                                ArrayHelper::map(Cities::find()->all(),'id','city_name'),
                                ['class' => 'form-control', 'prompt' => Yii::t('app','Select')]
                                ),
                                'value'=>function($model){
                                    return $model->city->city_name;
                                }
                            ],
                            
                            [
                                'attribute'=>'nas_id',
                                'visible'=> ( User::hasPermission("districts-bras-view") ) ? true : false,
                                'label'=>Yii::t("app","Bras"),
                                'headerOptions'=>['style'=>'width:5%;text-align:center;'],
                                'contentOptions'=>['style'=>'width:5%;text-align:center;'],

                                'filter' => Html::activeDropDownList(
                                $searchModel,
                                'nas_id',
                                ArrayHelper::map(Nas::find()->all(),'id','nasname'),
                                ['class' => 'form-control', 'prompt' => Yii::t('app','Select')]
                                ),
                                'value'=>function($model){
                                    if ($model->nas_id != "") {
                                        return $model->nas->nasname;
                                    }else{
                                        return Yii::t("app","Bras has not been selected"); 
                                    }
                                }
                            ],

                            [
                                'attribute'=>'device_registration',
                                'visible'=> (  User::hasPermission("districts-device_registration-view") ) ? true : false,
                                'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                                'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                                'filter' => Html::activeDropDownList(
                                $searchModel,
                                'device_registration',
                                \app\models\District::getDistrictUserRegistrationOnDeviceStatus(),
                                ['class' => 'form-control', 'prompt' => Yii::t('app','Select')]
                                ),
                                'value'=>function($model){
                                    return \app\models\District::getDistrictUserRegistrationOnDeviceStatus()[$model->device_registration ];
                                }
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'visible'=> (  User::canRoute(["/district/add-router"]) ) ? true : false,
                                'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                                'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                                'header' => Yii::t("app","Define"),
                                'template' => '{add-router}',
                                'buttons' => [
                                    'add-router' => function ($url, $model) {
                                        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>', $url, ['data' => ['pjax' => 0],'style'=>'text-align:center;display:block;','class'=>'modal-d','title'=> Yii::t('app','Define router for {distrcit_name} district',['distrcit_name'=>$model->district_name])]) ;
                                    },
                                  ],

                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'visible'=> ( User::canRoute(["/district/update"]) ) ? true : false,
                                'header'=>Yii::t('app','Update'),
                                'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                                'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                                'template'=>'{update}',
                                'buttons'=>[
                                    'update'=>function($url,$model){
                                        return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url."&city_id=".$model->city_id,[
                                            'title'=>$model->district_name,
                                            'data'=>['pjax'=>0],
                                        ]); 
                                     }
                                ]
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'visible'=> (  User::canRoute(["/district/delete"]) ) ? true : false,
                                'header'=>Yii::t('app','Delete'),
                                'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                                'contentOptions'=>['style'=>'width:3%;text-align:center;'],
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
    'title' =>Yii::t("app","District"),
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
                            alertify.success("'.Yii::t("app","District was deleted successfuly").'");
                        }else{
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a district").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});
        return false;
    });

');

 ?>
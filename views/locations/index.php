<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use app\widgets\GridPageSize;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel app\models\search\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Locations');
$this->params['breadcrumbs'][] = $this->title;
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key=AIzaSyBBK66Fyl1vplIe1V9xvNA4aSs3MuHxXvY', ['depends' => [yii\web\JqueryAsset::className()]]);


$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'location-grid-pjax',
    'pageName'=>'_grid_page_size_location'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";

?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if ( User::canRoute(["/locations/create"]) ): ?>
               <a class="btn btn-success" data-pjax="0" href="/locations/create">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <?=Yii::t("app","Create a location") ?>
               </a>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="card custom-card">
    <div class="row">
        <div class="col-sm-12">
              <?php Pjax::begin(['id'=>'location-grid-pjax']); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pager'=>[
                      'class'=>yii\bootstrap4\LinkPager::class
                    ], 
                    'tableOptions' =>['class' => 'table table-striped table-bordered','style'=>'width:100%'],
                    'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'visible'=> ( User::hasPermission("locations-serial-column-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                        ],
                     
                        [
                            'attribute'=>'name',
                            'visible'=> ( User::hasPermission("locations-name-view") ) ? true : false,
                            'headerOptions'=>['style'=>'width:25%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:25%;text-align:center;'],
                            'value'=>function ($model){
                              return $model->name;
                            }
                        ],

                        [
                            'attribute'=>'city',
                            'visible'=> (User::hasPermission("locations-city-view") ) ? true : false,
                            'label'=>Yii::t('app','Cities'),
                            'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                            'value' => 'city.city_name'
                        ],            

                        [
                            'attribute'=>'district',
                            'visible'=> ( User::hasPermission("locations-district-view") ) ? true : false,
                            'label'=>Yii::t('app','Districts'),
                            'headerOptions'=>['style'=>'width:10%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:10%;text-align:center;'],
                            'value' => 'district.district_name'
                        ],  

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=> ( User::canRoute(["/locations/map"]) ) ? true : false,
                            'header'=>Yii::t('app','On map'),
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                            'template'=>'{map}',
                                'buttons'=>[
                                    'map'=>function($url,$model){
                                        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>',$url,[
                                            'data'=>['pjax'=>0],'title'=>Yii::t('app','Users list on map in {location} location',['location'=>$model['name']])
                                        ]); 
                                     }
                                ]
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=> ( User::canRoute("/locations/update") ) ? true : false,
                            'header'=>Yii::t('app','Update'),
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
                            'template'=>'{update}',
                                'buttons'=>[
                                    'update'=>function($url,$model){
                                        return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url."&city_id=".$model->city_id,[
                                            'data'=>['pjax'=>0],
                                        ]); 
                                     }
                                ]
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=> ( User::canRoute('/locations/delete') ) ? true : false,
                            'header'=>Yii::t('app','Delete'),
                            'headerOptions'=>['style'=>'width:2%;text-align:center;'],
                            'contentOptions'=>['style'=>'width:2%;text-align:center;'],
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
                            alertify.success("'.Yii::t("app","Location was deleted successfuly").'");
                        }else{
                             alertify.error("'.Yii::t("app","Please reload page and try again...").'");
                        }
                   }
               });
            } 
        }).set({title:"'.Yii::t("app","Delete a location").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});
        return false;
    });

');

 ?>
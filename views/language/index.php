<?php

use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\widgets\GridBulkActions;
use app\widgets\GridPageSize;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\LanguageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t("app","Languages");
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'cities-grid-pjax',
    'pageName'=>'_grid_page_size_lang'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";

?>



<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <?php if ( User::canRoute('/language/create') ): ?>
               <a  class="btn btn-success modal-d add-element" data-pjax="0" href="<?=$langUrl ?>/language/create" title=" <?=Yii::t("app","Create a language") ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <?=Yii::t("app","Create a language") ?>
               </a>
            <?php endif ?>
        </div>
    </div>
</div>



<div class="card custom-card" >
    <div class="row">
        <div class="col-sm-12">
            <?php Pjax::begin(['id'=>'lang-grid-pjax']); ?>   
             <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'name',
                        'alias',
                        [
                            'attribute'=>'published',
                            'format'=>'raw',
                            'value'=>function($model){
                                if ($model->published == 1) {
                                   $icon = '<span class="badge  badge-success"><i class="fa fa-check"></span>';
                                }else{
                                    $icon = '<span class="badge  badge-danger"><i class="fa fa-times"></span>';
                                }
                                return Html::a($icon, ['toggle-attribute','attribute'=>'published','id'=>$model->id]);
                            }
                        ],

                        [
                          'options'=>['style'=>'width:50px;text-align:center;'],
                          'header' => '',
                          'headerOptions' => ['style' => 'color:#337ab7;text-align:center'],
                          'format'=>'raw',
                          'value'=>function($model){
                            return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg>', ['file-edit','id'=>$model->id], ['title'=>Yii::t("app",'Update'),'data' => ['pjax' => 0],'style'=>'text-align:center;display:block;']);
                          }
                        ],


                        [
                            'class' => 'yii\grid\ActionColumn',
                            'visible'=>User::canRoute(["/language/update"]),
                            'header'=>Yii::t('app','Update'),
                            'options'=>['style'=>'width:3%;text-align:center;'],
                            'headerOptions' => ['style' => 'width:3%;text-align:center;'],
                            'contentOptions' => ['style' => 'width:3%;text-align:center;'],
                            'template'=>'{update}',
                                'buttons'=>[
                                    'update'=>function($url,$model){
                                        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>',$url,[
                                            'data'=>['pjax'=>0],
                                            'class'=>'modal-d',
                                            'title'=>Yii::t('app','Update {language} language',['language'=>$model['name']])
                                        ]); 
                                     }
                                ]
                        ],



                        [
                            'class' => 'yii\grid\ActionColumn',
                            'options'=>['style'=>'width:50px;text-align:center;'],

                            'headerOptions' => ['style' => 'color:#337ab7;text-align:center'],
                            'contentOptions'=>['style'=>'width:50px;text-align:center;line-height: 20px'],
                            'template' => '{delete}',
                            'buttons' => [
                            'delete' => function ($url, $model) {
                            // return Html::a('', $url, ['data' => ['pjax' => 0],'title'=>Yii::t("app","Delete"),'style'=>'text-align:center;display:block;','class'=>'confirm']);
                            return '<a href="'.$url.'" title="'.Yii::t('app','Delete').'" aria-label="'.Yii::t('app','Delete').'" data-pjax="0" data-confirm="'.Yii::t('app','Are you sure want to delete this element ?').'" data-method="post"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>';
                                },
                            ],
                        ],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>


<?php 
Modal::begin([
    'title' => Yii::t("app","Request order information"),
    'id' => 'modal',
    'class'=>'drawer right-align',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],

    'size' => 'modal-lg',
    'asDrawer' => true,

]);
echo "<div id='modalContent'></div>";
Modal::end();
?>
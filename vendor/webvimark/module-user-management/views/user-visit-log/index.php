<?php

use webvimark\extensions\DateRangePicker\DateRangePicker;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\GridPageSize;
use yii\grid\GridView;
use yii\bootstrap4\Modal;
use webvimark\modules\UserManagement\models\User;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var webvimark\modules\UserManagement\models\search\UserVisitLogSearch $searchModel
 */

$this->title = Yii::t('app', 'Visit logs');
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";


$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'user-visit-log-grid-pjax',
    'pageName'=>'_grid_page_size_user_visit'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";
?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h4><?=$this->title ?> </h4> </div>
            <?php if ( User::canRoute(["/user-management/user/index"]) ): ?>
               <a class="btn btn-primary" data-pjax="0" href="<?=$langUrl ?>/user-management/user" title="<?=Yii::t('app','Users') ?>">
                <?=Yii::t("app","Users") ?>
               </a>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="card custom-card">
	<div class="row">
		<div class="col-sm-12">
			<?php Pjax::begin(['id'=>'user-visit-log-grid-pjax']) ?>
				<?= GridView::widget([
					'id'=>'user-visit-log-grid',
					'dataProvider' => $dataProvider,
			        'pager'=>[
			          'class'=>yii\bootstrap4\LinkPager::class
			        ],
					'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
					'filterModel' => $searchModel,
					'columns' => [
						[
							'class' => 'yii\grid\SerialColumn', 
		                    'headerOptions'=>['style'=>'width:1%;text-align:center;'],
		                    'contentOptions'=>['style'=>'width:1%;text-align:center;'],
						],

						[
							'attribute'=>'user_id',
		                    'headerOptions'=>['style'=>'width:30%;text-align:center;'],
		                    'contentOptions'=>['style'=>'width:30%;text-align:center;'],
							'format'=>'raw',
							'value'=>function($model){
									return Html::a(@$model->user->username, ['view', 'id'=>$model->id], ['data-pjax'=>0]);
							},
						],

						[
							'attribute'=>'language',
		                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
		                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
							'format'=>'raw',
							'value'=>function($model){
								return $model->language;
							},
						],

						[
							'attribute'=>'os',
		                    'headerOptions'=>['style'=>'width:10%;text-align:center;'],
		                    'contentOptions'=>['style'=>'width:10%;text-align:center;'],
							'format'=>'raw',
							'value'=>function($model){
								return $model->os;
							},
						],


						[
							'attribute'=>'browser',
		                    'headerOptions'=>['style'=>'width:10%;text-align:center;'],
		                    'contentOptions'=>['style'=>'width:10%;text-align:center;'],
							'format'=>'raw',
							'value'=>function($model){
								return $model->browser;
							},
						],

						[
							'attribute'=>'ip',
		                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
		                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
							'format'=>'raw',
							'value'=>function($model){
								return Html::a($model->ip, "http://ipinfo.io/" . $model->ip, ["target"=>"_blank"]);
							},
						],

			            [
			                'label'=>Yii::t('app','Logged at'),
		                    'headerOptions'=>['style'=>'width:20%;text-align:center;'],
		                    'contentOptions'=>['style'=>'width:20%;text-align:center;'],
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
			                  return date('d/m/Y H:i:s',$model['visit_time']);
			                }
			            ],

						[
						  'class' => 'yii\grid\ActionColumn',
		                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
		                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
						  'template' => '{view}',
						    'buttons' => [
						        'view' => function ($url, $model) {
						            return '<a title="'.Yii::t("app","Visit log").'" class="modal-d" href="'.$url.'"  ><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>';
						        },
						      ],
						],
					],
				]); ?>
			<?php Pjax::end() ?>
			
		</div>
	</div>
</div>


<?php 
    Modal::begin([
        'title' => Yii::t("app","Balance operation"),
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
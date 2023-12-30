<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use webvimark\extensions\GridBulkActions\GridBulkActions;
use webvimark\extensions\GridPageSize\GridPageSize;
use yii\grid\GridView;
use webvimark\modules\UserManagement\models\User;


/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var webvimark\modules\UserManagement\models\rbacDB\search\AuthItemGroupSearch $searchModel
 */

$this->title = Yii::t('app', 'Permission groups');
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
?>

	<div class="panel panel-default" style="width: 100%">
		<div class="row" style="padding: 10px 0">
		    <div class="col-sm-6">
		        <div class="col-sm-6">
				<?php if (User::canRoute("/user-management/auth-item-group/create")): ?>
	                <a class="btn btn-primary" data-pjax="0" href="/user-management/auth-item-group/create">
	                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
	                    <?=Yii::t("app","Add a permission group ") ?>
	                </a>
				<?php endif?>
		        </div>            
		    </div>
		    <div class="col-sm-6 ">
		         <div style="float: right;">
					<?= GridPageSize::widget(['pjaxId'=>'auth-item-group-grid-pjax']) ?>
		   		 </div>
			</div>
		</div>




			<?php Pjax::begin([
				'id'=>'auth-item-group-grid-pjax',
			]) ?>

			<?= GridView::widget([
				'id'=>'auth-item-group-grid',
				'dataProvider' => $dataProvider,
				'pager'=>[
					'options'=>['class'=>'pagination pagination-sm'],
					'hideOnSinglePage'=>true,
					'lastPageLabel'=>'>>',
					'firstPageLabel'=>'<<',
				],
				'layout'=>'{items}<div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">{summary}</div></div>',
				'filterModel' => $searchModel,
				'columns' => [
					['class' => 'yii\grid\SerialColumn', 'options'=>['style'=>'width:10px'] ],

					[
						'attribute'=>'name',
						'value'=>function($model){
								return Html::a($model->name, ['update', 'id'=>$model->code], ['data-pjax'=>0]);
							},
						'format'=>'raw',
					],
					'code',

					['class' => 'yii\grid\CheckboxColumn', 'options'=>['style'=>'width:10px'] ],

					[
					  'class' => 'yii\grid\ActionColumn',
					  'options'=>['style'=>'width:20px;text-align:center;'],

					  'headerOptions' => ['style' => 'color:#337ab7;text-align:center'],
					  'template' => '{view}',
					    'buttons' => [


					        'view' => function ($url, $model) {
					            return '<a  data-pjax="0"  data-type="ajax" data-fancybox="" href="javascript:;" data-src="'.$url.'"  ><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>';
					        },



					       
					      ],

					],

				            [
				                      'class' => 'yii\grid\ActionColumn',
				                      'options'=>['style'=>'width:20px;text-align:center;'],
				 
				                      'headerOptions' => ['style' => 'color:#337ab7;text-align:center'],
				                      'template' => '{update}',
				                        'buttons' => [
				                 

				                            'update' => function ($url, $model) {
				                                return '<a data-pjax="0" href="'.$url.'" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>';
				                            },



				                           
				                          ],

				            ],




				            [
				                      'class' => 'yii\grid\ActionColumn',
				                      'options'=>['style'=>'width:20px;text-align:center;'],
				                     
				                      'headerOptions' => ['style' => 'color:#337ab7;text-align:center'],
				                       'contentOptions'=>['style'=>'width:20px;text-align:center;line-height: 20px'],
				                      'template' => '{delete}',
				                        'buttons' => [
				                            'delete' => function ($url, $model) {
				                                // return Html::a('', $url, ['data' => ['pjax' => 0],'title'=>Yii::t("app","Delete"),'style'=>'text-align:center;display:block;','class'=>'confirm']);

				                                return '<a href="'.$url.'" title="'.Yii::t('app','Sil').'" aria-label="'.Yii::t('app','Sil').'" data-pjax="0" data-confirm="Bu elementi silmək istədiyinizə əminsinizmi?" data-method="post"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></a>';

				                            },
				                          ],




				            ],

				],
			]); ?>
		
			<?php Pjax::end() ?>

	</div>


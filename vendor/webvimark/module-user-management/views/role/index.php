<?php
use webvimark\extensions\GridBulkActions\GridBulkActions;
use app\widgets\GridPageSize;
use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\User;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;


/**
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var webvimark\modules\UserManagement\models\rbacDB\search\RoleSearch $searchModel
 * @var yii\web\View $this
 */
$this->title = Yii::t('app', 'Roles');
$this->params['breadcrumbs'][] = $this->title;
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'role-grid-pjax',
    'pageName'=>'_grid_page_size_role'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";

?>


<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
				<?php if (User::canRoute("/user-management/role/create")): ?>
	                <a title="<?=Yii::t("app","Create a role") ?>" class="btn btn-success modal-d" data-pjax="0" href="<?=$langUrl  ?>/user-management/role/create">
	                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
	                    <?=Yii::t("app","Create a role") ?>
	                </a>
				<?php endif?>   
        </div>
    </div>
</div>

<div class="card custom-card" >
	<div class="row">
		<div class="col-sm-12">
				<?php Pjax::begin(['id'=>'role-grid-pjax']) ?>
				<?= GridView::widget([
					'id'=>'role-grid',
					'dataProvider' => $dataProvider,
			        'pager'=>[
			          'class'=>yii\bootstrap4\LinkPager::class
			        ],
					'filterModel' => $searchModel,
					'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
					'columns' => [
			            [
			                'class' => 'yii\grid\SerialColumn',
			                'headerOptions'=>['style'=>'width:50px;text-align:center;'],
			                'contentOptions'=>['style'=>'width:50px;text-align:center;'],
			                'visible'=>  User::hasPermission("roles-serial-column"),
			            ],
						[
							'attribute'=>'description',
			                'headerOptions'=>['style'=>'width:250px;text-align:center;'],
			                'contentOptions'=>['style'=>'width:250px;text-align:center;'],
							'visible'=>  User::hasPermission("roles-description-view"),
							'value'=>function(Role $model){
									return Html::a($model->description, ['view', 'id'=>$model->name], ['data-pjax'=>0]);
								},
							'format'=>'raw',
						],

						[
							'attribute'=>'name',
						    'headerOptions'=>['style'=>'width:250px;text-align:center;'],
						    'contentOptions'=>['style'=>'width:250px;text-align:center;'],
							'format'=>'raw',
							'visible'=>  User::hasPermission("roles-name-view"),
							'value'=>function(Role $model){
								return $model->name;
							},
						],

						// ['class' => 'yii\grid\CheckboxColumn', 'options'=>['style'=>'width:10px'] ],
						[
						  'class' => 'yii\grid\ActionColumn',
						  'options'=>['style'=>'width:20px;text-align:center;'],
						  'headerOptions' => ['style' =>'text-align:center'],
						  						    'contentOptions'=>['style'=>'width:20px;text-align:center;'],
						  'visible'=>  User::canRoute("user-management/role/view"),
						  'template' => '{view}',
						    'buttons' => [
						        'view' => function ($url, $model) {
						            return '<a data-options=\'{"touch" : false}\' data-pjax="0" href="'.$url.'" ><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>';
						        }
						      ]
						],


			            [
	                      'class' => 'yii\grid\ActionColumn',
	                      'options'=>['style'=>'width:20px;text-align:center;'],
	                      'contentOptions'=>['style'=>'width:20px;text-align:center;'],
	                      'visible'=>  User::canRoute("user-management/role/update"),
	                      'headerOptions' => ['style' => 'color:#337ab7;text-align:center'],
	                      'template' => '{update}',
	                      'buttons' => [
	                       'update' => function ($url, $model) {
	                              return '<a class="modal-d" title="'.Yii::t("app","Update role {role}",['role'=>$model->description]).'" data-pjax="0" href="'.$url.'" ><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg></a>';
	                            }
	                          ],
			            ],

			            [
	                      'class' => 'yii\grid\ActionColumn',
	                      'options'=>['style'=>'width:20px;text-align:center;'],
	                      						    'contentOptions'=>['style'=>'width:20px;text-align:center;'],
	                      'visible'=>  User::canRoute("user-management/role/delete"),
	                      'headerOptions' => ['style' => 'color:#337ab7;text-align:center'],
	                      'contentOptions'=>['style'=>'width:20px;text-align:center;'],
	                      'template' => '{delete}',
	                        'buttons' => [
	                            'delete' => function ($url, $model) {
	                                return '<a href="'.$url.'" title="'.Yii::t('app','Delete').'" aria-label="'.Yii::t('app','Delete').'" data-pjax="0" data-confirm="'.Yii::t("app","Are you sure you want to delete this item?").'" data-method="post"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></a>';
	                            },
	                          ]
			            ]
					],
				]); ?>

				<?php Pjax::end() ?>
			
		</div>
	</div>
</div>

<?php 
Modal::begin([
    'title' => Yii::t("app","Roles"),
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
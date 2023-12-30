<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use webvimark\extensions\GridBulkActions\GridBulkActions;
use app\widgets\GridPageSize;
use yii\grid\GridView;
use yii\bootstrap4\Modal;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var webvimark\modules\UserManagement\models\search\UserSearch $searchModel
 */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";


$content = '';
$actions = '';

$pageSize = GridPageSize::widget([
    'pjaxId'=>'user-grid-pjax',
    'pageName'=>'_grid_page_size_users'
]);

$pageSizeContainer = "<div class='page-size-container'>".$pageSize."</div>";
$content = "<div class='helper-container'>".$pageSizeContainer."</div>";

?>



<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
			<?php if (User::canRoute("/user-management/user/create")): ?>
                <a class="btn btn-success" data-pjax="0" href="/user-management/user/create">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <?=Yii::t("app","Create a user") ?>
                </a>
			<?php endif?>
        </div>
    </div>
</div>

<div class="card custom-card">
    <div class="row">


 		
 		<div class="col-sm-12">
			<?php Pjax::begin([
				'id'=>'user-grid-pjax',
			]) ?>

			<?= GridView::widget([
				'id'=>'user-grid',
				'dataProvider' => $dataProvider,
		        'pager'=>[
		          'class'=>yii\bootstrap4\LinkPager::class
		        ],
				'filterModel' => $searchModel,
      			'layout'=>' '.$content .' {items}<div class="grid-bottom"><div class="summary">{summary}</div><div>{pager}</div></div>',
				'columns' => [

					[
						'label'=>Yii::t('app',"Photo"),
	                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
	                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
						'format'=>'raw',
						'filter'=>false,
						'value'=>function($model){
						 if($model['photo_url'] == ""){
								if (Yii::$app->request->cookies['theme'] == "light") {
									$image =Yii::$app->request->baseUrl.'/uploads/users/profile/avatar_light.png';
								}else{
									$image =Yii::$app->request->baseUrl.'/uploads/users/profile/avatar_dark.png';
								}
							}else{
								$image = '/uploads/users/profile/'.$model['photo_url'];
							}
							return '<img style="height:30px;width:unset" src="'.$image.'"/>';
						 }
					],

					// [
					// 	'attribute'=>'id',
			        //     'headerOptions'=>['style'=>'width:80px;height:30px;text-align:center; '],
			        //     'contentOptions'=>['style'=>'width:80px;height:30px;text-align:center;'],
					// 	'format'=>'raw',
					// 	'value'=>function(User $model){
					// 		return Html::a($model->id,['view', 'id'=>$model->id],['data-pjax'=>0]);
					// 	},
					// ],

					[
						'attribute'=>'username',
						'label'=>Yii::t('app','Username'),
						'visible'=>User::hasPermission('viewUserUserName'),
	                    'headerOptions'=>['style'=>'width:30%;text-align:center;'],
	                    'contentOptions'=>['style'=>'width:30%;text-align:center;'],
						'format'=>'raw',
						'value'=>function(User $model){
							return Html::a($model->username,['view', 'id'=>$model->id],['data-pjax'=>0]);
						},
					],

					// [
					// 	'attribute'=>'auth_key',
					// 	'label'=>Yii::t("app","Auth key"),
					// 	'headerOptions'=>['style'=>'width:200px;height:30px;text-align:center;'],
		        	//     'contentOptions'=>['style'=>'width:200px;height:30px;text-align:center;'],
					// 	'visible'=>User::hasPermission('viewAuthKeyEmail'),
					// 	'value'=>function(User $model){
					// 			return $model->auth_key;
					// 		},
					// 	'format'=>'raw',
					// ],

					[
						'attribute'=>'email',
						'format'=>'raw',
	                    'headerOptions'=>['style'=>'width:10%;text-align:center;'],
	                    'contentOptions'=>['style'=>'width:10%;text-align:center;'],
						'visible'=>User::hasPermission('viewUserEmail'),
					],



	




					// [
					// 	'class'=>'webvimark\components\StatusColumn',
					// 	'attribute'=>'email_confirmed',
					// 	'visible'=>User::hasPermission('viewUserEmail'),
					// 	'value'=>function(User $model){
					// 		if ($model->email_confirmed == 1) {
					// 			$string = '<span style="font-size:85%;" class="badge badge-warning"> No </span>';
					// 		}else{
					// 			$string = '<span style="font-size:85%;" class="badge badge-success"> Yes </span>';
					// 		}

					// 			return $string;
					// 		},
					// 	'format'=>'raw',
					// ],
					[
						'attribute'=>'gridRoleSearch',
	                    'headerOptions'=>['style'=>'width:10%;text-align:center;'],
	                    'contentOptions'=>['style'=>'width:10%;text-align:center;'],
						'filter'=>ArrayHelper::map(Role::getAvailableRoles(Yii::$app->user->isSuperAdmin),'name', 'description'),
						'value'=>function(User $model){
							return implode(', ', ArrayHelper::map($model->roles, 'name', 'description'));
						},
						'format'=>'raw',
						'visible'=>User::hasPermission('viewUserRoles'),
					],
					[
						'attribute'=>'registration_ip',
						'label'=>Yii::t("app","Registration IP"),
	                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
	                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
						'value'=>function(User $model){
							return Html::a($model->registration_ip, "http://ipinfo.io/" . $model->registration_ip, ["target"=>"_blank"]);
						 },
						'format'=>'raw',
						'visible'=>User::hasPermission('viewRegistrationIp'),
					],


                	[
		                'attribute'=>'personal',
		                'label'=>Yii::t('app','Personal'),
	                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
	                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
		                'visible'=>User::hasPermission('viewUserAsPersonal'),
		                'filter' => Html::activeDropDownList(
		                $searchModel,
		                'personal',
		                User::personalStatus(),
		                ['class' => 'form-control', 'prompt' => Yii::t('app','Select')]
		                ),
		                'value'=>function($model){
		                return User::personalStatus()[$model->personal];
		                }
	                ],
					[
					    'attribute'=>'api_access',
					    'label'=>Yii::t("app","API access"),
					    'visible'=>User::hasPermission('viewApiAccess'),
					    'label'=>Yii::t('app','API access'),
	                    'headerOptions'=>['style'=>'width:5%;text-align:center;'],
	                    'contentOptions'=>['style'=>'width:5%;text-align:center;'],
					    'format'=>'raw',
		                'filter' => Html::activeDropDownList(
		                $searchModel,
		                'api_access',
		                User::apiStatus(),
		                ['class' => 'form-control', 'prompt' => Yii::t('app','Select')]
		                ),
					    'value'=>function($model){
					        if ($model->api_access == 1) {
					           $icon = '<span class="badge  badge-success"><i class="fa fa-check"></span>';
					        }else{
					            $icon = '<span class="badge  badge-danger"><i class="fa fa-times"></span>';
					        }
					        return Html::a($icon, ['toggle-attribute','attribute'=>'api_access','id'=>$model->id]);
					    }
					],

					[
						'headerOptions'=>['style'=>'width:5%;text-align:center;'],
						'contentOptions'=>['style'=>'width:5%;text-align:center;'],
						'class'=>'webvimark\components\StatusColumn',
						'attribute'=>'status',
						'optionsArray'=>[
							[User::STATUS_ACTIVE, Yii::t('app', 'Active'), 'success'],
							[User::STATUS_INACTIVE, Yii::t('app', 'Inactive'), 'primary'],
							[User::STATUS_BANNED, Yii::t('app', 'Banned'), 'danger'],
						],
					],

					[
						'label'=>Yii::t('app', 'Assign a role'),
	                    'headerOptions'=>['style'=>'width:3%;text-align:center;'],
	                    'contentOptions'=>['style'=>'width:3%;text-align:center;'],
						'value'=>function(User $model){
								$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
								$url = $langUrl.'/user-management/user-permission/role-set?id='.$model->id;
						        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M4 3h16a2 2 0 0 1 2 2v6a10 10 0 0 1-10 10A10 10 0 0 1 2 11V5a2 2 0 0 1 2-2z"></path><polyline points="8 10 12 14 16 10"></polyline></svg> ',$url,[
						            'data'=>['pjax'=>0],
						            'style'=>'text-align:center;font-size: 14px;',
						            'class'=>'modal-d',
						            'title'=>Yii::t('app', 'Assign a role for {user}',['user'=>$model->username])
						        ]); 
						},
						'format'=>'raw',
						'visible'=>User::canRoute('/user-management/user-permission/set'),
						'options'=>[
							'width'=>'10px',
						],
					],

					// [
					// 	'value'=>function(User $model){


					// 		return GhostHtml::a(
					// 				UserManagementModule::t('back', 'Roles'),
					// 				['/user-management/user-permission/set', 'id'=>$model->id],
					// 				['class'=>'btn btn-sm btn-primary', 'data-pjax'=>0,'style'=>'background-color:#191e3a!important']);
					// 		},
	      			//     'headerOptions'=>['style'=>'width:200px;text-align:center;color:#337ab7;'],
	        		// 	'contentOptions'=>['style'=>'width:200px;text-align:center;line-height: 20px'],

					// 	'format'=>'raw',
					// 	'visible'=>User::canRoute('/user-management/user-permission/set'),
					// 	'options'=>[
					// 		'width'=>'10px',
					// 	],
					// ],

					[
						'label'=>Yii::t('app', 'Change password'),
	                    'headerOptions'=>['style'=>'width:3%;text-align:center;'],
	                    'contentOptions'=>['style'=>'width:3%;text-align:center;'],
						'value'=>function(User $model){
								$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
								$url = $langUrl.'/user-management/user/change-password?id='.$model->id;
						        return Html::a('<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>',$url,[
						            'data'=>['pjax'=>0],
						            'style'=>'text-align:center;font-size:14px',
						            'class'=>'modal-d',
						            'title'=>Yii::t('app', 'Change password for {username}',['username'=>$model->username])
						        ]); 
						},
						'format'=>'raw',
						'visible'=>User::canRoute('/user-management/user/change-password'),
						'options'=>['width'=>'10px'],
					],


		            [
					  'header'=>Yii::t('app', 'Update'),
                      'class' => 'yii\grid\ActionColumn',
                      'options'=>['style'=>'text-align:center;'],
					  'visible'=>User::canRoute('/user-management/user/change-password'),
                      'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                      'contentOptions'=>['style'=>'width:3%;text-align:center;'],
                      'template' => '{update}',
                       'buttons' => [
                            'update' => function ($url, $model) {
                            	if (isset($model->location->city_id)) {
                            		$new_url = $url."&?city_id=".$model->location->city_id;
                            		if (isset($model->location->district_id)) {
                            			$new_url = $url."&city_id=".$model->location->city_id."&district_id=".$model->location->district_id;
                            			if (isset($model->location->location_id)) {
                            				$new_url = $url."&city_id=".$model->location->city_id."&district_id=".$model->location->district_id."&location_id=".$model->location->location_id;
                            			}
                            		}
                            	}else{
                            		$new_url = $url;
                            	}

                                return '<a data-pjax="0" href="'.$new_url.'" ><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg></a>';
                            },
                          ],
		            ],

					[
					  'header'=>Yii::t('app', 'Delete'),
					  'class' => 'yii\grid\ActionColumn',
					  'options'=>['style'=>'width:50px;text-align:center;'],
					  'visible'=>User::canRoute('/user-management/user/update'),
                      'headerOptions'=>['style'=>'width:3%;text-align:center;'],
                      'contentOptions'=>['style'=>'width:3%;text-align:center;'],
					  'template' => '{delete}',
					    'buttons' => [
					        'delete' => function ($url, $model) {
					            return '<a href="'.$url.'" title="'.Yii::t('app','Sil').'" aria-label="'.Yii::t('app','Sil').'" data-pjax="0" data-confirm="Bu elementi silmək istədiyinizə əminsinizmi?" data-method="post"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>';
					        }
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
    'title' => Yii::t("app","Members"),
    'id' => 'modal',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],
    'size' => 'modal-lg',
]);
echo "<div id='modalContent'></div>";
Modal::end();
?>
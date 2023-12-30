<?php
/**
 * @var $this yii\web\View
 * @var yii\widgets\ActiveForm $form
 * @var array $routes
 * @var array $childRoutes
 * @var array $permissionsByGroup
 * @var array $childPermissions
 * @var yii\rbac\Permission $item
 */

use webvimark\modules\UserManagement\components\GhostHtml;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Setting for "{permission}" permission',['permission'=>$item->description]);

?>

<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="/">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 511 511.999" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M498.7 222.695c-.016-.011-.028-.027-.04-.039L289.805 13.81C280.902 4.902 269.066 0 256.477 0c-12.59 0-24.426 4.902-33.332 13.809L14.398 222.55c-.07.07-.144.144-.21.215-18.282 18.386-18.25 48.218.09 66.558 8.378 8.383 19.44 13.235 31.273 13.746.484.047.969.07 1.457.07h8.32v153.696c0 30.418 24.75 55.164 55.168 55.164h81.711c8.285 0 15-6.719 15-15V376.5c0-13.879 11.293-25.168 25.172-25.168h48.195c13.88 0 25.168 11.29 25.168 25.168V497c0 8.281 6.715 15 15 15h81.711c30.422 0 55.168-24.746 55.168-55.164V303.14h7.719c12.586 0 24.422-4.903 33.332-13.813 18.36-18.367 18.367-48.254.027-66.633zm-21.243 45.422a17.03 17.03 0 0 1-12.117 5.024H442.62c-8.285 0-15 6.714-15 15v168.695c0 13.875-11.289 25.164-25.168 25.164h-66.71V376.5c0-30.418-24.747-55.168-55.169-55.168H232.38c-30.422 0-55.172 24.75-55.172 55.168V482h-66.71c-13.876 0-25.169-11.29-25.169-25.164V288.14c0-8.286-6.715-15-15-15H48a13.9 13.9 0 0 0-.703-.032c-4.469-.078-8.66-1.851-11.8-4.996-6.68-6.68-6.68-17.55 0-24.234.003 0 .003-.004.007-.008l.012-.012L244.363 35.02A17.003 17.003 0 0 1 256.477 30c4.574 0 8.875 1.781 12.113 5.02l208.8 208.796.098.094c6.645 6.692 6.633 17.54-.031 24.207zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
            </a>
        </li>

        <li class="breadcrumb-item">
            <a href="javascript:void(0);">
				<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" fill-rule="evenodd" class=""><g><path d="M10.5 20.263H2.95a.2.2 0 0 1-.2-.2v-1.45c0-.831.593-1.563 1.507-2.185 1.632-1.114 4.273-1.816 7.243-1.816.49 0 .971.02 1.441.057a.75.75 0 1 0 .118-1.495 19.38 19.38 0 0 0-1.559-.062c-3.322 0-6.263.831-8.089 2.076-1.393.95-2.161 2.157-2.161 3.424v1.451a1.7 1.7 0 0 0 1.7 1.699l7.55.001a.75.75 0 0 0 0-1.5zM11.5 1.25C8.464 1.25 6 3.714 6 6.75s2.464 5.5 5.5 5.5S17 9.786 17 6.75s-2.464-5.5-5.5-5.5zm0 1.5c2.208 0 4 1.792 4 4s-1.792 4-4 4-4-1.792-4-4 1.792-4 4-4zM18.152 20.208a4.003 4.003 0 1 0-2.233-6.786 3.997 3.997 0 0 0-1.127 3.427L12.47 19.17a.75.75 0 0 0-.22.531V22c0 .414.336.75.75.75h2.299a.75.75 0 0 0 .531-.22zm-.052-1.54a.75.75 0 0 0-.723.194l-2.388 2.388H13.75v-1.239l2.388-2.388a.75.75 0 0 0 .194-.723 2.504 2.504 0 0 1 4.186-2.418 2.504 2.504 0 0 1-2.418 4.186z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M17.982 17.018a1.085 1.085 0 1 1 1.535-1.533 1.085 1.085 0 0 1-1.535 1.533z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
            </a>
        </li>

        
        <li class="breadcrumb-item" aria-current="page"><a href="/user-management/permission/index"><?=Yii::t("app","Permissions") ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><span><?=$this->title ?></span></li>
    </ol>
</nav>


<?php if ( Yii::$app->session->hasFlash('success') ): ?>
	<div class="alert alert-success text-center">
		<?= Yii::$app->session->getFlash('success') ?>
	</div>
<?php endif; ?>


<div class="card custom-card rbac" style="width: 100%">
	<div class="row">

	<div class="col-sm-12">
		<div class="custom-card">
			<div class="card-head">
					 <div class="head-text" style="margin-left:10px">
					 	<strong style="font-weight: 500; font-size: 16px;"><?=Yii::t("app","Routes") ?></strong>
					 </div>

					 <div class="head-search" style="margin-top:15px">
					 						<?= Html::a(
						Yii::t('app', 'Refresh routes (and delete unused)'),
						['refresh-routes', 'id'=>$item->name, 'deleteUnused'=>1],
						[
							'class' => 'btn btn-primary btn-md pull-right',
							'style'=>'margin-top: -5px; text-transform: none; margin-left: 20px;',
							'data-confirm'=>Yii::t('app', 'Routes that are not exists in this application will be deleted'),
						]
					) ?>

					<?= Html::a(
						Yii::t('app', 'Refresh routes'),
						['refresh-routes', 'id'=>$item->name],
						[
							'class' => 'btn btn-primary btn-md pull-right',
							'style'=>'margin-top:-5px; text-transform:none;',
						]
					) ?>
					 </div>
			</div>

			<div class="card-body">
				<?= Html::beginForm(['set-child-routes', 'id'=>$item->name]) ?>
				<div class="row">
					<div class="col-sm-3">
						<?= Html::submitButton(Yii::t('app', 'Save'),['class'=>'btn btn-primary btn-md']) ?>
					</div>
					<div class="col-sm-6">
						<input id="search-in-routes" autofocus="on" type="text" class="form-control input-sm" placeholder="<?= Yii::t('app', 'Search'); ?>">
					</div>
					<div class="col-sm-3 text-right">
						<span id="show-only-selected-routes" class="btn btn-primary btn-md">
							<i class="fa fa-minus"></i> <?= Yii::t('app', 'Show all selected'); ?>
						</span>
						<span id="show-all-routes" class="btn btn-primary btn-md hide">
							<i class="fa fa-plus"></i> <?= Yii::t('app', 'Show all'); ?>
						</span>
					</div>
				</div>
				<hr/>
				<?= Html::checkboxList(
					'child_routes',
					ArrayHelper::map($childRoutes, 'name', 'name'),
					ArrayHelper::map($routes, 'name', 'name'),
					[
						'id'=>'routes-list',
						'separator'=>'<div class="separator"></div>',
						'item'=>function($index, $label, $name, $checked, $value) {
								return Html::checkbox($name, $checked, [
									'value' => $value,
									'label' => '<span class="route-text">' . $label . '</span>',
									'labelOptions'=>['class'=>'route-label'],
									'class'=>'route-checkbox',
								]);
						},
					]
				) ?>
				<hr/>
				<?= Html::submitButton(Yii::t('app', 'Save'),['class'=>'btn btn-primary btn-md']) ?>
				<?= Html::endForm() ?>

			</div>
		</div>
	</div>
</div>	
</div>


<?php
$js = <<<JS

var routeCheckboxes = $('.route-checkbox');
var routeText = $('.route-text');

// For checked routes
var backgroundColor = '#2196f3';

function showAllRoutesBack() {
	$('#routes-list').find('.hide').each(function(){
		$(this).removeClass('hide');
	});
}

//Make tree-like structure by padding controllers and actions
routeText.each(function(){
	var _t = $(this);

	var chunks = _t.html().split('/').reverse();
	var margin = chunks.length * 40 - 40;

	if ( chunks[0] == '*' )
	{
		margin -= 40;
	}

	_t.closest('label').css('margin-left', margin);

});

// Highlight selected checkboxes
routeCheckboxes.each(function(){
	var _t = $(this);

	if ( _t.is(':checked') )
	{
		_t.closest('label').css('background', backgroundColor);
	}
});

// Change background on check/uncheck
routeCheckboxes.on('change', function(){
	var _t = $(this);

	if ( _t.is(':checked') )
	{
		_t.closest('label').css('background', backgroundColor);
	}
	else
	{
		_t.closest('label').css('background', 'none');
	}
});


// Hide on not selected routes
$('#show-only-selected-routes').on('click', function(){
	$(this).addClass('hide');
	$('#show-all-routes').removeClass('hide');

	routeCheckboxes.each(function(){
		var _t = $(this);

		if ( ! _t.is(':checked') )
		{
			_t.closest('label').addClass('hide');
			_t.closest('div.separator').addClass('hide');
		}
	});
});

// Show all routes back
$('#show-all-routes').on('click', function(){
	$(this).addClass('hide');
	$('#show-only-selected-routes').removeClass('hide');

	showAllRoutesBack();
});

// Search in routes and hide not matched
$('#search-in-routes').on('change keyup', function(){
	var input = $(this);

	if ( input.val() == '' )
	{
		showAllRoutesBack();
		return;
	}

	routeText.each(function(){
		var _t = $(this);

		if ( _t.html().indexOf(input.val()) > -1 )
		{
			_t.closest('label').removeClass('hide');
			_t.closest('div.separator').removeClass('hide');
		}
		else
		{
			_t.closest('label').addClass('hide');
			_t.closest('div.separator').addClass('hide');
		}
	});
});

JS;

$this->registerJs($js);
?>

<style type="text/css">
	.hide{display: none;}


	.card-head{
		display: flex;
		justify-content: space-between;
	}

	.card-body{padding: 10px 10px}


</style>
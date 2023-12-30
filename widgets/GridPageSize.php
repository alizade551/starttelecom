<?php
namespace app\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use Yii;

class GridPageSize extends Widget
{
	/**
	 * Event listeners will be delegated via 'body', so this plugin will
	 * work even after grid separately loaded via AJAX.
	 *
	 * You can specify some closer container to improve performance
	 *
	 * @var string
	 */
	public $domContainer = 'body';

	/**
	 * You can render different views for different places
	 *
	 * @var string
	 */

	/**
	 * @var string
	 */
	public $pjaxId;


	/**
	 * @var string
	 */
	public $pageName;


	/**
	 * Default - Url::to(['grid-page-size'])
	 *
	 * @var string
	 */
	public $url;

	/**
	 * @var array
	 */
	public $dropDownOptions;

	/**
	 * Text "Records per page"
	 *
	 * @var string
	 */
	public $text;

	/**
	 * Show or not "Clear filters" button when grid filters are changed
	 *
	 * @var bool
	 */
	public $enableClearFilters = true;

	/**
	 * Optional. Used only for "Clear filters" button.
	 * If not set, then it will be guessed via $pjaxId
	 *
	 * @var string
	 */
	public $gridId;

	/**
	 * @var array additional options to be passed to the pjax JS plugin. Please refer to the
	 * [pjax project page](https://github.com/yiisoft/jquery-pjax) for available options.
	 */
	public $clientOptions;

	/**
	 * Multilingual support
	 */
	public function init()
	{
		parent::init();

		$this->text = $this->text ? $this->text : Yii::t('app', 'Records per page');
	}


	/**
	 * @throws \yii\base\InvalidConfigException
	 * @return string
	 */
	public function run()
	{
		if ( ! $this->pjaxId )
		{
			throw new InvalidConfigException('Missing pjaxId param');
		}

		$this->setDefaultOptions();

		$this->view->registerJs($this->js());

		?>
		<div class="form-inline pull-right">
			<?php if ( $this->enableClearFilters ): ?>

				<span style="display: none" id="<?= ltrim($this->gridId, '#') ?>-clear-filters-btn" class="btn btn-sm btn-default">
					<?= Yii::t('app', 'Clear filters') ?>
				</span>
			<?php endif; ?>


			<span style="margin-right:5px"><?= $this->text ?></span>

			<?= Html::dropDownList(
				$this->pageName, \Yii::$app->request->cookies->getValue($this->pageName, 20),
				$this->dropDownOptions,
				['class'=>'form-control input-sm','style'=>'width:80px']
			) ?>
		</div>
		<?php
	}

	/**
	 * Set default options
	 */
	protected function setDefaultOptions()
	{
		$this->pjaxId = '#' . ltrim($this->pjaxId, '#');

		if ( !$this->gridId )
		{
			// Remove "-pjax" from the end
			$this->gridId = substr($this->pjaxId, 0, -5);
		}

		$this->gridId = '#' . ltrim($this->gridId, '#');

		if ( ! $this->dropDownOptions )
		{
			$this->dropDownOptions = [5=>5, 10=>10, 20=>20, 50=>50, 100=>100, 200=>200, 500=>500];
		}

		if ( ! $this->url )
		{
			$this->url = Url::to(['grid-page-size']);
		}
	}

	protected function guessGridId()
	{
		$this->gridId = '';
	}

	/**
	 * @return string
	 */
	protected function js()
	{
		$options = ['container' => $this->pjaxId];
		if( $this->clientOptions ){
			$options = ArrayHelper::merge($options, $this->clientOptions);
		}
		$options = json_encode($options);
		$js = <<<JS
			$('$this->domContainer').off('change', '[name="$this->pageName"]').on('change', '[name="$this->pageName"]', function () {
				var _t = $(this);
				$.post('$this->url', { 'grid-page-size': _t.val(),'name':'$this->pageName' })
					.done(function(){
						$.pjax.reload($options);
					});
			});
JS;

		return $this->enableClearFilters ? $this->jsWithClearFilters() . $js : $js;

	}
	/**
	 * @return string
	 */
	protected function jsWithClearFilters()
	{
		$filterSelectors = $this->gridId . ' .filters input[type="text"], ' . $this->gridId . ' .filters select';
		$clearBtnId = $this->gridId . '-clear-filters-btn';

		$js = <<<JS
			var clearFiltersBtn = $('$clearBtnId');
			var domContainer = $('$this->domContainer');

			function showOrHideClearFiltersBtn() {
				var showClearFiltersButton = false;

				$('$filterSelectors').each(function(){
					var _t = $(this);

					if ( _t.val() )
					{
						showClearFiltersButton = true;
					}
				});

				if ( showClearFiltersButton )
				{
					clearFiltersBtn.show();
				}
				else
				{
					clearFiltersBtn.hide();
				}
			}

			showOrHideClearFiltersBtn();

			// Show button if filters not empty and hide it if they are empty
			domContainer.off('change', '$filterSelectors').on('change', '$filterSelectors', function () {
				showOrHideClearFiltersBtn();
			});

			// Clear filters on button click
			domContainer.off('click', '$clearBtnId').on('click', '$clearBtnId', function () {
				var filter;

				$('$filterSelectors').each(function(){
					filter = $(this);
					filter.val('');
				});

				filter.trigger('change');
			});

JS;

		return $js;

	}
} 

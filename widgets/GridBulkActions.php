<?php
namespace app\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Url;
use yii\helpers\Html;
use Yii;

class GridBulkActions extends Widget
{
	/**
	 * @var array
	 */
	public $actions;

	/**
	 * @var string
	 */
	public $gridId;

	/**
	 * Default - $this->gridId . '-pjax'
	 *
	 * @var string
	 */
	public $pjaxId;

	/**
	 * @var string
	 */
	public $okButtonClass = 'btn btn-sm btn-default';

	/**
	 * @var string
	 */
	public $dropDownClass = 'form-control input-md';

	/**
	 * @var string
	 */
	public $wrapperClass = 'actions';

	/**
	 * @var string
	 */
	public $promptText;

	/**
	 * @var string
	 */
	public $confirmationText;

	/**
	 * Multilingual support
	 */
	public function init()
	{
		parent::init();

		$this->promptText = $this->promptText ? $this->promptText : Yii::t('app', 'Select');
		$this->confirmationText = $this->confirmationText ? $this->confirmationText : Yii::t('app', 'Delete elements?');
	}

	/**
	 * @throws \yii\base\InvalidConfigException
	 * @return string
	 */
	public function run()
	{
		if ( ! $this->gridId )
		{
			throw new InvalidConfigException('Missing gridId param');
		}

		$this->setDefaultOptions();

		$this->view->registerJs($this->js());

$gridId = uniqid($this->gridId.'-');
?>
<div class="<?= $this->wrapperClass ?>">

	<?= Html::dropDownList(
		'grid-bulk-actions',
		null,
		$this->actions,
		[
			'class'=>$this->dropDownClass,
			'id'=>"{$gridId}-bulk-actions",
			'data-ok-button'=>"#{$gridId}-ok-button",
			'prompt'=>$this->promptText,
		]
	) ?>

	<?= Html::tag('button', 'OK', [
		'class'     => "grid-bulk-ok-button btn btn-primary {$this->okButtonClass} disabled",
		'id'        => "{$gridId}-ok-button",
		'data-list' => "#{$gridId}-bulk-actions",
		'data-pjax' => "#{$this->pjaxId}",
		'data-grid' => "#{$this->gridId}",
	]);
	}

	/**
	 * Set default options
	 */
	protected function setDefaultOptions()
	{
		if ( ! $this->actions )
		{
			$this->actions = [
				Url::to(['bulk-activate'])=>Yii::t('app', 'Activate'),
				Url::to(['bulk-deactivate'])=>Yii::t('app', 'Deactivate'),
				'----'=>[
					Url::to(['bulk-delete'])=>Yii::t('app', 'Delete'),
				],
			];
		}

		if ( ! $this->pjaxId )
		{
			$this->pjaxId = $this->gridId . '-pjax';
		}
		$this->gridId = ltrim($this->gridId, '#');
		$this->pjaxId = ltrim($this->pjaxId, '#');
	}

	/**
	 * @return string
	 */
	protected function js()
	{
		$js = <<<JS

		// Select values in bulk actions list
		$(document).off('change', '[name="grid-bulk-actions"]').on('change', '[name="grid-bulk-actions"]', function () {
			var _t = $(this);
			var okButton = $(_t.data('ok-button'));

			if (_t.val()) {
				okButton.removeClass('disabled');
			}
			else {
				okButton.addClass('disabled');
			}
		});

		// Clicking OK button
		$(document).off('click', '.grid-bulk-ok-button').on('click', '.grid-bulk-ok-button', function () {
			var _t = $(this);
			var list = $(_t.data('list'));

			if (list.val().indexOf('bulk-delete') >= 0) {
				if ( ! confirm('$this->confirmationText') )
					return false;
			}

			$.post(list.val(), $(_t.data('grid') + ' [name="selection[]"]').serialize() )
				.done(function(){
					_t.addClass('disabled');
					list.val('');

					$.pjax.reload({container: _t.data('pjax')});
				});
		});
JS;

		return $js;

	}
} 

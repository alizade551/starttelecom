<?php 
use yii\helpers\Html;
use yii\bootstrap\Alert;
$this->title = Yii::t('app','Settings');

$this->params['breadcrumbs'][] = $this->title;
 ?>
<?php if (file_exists(Yii::getAlias("@app").'/runtime/setting/params.php')):
$file = include_once(Yii::getAlias("@app").'/runtime/setting/params.php');

 ?>

<div class="col-md-6">
	<form method="post" action="/site/config">
	<?php if ($file): ?>
		<?php foreach ($file as $key => $value): ?>
			<div class="form-group">
				<label><?=$key?></label>
				<input type="text"  class="form-control"  name="data[<?=$key?>]" value="<?=$value?>" />
			</div>

		<?php endforeach ?>
	<?php endif ?>

	<div class="form-group">
         <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-primary']) ?>
    </div>
	
  </form>


	</div>
<?php endif ?>


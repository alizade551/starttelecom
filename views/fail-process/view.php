<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\FailProcess */

$this->title = $model['id'];
\yii\web\YiiAsset::register($this);
?>
<div class="fail-process-view">
<?php 
  if( $model['status'] == 1 ){
    $span = '<span class="badge badge-success" >'.\app\models\FailProcess::getStatus()[$model['status']].'</span>';
  }else {
    $span = '<span class="badge badge-warning" >'.\app\models\FailProcess::getStatus()[$model['status']].'</span>';
  }

 ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'action',
            [
            'label'=>Yii::t('app','Member fullname'),
            'format'=>'raw',
            'value'=>$model['member_fullaname'],
            ],
            [
            'label'=>Yii::t('app','Parametrs'),
            'format'=>'raw',
            'value'=>\app\components\Utils::failProcessText(unserialize($model['params'])),
            ],
            [
            'label'=>Yii::t('app','Status'),
            'format'=>'raw',
            'value'=>$span,
            ],
            [
            'label'=>Yii::t('app','Created at'),
            'format'=>'raw',
            'value'=>date('d-m-Y H:i',$model['created_at']),
            ],
        ],
    ]) ?>

</div>

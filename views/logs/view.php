<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Logs */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logs-view">

   


    <?= DetailView::widget([
        'model' => $model,
            'attributes' => [
                'member',
            [
                'label' => Yii::t('app','Customer'),
                'value' =>isset( $model->user->fullname ) ? $model->user->fullname : "-",
            ],
            
            [
                'label' => Yii::t('app','Text'),
                'format'=>'raw',
                'value' =>$model->text,
            ],
             
            [
                'label' =>  Yii::t('app','Time'),
                'format'=>'raw',
                'value' =>date('d/m/Y H:i:s',$model->time),
            ], 
        ],
    ]) ?>

</div>

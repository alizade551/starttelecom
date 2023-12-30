<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\RequestOrder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Request Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$all_packets = '';
foreach ($model->usersServicesPackets as $key_s => $value_s) {
   $all_packets .=$value_s->service->service_name."/".$value_s->packet->packet_name.", ";
}

$status = '';
if ($model->second_status == '1') {
    $status =  "<span class='badge badge-warning'>".Yii::t('app','Damage')."</span>";
}elseif ($model->second_status == '4') {
   $status = '<span class="badge badge-info">'.Yii::t('app','Re-connecting').'</span>';
}elseif ($model->second_status == '5') {
   $status = '<span class="badge badge-primary">'.Yii::t('app','New service').'</span>';
}
if ($model->status == 1) {
    $status.= ' <span class="badge badge-success">'.Yii::t('app','Active').'</span>';
}elseif ($model->status == 3){
    $status.= ' <span style="background-color: #795548;color:#fff" class="badge ">'.Yii::t('app','Archive').'</span>';
}elseif ($model->status == 0){
    $status.= ' <span  class="badge badge-warning">'.Yii::t('app','Pending').'</span>';
}

if ( isset( $model->city->city_name ) &&  isset( $model->district->district_name ) && isset( $model->locations->name ) ) {
   $address =  $model->city->city_name .", ".$model->district->district_name.", ".$model->locations->name.", ".$model->room;
}else{
    $address = "";
}


?>
<div class="request-order-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
        [
            'label' => Yii::t('app','ID'),
            'value' =>$model->id,
        ], 

        [
            'label' => Yii::t('app','Customer'),
            'value' =>$model->fullname,
        ], 
        [
            'label' => Yii::t('app','Company name'),
            'value' =>$model->company,
        ], 
        [
            'label' => Yii::t('app','Phone'),
            'value' =>$model->phone,
        ],

        [
            'label' => Yii::t('app','E-mail '),
            'value' =>$model->email,
        ],


        [
            'label' => Yii::t('app','Services / Packets'),
            'value' =>substr($all_packets, 0,-2),
        ],      
        [
            'label' =>  Yii::t('app','Address'),
            'value' =>$address
        ],  
  
  
        [
            'label' => Yii::t('app','Description'),
            'format'=>'raw',
            'value' =>$model->description,
        ], 

        [
            'label' => Yii::t('app','Status'),
            'format'=>'raw',
            'value' =>$status,
        ], 

        ],
    ]) ?>


</div>


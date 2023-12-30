<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use webvimark\modules\UserManagement\models\User;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $model app\models\RequestOrder */

$this->title = Yii::t("app","Information page for {customer}",['customer'=>$model->fullname]);
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$all_packets = '';
foreach ($model->usersServicesPackets as $key_s => $value_s) {
    if(isset($value_s->packet->packet_name)){
       $all_packets .=$value_s->service->service_name."/".$value_s->packet->packet_name.", ";
    }
}
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
?>

<?php $this->registerCssFile("/css/icons.css"); ?>
<?php $this->registerCssFile("/css/bootstrap-editable.css"); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap-editable.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/js/moment.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$siteConfig['google_map_js_token'].' ', ['depends' => [yii\web\JqueryAsset::className()]]); ?>

<?php  $isChecked = ($model->status == '7') ? "checked" : ""; ?>
<?php  $isCheckedBank = ($model->bank_status == '1') ? "checked" : ""; ?>

<?php 
if ($model->status == 0) {
    $status =  " <span class='badge badge-warning'>".Yii::t("app","Pending")."</span>";
}elseif ($model->status == 1) {
 $status =  " <span class='badge badge-success'>".Yii::t("app","Active")."</span>";
}elseif($model->status == 2){
   $status =  " <span class='badge badge-danger'>".Yii::t("app","Deactive")."</span>";  
}elseif($model->status == 3){
   $status =  " <span style='background-color: #795548;color:#fff' class='badge '>".Yii::t("app","Archive")."</span>";  
}elseif ($model->status == 6) {
   $status = ' <span class="badge badge-danger">'.Yii::t("app","Canalled").'</span>';
}elseif ($model->status == 7) {
  $status =  " <span class='badge badge-primary'>".Yii::t("app","VIP")."</span>";
}



if($model->second_status == '4'){
   $status .=  " <span class='badge badge-primary'>".Yii::t("app","Re-coonecting")."</span>";  
}

if($model->second_status == '5'){
   $status .= ' <span class="badge badge-info">'.Yii::t("app","New service").'</span>';
}

if($model->damage_status == '1'){
   $status .= ' <span class="badge badge-warning">'.Yii::t("app","Damaged").'</span>';
}

$service_credit_status = '';
if($model->credit_status == '0'){
   $service_credit_status = ' <span class="badge badge-danger">'.Yii::t("app","Deactive").'</span>';
}

if($model->credit_status == '1'){
   $service_credit_status = ' <span class="badge badge-success">'.Yii::t("app","Active").'</span>';
}


if($model->last_payment == ""){$last_payment = Yii::t("app","Dont found last payment");}else{$last_payment =  date('d/m/Y H:i:s',$model->last_payment);}
$service_group = \app\models\UsersServicesPackets::find()->where(['user_id'=>$model->id])->groupBy(['service_id'])->all();
?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <div>
                <a href="<?=Yii::$app->request->referrer ?>" style="margin-left: 10px;" class="btn btn-primary"><?=Yii::t("app","Back to All customers") ?></a>
            </div>
        </div>
    </div>
</div>


<div class="card user-view">
    <div class="card-body">
        <ul class="nav user-view-nav" style="margin-bottom: 10px;">
        <?php if ( User::canRoute(['/users/add-balance']) &&  $model->status != 7 && $model->status !=3 && $model->status != 6  ): ?>
        <li class="nav-item" id="add-balance">
            <a title="<?=Yii::t('app','Add a balance for {customer}',['customer'=>$model->fullname]) ?>" data-pjax="0" class="nav-link modal-d" href="<?=$langUrl.Url::to("/users/add-balance").'?id='.$model->id ?>"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg> <?=Yii::t('app','Add a balance') ?></a>
        </li>   
        <?php endif ?>


        <?php if ( User::canRoute(['/users/add-debit']) &&  $model->status != 7 && $model->status !=3 && $model->status != 6  ): ?>
        <li class="nav-item" id="add-balance">
            <a title="<?=Yii::t('app','Add a debit for {customer}',['customer'=>$model->fullname]) ?>" data-pjax="0" class="nav-link modal-d" href="<?=$langUrl.Url::to("/users/add-debit").'?id='.$model->id ?>"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg> <?=Yii::t('app','Add a debit') ?></a>
        </li>   
        <?php endif ?>

        <?php if (User::canRoute(['/users/add-new-service']) && $model->status == 1  && $model->second_status != 3): ?>
        <li class="nav-item" id="add-new-service">
            <a title="<?=Yii::t('app','Add a new service for {customer}',['customer'=>$model->fullname]) ?>" data-pjax="0"  class="nav-link modal-d" href="<?=$langUrl.Url::to("/users/add-new-service").'?id='.$model->id ?>">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> 
            <?=Yii::t('app','Add a new service') ?>
            </a>
        </li>
        <?php endif ?>

        <?php if (User::canRoute(['/users/add-item-to-user']) && $model->status != 6): ?>
        <li  class="nav-item">   
          <a title="<?=Yii::t('app','Add an item for  {customer}',['customer'=>$model->fullname]) ?>" data-pjax="0"  class="modal-d nav-link" href="<?=$langUrl.Url::to('/users/add-item-to-user').'?id='.$model->id ?>">
           <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
             <?=Yii::t('app','Add an item') ?></a>
        </li>
        <?php endif ?>

        <?php if ( User::canRoute(['/users/add-note']) ): ?>
        <li class="nav-item">
          <a  title="<?=Yii::t('app','Add a note to {customer}',['customer'=>$model->fullname]) ?>"  data-pjax="0"   class="modal-d nav-link" href="<?=$langUrl.Url::to('/users/add-note').'?id='.$model->id ?>">
             <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
             <?=Yii::t('app','Add a note') ?>
        </a>
        </li>
        <?php endif ?>

        <?php if (User::canRoute(['/users/add-report']) &&  $model->status != 3 && $model->status != 6 ): ?>
        <li id="add-a-report" class="nav-item">
            <a title="<?=Yii::t('app','Add a report to {customer}',['customer'=>$model->fullname]) ?>" data-pjax="0"   class="nav-link modal-d" href="<?=$langUrl.Url::to("/users/add-report").'?id='.$model->id ?>">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg> <?=Yii::t("app","Add a report") ?>
            </a>
        </li>
        <?php endif ?>

        <?php if (User::canRoute(['/users/send-to-archive']) && $model->second_status !=7 && $model->second_status !=5 ): ?>
        <?php if ($model->status == 2 || $model->status == 1 ): ?>
             <li id="send-to-archive" class="nav-item">
                <a title="<?=Yii::t('app','Send to archive  {customer}',['customer'=>$model->fullname]) ?>"  data-pjax="0"   class="nav-link modal-d"  href="<?=$langUrl.Url::to("/users/send-to-archive").'?id='.$model->id ?>"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> <?=Yii::t("app","Send to archive") ?></a>
            </li> 
        <?php endif ?>
        <?php endif ?>

        <?php if (User::canRoute(['/users/re-connect']) && $model->status == 3  || $model->status == 6): ?>
        <?php if ( $model->second_status != "4" ): ?>
            <li id="user-reconnect" class="nav-item"> 
                <a title="<?=Yii::t('app','Re-connecting for {customer}',['customer'=>$model->fullname]) ?>" data-pjax="0"   class="nav-link modal-d" href="<?=$langUrl.Url::to("/users/re-connect").'?id='.$model->id ?>" title="<?=Yii::t("app","Reconnecting") ?>" > 
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>  
                    <?=Yii::t("app","Reconnecting") ?>
                </a>
            </li> 
        <?php endif ?>
        <?php endif ?>


        <?php if (User::canRoute(['/users/give-credit']) &&  $model->status != 7 && $model->status !=3  && $model->status != 6 && $model->status != 1 ): ?>
        <li id="give-a-credit" class="nav-item">
            <a title="<?=Yii::t('app','Give a credit for {customer}',['customer'=>$model->fullname]) ?>" data-pjax="0"  class="nav-link modal-d" href="<?=$langUrl.Url::to("/users/give-credit").'?id='.$model->id ?>"> <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M4 3h16a2 2 0 0 1 2 2v6a10 10 0 0 1-10 10A10 10 0 0 1 2 11V5a2 2 0 0 1 2-2z"></path><polyline points="8 10 12 14 16 10"></polyline></svg> <?=Yii::t('app','Give a credit') ?></a>
        </li>   
        <?php endif ?>

        <?php if (User::canRoute(['/users/refund-balance']) &&  $model->second_status != 7 &&  $model->status !=1 ): ?>
        <?php if ($model->balance > 0): ?>
            <li id="refund-user-balance" da class="nav-item">
                 <a title="<?=Yii::t('app','Refund balance for {customer}',['customer'=>$model->fullname]) ?>" data-pjax="0"  class="nav-link modal-d" href="<?=$langUrl.Url::to("/users/refund-balance").'?id='.$model->id ?>"><i class="fa fa-handshake-o" aria-hidden="true"></i> <?=Yii::t('app','Refund') ?></a>
            </li> 
        <?php endif ?>  
        <?php endif ?>
        <?php 
          $is_credit = false;
          foreach ( $itemUsage as $key => $item ) {
            if ( ($item['status'] == 6 && $item['credit'] == "1") || ($item['status'] == 4 && $item['credit'] == "2")  ) {
              $is_credit = true;
            }
          }
        ?>


        <?php if (User::canRoute(['/users/contract']) && $model->status != 6): ?>
        <li class="nav-item">
        <a title="<?=Yii::t('app','Update contract number for {customer}',['customer'=>$model->fullname]) ?>" data-pjax="0"  class="nav-link modal-d " href="<?=$langUrl.Url::to("/users/contract").'?id='.$model->id ?>">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg> <?=Yii::t('app','Update contract number') ?></a>
        </li>
        <?php endif ?>


        <?php if (User::canRoute(['/users/add-black-list']) && $model->status != 6): ?>
        <li class="nav-item">
          <a title="<?=Yii::t('app','Add {customer} to black list',['customer'=>$model->fullname]) ?>"  data-pjax="0"   class="modal-d nav-link" href="<?=$langUrl.Url::to('/users/add-black-list').'?user_id='.$model->id ?>"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>  <?=Yii::t('app','Add black list') ?></a>
        </li>
        <?php endif ?>


        <?php if (User::canRoute(['/users/delete-user']) && $model->status == 6 && $model->second_status != 4): ?>
        <li class="nav-item">
          <a title="<?=Yii::t('app','Delete {customer} from the user list',['customer'=>$model->fullname]) ?>"  data-pjax="0"   class="modal-d nav-link" href="<?=$langUrl.Url::to('/users/delete-user').'?user_id='.$model->id ?>"><svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>  <?=Yii::t('app','Delete user') ?></a>
        </li>
        <?php endif ?>

        <?php if ( User::canRoute(['/users/add-cordinate']) ): ?>
        <li class="nav-item">
            <?php if ( $model['cordinate'] == "" ): ?>
                 <?php $cordinateTitle = Yii::t('app','Add a cordinate for {customer}',['customer'=>$model->fullname]) ?> 
             <?php else: ?>
                 <?php $cordinateTitle = Yii::t('app','Update a cordinate for {customer}',['customer'=>$model->fullname]) ?> 
             <?php endif ?>
          <a title="<?=$cordinateTitle ?>"  data-pjax="0" id="t-map"   class="modal-d  nav-link" href="<?=$langUrl.Url::to('/users/add-cordinate').'?id='.$model->id ?>">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
             <?php if ( $model['cordinate'] == "" ): ?>
                 <?=Yii::t('app','Add a cordinate') ?>
             <?php else: ?>
                 <?=Yii::t('app','Update a cordinate') ?>
             <?php endif ?>
          </a>
        </li>
        <?php endif ?>
        </ul>
        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
        <?php if (User::hasPermission('customer-information')): ?>
        <li class="nav-item">
            <a data-pjax="0"  class="nav-link active" data-toggle="tab" href="#user-info" role="tab">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg> <?=Yii::t('app','Information') ?>
            </a>
        </li>
        <?php endif ?>
        <?php foreach ($service_group as $ser_key => $service_one): ?>
         <?php if ($service_one->service->service_alias == "internet"): ?>
            <li class="nav-item">
                <a  class="nav-link" data-toggle="tab" href="#<?=$service_one->service->service_alias ?>" role="tab">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>  <?=Yii::t('app','Internet') ?>
                </a>
            </li>
         <?php endif ?>
         <?php if ($service_one->service->service_alias == "tv"): ?>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#<?=$service_one->service->service_alias ?>" role="tab">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg><span> <?=Yii::t('app','TV') ?></span>
              </a>
            </li>
         <?php endif ?>
         <?php if ($service_one->service->service_alias == "wifi"): ?>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#<?=$service_one->service->service_alias ?>" role="tab">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg> <?=Yii::t('app','Wifi') ?>
                </a>
            </li>
         <?php endif ?>

         <?php if ($service_one->service->service_alias == "voip"): ?>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#<?=$service_one->service->service_alias ?>" role="tab">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg> <?=Yii::t('app','VoIP') ?>
                </a>
            </li>
         <?php endif ?>

        <?php endforeach ?>
        <?php if (User::hasPermission('customer-payment-history')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#payment_history" role="tab">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <?=Yii::t('app','Payment history') ?>
            </a>
        </li>
        <?php endif ?>
        <?php if (User::hasPermission('customer-damage-history')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#settings1" role="tab">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg> 
                <?=Yii::t('app','Report history') ?>
            </a>
        </li>
        <?php endif ?>
        <?php if (User::hasPermission('customer-history')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#user_history_tab" role="tab">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                <?=Yii::t('app','History') ?>
            </a>
        </li>
        <?php endif ?>
        <?php if (User::hasPermission('customer-note-history')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#user_notes" role="tab">
               <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                  <?=Yii::t('app','Notes') ?>
            </a>
        </li>
        <?php endif ?>
        <?php if (User::hasPermission('customer-sms-history')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#user_sms" role="tab">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
               <?=Yii::t('app','Messages') ?>
            </a>
        </li>
        <?php endif ?>
        <?php if (User::hasPermission('customer-item-history')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#user_item" role="tab">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                <?=Yii::t('app','Items') ?>
            </a>
        </li>
        <?php endif ?>
        <?php if (User::hasPermission('customer-log-history')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#user_logs" role="tab">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                <?=Yii::t('app','Logs') ?>
            </a>
        </li>
        <?php endif ?>
        <?php if (User::hasPermission('customer-cordinate')): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#user_cordinate" role="tab">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>                    
                <?=Yii::t("app","Cordinate on map") ?>
            </a>
        </li>
        <?php endif ?>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
        <?php if (User::hasPermission('customer-information')): ?>
        <div class="tab-pane active p-3 scrollbar-custom" id="user-info" role="tabpanel" style="height: 100%;overflow-y: scroll;">
          <?php  Pjax::begin(['id'=>'pjax-user-info','enablePushState'=>true]);  ?>
          <?php 
          $damage_reason = '';
          foreach (\app\models\UserDamages::getDamageReason() as $dkey => $dm_reason) {
              $damage_reason.= ' <option value="'.$dkey.'">'.$dm_reason.'</option>';
          }
           ?>
            <div class="card">
                <div class="card-body" style="padding: 0">
                    <table class="table table-striped mb-0">
                        <tbody>
                            <tr>
                                <td style="width: 50%;"><?=Yii::t('app','ID') ?></td>
                                <td><?=$model->id ?></td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app','Customer') ?></td>
                                <td><a href="#" id="inline-fullname" data-type="text" data-pk="1" data-title="Enter username"><?=$model->fullname ?></a></td>
                            </tr>
                            <?php if ( $model->status == 3 ): ?>
                            <tr>
                                <td><?=Yii::t('app','Note') ?></td>
                                <td><a href="#" id="inline-fullname" data-type="text" data-pk="1" data-title="Enter username"><?=\app\models\Users::getArchiveReason()[$model->note] ?></a></td>
                            </tr>
                            <?php endif ?>
                            <tr>
                                <td><?=Yii::t('app','Contract number') ?></td>
                                <td>
                                    <?=$model->contract_number; ?> 
                                    <?php if ( User::canRoute('/users/send-contract-number') ): ?>
                                        <a style="margin-left: 5px" data-fancybox="" data-type="ajax"  data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/users/send-contract-number?id=<?=$model->id?>" href="javascript:;" >
                                          <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        </a>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app','Company name') ?></td>
                                <td>
                                    <a href="#" id="inline-company" data-type="text"   data-pk="1" data-placement="right" data-placeholder="Required" data-title="Enter your firstname"><?=$model->company ?></a>
                                </td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app','Phones') ?></td>
                                <td>
                                    <?=$model->phone ?> 
                                    <?php if ($model->extra_phone != ""): ?>
                                        , <?=$model->extra_phone ?>
                                    <?php endif ?>
                                    <?php if ( User::canRoute('/users/update-phones') ): ?>
                                        <a style="margin-left: 5px;vertical-align: top;" data-fancybox="" data-type="ajax"  data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/users/update-phones?id=<?=$model->id?>" href="javascript:;" >
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                        </a>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app','E-mail') ?></td>
                                <td><a href="#" id="inline-email" data-type="text" data-pk="1" data-title="Enter username"><?=$model->email ?></a></td>
                            </tr>
                           <tr>
                                <td><?=Yii::t('app','Photos') ?></td>
                                <td>
                                    <?php $photos = \app\models\UserPhotos::find()->select('photo_url')->where(['user_id'=>$model->id])->asArray()->all();
                         
                                     ?>
                                     <?php if (count($photos) > 0): ?>
                                     <div class="imglist">
                                     <?php foreach ($photos as $key => $photo): ?>
                                          <a href="/uploads/user_photos/<?=$photo['photo_url'] ?>" data-fancybox data-caption="<?=$model->fullname ?>">
                                            <?php 
                                            if ($photo['photo_url'] != "") {
                                                // code...
                                                $photo_url = "/uploads/user_photos/".$photo['photo_url']." ";
                                            }else{
                                               $photo_url = "/img/nopic.jpg ";
                                            }
                                            ?>
                                            <img height="50px" src="<?=$photo_url ?>" />
                                          </a>
                                     <?php endforeach ?>
                                     <?php if ( User::canRoute('/users/upload-form') ): ?>
                                        <a  data-pjax="0"  href="/users/upload-form?id=<?=$model->id ?>"><?=Yii::t('app','Upload photo') ?></a>
                                     <?php endif ?>
                                    </div>
                                     <?php else: ?>
                                        <img height="50px" src="/img/nopic.jpg" />
                                        <?php if ( User::canRoute('/users/upload-form') ): ?>
                                            <a  data-pjax="0"  href="/users/upload-form?id=<?=$model->id ?>"><?=Yii::t('app','Upload photo') ?></a>
                                        <?php endif ?>
                                     <?php endif ?>
                                </td>
                            </tr>

                            <tr>
                                <td><?=Yii::t('app','Renewal date') ?></td>
                                <td>
                                    <?php  ($model->updated_at != 0 ) ? $cron_will_updated_at = date('d/m/Y H:i:s',$model->updated_at) : $cron_will_updated_at = "-"; ?>
                                    <?php  echo $cron_will_updated_at; ?>
                                </td>
                            </tr> 

                            <tr>
                                <td><?=Yii::t('app','Service / Packets') ?></td>
                                <td><?=substr($all_packets, 0,-2) ?></td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app','Tariff') ?></td>
                                <td><?=$model->tariff ?>  <?=$siteConfig['currency'] ?></td>
                            </tr>
                             <tr>
                                <td><?=Yii::t('app','Balance') ?></td>
                                <td>
                                     <?=$model->balance ?>  <?=$siteConfig['currency'] ?>
                                </td>
                            </tr>
                             <tr>
                                <td><?=Yii::t('app','Bonus') ?></td>
                                <td>
                                     <?=$model->bonus ?>  <?=$siteConfig['currency'] ?>
                                </td>
                            </tr>
                             <tr>
                                <td><?=Yii::t('app','Payment time period') ?></td>
                                <td>
                                     <?=($model->paid_time_type == "1") ? $model->paid_day : "01" ?> 
                                     <a title="<?=Yii::t('app','{user_fullname} adlı abunəçinin qoşulma günün dəyişilmə formu',['user_fullname'=>$model['fullname']]) ?>"   href="<?=$langUrl ?>/users/change-paid-day?id=<?=$model['id'] ?>" class="modal-d" >
                                          <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                        </a>
                                </td>
                            </tr>
                             <tr>
                                <td><?=Yii::t('app','Paid type') ?></td>
                                <td>
                                     <?=\app\models\RequestOrder::getPaidDayType()[$model->paid_time_type ] ?> 
                                </td>
                            </tr>

                           <tr>
                                <td><?=Yii::t('app','Address') ?></td>
                                <td>
                                    <a href="#" id="inline-address-city" data-type="select" data-pk="1" data-value="" data-title="Select city"><?=$model->city->city_name ?></a>,
                                    <a href="#" id="inline-address-district" data-type="select" data-pk="<?=$model->city_id ?>" data-value="" data-title="Select District"><?=$model->district->district_name ?></a>,
                                    <a href="#" id="inline-address-location" data-type="select" data-pk="1" data-value="" data-title="Select Location"><?=$model->locations->name ?></a>,
                                    <a href="#" id="inline-room" data-type="text" data-pk="1" data-title="Enter username"><?=$model->room ?></a>
                                </td>
                            </tr>




                            <?php if ( User::canRoute('/users/check-free-status')   && ($model->status == 1 || $model->status == 7)): ?>
                                <tr>
                                    <td><?=Yii::t('app','VIP') ?></td>
                                    <td>
                                        <?php echo '<div style=" display: inline-block;">                                  
                                        <input name="input_cj" class="stat_us_free" data-user_id="'.$model->id.'" 

                                        type="checkbox" '.$isChecked.'  hidden="hidden"   id="packets_check_free'.$model->id.'">
                                        <label class="c-switch" for="packets_check_free'.$model->id.'"></label>
                                        </div>   '; ?>   
                                    </td>    
                                </tr>
                            <?php endif ?>

                       
                            <?php if ( User::canRoute('/users/check-bank-status')   && ($model->bank_status == 0 || $model->bank_status == 1)): ?>
                                <tr>
                                    <td><?=Yii::t('app','Bank') ?></td>
                                    <td>
                                        <?php echo '<div style=" display: inline-block;">                                  
                                        <input name="input_cj_bank_status" class="stat_us_bank_status" data-user_id="'.$model->id.'" 

                                        type="checkbox" '.$isCheckedBank.'  hidden="hidden"   id="packets_check_bank_status'.$model->id.'">
                                        <label class="c-switch" for="packets_check_bank_status'.$model->id.'"></label>
                                        </div>   '; ?>   
                                    </td>    
                                </tr>
                            <?php endif ?>
                        
                            <tr>
                                <td><?=Yii::t('app','At connection') ?></td>
                                <td><?=app\models\Users::getAtConnections($model->id) ?></td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app','Status') ?></td>
                                <td><?=$status ?></td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app','Credit status') ?></td>
                                <td><?=$service_credit_status ?></td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app', 'Last credit time')?></td>
                                <td>
                                   <?php ($model->credit_time != "") ? $credit_time = date('d/m/Y H:i:s', $model->credit_time) : $credit_time = "-";?>
                                   <?php echo $credit_time; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app','Last payment') ?></td>
                                <td>
                                   <?php echo $last_payment; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?=Yii::t('app','Created at') ?></td>
                                <td>
                                  <?php  echo date('d/m/Y H:i:s',$model->created_at) ?>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
           <?php if (User::canRoute(['/users/editable'])): ?>
        <?php 
        $this->registerJs('
            $(function() {
                $.fn.editableform.buttons = \'\<button type="submit" class="btn btn-success editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button><button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect waves-light"><i class="mdi mdi-close"></i></button>\', 


                $("#inline-fullname").editable({

                    validate: function(e) {
                        if ("" == $.trim(e)) return "'.Yii::t('app','This field is required').'"
                    },
                    type: "text",
                    name: "fullname",
                    title: "Enter fullname",
                    mode: "inline",
                    inputclass: "form-control-sm",
                       url: "'.Url::to("editable").'?id='.$model->id.'",
                       type: "text",   
                       title: "Edit fullname",
                       ajaxOptions:{
                        type:"post"
                       } ,
                }),

                $("#inline-company").editable({
                    type: "text",
                    name: "company",
                    title: "Enter company",
                    mode: "inline",
                    inputclass: "form-control-sm",
                       url: "'.Url::to("editable").'?id='.$model->id.'",
                       type: "text",
                       title: "Edit company",
                       ajaxOptions:{
                        type:"post"
                       } ,
                }),
                $("#inline-room").editable({
                    type: "text",
                    name: "room",
                    title: "Enter room",
                    mode: "inline",
                    inputclass: "form-control-sm",
                       url: "'.Url::to("editable").'?id='.$model->id.'",
                       type: "text",
                       title: "Edit room",
                       ajaxOptions:{
                        type:"post"
                       } ,
                }),


                $("#inline-phone").editable({
                    type: "text",
                    name: "phone",
                    title: "Enter phone",
                    mode: "inline",
                    inputclass: "form-control-sm",
                       url: "'.Url::to("editable").'?id='.$model->id.'",
                       type: "text",
                       title: "Edit phone",
                       ajaxOptions:{
                        type:"post"
                       } ,
                }),

                $("#inline-email").editable({
                    type: "text",
                    name: "email",
                    title: "Enter email",
                    mode: "inline",
                    inputclass: "form-control-sm",
                       url: "'.Url::to("editable").'?id='.$model->id.'",
                       type: "text",
                       title: "Edit email",
                       ajaxOptions:{
                        type:"post"
                       } 
                });

                $("#inline-address-district").editable({
                    validate: function(e) {
                        if ("" == $.trim(e)) return "'.Yii::t('app','This field is required').'"
                    },
                    mode: "inline",
                     name: "district",
                    inputclass: "form-control-sm",
                    source: '.\app\models\District::getDistrictEditableValue($model->city_id).',
                       url: "'.Url::to("editable").'?id='.$model->id.'",
                       title: "Edit email",
                       ajaxOptions:{
                        type:"post"
                       },
                                success: function(response, newValue) {

                                    $(this).attr("data-pk",newValue);
                                    $("#inline-address-location").attr("data-pk",newValue);
                                    $("#inline-address-location").html("'.Yii::t('app','Please select location').'");
                                    $("#inline-address-location").editable("destroy");

                                    $("#inline-address-location").editable({
                                        validate: function(e) {
                                            if ("" == $.trim(e)) return "'.Yii::t('app','This field is required').'"
                                        },
                                        mode: "inline",
                                         name: "location",
                                        inputclass: "form-control-sm",
                                        source: response,
                                           url: "'.Url::to("editable").'?id='.$model->id.'",
                                           title: "Edit email",
                                           ajaxOptions:{
                                            type:"post"
                                           } 

                                    });
                                }

                }),

                $("#inline-address-location").editable({
                    validate: function(e) {
                        if ("" == $.trim(e)) return "'.Yii::t('app','This field is required').'"
                    },
                    mode: "inline",
                     name: "location",
                    inputclass: "form-control-sm",
                    source: '.\app\models\Locations::getLocationEditableValue($model->district_id).',
                       url: "'.Url::to("editable").'?id='.$model->id.'",
                       title: "Edit email",
                       ajaxOptions:{
                        type:"post"
                       } 

                }),

                 $("#inline-address-city").editable({
                    validate: function(e) {
                        if ("" == $.trim(e)) return "'.Yii::t('app','This field is required').'"
                    },
                    mode: "inline",
                     name: "city",
                    inputclass: "form-control-sm",
                    source: '.\app\models\Cities::getCityEditableValue().',
                       url: "'.Url::to("editable").'?id='.$model->id.'",
                       title: "Edit email",
                       ajaxOptions:{
                        type:"post"
                       },
                        success: function(response, newValue) {
                            $(this).attr("data-pk",newValue);
                            $("#inline-address-district").attr("data-pk",newValue);
                            $("#inline-address-district").html("'.Yii::t('app','Please select district').'");
                            $("#inline-address-district").editable("destroy");

                            $("#inline-address-district").editable({
                                validate: function(e) {
                                    if ("" == $.trim(e)) return "'.Yii::t('app','This field is required').'"
                                },      
                                mode: "inline",
                                name: "district",
                                inputclass: "form-control-sm",
                                source: response,
                                url: "'.Url::to("editable").'?id='.$model->id.'",
                                title: "Edit email",
                                ajaxOptions:{
                                type:"post"
                                },
                                success: function(response, newValue) {

                                    $(this).attr("data-pk",newValue);
                                    $("#inline-address-location").attr("data-pk",newValue);
                                    $("#inline-address-location").html("'.Yii::t('app','Please select location').'");
                                    $("#inline-address-location").editable("destroy");

                                    $("#inline-address-location").editable({
                                        validate: function(e) {
                                            if ("" == $.trim(e)) return "'.Yii::t('app','This field is required').'"
                                        },
                                        mode: "inline",
                                         name: "location",
                                        inputclass: "form-control-sm",
                                        source: response,
                                           url: "'.Url::to("editable").'?id='.$model->id.'",
                                           title: "Edit email",
                                           ajaxOptions:{
                                            type:"post"
                                           } 

                                    });
                                }

                             });

                        }

                });
            });
        ');?>
           <?php endif ?>
          <?php  Pjax::end();  ?>
        </div>                                   
        <?php endif ?>
        <?php if (User::hasPermission('customer-payment-history')): ?>
        <div class="tab-pane p-3" id="payment_history" role="tabpanel">
  
          <?php  Pjax::begin(['id'=>'pjax-user-payment-history','enablePushState'=>true]);  ?>
         <?php if (count($transactions) > 0): ?>
            <div class="balance_list_container">
                <ul class="balance_list scrollbar-custom"> 
                    <?php foreach ($transactions as $key_balance => $balance_one): ?>
                      <?php if ($balance_one['balance_in'] > 0 || $balance_one['bonus_in'] > 0 ): ?>
                       <li style="float: none;display: block;"> 
                            <div class="payment_his"> 
                                <label style="color: #28d828; margin: 0; text-decoration: underline;" >
                                 <?php 
                                    if ($balance_one['payment_method'] == 0) {
                                      $payment_method = Yii::t("app","Internal");
                                    } elseif($balance_one['payment_method'] == 1){
                                      $payment_method = Yii::t("app","External");
                                    }else{
                                      $payment_method = Yii::t("app","Unkown payment method");
                                    }

                                    if ($balance_one['receipt_name'] != "") {
                                        $receipt_name = $balance_one['receipt_name'];
                                    }else{
                                       $receipt_name = Yii::t("app","(Receipt is not defined)");
                                    }
                                 ?>  
                                <?=Yii::t('app', '{balance_in} {currency} and {bonus_in} {currency} bonus added from {payment_method} at {created_at} ( Check number : {receipt_name} )', [
                                 'payment_method' => $payment_method, 
                                 'currency' => $siteConfig['currency'], 
                                 'bonus_in' => $balance_one['bonus_in'], 
                                 'balance_in' => $balance_one['balance_in'], 
                                 'receipt_name' => $receipt_name, 
                                 'created_at' => date('d/m/Y H:i',$balance_one['created_at']), 
                                ]); ?>
                             </label> 
                            </div> 
                        </li> 
                      <?php endif ?>
                      <?php if ( $balance_one['balance_out'] > 0 || $balance_one['bonus_out'] > 0 ): ?>
                        <li style="float: none;display: block;"> 
                        <div class="payment_his"> 
                            <?php
                                $bpf = '';
                                 if ( $balance_one['pay_for'] == 3 ) {
                                    $bpf = strtoupper(Yii::t("app","item"));
                                }elseif( $balance_one['pay_for'] == 0 ){
                                    $bpf = strtoupper(Yii::t("app","Internet"));
                                }elseif( $balance_one['pay_for'] == 1 ){
                                    $bpf = strtoupper(Yii::t("app","tv"));
                                 }elseif ($balance_one['pay_for'] == 2 ){
                                    $bpf = strtoupper(Yii::t("app","wifi"));
                                }elseif( $balance_one['pay_for'] == 7 ){
                                    $bpf = strtoupper(Yii::t("app","Refunded"));
                                }elseif( $balance_one['pay_for'] == 4 ){
                                    $bpf = strtoupper(Yii::t("app","voip"));
                                }
                            ?>
                            <?php if ( ( $balance_one['balance_out'] > 0 || $balance_one['bonus_out'] > 0 ) && $balance_one['status'] == 0 ): ?>
                                <label style="color: #dc3545; margin: 0; text-decoration: underline;" >
                                <?=Yii::t('app', '{balance_out} {currency} main and {bonus_out} {currency} bonus from balance by system for {pay_for} at {created_at}', [
                                 'pay_for' => $bpf, 
                                 'currency' => $siteConfig['currency'], 
                                 'bonus_out' => $balance_one['bonus_out'], 
                                 'balance_out' => $balance_one['balance_out'], 
                                 'created_at' => date('d/m/Y H:i',$balance_one['created_at']), 
                                ]); ?>

                                </label> 
                                
                            <?php else: ?>
                                <label style="color: #dc3545; margin: 0; text-decoration: underline;" >
                                <?=Yii::t('app', '{balance_out} {currency} deducted for {pay_for} at {created_at}  ( payment status: free )', [
                                 'currency' => $siteConfig['currency'], 
                                 'pay_for' => $bpf, 
                                 'balance_out' => $balance_one['balance_out'], 
                                 'created_at' => date('d/m/Y H:i',$balance_one['created_at']), 
                                ]); ?>

                                </label> 
                            <?php endif ?>
                        </div> 
                        </li> 
                      <?php endif ?>
                    <?php endforeach ?>
                </ul>
            </div>
         <?php else: ?>
            <h2 > <?=Yii::t("app","There is no any transaction for customer") ?> </h2> 
         <?php endif ?>
           <?php  Pjax::end();  ?>            
       
        </div>
        <?php endif ?>

        <?php foreach ($service_group as $ser_key => $service_one): ?>
            
            <?php if ($service_one->service->service_alias == "internet"): ?>   
                <div class="tab-pane p-3" id="<?=$service_one->service->service_alias ?>" role="tabpanel">
                 <?php  Pjax::begin(['id'=>'pjax-inet-table','enablePushState'=>true]);  ?>
                        <?php $model_user_inet = \app\models\UsersInet::find()
                        ->select('users_inet.*,address_district.router_id as router_id,service_packets.packet_price as packet_price,users_services_packets.price as custom_price,users.fullname as user_fullname,service_packets.packet_name as packet_name,routers.nas as nas,routers.name as router_name')
                        ->leftJoin('users','users.id=users_inet.user_id')
                        ->leftJoin('users_services_packets','users_services_packets.id=users_inet.u_s_p_i')
                        ->leftJoin('service_packets','service_packets.id=users_inet.packet_id')
                        ->leftJoin('address_district','users.district_id=address_district.id')
                        ->leftJoin('routers','address_district.router_id=routers.id')
                        ->where(['users_inet.user_id'=>$model->id])
                        ->asArray()
                        ->all();
                        $c=0;
                        ?>
                        <table class="table table-striped">
                            <thead> 
                                <tr> 
                                    <th>#</th> 
                                    <th><?=Yii::t('app','Price') ?></th> 
                                    <th><?=Yii::t('app','Packet') ?></th> 
                                    
                                     <?php if (User::canRoute('/users/packet-ajax-status')): ?>
                                    <th><?=Yii::t('app','Enable/Disable') ?></th> 
                                     <?php endif ?>  

                                    <?php if (User::canRoute('/users/rx-tx')): ?>
                                    <th><?=Yii::t('app','Real time chart') ?></th> 
                                    <?php endif ?>  

                                    <?php if (User::canRoute('/users/check-user-internet')): ?>
                                    <th><?=Yii::t('app','Info') ?></th> 
                                    <?php endif ?>  

                                    <?php if ( User::canRoute('/users/tag-port-user-packet') && $model->district->device_registration == "1" ): ?>
                                    <th><?=Yii::t('app','Port') ?></th> 
                                    <?php endif ?>  
 
                                    <?php if (User::canRoute('/users/send-packet-detail')): ?>
                                    <th><?=Yii::t('app','Send packet detail') ?></th> 
                                    <?php endif ?>  

                                    <?php if (User::canRoute('/users/change-packet')): ?>
                                    <th><?=Yii::t('app','Change') ?></th> 
                                    <?php endif ?>  

                                    <?php if (User::canRoute('/users/service-delete')): ?>
                                    <th><?=Yii::t('app','Delete') ?> </th> 
                                    <?php endif ?>  
                                </tr> 
                            </thead> 
                            <tbody>
                                <?php $c=0; ?>
                                <?php foreach ($model_user_inet as $key => $value_inet): ?>
                                <?php $c++; ?>
                                <tr>  
                                    <td><?=$c; ?></td> 
                                    <td>
                                        <?php 
                                            if( $value_inet['custom_price'] != 0 || $value_inet['custom_price'] != null ){
                                                echo $value_inet['custom_price'];
                                            }else{
                                                echo $value_inet['packet_price'];
                                            }
                                        ?>
                                        <?=$siteConfig['currency'] ?>
                                    </td> 
                                    <td><?php if(isset( $value_inet['login']  ) && $value_inet['status'] == "1" ){echo $value_inet['packet_name'];}else{echo "BLOCKED";} ?></td>
                                

                                    <?php if (User::canRoute('/users/packet-ajax-status')): ?>
                                        <td>
                                            <?php  $isChecked = ($value_inet['status'] == '1') ? "checked" : ""; ?>
                                            <input name="input_cj" class="stat_us" data-user_id="<?=$value_inet['user_id'] ?>" data-packet_id="<?=$value_inet['packet_id'] ?>"
                                            data-service_id="<?=$service_one->service->id ?>"  data-usp-id= "<?=$value_inet['u_s_p_i'] ?>"
                                              type="checkbox" <?=$isChecked ?> hidden="hidden"   id="packets_check<?=$value_inet['id'] ?>">
                                            <label class="c-switch" for="packets_check<?=$value_inet['id'] ?>"></label>      
                                        </td>
                                    <?php endif ?> 

                                    <?php if (User::canRoute('/users/rx-tx')): ?>
                                        <td class="check-user-internet">
                                            <a data-pjax="0" style="margin-left: -30px; display: block; text-align: center;"   href="<?=$langUrl ?>/users/rx-tx?login=<?=$value_inet['login'] ?>"  >
                                              <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                            </a>
                                        </td>                                              
                                    <?php endif ?>  


                                    <?php if (User::canRoute('/users/check-user-internet')): ?>
                                        <td class="check-user-internet">
                                            <a  style="margin-left: -30px; display: block; text-align: center;" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/users/check-user-internet?login=<?=$value_inet['login'] ?>" href="javascript:;" >
                                              <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="22" y1="12" x2="2" y2="12"></line><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path><line x1="6" y1="16" x2="6.01" y2="16"></line><line x1="10" y1="16" x2="10.01" y2="16"></line></svg>
                                            </a>
                                        </td>                                              
                                    <?php endif ?>  

                                    <?php if ( User::canRoute('/users/tag-port-user-packet') && $model->district->device_registration == "1" ): ?>
                                        <td class="tag-port-user-packet">
                                            <a  style="margin-left: -30px; display: block; text-align: center;" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/users/tag-port-user-packet?id=<?=$value_inet['u_s_p_i'] ?>" href="javascript:;" >
                                              <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                                            </a>
                                        </td>                                              
                                    <?php endif ?>  

                                     <?php if (User::canRoute('/users/send-packet-detail')): ?>
                                        <td class="change-packet" >
                                            <a title="<?=Yii::t("app","Sending {login} login detail to {customer}",['login'=>$value_inet['login'],'customer'=>$model['fullname']]) ?>" class="modal-d"  href="<?=$langUrl ?>/users/send-packet-detail?id=<?=$value_inet['u_s_p_i'] ?>">
                                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                            </a>
                                        </td>                                              
                                    <?php endif ?> 
                                    
                                     <?php if (User::canRoute('/users/change-packet')): ?>
                                        <td class="change-packet">
                                            <a title="<?=Yii::t( "app", "Change {customer} {packet} packet !", [ 'customer'=>$model['fullname'], 'packet'=>$value_inet['packet_name'] ] ) ?>" class="modal-d" href="<?=$langUrl ?>/users/change-packet?id=<?=$value_inet['u_s_p_i'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                            </a>
                                        </td>                                              
                                    <?php endif ?>  

                                    <?php if (User::canRoute('/users/service-delete')): ?>
                                        <td>
                                            <a style="margin-left: -30px; display: block; text-align: center;" data-fancybox data-src="#hidden-content-<?=$value_inet['id'] ?>" href="javascript:void(0)">
                                               <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </a>
                                        </td>                 
                                    <?php endif ?>  
                                </tr> 
                                <?php if (User::canRoute('/users/service-delete')): ?>
                                    <div style="display: none;max-width: 400px;" id="hidden-content-<?=$value_inet['id'] ?>">
                                            <div class="fcc">
                                              <h4 ><b><?=Yii::t("app","Packet deleting - {user_fullname}",['user_fullname'=>$value_inet['user_fullname']]) ?> </b></h4>
                                              <p>
                                                <?=Yii::t("app","Are you sure want delete {packet_name} packet ?",['packet_name'=> (isset($value_inet['packet_name'])) ? $value_inet['packet_name'] : '' ]) ?> 
                                              </p>
                                              <button class="btn btn-primary delete-packet" data-user_id="<?=$value_inet['user_id'] ?>"  data-service="<?=$service_one->service->id ?>"  data-packet="<?=$value_inet['packet_id']  ?>" data_packet_id_ser="<?=$value_inet['u_s_p_i'] ?>"   title="<?=Yii::t('app','Delete') ?>" ><?=Yii::t('app','Delete') ?></button>
                                              <button data-fancybox-close="" class="btn btn-secondary"  title="<?=Yii::t('app','Close') ?>" ><?=Yii::t('app','Close') ?></button>           
                                            </div>
                                    </div> 
                                <?php endif ?> 
                              <?php endforeach ?>
                            </tbody> 
                        </table>
                </div>              
              <?php  Pjax::end();  ?>
            <?php endif ?>

            <?php if ($service_one->service->service_alias == "tv"): ?>   
                <div class="tab-pane p-3" id="<?=$service_one->service->service_alias ?>" role="tabpanel">
                        <?php  Pjax::begin(['id'=>'pjax-tv-table','enablePushState'=>true]);  ?>
                                <?php 
                                $model_user_tv = \app\models\UsersTv::find()
                                ->select('users_tv.*,users_services_packets.price as custom_price,users.fullname as user_fullname,service_packets.packet_price as packet_price,service_packets.packet_name as packet_name')
                                ->leftJoin('users','users.id=users_tv.user_id')
                                ->leftJoin('service_packets','service_packets.id=users_tv.packet_id')
                                ->leftJoin('users_services_packets','users_services_packets.id=users_tv.u_s_p_i')
                                ->where(['users_tv.user_id'=>$model->id])
                                ->asArray()
                                ->all();
                                ?>
                                    <table class="table table-striped">
                                        <thead> 
                                            <tr> 
                                                <th>#</th> 
                                                <th><?=Yii::t('app','Price') ?></th> 
                                                <th><?=Yii::t('app','Packet') ?></th> 
                                                <th><?=Yii::t('app','Status') ?></th> 
                                                 <?php if (User::canRoute('/users/packet-ajax-status')): ?>
                                                <th><?=Yii::t('app','Active') ?>  / <?=Yii::t('app','Deactive') ?></th> 
                                                 <?php endif ?>  
                                                <?php if (User::canRoute('/users/change-packet')): ?>
                                                <th><?=Yii::t('app','Change') ?></th> 
                                                <?php endif ?>  
                                                <th><?=Yii::t('app','Created at') ?></th> 
                                                <?php if (User::canRoute('/users/service-delete')): ?>
                                                <th><?=Yii::t('app','Delete') ?> </th> 
                                                <?php endif ?>  
                                            </tr> 
                                        </thead> 
                                        <tbody>
                                            <?php $c=0; ?>
                                            <?php foreach ($model_user_tv as $key => $tv_value): ?>
                                            <?php $c++; ?>
                                            <tr>  
                                                <td><?=$c++; ?></td> 
                                                <td>
                                                    <?php 
                                                        if( $tv_value['custom_price'] != 0 || $tv_value['custom_price'] != null ){
                                                            echo $tv_value['custom_price'];
                                                        }else{
                                                            echo $tv_value['packet_price'];
                                                        }
                                                    ?>
                                                     <?=$siteConfig['currency'] ?>
                                                </td> 
                                                <td><?=$tv_value['packet_name'] ?></td> 
                                                <td>
                                                    <?php if ($tv_value['status'] == 1): ?>
                                                       <span class="badge badge-success"><?=Yii::t('app','Active') ?></span>
                                                    <?php elseif($tv_value['status'] == 2): ?>
                                                     <span class="badge badge-danger"><?=Yii::t('app','Deactive') ?></span>
                                                    <?php elseif($tv_value['status'] == 3): ?>
                                                     <span style="background-color: #795548;color: #fff;padding: 0 5px" class="badge badge-default"><?=Yii::t('app','Archive') ?></span>
                                                    <?php endif ?>

                                                 </td> 
                                              
                                                <?php if (User::canRoute('/users/packet-ajax-status')): ?>
                                                <td>
                                                    <?php  $isChecked = ($tv_value['status'] == '1') ? "checked" : ""; ?>
                                                    <input name="input_cj" class="stat_us" data-user_id="<?=$tv_value['user_id'] ?>" data-packet_id="<?=$tv_value['packet_id'] ?>"
                                                    data-service_id="<?=$service_one->service->id ?>"  data-usp-id= "<?=$tv_value['u_s_p_i'] ?>"
                                                      type="checkbox" <?=$isChecked ?> hidden="hidden"   id="packets_check<?=$tv_value['id'] ?>">
                                                    <label class="c-switch" for="packets_check<?=$tv_value['id'] ?>"></label>      
                                                </td>
                                                <?php endif ?> 

                                                 <?php if (User::canRoute('/users/change-packet')): ?>
                                                    <td class="change-packet">
                                                        <a title="<?=Yii::t( "app", "Change {customer} {packet} packet !", [ 'customer'=>$model['fullname'], 'packet'=>$tv_value['packet_name'] ] ) ?>" class="modal-d" href="<?=$langUrl ?>/users/change-packet?id=<?=$tv_value['u_s_p_i'] ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                                        </a>
                                                    </td>                                              
                                                <?php endif ?>                                             
                                            
                                                <td><?=date('m/d/Y H:i:s', $tv_value['created_at']) ?></td>     
                                                <?php if (User::canRoute('/users/service-delete')): ?>
                                                <td>
                                                <a data-fancybox data-src="#hidden-content-<?=$service_one->service->id ?>-<?=$tv_value['id'] ?>" href="javascript:void(0)">
                                                     <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </a>
                                                </td>                 
                                                <?php endif ?>  
                                            </tr> 

                                            <?php if (User::canRoute('/users/service-delete')): ?>
                                                <div style="display: none;max-width: 400px;" id="hidden-content-<?=$service_one->service->id ?>-<?=$tv_value['id'] ?>">
                                                    <div class="fcc">
                                                      <h4>
                                                        <b>
                                                            <?=Yii::t("app","Packet deleting - {user_fullname}",['user_fullname'=>$tv_value['user_fullname']])  ?>
                                                        </b>
                                                      </h4>
                                                      <p ><?=Yii::t("app","Are you sure want delete {packet_name} packet ?",['packet_name'=>$tv_value['packet_name']]) ?> </p>
                                                      <button class="btn btn-primary delete-packet" data-user_id="<?=$tv_value['user_id'] ?>"  data-service="<?=$service_one->service->id ?>"  data-packet="<?=$tv_value['packet_id']  ?>" data_packet_id_ser="<?=$tv_value['u_s_p_i'] ?>"   title="<?=Yii::t('app','Delete') ?>" ><?=Yii::t('app','Delete') ?></button>
                                                      <button data-fancybox-close="" class="btn btn-secondary"  title="<?=Yii::t('app','Close') ?>" ><?=Yii::t('app','Close') ?></button>           
                                                    </div>
                                                </div> 
                                            <?php endif ?>
                                          <?php endforeach ?>
                                        </tbody> 
                                    </table>
                                 </div>     
                        <?php  Pjax::end();  ?>         
            <?php endif ?>

            <?php if ($service_one->service->service_alias == "wifi"): ?>
                     <div class="tab-pane p-3" id="<?=$service_one->service->service_alias ?>" role="tabpanel">
                        <?php  Pjax::begin(['id'=>'pjax-wifi-table','enablePushState'=>true]);  ?>   
                                <?php 
                                    $model_user_wifi = \app\models\UsersWifi::find()
                                    ->select('users_wifi.*,users_services_packets.price as custom_price,users.fullname as user_fullname,service_packets.packet_price as packet_price,service_packets.packet_name as packet_name')
                                    ->leftJoin('users','users.id=users_wifi.user_id')
                                    ->leftJoin('service_packets','service_packets.id=users_wifi.packet_id')
                                    ->leftJoin('users_services_packets','users_services_packets.id=users_wifi.u_s_p_i')
                                    ->where(['users_wifi.user_id'=>$model->id])
                                    ->asArray()
                                    ->all();
                                ?>
                                    <table class="table table-striped">
                                        <thead> 
                                            <tr> 
                                                <th>#</th> 
                                                <th><?=Yii::t('app','Price') ?></th> 
                                                <th><?=Yii::t('app','Packet') ?></th> 
                                                <th><?=Yii::t('app','Status') ?></th> 
                                                 <?php if (User::canRoute('/users/packet-ajax-status')): ?>
                                                <th><?=Yii::t('app','Active') ?>  / <?=Yii::t('app','Deactive') ?></th> 
                                                 <?php endif ?>  
                                                <?php if (User::canRoute('/users/change-packet')): ?>
                                                <th><?=Yii::t('app','Change') ?></th> 
                                                <?php endif ?>  
                                                <th><?=Yii::t('app','Time') ?></th> 
                                                <?php if (User::canRoute('/users/service-delete')): ?>
                                                <th><?=Yii::t('app','Delete') ?> </th> 
                                                <?php endif ?>  
                                            </tr> 
                                        </thead> 
                                        <tbody>
                                            <?php $c=0; ?>
                                            <?php foreach ($model_user_wifi as $key => $wifi_value): ?>
                                            <?php $c++; ?>
                                            <tr>  
                                                <td><?=$c++; ?></td> 
                                                <td>
                                                    <?php 
                                                        if( $wifi_value['custom_price'] != 0 || $wifi_value['custom_price'] != null ){
                                                            echo $wifi_value['custom_price'];
                                                        }else{
                                                            echo $wifi_value['packet_price'];
                                                        }
                                                    ?>
                                                     <?=$siteConfig['currency'] ?>
                                                </td>
                                                <td><?=$wifi_value['packet_name'] ?></td> 
                                                <td>
                                                    <?php if ($wifi_value['status'] == 1): ?>
                                                       <span class="badge badge-success"><?=Yii::t('app','Active') ?></span>
                                                    <?php elseif($wifi_value['status'] == 2): ?>
                                                     <span class="badge badge-danger"><?=Yii::t('app','Deactive') ?></span>
                                                    <?php elseif($wifi_value['status'] == 3): ?>
                                                     <span style="background-color: #795548;color: #fff;padding: 0 5px" class="badge badge-default"><?=Yii::t('app','Archive') ?></span>
                                                    <?php endif ?>

                                                 </td> 
                                              
                                                <?php if (User::canRoute('/users/packet-ajax-status')): ?>
                                                <td>
                                                    <?php  $isChecked = ($wifi_value['status'] == '1') ? "checked" : ""; ?>
                                                    <input name="input_cj" class="stat_us" data-user_id="<?=$wifi_value['user_id'] ?>" data-packet_id="<?=$wifi_value['packet_id'] ?>"
                                                    data-service_id="<?=$service_one->service->id ?>"  data-usp-id= "<?=$wifi_value['u_s_p_i'] ?>"
                                                      type="checkbox" <?=$isChecked ?> hidden="hidden"   id="packets_check<?=$wifi_value['id'] ?>">
                                                    <label class="c-switch" for="packets_check<?=$wifi_value['id'] ?>"></label>      
                                                </td>
                                                <?php endif ?> 

                                                 <?php if (User::canRoute('/users/change-packet')): ?>
                                                    <td class="change-packet">
                                                        <a title="<?=Yii::t( "app", "Change {customer} {packet} packet !", [ 'customer'=>$model['fullname'], 'packet'=>$wifi_value['packet_name'] ] ) ?>" class="modal-d" href="<?=$langUrl ?>/users/change-packet?id=<?=$wifi_value['u_s_p_i'] ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                                        </a>
                                                    </td>                                              
                                                <?php endif ?>  

                                                <td><?=date('m/d/Y H:i:s', $wifi_value['created_at']) ?></td>     
                                                <?php if (User::canRoute('/users/service-delete')): ?>
                                                <td>
                                                <a data-fancybox data-src="#hidden-content-<?=$service_one->service->id ?>-<?=$wifi_value['id'] ?>" href="javascript:void(0)">
                                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                </a>
                                                </td>                 
                                                <?php endif ?>  
                                            </tr> 

                           
                                           <?php if (User::canRoute('/users/service-delete')): ?>
                                                <div style="display: none;max-width: 400px;" id="hidden-content-<?=$service_one->service->id ?>-<?=$wifi_value['id'] ?>">
                                                        <div class="fcc">
                                                          <h2 ><b>
                                                            <?=Yii::t("app","Packet deleting - {user_fullname}",['user_fullname'=>$wifi_value['user_fullname']])  ?>
                                                        </b></h2>
                                                          <p ><?=Yii::t("app","Are you sure want delete {packet_name} packet ?",['packet_name'=>$wifi_value['packet_name']]) ?> </p>
                                                          <button class="btn btn-primary delete-packet" data-user_id="<?=$wifi_value['user_id'] ?>"  data-service="<?=$service_one->service->id ?>"  data-packet="<?=$wifi_value['packet_id']  ?>" data_packet_id_ser="<?=$wifi_value['u_s_p_i'] ?>"   title="<?=Yii::t('app','Delete') ?>" ><?=Yii::t('app','Delete') ?></button>
                                                          <button data-fancybox-close="" class="btn btn-secondary"  title="<?=Yii::t('app','Close') ?>" ><?=Yii::t('app','Close') ?></button>           
                                                        </div>
                                                </div> 
                                            <?php endif ?>
                                          <?php endforeach ?>
                                        </tbody> 
                                    </table>
                                 </div>
                        <?php  Pjax::end(); ?>           
            <?php endif ?>

            <?php if ($service_one->service->service_alias == "voip"): ?>

                 <div class="tab-pane p-3" id="<?=$service_one->service->service_alias ?>" role="tabpanel">
                <?php  Pjax::begin(['id'=>'pjax-voip-table','enablePushState'=>true]);  ?>   
                    <?php 
                        $userServicePacketModel = \app\models\UsersServicesPackets::find()
                        ->where(['users_services_packets.user_id'=>$model->id])
                        ->andWhere(['users_services_packets.service_id'=>$service_one->service->id])
                        ->all();
                    ?>
                    <table class="table table-striped">
                        <thead> 
                            <tr> 
                                <th>#</th> 
                                <th><?=Yii::t('app','Phone number') ?></th> 
                                <th><?=Yii::t('app','Price') ?></th> 
                                <th><?=Yii::t('app','Packet') ?></th> 
                                <th><?=Yii::t('app','Status') ?></th> 
                   
                                <?php if (User::canRoute('/users/change-packet')): ?>
                                <th><?=Yii::t('app','Change') ?></th> 
                                <?php endif ?>  
                                <th><?=Yii::t('app','Time') ?></th> 
                                <?php if (User::canRoute('/users/service-delete')): ?>
                                <th><?=Yii::t('app','Delete') ?> </th> 
                                <?php endif ?>  
                            </tr> 
                        </thead> 
                        <tbody>
                            <?php $c=0; ?>
                            <?php foreach ($userServicePacketModel as $key => $voip): ?>
                            <?php $c++; ?>
                            <tr>  
                                <td><?=$c++; ?></td> 
                                <td><?=$voip->usersVoip->phone_number ?></td> 
                                <td>
                                    <?php 
                                        if( $voip->price != 0 || $voip->price != null ){
                                            echo $voip->price;
                                        }else{
                                            echo $voip->packet->packet_price ;
                                        }
                                    ?>
                                     <?=$siteConfig['currency'] ?>
                                </td>
                                <td><?=$voip->packet->packet_name ?></td> 
                                <td>
                                    <?php if ($voip['status'] == 1): ?>
                                       <span class="badge badge-success"><?=Yii::t('app','Active') ?></span>
                                    <?php elseif($voip['status'] == 2): ?>
                                     <span class="badge badge-danger"><?=Yii::t('app','Deactive') ?></span>
                                    <?php elseif($voip['status'] == 3): ?>
                                     <span style="background-color: #795548;color: #fff;padding: 0 5px" class="badge badge-default"><?=Yii::t('app','Archive') ?></span>
                                    <?php endif ?>

                                 </td> 
                              
                                 <?php if (User::canRoute('/users/change-packet')): ?>
                                    <td class="change-packet">
                                        <a title="<?=Yii::t( "app", "Change {customer} {packet} packet !", [ 'customer'=>$model['fullname'], 'packet'=>$voip->packet->packet_name ] ) ?>" class="modal-d" href="<?=$langUrl ?>/users/change-packet?id=<?=$voip['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                        </a>
                                    </td>                                              
                                <?php endif ?>  
                                             
                                <td><?=date('m/d/Y H:i:s', $voip['created_at']) ?></td>     
                                <?php if (User::canRoute('/users/service-delete')): ?>
                                <td>
                                <a data-fancybox data-src="#hidden-content-<?=$service_one->service->id ?>-<?=$voip['id'] ?>" href="javascript:void(0)">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </a>
                                </td>                 
                                <?php endif ?>  
                            </tr> 

                           <?php if (User::canRoute('/users/service-delete')): ?>
                                <div style="display: none;max-width: 400px;" id="hidden-content-<?=$service_one->service->id ?>-<?=$voip['id'] ?>">
                                    <div class="fcc">
                                        <h4>
                                            <b><?=Yii::t("app","Packet deleting - {user_fullname}",['user_fullname'=>$voip->user->fullname])  ?></b>
                                        </h4>
                                          <p ><?=Yii::t("app","Are you sure want delete {packet_name} packet ?",['packet_name'=>$voip->packet->packet_name]) ?> </p>
                                         <button class="btn btn-primary delete-packet" data-user_id="<?=$voip['user_id'] ?>"  data-service="<?=$service_one->service->id ?>"  data-packet="<?=$voip['packet_id']  ?>" data_packet_id_ser="<?=$voip['id'] ?>"   title="<?=Yii::t('app','Delete') ?>" ><?=Yii::t('app','Delete') ?></button>    
                                          <button data-fancybox-close="" class="btn btn-secondary"  title="<?=Yii::t('app','Close') ?>" ><?=Yii::t('app','Close') ?></button>           
                                    </div>
                                </div> 
                            <?php endif ?>
                          <?php endforeach ?>
                        </tbody> 
                    </table>
                <?php  Pjax::end(); ?>           
                 </div>
            <?php endif ?>
        <?php endforeach ?>

        <?php if (User::hasPermission('customer-damage-history')): ?>
            <div class="tab-pane p-3" id="settings1" role="tabpanel">
                <div class="card">
                     <?php if ( count($damages) > 0 ): ?>
                        <ul  class="damage_container scrollbar-custom"> 
                            <?php foreach ($damages as $key_d => $damage_one): ?>
                              <?php if ($damage_one['message'] != ''): ?>
                                  <?php $us_message = '<label><b>'.Yii::t("app","More detail").' </b></label> :'.$damage_one['message'].' '; ?>
                              <?php else: ?>
                                  <?php $us_message = ''; ?>
                              <?php endif ?>
                              <?php if ($damage_one['damage_result'] !=""): ?>
                                   <?php $p_r = ' <div><label><b>'.Yii::t("app","Report result").' </b></label>: '.$damage_one['damage_result'].'</div>'; ?>
                              <?php else: ?>
                                  <?php $p_r = "<div><label style='color:red;'>".Yii::t("app","The problem has not been resolved!")."</label></div>"; ?>
                              <?php endif ?>
                                  
                            <li class="<?php if($damage_one['status']){echo "damage-ok";}else{echo "damage-have";} ?>"> 
                                <label><b><?=Yii::t("app","Reported reason") ?> : </b> 
                                    <?php
                                        if (isset(\app\models\UserDamages::getDamageReason()[$damage_one['damage_reason']])) {
                                           echo (\app\models\UserDamages::getDamageReason()[$damage_one['damage_reason']]);
                                        }else{
                                            echo $damage_one['damage_reason'];
                                        }
                                     ?>  
                                </label> 
                                    <div> <?=$us_message ?></div>
                                    <?php echo $p_r; ?>
                                     <div> 
                                        <b><?=Yii::t("app","Created at") ?></b> :
                                       <?=date('d/m/Y H:i:s',$damage_one['created_at']) ?>
                                    </div>
                                     <div> 
                                        <b><?=Yii::t("app","User") ?></b> : 
                                        <?=$damage_one['member_name'] ?>
                                    </div>

                            </li> 
                            <?php endforeach ?>
                        </ul>
                     <?php else: ?>
                        <h2><?=Yii::t("app","The customer has not reported any complaints.") ?></h2> 
                     <?php endif ?>      
                    
                </div>
            </div>
        <?php endif ?>

        <?php if (User::hasPermission('customer-history')): ?>
            <div class="tab-pane p-3" id="user_history_tab" role="tabpanel">
            <?php  Pjax::begin(['id'=>'pjax-user-history','enablePushState'=>true]);  ?>
            <ul class="customer-history">
                <?php foreach ($user_history as $key => $us_h_one): ?>
                    <li><b><?php 
                    echo  date('d/m/Y H:i:s',$us_h_one->time)." ".$us_h_one->text;
                    ?></b>    </li>
                <?php endforeach ?>
            </ul>
            <?php  Pjax::end();  ?>
            </div>
        <?php endif ?>

        <?php if (User::hasPermission('customer-note-history')): ?>
            <div class="tab-pane p-3" id="user_notes" role="tabpanel">
            <?php  Pjax::begin(['id'=>'pjax-note-form','enablePushState'=>true]);  ?>
            <?php if ( $notes != null ): ?>
                <div class="comment-widgets scrollbar-custom" id="scrollbar-custom">
                    <?php foreach ($notes as $key => $note): ?>
                            <div class="d-flex flex-row comment-row">
                                <div class="comment-text w-100">
                                    <div class="comment-footer">
                                        <span style="padding: 0; font-size: 13px;" class="label comment-info">
                                             <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            <?=date('d/m/Y H:i:s',$note['time']); ?> - <b><?=$note['member_name'] ?></b></span>
                                    </div>
                                    <p class="mb-1"><?=$note['note'] ?></p>
                                </div>
                            </div>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
            <h2><?=Yii::t('app','There is no note about customer') ?></h2>
            <?php endif ?>
            <?php Pjax::end(); ?>  
            </div>
        <?php endif ?>

        <?php if (User::hasPermission('customer-sms-history')): ?>
            <div class="tab-pane p-3" id="user_sms" role="tabpanel">
                <div class="row">
                    <div class="col-sm-12">
                        <?php if ($messages != null): ?>
                        <div class="comment-widgets scrollbar-custom" id="scrollbar-custom">
                            <?php foreach ($messages as $key => $message): ?>
                                    <div class="d-flex flex-row comment-row">
                                        <div class="comment-text w-100">
                                            <div class="comment-footer">
                                                <span style="padding: 0; font-size: 13px;" class="label comment-info">
                                                    <?php if ( $message['type'] == 'whatsapp' ): ?>
                                                     <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="24" height="24" x="0" y="0" viewBox="0 0 152 152" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g id="Layer_2" data-name="Layer 2"><g id="_08.whatsapp" data-name="08.whatsapp"><circle id="background" cx="76" cy="76" fill="#2aa81a" r="76" data-original="#2aa81a" class=""></circle><g id="icon" fill="#fff"><path d="m102.81 49.19a37.7 37.7 0 0 0 -60.4 43.62l-4 19.42a1.42 1.42 0 0 0 .23 1.13 1.45 1.45 0 0 0 1.54.6l19-4.51a37.7 37.7 0 0 0 43.6-60.26zm-5.94 47.37a29.56 29.56 0 0 1 -34 5.57l-2.66-1.32-11.67 2.76v-.15l2.46-11.77-1.3-2.56a29.5 29.5 0 0 1 5.43-34.27 29.53 29.53 0 0 1 41.74 0l.13.18a29.52 29.52 0 0 1 -.15 41.58z" fill="#ffffff" data-original="#ffffff" class=""></path><path d="m95.84 88c-1.43 2.25-3.7 5-6.53 5.69-5 1.2-12.61 0-22.14-8.81l-.12-.11c-8.29-7.74-10.49-14.19-10-19.3.29-2.91 2.71-5.53 4.75-7.25a2.72 2.72 0 0 1 4.25 1l3.07 6.94a2.7 2.7 0 0 1 -.33 2.76l-1.56 2a2.65 2.65 0 0 0 -.21 2.95 29 29 0 0 0 5.27 5.86 31.17 31.17 0 0 0 7.3 5.23 2.65 2.65 0 0 0 2.89-.61l1.79-1.82a2.71 2.71 0 0 1 2.73-.76l7.3 2.09a2.74 2.74 0 0 1 1.54 4.14z" fill="#ffffff" data-original="#ffffff" class=""></path></g></g></g></g></svg>
                                                    <?php else: ?>
                                                     <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                    <?php endif ?>
                                                    <?=date('d/m/Y H:i:s',$message['message_time']); ?> - <b><?=$message['member_name'] ?></b></span> - 
                                                    <?php if ( $message['status'] == 0 ): ?>
                                                        <span class="badge badge-danger"><?=Yii::t("app","Unsuccessfully") ?></span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success"><?=Yii::t("app","Successfully") ?></span>
                                                    <?php endif ?>
                                            </div>
                                            <p class="mb-1"><?=$message['text'] ?></p>
                                        </div>
                                    </div>
                            <?php endforeach ?>
                        </div>
                        <?php else: ?>
                        <h2 style="padding-left: 15px;"><?=Yii::t('app','Customer doesnt have any message') ?></h2>
                        <?php endif ?>
                    </div>   
                </div> 
            </div>
        <?php endif ?>

        <?php if (User::hasPermission('customer-item-history')): ?>
            <div class="tab-pane p-3" id="user_item" role="tabpanel">
               <div class="card ">
                   <div class="card-body">
                       <div class="col-sm-12" style="margin-top: 0; padding: 0;">
                            <?php  Pjax::begin(['id'=>'pjax-user-item-info','enablePushState'=>true]);  ?>
                              <div class="table-responsive">
                                <?php if ( $itemUsage !=null ): ?>
                                 <table class="table table-striped">
                                    <thead>
                                       <tr>
                                          <th>#</th>
                                          <th><?=Yii::t('app','Item') ?></th>
                                          <th><?=Yii::t('app','Quantity') ?></th>
                                          <th><?=Yii::t('app','Personal') ?></th>
                                          <th><?=Yii::t('app','Total price') ?></th>
                                          <th><?=Yii::t('app','Credit price per month') ?></th>
                                          <th><?=Yii::t('app','Month count') ?></th>
                                          <th><?=Yii::t('app','Status') ?></th>
                                          <th><?=Yii::t('app','Created at') ?></th>
                                          <?php if (User::canRoute('/users/user-item-delete')): ?>
                                            <th><?=Yii::t('app','Delete') ?></th>
                                          <?php endif ?>
                                       </tr>
                                    </thead>
                                    <tbody>
                                      <?php $c = 0; ?>
                                      <?php foreach ( $itemUsage as $key => $inf ): ?>
                                      <?php $c++; ?>
                                       <tr>
                                                <th scope="row"><?=$c ?></th>
                                                <td><?=$inf['item_name'] ?></td>
                                                <td><?=$inf['quantity'] ?></td>
                                                <td><?=\app\models\ItemUsage::getItemUsagePersonals($inf['id']) ?></td>
                                               <td>
                                                <?=$inf['quantity'] * $inf['price']; ?>  <?=$siteConfig['currency'] ?>
                                               </td>
                                                <td><?= ( $inf['status'] == 6 ) ? ceil( $credit_price = ( $inf['quantity'] * $inf['price'] ) / $inf['month'] ). " " .$siteConfig['currency']  : "-";?></td>
                                                <td><?=($inf['month']) ? $inf['month'] : "-"; ?></td>
                                                <td>
                                                    <?php
                                                            if ($inf['credit'] == '0' && $inf['status'] == 6 ) {
                                                               echo " <span class='badge badge-pill badge-success'>".app\models\ItemUsage::getItemStatus()[$inf['status']]."-success</span>";
                                                               if (User::canRoute('/users/credit-history')) {
                                                                    echo "<a style='margin-left:2px' href='javascript:;' data-fancybox data-type='ajax'  data-fancybox data-type='ajax' data-src=".$langUrl.Url::to('/users/credit-history').'?user_id='.$inf['user_id'].'&item_usage_id='.$inf['id']." href='javascript:void(0);'>".Yii::t("app","History")."</a>";
                                                               }
                                                            }elseif($inf['credit'] == '1' && $inf['status'] == 6 ){
                                                                    echo " <span class='badge badge-pill badge-warning'>".app\models\ItemUsage::getItemStatus()[$inf['status']]."</span>";
                                                             if (User::canRoute('/users/credit-history')) {
                                                                    echo "<a style='margin-left:2px' href='javascript:;' data-fancybox data-type='ajax'  data-fancybox data-type='ajax' data-src=".$langUrl.Url::to('/users/credit-history').'?user_id='.$inf['user_id'].'&item_usage_id='.$inf['id']."  href='javascript:void(0);'>".Yii::t("app","History")."</a>";
                                                             }
                                                            }elseif($inf['credit'] == '2' && $inf['status'] == 4 ){
                                                                    echo " <span class='badge badge-pill badge-warning'>".app\models\ItemUsage::getItemStatus()[$inf['status']]."</span>";
                                                             if (User::canRoute('/users/gift-history')) {
                                                                        echo "<a style='margin-left:2px' href='javascript:;' data-fancybox data-type='ajax'  data-fancybox data-type='ajax' data-src=".$langUrl.Url::to('/users/gift-history').'?user_id='.$inf['user_id'].'&item_usage_id='.$inf['id']." href='javascript:void(0);'>".Yii::t("app","History")."</a>";
                                                             }
                                                            }elseif($inf['credit'] == '3' && $inf['status'] == 4 ){
                                                                    echo " <span class='badge badge-pill badge-success'>".app\models\ItemUsage::getItemStatus()[$inf['status']]."</span>";  
                                                               if (User::canRoute('/users/gift-history')) {
                                                                  echo "<a style='margin-left:2px' href='javascript:;' data-fancybox data-type='ajax'  data-fancybox data-type='ajax' data-src=".$langUrl.Url::to('/users/gift-history').'?user_id='.$inf['user_id'].'&item_usage_id='.$inf['id']."  href='javascript:void(0);'>".Yii::t("app","History")."</a>";
                                                               }
                                                            }else{
                                                                  echo app\models\ItemUsage::getItemStatus()[$inf['status']];
                                                            }
                                                    ?>
                                                 
                                             </td>
                                                <td><?=date('d/m/Y H:i:s',$inf['created_at']) ?></td>
                                          
                                                <?php if (User::canRoute('/users/user-item-delete')): ?>
                                                <td>
                                                    <a data-fancybox data-src="#hidden-content-item-delete-<?=$inf['id'] ?>" href="javascript:void(0)">  <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
                                                    <?php if (User::canRoute('/users/service-delete')): ?>
                                                    <div style="display: none;" id="hidden-content-item-delete-<?=$inf['id'] ?>">
                                                            <div class="fcc">
                                                              <h2 ><b><?=Yii::t("app","Delete an item") ?> </b></h2>
                                                              <p ><?=Yii::t("app","Are you sure want delete {item} ?", ['item' => $inf['item_name']]) ?> </p>
                                                              <button class="btn btn-primary item-delete" data-item_usage_id="<?=$inf['id'] ?>" data-item_id="<?=$inf['item_id'] ?>" data-item-user_id="<?=$inf['user_id'] ?>" title="<?=Yii::t('app','Delete') ?>" ><?=Yii::t('app','Delete') ?></button>
                                                              <button data-fancybox-close="" class="btn btn-secondary"  title="<?=Yii::t('app','Close') ?>" ><?=Yii::t('app','Close') ?></button>           
                                                            </div>
                                                    </div> 
                                                    <?php endif ?> 
                                                </td>  
                                                <?php endif ?>
                                       </tr>
                                      <?php endforeach ?>
                                    </tbody>
                                 </table>
                                <?php else: ?>
                                  <h2 style="text-align: center;"><?=Yii::t('app','Customer doesnt have any item') ?></h2>
                                <?php endif ?>
                              </div>
                            <?php  Pjax::end();  ?>
                       </div>
                   </div>
                </div>
            </div>
        <?php endif ?>

        <?php if (User::hasPermission('customer-log-history')): ?>
            <div class="tab-pane p-3" id="user_logs" role="tabpanel">
               <div class="card ">
                   <div class="card-body">
                       <div class="col-sm-12" style="margin-top: 0; padding: 0;">
                        <?php  Pjax::begin(['id'=>'pjax-user-item-info','enablePushState'=>true]);  ?>
                          <div class="table-responsive" style="max-height:600px;overflow-y: auto;">
                            <?php if ($user_logs !=null): ?>
                          <table class="table table-striped mb-0">
                                <thead>
                                   <tr>
                                      <th>#</th>
                                      <th><?=Yii::t('app','Customer') ?></th>
                                      <th><?=Yii::t('app','Log') ?></th>
                                      <th><?=Yii::t('app','Created at') ?></th>
                                   </tr>
                                </thead>
                                <tbody>
                                  <?php $c = 0; ?>
                                  <?php foreach ($user_logs as $key => $log): ?>
                                  <?php $c++; ?>
                                    <tr>
                                       <th scope="row"><?=$c ?></th>
                                       <td><?=$log['member']?></td>
                                       <td><?=$log['text']?></td>
                                       <td><?=date('d/m/Y H:i:s',$log['time']) ?></td>
                                   </tr>
                                  <?php endforeach ?>
                                </tbody>
                             </table>
                            <?php else: ?>
                              <h2 style="text-align: center;"><?=Yii::t('app','User doesnt have any log') ?></h2>
                            <?php endif ?>
                          </div>
                          <?php  Pjax::end();  ?>
                       </div>
                   </div>
                </div>
            </div>
        <?php endif ?>

        <?php if (User::hasPermission('customer-cordinate')): ?>
            <div class="tab-pane p-3" id="user_cordinate" role="tabpanel">
               <div class="card ">
                   <div class="card-body">
                    <?php if ( $model['cordinate'] == null ): ?>
                    <div class="add-cordinate">
                        <h4><?=Yii::t('app','{fullname}\'s location coordinates is not set',['fullname'=>$model['fullname']]) ?></h4>
                    </div>
                    <?php else: ?>
                    <div id='mapBox'></div>

                        <?php
                         $long = explode(",",$model['cordinate'])[0];
                         $lat = explode(",",$model['cordinate'])[1];
                        $this->registerJs('
                        var long = '.$long.';
                        var lat = '.$lat.';
                        var infowindow = new google.maps.InfoWindow();

                        var map = new google.maps.Map(document.getElementById("mapBox"), {
                            zoom: 20,
                            center: new google.maps.LatLng(long,lat),
                            mapTypeId: google.maps.MapTypeId.HYBRID
                        });

                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(long, lat),
                            map: map
                        });
                        ');
                        ?>
                    <?php endif ?>
                        
                   </div>
                </div>
            </div>
        <?php endif ?>

        </div>
        </div>
</div>

<style type="text/css">


.comment-text{
   margin-bottom: 10px;  
}

.label-success {
    background-color: #22b66f;
}
.label-warning {
    background-color: #F3C111;
}


.damage_add label{margin: 0}
.damage_add li{
    border-bottom: 1px solid #e8e6e6;
    margin-bottom: 5px;
    padding-bottom: 5px;
}
.load_balance{background-color: #00bcd4}
.load_balance:hover{background-color: #00bcd4c7}
.alert-primary{
    width: 200px;
    margin-top: 25px;
    text-align: center;
}
.more{margin-top: 25px}
.c-switch {
    display: inline-block;
    position: relative;
    width: 43px;
    height: 18px;
    border-radius: 20px;
    background: #fb4d40;
    transition: background 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    vertical-align: middle;
    cursor: pointer;
}
.c-switch::before {
    content: '';
    position: absolute;
    top: 1px;
    left: 3px;
    width: 16px;
    height: 16px;
    background: #fafafa;
    border-radius: 50%;
    transition: left 0.28s cubic-bezier(0.4, 0, 0.2, 1), background 0.28s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.28s cubic-bezier(0.4, 0, 0.2, 1);
}
.c-switch:active::before {
    box-shadow: 0 2px 8px rgba(0,0,0,0.28), 0 0 0 20px rgba(128,128,128,0.1);
}
input:checked + .c-switch {
    background: #72da67;
}
input:checked + .c-switch::before {
    left: 23px;
    background: #fff;
}
input:checked + .c-switch:active::before {
    box-shadow: 0 2px 8px rgba(0,0,0,0.28), 0 0 0 20px rgba(0,150,136,0.2);
}


#mapBox {
    width: 100%;
    height: 600px;
    position: relative;
    padding: 0;
    margin: 0;
}
#mapBox h2 {
    text-align: center;
    position: absolute;
    left: 50%;
    top: 50%;
    font-size: 20px;
    margin-left: -110px;
}


</style>
<?php 

$this->registerJs("




$(document).on(\"click\",\".stat_us_free\",function(){
    var user_id = $(this).attr(\"data-user_id\");
    var url = '".Url::toRoute('users/index')."';   
    
    if($(this).is(\":checked\")){
    $(this).prop(\"checked\",true);  
        var checked = 7;
         $('#add-new-service').hide();
         $('#add-balance').hide();
    }else{
        var checked = 1;
        $(this).prop(\"checked\",false);  
         $('#add-new-service').show();
         $('#add-balance').show();

    }
    $.ajax({
        url:\"check-free-status\",
        type:\"post\",
        beforeSend:function(){
            $(\".loader\").show();
            $(\".overlay\").show();
        },
        data:{checked:checked,user_id:user_id},
        success:function(response){
         alertify.set('notifier','position', 'top-right');
         alertify.success(response.message);
            $(\".loader\").hide();
            $(\".overlay\").hide();
           setTimeout(()=>{
             location.reload();
             },1000)
        }
    });

})


$(document).on(\"click\",\".stat_us_bank_status\",function(){
    var user_id = $(this).attr(\"data-user_id\");
    var url = '".Url::toRoute('users/index')."';   

    if($(this).is(\":checked\")){
    $(this).prop(\"checked\",true);  
        var checked = 1;
    }else{
        var checked = 0;
        $(this).prop(\"checked\",false);  
    }
    $.ajax({
        url:\"check-bank-status\",
        type:\"post\",
        data:{checked:checked,user_id:user_id},
        success:function(response){
             $.pjax.reload({
                url: '".Url::to(['view']).'?id='.$model->id."',
                container: '#pjax-user-info',
                timeout: 5000
            });
        }
    });

})
");
 ?>

 <?php 
$this->registerJs('

$(document).on("click",".modal-d",function(){
    $("#modal").addClass("drawer right-align");
});

$(document).on("click","#t-map",function(){
    $("#modal").removeClass("drawer right-align")
});

$(document).on("click",".delete-packet",function(){
    var url = "'.Url::toRoute('users/index').'";    
    var user_id = $(this).attr("data-user_id");
    var service_id = $(this).attr("data-service");
    var packet_id = $(this).attr("data-packet");
    var id_usrp = $(this).attr("data_packet_id_ser");
    var that = $(this);
    $.ajax({
        url:"'.Url::to('service-delete').'",
        method:"POST",
        data:{user_id:user_id,service_id:service_id,packet_id:packet_id,id_usrp:id_usrp},
        success:function(res){
           if(res.code == "success"){
             alertify.set("notifier","position", "top-right");
             alertify.success(res.message);
             $.fancybox.close();
             location.reload();
           }
        }
    });
});


$(document).on("click",".stat_us",function(){
    var _user_id = $(this).attr("data-user_id");
    var _packet_id = $(this).attr("data-packet_id");
    var _service_id = $(this).attr("data-service_id");
    var _usp_id = $(this).attr("data-usp-id");

    if($(this).is(":checked")){
    $(this).prop("checked",true);  
    var checked = 1;
}else{
    var checked = 2;
    $(this).prop("checked",false);  
}
$.ajax({
    url:"packet-ajax-status",
    type:"post",
    data:{checked:checked,_user_id:_user_id,_packet_id:_packet_id,_service_id:_service_id,_usp_id:_usp_id},
});
 e.preventDefault();
 return false;
})


$(document).on("click",".item-delete",function(){
    var user_id = $(this).attr("data-item-user_id");
    var item_usage_id = $(this).attr("data-item_usage_id");
    var item_id = $(this).attr("data-item_id");

    var that = $(this);
   $.ajax({
    url:"'.Url::to('user-item-delete').'",
    beforeSend:function(){
      $(".loader").show();
      $(".overlay").addClass("show");

    },
    method:"POST",
    data:{user_id:user_id,item_usage_id:item_usage_id,item_id:item_id},
    success:function(res){
       if(res.status == "success"){
             $.pjax.reload({
                url: "'.Url::to(['view']).'?id='.$model->id.'",
                container: "#pjax-user-item-info",
                timeout: 5000
            }).done(function(){
                $.pjax.reload({
                    url: "'.Url::to(['view']).'?id='.$model->id.'",
                    container: "#pjax-user-info",
                    timeout: 5000
                });
                $.fancybox.close();
                $(".loader").hide();
                $(".overlay").removeClass("show"); 
                alertify.set("notifier","position", "top-right");
                alertify.success(res.message);

            });

       }else{
                $(".loader").hide();
                $(".overlay").removeClass("show"); 
                alertify.set("notifier","position", "top-right");
                alertify.error(res.message);
       }
    }
    });
});
');
 ?>

<?php 
Modal::begin([
    'title' => $model->fullname,
    'id' => 'modal',
    'class'=>'drawer right-align',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],

    'size' => 'modal-lg',
    'asDrawer' => true,

]);
echo "<div id='modalContent'></div>";
Modal::end();
?>
<style type="text/css">
  .payment_his{margin-top: 2px;font-size: 15px;}
  .label-primary{background-color: #1699dd;}
  .card-body{padding: 0 !important;}
</style>

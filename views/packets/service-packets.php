<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\Modal;

$this->title = Yii::t('app','{service_name} service packets',['service_name'=>$service->service_name]);
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";

 ?>

<nav class="breadcrumb-one" aria-label="breadcrumb" style="    padding: 0 15px;">
    <ol class="breadcrumb">
        <li class="breadcrumb-item parent"><a href="/packets"><?=Yii::t("app","Services") ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?=$this->title ?></li>
    </ol>
</nav>
<div class=" container-fluid" style="padding:0 0">
    <ul  style="list-style: none;margin: 0;padding: 0" class="row data-sortable">
      <?php foreach ( $model as $key => $packet_one ): ?>
        <li class="col-md-3" data-id="<?=$packet_one->id ?>" style="margin-bottom:10px">
        <div class="card component-card_1 " <?php if ( $packet_one->packet_price == 0 ): ?>
            style="background: #f44336;"
        <?php endif ?>  >
            <div class="card-body">
                <div class="card_header">
                    <h5 class="card-title"><?=Yii::t("app","{packet_name}",['packet_name'=>$packet_one->packet_name]) ?></h5>
                    <div class="icon-svg">
                       <svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg>
                    </div>
                </div>
                <p><?=Yii::t("app","Price") ?> <?=$packet_one->packet_price ?> AZN</p>
                <div class="btn-group mb-4 mr-2" role="group">
                    <button id="btndefault" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=Yii::t("app","Actions") ?> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                        <div class="dropdown-menu" aria-labelledby="btndefault" style="will-change: transform;">
                            <a title="<?=Yii::t('app','Transfer user to another packet') ?>" href="<?=$langUrl.Url::to("/packets/transfer-packet")."?id=".$packet_one->id."&service=".$packet_one->service->service_alias ?>" class="dropdown-item modal-d"><i class="flaticon-home-fill-1 mr-1"></i><?=Yii::t("app","Transfer packet") ?></a>
                            <a href="<?=$langUrl.Url::to("/packets/detail")."?id=".$packet_one->id."&service=".$packet_one->service->service_alias ?>" class="dropdown-item"><i class="flaticon-home-fill-1 mr-1"></i><?=Yii::t("app","Statistic") ?></a>
                            <a href="<?=$langUrl.Url::to("/packets/update")."?id=".$packet_one->id."&service=".$packet_one->service->service_alias ?>" class="dropdown-item modal-d"><i class="flaticon-home-fill-1 mr-1"></i><?=Yii::t("app","Update") ?></a>
                            <a data-pjax="0" data-fancybox data-src="#hidden-packet-delete<?=$packet_one->id ?>" href="javascript:void(0)"  class="dropdown-item"><i class="flaticon-home-fill-1 mr-1"></i><?=Yii::t("app","Delete") ?></a>
                        </div>
                </div>
            </div>
        </div>
            
        </li> 
        <div class="delete_packet" style="display: none;" id="hidden-packet-delete<?=$packet_one->id ?>">
            <div class="fcc">
               <h2 ><b><?=Yii::t("app","Delete packet : {packet_name}",['packet_name'=>$packet_one->packet_name]) ?></b></h2>
              <p ><b style="color: red"></b> <?=Yii::t("app","Are you sure want to delete {packet_name} ?",['packet_name'=>$packet_one->packet_name]) ?></p>
              <button class="btn btn-danger" data-service-id="<?=$packet_one->service->id ?>" data-packet_id="<?=$packet_one->id ?>"     title="Delete" ><?=Yii::t("app","Delete") ?></button>
              <button data-fancybox-close="" class="btn btn-primary"  title="Close" ><?=Yii::t("app","Close") ?></button>           
            </div>
        </div>  
      <?php endforeach ?>
    </ul>
</div>

<?php 

Modal::begin([
    'title' => Yii::t("app","Service packets"),
    'id' => 'modal',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],
    'size' => 'modal-lg',

]);
echo "<div id='modalContent'></div>";
Modal::end();

?>
<?php $this->registerJs('
$(document).on("click",".delete_packet",function(){
    var that = $(this);
    var packet_id = that.find(".btn-danger").attr("data-packet_id");
    var service_id = that.find(".btn-danger").attr("data-service-id");
     var url = "'.Url::toRoute('/packets/service-packets').'?id="+service_id; 
       console.log(packet_id)
    $.ajax({
        url:"'.$langUrl.Url::to("/packets/delete").'?id="+packet_id,
        success:function(res){
          if(res.code == "success"){
          window.location.href = url;
          }

          if(res.code == "error"){
            alertify.set("notifier","position", "top-right");
            alertify.error(res.message);
          }

        }
    });
});


'); ?>




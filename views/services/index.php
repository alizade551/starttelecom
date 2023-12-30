<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t("app","Services");
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">

    <div class=" panel panel-default"  style="padding: 10px">

     
            <h1><?= Html::encode($this->title) ?></h1>
      <div class="row">
    <?php foreach ($model as $key => $service_one): ?>
        <?php if ($service_one->service_alias == "internet"): ?>
            <div class="col-lg-4 col-md-6 animatedParent animateOnce " style="margin-bottom: 15px">
                    <div class="panel minimal panel-default animated fadeInUp go">
                        <div class="panel-heading clearfix"> 
                            <div class="panel-title"><b><?=$service_one->service_name ?></b>  <?=Yii::t("app","service") ?></div> 
                            <ul class="panel-tool-options"> 
                                <li class="dropdown">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false"><i class="icon-cog"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a class="modal-d" href="<?=Url::to('/services/update').'?id='.$service_one->id ?>"><i class="icon-arrows-ccw"></i><?=Yii::t("app","Update") ?> </a></li>
                                        <li><a data-pjax="0" data-fancybox data-src="#hidden-service-inet-statistic<?=$service_one->id ?>" href="javascript:void(0)"><i class="icon-chart-pie"></i><?=Yii::t("app","Statistics") ?> </a></li>

                                    </ul>
                                 </li>
                            </ul> 
                        </div> 
                        <!-- panel body --> 
                        <div class="panel-body"> 
                            <div class="stack-order">
                              
                                <small>
                                    <?php
                                    $new_us_md = \app\models\UsersServicesPackets::find()->where(['service_id'=>$service_one->id])->orderBy(['created_at'=>SORT_DESC])->groupBy(['user_id'])->all(); 
                                    $us_new=0; 
                                    foreach ($new_us_md as $key => $new_us) {
                                    if ($new_us->created_at +2592000 > time()) {
                                    $us_new++;
                                    }
                                    }
                                    echo Yii::t("app","The number of registered users this month ").$us_new;              
                                                                  
                                    ?>
                                </small>
                            </div>
                            <div class="bar-chart-globe"></div>
                        </div> 
                    </div>
                </div>

               

        <?php endif ?>

        <?php if ($service_one->service_alias == "tv"): ?>
            <div class="col-lg-4 col-md-6 animatedParent animateOnce " style="margin-bottom: 15px">
                    <div class="panel minimal panel-default animated fadeInUp go">
                        <div class="panel-heading clearfix"> 
                            <div class="panel-title"><b><?=$service_one->service_name ?></b>  <?=Yii::t("app","service") ?></div> 
                            <ul class="panel-tool-options"> 
                                <li class="dropdown">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false"><i class="icon-cog"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a class="modal-d" href="<?=Url::to('/services/update').'?id='.$service_one->id ?>"><i class="icon-arrows-ccw"></i><?=Yii::t("app","Update") ?> </a></li>
                                        <li><a data-pjax="0" data-fancybox data-src="#hidden-service-inet_log-<?=$service_one->id ?>" href="javascript:void(0)"><i class="icon-list"></i> <?=Yii::t("app","Detailed log") ?></li>
                                        <li><a data-pjax="0" data-fancybox data-src="#hidden-service-inet-statistic<?=$service_one->id ?>" href="javascript:void(0)"><i class="icon-chart-pie"></i><?=Yii::t("app","Statistics") ?> </a></li>
                                    </ul>
                                 </li>
                            </ul> 
                        </div> 
                        <!-- panel body --> 
                        <div class="panel-body"> 
                            <div class="stack-order">
                                
                                <small>
<?php
$new_us_md = \app\models\UsersServicesPackets::find()->where(['service_id'=>$service_one->id])->orderBy(['created_at'=>SORT_DESC])->groupBy(['user_id'])->all(); 
$us_new=0; 
        foreach ($new_us_md as $key => $new_us) {
           if ($new_us->created_at +2592000 > time()) {
                $us_new++;
                }
        }
                   echo Yii::t("app","The number of registered users this month ").$us_new;                       
                                                              
?> </small>
                            </div>
                            <div class="bar-chart-tv"></div>
                        </div> 
                    </div>
                </div>


                 
                       
                           <h2 style="color: #16202f"><b><?=$service_one->service_name ?></b> service information</h2>

                        <table class="table-fill">

                        <tbody class="table-hover">
                            <tr>
                                <td class="text-left">Name</td>
                                <td class="text-left"><?=$service_one->service_name ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Alias</td>
                                <td class="text-left"><?=$service_one->service_alias ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Updated at</td>
                                <td class="text-left"><?=date('d/m/Y H:i:s', $service_one->updated_at); ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Created at</td>
                                <td class="text-left"><?=date('d/m/Y H:i:s',$service_one->created_at); ?></td>
                            </tr>
                        </tbody>
                        </table>

                        <button style="display: block;float: left; margin-top: 10px" data-fancybox-close="" class="btn btn-success">Close me</button>       
                     
                    </div>
        <?php endif ?>

        <?php if ($service_one->service_alias == "wifi"): ?>
            <div class="col-lg-4 col-md-6 animatedParent animateOnce" style="margin-bottom: 15px;">
                    <div class="panel minimal panel-default animated fadeInUp go">
                        <div class="panel-heading clearfix"> 
                            <div class="panel-title"><b><?=$service_one->service_name ?></b>  xidməti</div> 
                            <ul class="panel-tool-options"> 
                                <li class="dropdown">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false"><i class="icon-cog"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a class="modal-d" href="<?=Url::to('/services/update').'?id='.$service_one->id ?>"><i class="icon-arrows-ccw"></i> Dəyiş</a></li>
                                        <li><a data-pjax="0" data-fancybox data-src="#hidden-service-inet_log-<?=$service_one->id ?>" href="javascript:void(0)"><i class="icon-list"></i> Detailed log</li>
                                        <li><a data-pjax="0" data-fancybox data-src="#hidden-service-inet-statistic<?=$service_one->id ?>" href="javascript:void(0)"><i class="icon-chart-pie"></i> Statistics</a></li>
                                    </ul>
                                 </li>
                            </ul> 
                        </div> 
                        <!-- panel body --> 
                        <div class="panel-body"> 
                            <div class="stack-order">
                                
                                <small>
<?php
$new_us_md = \app\models\UsersServicesPackets::find()->where(['service_id'=>$service_one->id])->orderBy(['created_at'=>SORT_DESC])->groupBy(['user_id'])->all(); 
$us_new=0; 
        foreach ($new_us_md as $key => $new_us) {
           if ($new_us->created_at +2592000 > time()) {
                $us_new++;
                }
        }
                     echo Yii::t("app","The number of registered users this month ").$us_new;                    
                                                              
?></small>
                            </div>
                            <div class="bar-chart-wifi"></div>
                        </div> 
                    </div>
                </div>

                    </div>

                       
                           <h2 style="color: #16202f"><b><?=$service_one->service_name ?></b> service information</h2>

                        <table class="table-fill">

                        <tbody class="table-hover">
                            <tr>
                                <td class="text-left">Name</td>
                                <td class="text-left"><?=$service_one->service_name ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Alias</td>
                                <td class="text-left"><?=$service_one->service_alias ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Updated at</td>
                                <td class="text-left"><?=date('d/m/Y H:i:s', $service_one->updated_at); ?></td>
                            </tr>
                            <tr>
                                <td class="text-left">Created at</td>
                                <td class="text-left"><?=date('d/m/Y H:i:s',$service_one->created_at); ?></td>
                            </tr>
                        </tbody>
                        </table>

                        <button style="display: block;float: left; margin-top: 10px" data-fancybox-close="" class="btn btn-success">Close me</button>       
                     
                    </div>
        <?php endif ?>
    <?php endforeach ?>
</div>


    </div>
</div>
<?php 

Modal::begin([
    'title' => Yii::t('app','Service'),
    'id' => 'modal',
    'options' => [
        'tabindex' => false // important for Select2 to work properly
    ],
    'size' => 'modal-sm',
]);
echo "<div id='modalContent'></div>";
Modal::end();

?>
<?php $this->registerJs('
$(document).on("click",".delete_service",function(){
    var that = $(this);
    var service_id = that.find(".btn-success").attr("data-service_id");
    $.ajax({
        url:"'.Url::to("/services/delete").'?id="+service_id
    });
});


'); ?>


 <style type="text/css">
   .fancybox-active {
    height: 100%;
}

/*** Table Styles **/

.table-fill {
  background: white;
  border-radius:3px;
  border-collapse: collapse;
  height: 70px;
  margin: auto;
  max-width: 600px;
  padding:10px;
  width: 100%;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
  animation: float 5s infinite;
}
tr {
  border-top: 1px solid #C1C3D1;
  border-bottom-: 1px solid #C1C3D1;
  color:#333;
  font-size:16px;
  font-weight:normal;

}
 
tr:hover td {
  background:#4E5066;
  color:#FFFFFF;
  border-top: 1px solid #22262e;
}
 
tr:first-child {
  border-top:none;
}

tr:last-child {
  border-bottom:none;
}
 
tr:nth-child(odd) td {
  background:#EBEBEB;
}
 
tr:nth-child(odd):hover td {
  background:#4E5066;
}

tr:last-child td:first-child {
  border-bottom-left-radius:3px;
}
 
tr:last-child td:last-child {
  border-bottom-right-radius:3px;
}
 

td {
    height: 10px !important;
    background: #FFFFFF;
    padding: 5px;
    text-align: left;
    vertical-align: middle;
    font-size: 15px;
    border-right: 1px solid #C1C3D1;
}


td:last-child {
  border-right: 0px;
}

th.text-left {
  text-align: left;
}

th.text-center {
  text-align: center;
}

th.text-right {
  text-align: right;
}

td.text-left {
  text-align: left;
}

td.text-center {
  text-align: center;
}

td.text-right {
  text-align: right;
}

 </style>
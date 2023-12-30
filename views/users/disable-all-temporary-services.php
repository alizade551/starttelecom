<?php 
use webvimark\modules\UserManagement\models\User;
use yii\widgets\Pjax;
$this->title = Yii::t("app",'Disable all temporary services')
?>

<div class="statbox widget box box-shadow col-xl-12 col-md-12 col-sm-12 col-12">
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item parent"><a data-menu_id="adminstration" href="javascript:void(0);"><?=Yii::t("app","Adminstration") ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?=$this->title  ?></li>
    </ol>
</nav>
<?php  Pjax::begin(['id'=>'pjax-disable-all','enablePushState'=>true]);  ?>
    <div class="widget-content widget-content-area">
        <div class="table-responsive">
            <table class="table table-bordered  mb-4">
                <thead>
                    <tr>
                        <th><?=Yii::t("app","#") ?></th>
                        <th><?=Yii::t("app","User fullname") ?></th>
                        <th><?=Yii::t("app","Service") ?></th>
                        <th><?=Yii::t("app","Tariff") ?></th>
                        <th><?=Yii::t("app","Balance") ?></th>
                        <th><?=Yii::t("app","Last payment") ?></th>
                        <th><?=Yii::t("app","Enable/Disable") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model as $key => $packet): ?>
                        <tr>
                            <td><?=$key+1 ?></td>
                            <td><?=$packet['user_fullname'] ?></td>
                            <td><?=$packet['service_alias_name'] ?></td>
                            <td><?=$packet['user_packet_price'] ?> AZN</td>
                            <td><?=$packet['user_balance'] ?> AZN</td>
                            <td><?=date('d/m/Y H:i:s',$packet['last_user_payment']); ?></td>
                            <td> 
                            <?php if (User::canRoute('/users/packet-ajax-status')): ?>
                                <?php  $isChecked = ($packet['status'] == '1') ? "checked" : ""; ?>
                                <input name="input_cj" class="stat_us" data-user_id="<?=$packet['user_id'] ?>" data-packet_id="<?=$packet['packet_id'] ?>"
                                data-service_id="<?=$packet['service_id'] ?>"  data-usp-id= "<?=$packet['id'] ?>"
                                type="checkbox" <?=$isChecked ?> hidden="hidden"   id="packets_check<?=$packet['id'] ?>">
                                <label class="c-switch" for="packets_check<?=$packet['id'] ?>"></label>      
                            <?php endif ?> 
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
       <button class="btn btn-danger btn-block mb-4 mr-2 disabled-all"><?=Yii::t("app","Disable all temporary services") ?></button>
    </div>
<?php  Pjax::end();  ?>
</div>

<?php 
$this->registerJs('
$(document).on("click",".stat_us",function(e){
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
        beforeSend:function(){
          $(".loader").show();
          $(".overlay").addClass("show");
        },
        data:{checked:checked,_user_id:_user_id,_packet_id:_packet_id,_service_id:_service_id,_usp_id:_usp_id},
        success:function(res){
            if(res.code="success"){
                $(".loader").hide();
                $(".overlay").removeClass("show");
                $.pjax.reload({
                    container: "#pjax-disable-all",
                    timeout: 5000
                }).done(function(){
                    $(".loader").hide();
                    $(".overlay").removeClass("show");
                });
            }


        }
    });
});


$(document).on("click",".disabled-all",function(e){
    if (confirm("'.Yii::t("app","Are you sure want to disable all temporary services ?").'")) {
        $.ajax({
            url:"/users/disable-all-temporary-services",
            type:"post",
            beforeSend:function(){
                $(".loader").show();
                $(".overlay").addClass("show");
            },
            success:function(res){
                if(res.code="success"){
                    $(".loader").hide();
                    $(".overlay").removeClass("show");
                    $.pjax.reload({
                        container: "#pjax-disable-all",
                        timeout: 5000
                    }).done(function(){
                        $(".loader").hide();
                         $(".overlay").removeClass("show");
                    })
                }
            }
        });
    }
});

');


 ?>
<style type="text/css">

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

</style>
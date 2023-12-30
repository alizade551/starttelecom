<?php 
use yii\helpers\Url;
use webvimark\modules\UserManagement\models\User;

$this->title = Yii::t('app','Delete receipt from user');



 ?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <div class="container-actions">

                <?php if (User::canRoute("/receipt/member-recipet")): ?>
                <a class="btn btn-primary add-element" data-pjax="0" href="/receipt/member-recipet" style="margin-left:10px;margin-bottom: 10px;">
                   <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <?=Yii::t("app","Define receipt to user") ?>
                </a>
                <?php endif ?>
                <?php if (User::canRoute("/receipt/create")): ?>
                <a class="btn btn-success add-element" data-pjax="0" href="/receipt/create" style="margin-left:10px;margin-bottom: 10px;">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <?=Yii::t("app","Create receiptes") ?>
                </a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

 <div class="card custom-card" style="padding: 20px;width: 100%;">

 	<div class="row">
 		<?php if ($model != null): ?>
	 	 	<?php foreach ($model as $key => $rg): ?>
			  	<div class="col-md-3">
					<div class="card component-card_4 " >
					    <div class="card-body">
					        <div class="user-info" style="position:relative;">
					        	<div class="receipt-delete" style="position: absolute;right: 0;"> 
					        		<a href="javascript:void(0)" 
					        		data-seria="<?=$rg['seria'] ?>" 
					        		data-min_number="<?=$rg['min_number'] ?>" 
					        		data-max_number="<?=$rg['max_number'] ?>"
					        		class="delete-receipt-serias">
					        			<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
					        		</a>
					        	</div>
					            <h4 class="card-user_name"><?=$rg['fullname'] ?></h4>
					            <p class="card-user_occupation"><?=Yii::t('app','Role') ?> : <?php 
									$a = array_keys(\webvimark\modules\UserManagement\models\rbacDB\Role::getUserRoles($rg['member_id']));
									if ($a != null) {
										$role =  $a[0];
									}else{
										$role = Yii::t("app","Role not selected");
									}
									if ($rg['superadmin'] == "1") {
										$role = Yii::t("app","Adminstration");
									}

									echo $role;
					             ?></p>
					            <div class="card-star_rating">
					               <div>
					               		<span class="badge badge-success"><?=$rg['seria'].sprintf("%06d",$rg['min_number']); ?></span>
					               		- 
					               		<span class="badge badge-danger"><?=$rg['seria'].sprintf("%06d",$rg['max_number']); ?></span>
					               </div>
					            </div>
					        </div>
					    </div>
					</div> 		
			 	</div>	
		 	<?php endforeach ?>
 		<?php else: ?>
 			<h2 style="text-align: center;margin:0 auto;"><?=Yii::t("app","Not defiend recipet for members") ?></h2>
 		<?php endif ?>
 	</div>
 </div>

<?php
$this->registerJs('

  $(document).on("click",".delete-receipt-serias",function(){
	  var that = $(this);
	  const seria = that.data("seria");
	  const min_number = that.data("min_number");
	  const max_number = that.data("max_number");

      var message  = "'.Yii::t("app","Are you sure want to delete this ?").'";
          alertify.confirm( message, function (e) {
            if (e) {
				$.ajax({
				      url: "'.\yii\helpers\Url::to(["delete-receipt-from-member"]).'",
				      type: "post",
					  beforeSend:function(){
					 	$(".loader").show();
						$(".overlay").show();
					  },
				      data: {seria,min_number,max_number},
				      success: function (response) {
				          if(response.status == "success"){
				          	that.parents(".col-md-3").hide()
			                $(".loader").hide();
			                $(".overlay").hide();
		                    alertify.set("notifier","position", "top-right");
				          	alertify.success(response.message);
				          }
				      }
				 });
            } 
        }).set({title:"'.Yii::t("app","Delete a receipt from user").'"}).set("labels", {ok:"'.Yii::t('app','Confrim').'", cancel:"'.Yii::t('app','Cancel').'"});  
        return false;
    });
');
 ?>


 <style type="text/css">
 .badge {
    border-radius: 5px;
    padding: 5px;
    font-size: 14px;
}
.card-user_occupation{font-size: 16px}
 </style>

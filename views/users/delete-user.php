<?php 
use yii\helpers\Url;
$langUrl = (Yii::$app->language == "en") ? "/" : "/".Yii::$app->language."/";

?>

<?php if ($model->status == 6): ?>
	<h6 class="credit-text"><?=Yii::t("app","Are you sure want to delete user?") ?> </h6>
	<button data-id="<?=$model->id ?>" type="submit" class="btn btn-danger delete-user"><?=Yii::t("app","Delete") ?></button>
	<button  type="submit" class="btn btn-secondary"><?=Yii::t("app","Close") ?></button>
	<?php 

$this->registerJs('

$(".btn-secondary").on("click",function(){
	$("#modal").modal("toggle");
});

$(".delete-user").on("click",() => {
  let id = $(this).data("id");
  if (confirm("'.Yii::t("app","Are you sure want to delete user?").'")) {
	  $.ajax({
	      url:"'.$langUrl.Url::to("users/delete-user").'?user_id='.$model->id.'",
	      type:"POST",
	      data:{id:id},
		  beforeSend:function(){
			$(".loader").show();
			$(".overlay").addClass("show");
			},
	      success:function(res){
			$(".loader").hide();
			$(".overlay").removeClass("show"); 
		    $("#modal").modal("toggle");

	      }
	  });
  }
});
');
	 ?>		
<?php else: ?>

<?php endif ?>


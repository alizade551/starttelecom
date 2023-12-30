<?php 
use yii\helpers\Url;

?>

<?php if ($model->credit_status == 0): ?>
	<h6 class="credit-text"><?=Yii::t("app","Are you sure want to open service of credit for 3 day?") ?> </h6>
	<button data-id="$model->id" type="submit" class="btn btn-primary give-credit"><?=Yii::t("app","Give a credit") ?></button>
	<?php 

	$this->registerJs('
	$(".give-credit").on("click",() => {
	 
	  let id = $(this).data("id");
	  if (confirm("'.Yii::t("app","Are you sure want to open service of credit for 3 day?").'")) {
		  $.ajax({
		      url:"'.Url::to("/users/give-credit").'?id='.$model->id.'",
		      type:"POST",
		      data:{id:id},
				beforeSend:function(){
					$(".loader").show();
					 $(".overlay").addClass("show");
				},
		      success:function(res){
		      	if(res.status == "error"){
		      		alertify.set("notifier","position", "top-right");
		      		 alertify.error(res.message);
                     $("#modal").modal("toggle");
                  		$(".loader").hide();
                  	    $(".overlay").removeClass("show");
		      	}

		      	if(res.status == "success"){
		      		$("#modal").modal("toggle");
		      		alertify.set("notifier","position", "top-right");
		      		 alertify.success(res.message);

					         $(".loader").hide();
                  			 $(".overlay").removeClass("show");

		             $.pjax.reload({
		                container: "#pjax-user-info",
		                timeout: 5000,
		             }).done(function(){
	                     $.pjax.reload({
	                        container: "#pjax-user-history",
	                        timeout: 5000,
	                    }).done(function(){
					              $.pjax.reload({
							            container: "#pjax-inet-table",
							            timeout: 5000,
							        }).done(function(){
							     			$.pjax.reload({
							            	container: "#pjax-tv-table",
							            	timeout: 5000,
							        		}).done(function(){
								     			$.pjax.reload({
								            	container: "#pjax-wifi-table",
								            	timeout: 5000,
								        		})
							        		})
							        	})
	                    	})
		             	});



		      	}		  
		      }
		  });
	  }

	})



	');


	 ?>		
<?php else: ?>
		<h6 class="credit-text"><?=Yii::t("app","Credit is active now") ?> </h6>
<?php endif ?>


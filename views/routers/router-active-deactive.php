<h5><?=Yii::t("app","Real time active / deactive inet login count") ?></h5>
<table id="w0" class="table table-striped table-bordered detail-view">
   <tbody>
      <tr>
         <th><?=Yii::t('app','Active inet login') ?></th>
         <td id="active-inet-login"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','Deactive inet login') ?></th>
         <td id="deactive-inet-login"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','Unlegal logins') ?></th>
         <td id="un-legal-login"><?=Yii::t('app','Loading') ?></td>
      </tr>

   </tbody>
</table>


<style type="text/css">
.progress {
  margin: 10px 0;
  max-width: 700px;
}
#un-legal-login{max-width: 400px}
.progress-bar-danger {
    background-color: red !important;
    font-size: 13px;
    padding-left: 3px;
}

</style>


<?php 
$this->registerJs("
	var getRouterInfoInterval = setInterval(function(){
		getRouterInfo()
		},1000)

	var getRouterInfo = () => {
		$.ajax({
		    url: '".\yii\helpers\Url::to(["routers/get-active-deactive?nas="]).$model['nas']."&username=".$model['username']."&password=".$model['password']." ',
		    type: 'post',
		    success: function (response) {
		    	console.log(response)
		    	$('#active-inet-login').text(response['activeCount']);
		    	$('#deactive-inet-login').text(response['deactiveCount']);
		    	$('#un-legal-login').html(response['unLegalLogins']);

		    }
		}); 
	}

    $('#modal').on('hidden.bs.modal', function () {
      clearInterval(getRouterInfoInterval)
    })

");



 ?>
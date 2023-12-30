<h5><?=Yii::t("app","CPU load") ?></h5>
<div class="progress">
  <div id="dynamic" class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
    <span id="current-progress" ></span>
  </div>
</div>

<table id="w0" class="table table-striped table-bordered detail-view">
   <tbody>
      <tr>
         <th><?=Yii::t('app','Board name') ?></th>
         <td id="board-name"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','Build time') ?></th>
         <td id="build-time"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','Uptime') ?></th>
         <td id="uptime"><?=Yii::t('app','Loading') ?></td>
      </tr>

      <tr>
         <th><?=Yii::t('app','CPU') ?></th>
         <td id="cpu"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','CPU load') ?></th>
         <td id="cpu-load"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','CPU count') ?></th>
         <td id="cpu-count"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','CPU frequency') ?></th>
         <td id="cpu-frequency"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','Factory software') ?></th>
         <td id="factory-software"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','Free hdd space') ?></th>
         <td id="free-hdd-space"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','Free memory') ?></th>
         <td id="free-memory"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','Platform') ?></th>
         <td id="platform"><?=Yii::t('app','Loading') ?></td>
      </tr>

      <tr>
         <th><?=Yii::t('app','Total hdd space') ?></th>
         <td id="total-hdd-space"><?=Yii::t('app','Loading') ?></td>
      </tr>
      <tr>
         <th><?=Yii::t('app','Total memory') ?></th>
         <td id="total-memory"><?=Yii::t('app','Loading') ?></td>
      </tr>
       <tr>
         <th><?=Yii::t('app','Version') ?></th>
         <td id="version"><?=Yii::t('app','Loading') ?></td>
      </tr>

   </tbody>
</table>


<style type="text/css">
.progress {
  margin: 10px 0;
  max-width: 700px;
}
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
		    url: '".\yii\helpers\Url::to(["routers/get-router-usage?nas="]).$model['nas']."&username=".$model['username']."&password=".$model['password']." ',
		    type: 'post',
		    success: function (response) {

		    	$('#board-name').text(response['board-name']);
		    	$('#build-time').text(response['build-time']);
		    	$('#cpu').text(response['cpu']);
		    	$('#cpu-count').text(response['cpu-count']);
		    	$('#cpu-frequency').text(response['cpu-frequency']);
		    	$('#factory-software').text(response['factory-software']);
		    	$('#free-hdd-space').text(response['free-hdd-space']);
		    	$('#free-memory').text(response['free-memory']);
		    	$('#platform').text(response['platform']);
		    	$('#total-hdd-space').text(response['total-hdd-space']);
		    	$('#total-memory').text(response['total-memory']);
		    	$('#uptime').text(response['uptime']);
		    	$('#version').text(response['version']);
		    	$('#cpu-load').text(response['cpu-load']+' %');
		   	var val = response['cpu-load'];
		      $('#dynamic')
		      .css('width', val + '%')
		      .attr('aria-valuenow', val)
		      .text(val + '% usage');


		    }
		}); 
	}

    $('#modal').on('hidden.bs.modal', function () {
      clearInterval(getRouterInfoInterval)
    })

");



 ?>
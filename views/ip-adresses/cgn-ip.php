<div style="max-height:400px; overflow-y: auto;">
	<table class="table table-striped">
	    <thead> 
	        <tr> 
	            <th>#</th> 
	            <th><?=Yii::t('app','Public ip') ?></th> 
	            <th><?=Yii::t('app','Port range') ?></th> 
	            <th><?=Yii::t('app','Internal ip') ?></th> 
	        </tr> 
	    </thead> 
	    <tbody>
	    	<?php foreach ($model as $cgnKey => $cgn): ?>
	        <tr>  
	            <td><?=$cgnKey + 1 ?></td> 
	            <td><?=$cgn['public_ip'] ?></td> 
	            <td><?=$cgn['port_range'] ?></td> 
	            <td><?=$cgn['internal_ip'] ?></td> 
	        </tr> 
	    	<?php endforeach ?>
	                                                                    
	     </tbody> 
	</table>
</div>
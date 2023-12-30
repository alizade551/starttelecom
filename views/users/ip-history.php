<?php
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\TrafficSearchForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Accounting';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Pjax::begin(['id' => 'traffic-grid', 'timeout' => 5000]); ?>
<?php $form = ActiveForm::begin([
    'id' => 'ip-history-form',
    'options' => [
        'data-pjax' => '1',
        'autocomplete' => 'off'
    ],
]); ?>

    <?= $form->field($model, 'startDate')->textInput(['type' => 'date']) ?>

    <?= $form->field($model, 'endDate')->textInput(['type' => 'date']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Search'), ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php if ( $result != null): ?>
    <div style="max-height:600px;overflow-y:auto;">
        <table class="table table-striped">
            <thead> 
                <tr> 
                    <th>#</th> 
                    <th><?=Yii::t('app','Nasipaddress') ?></th> 
                    <th><?=Yii::t('app','Framedipaddress') ?></th> 
                    <th><?=Yii::t('app','Acctstoptime') ?></th> 
                    <th><?=Yii::t('app','Uptime') ?></th> 
                    <th><?=Yii::t('app','Download') ?></th> 
                    <th><?=Yii::t('app','Upload') ?></th> 
        
                </tr> 
            </thead> 
            <tbody>
                <?php $c=0; ?>
                <?php foreach ($result as $key => $acct): ?>
                <?php $c++; ?>
                <tr>  
                    <td><?=$c++; ?></td> 
                    <td><?=$acct['nasipaddress']; ?></td> 
                    <td><?=$acct['framedipaddress']; ?></td> 
                    <td><?=$acct['acctstoptime']; ?></td> 
                    <td><?=\app\models\radius\Radacct::formatAcctSessionTime($acct['acctsessiontime'])  ?></td> 
                    <td><span class="badge badge-success" style="background-color: #54c2c1 !important;"><?=round ( $acct['acctoutputoctets'] / (1024 * 1024), 2 ) ?> MB</span> </td> 
                    <td><span class="badge badge-success" style="background-color: #c56dd3;"><?=round ( $acct['acctinputoctets'] / (1024 * 1024), 2 ); ?> MB</span></td> 
                </tr> 
              <?php endforeach ?>
            </tbody> 
        </table>
    </div>
<?php else: ?>
	<h5 style="text-align:center;"><?=Yii::t('app','Data not found') ?></h5>
<?php endif ?>
<?php  Pjax::end(); ?>    






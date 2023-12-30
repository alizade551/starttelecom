<?php
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app','Define receipt to user');
?>

<div class="widget widget-content-area mb-3">
    <div class="widget-one">
        <div class="actions-container" style="display: flex; justify-content: space-between;">
            <div class="page-title"> <h5><?=$this->title ?> </h5> </div>
            <div class="container-actions">
                <?php if (User::canRoute("/receipt/delete-receipt-from-member")): ?>
                    <a class="btn btn-danger add-element" data-pjax="0" href="/receipt/delete-receipt-from-member" style="margin-left:10px;margin-bottom: 10px;">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        <?=Yii::t("app","Delete receipt from user") ?>
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

<div class="card custom-card" style="padding: 15px;">
	 <div class="row">
		 <div class="col-lg-12">
			    <?php $form = ActiveForm::begin(['id' => 'form-add-contact']); ?>
				    <?php if ($model->isNewRecord): ?>
				        <?=$form->field($model, 'seria')->dropDownList(ArrayHelper::map(
				                    $serias
				                    ,'seria',
				                    'seria'
				                ),[
				         'onchange'=>'
				           let that = $(this);
						   $.ajax({
						    url:"'.Url::to('get-seria-detail').'",
						    method:"POST",
						    data:{seria:that.val()},
						    success:function(res){
						      $("#receipt-start_int").val(res.min)
						      $("#receipt-end_int").val(res.max)
						    }
						    });
				        ',
				        'prompt'=>Yii::t("app","Select")])?>


				    <?=$form->field($model, 'start_int')->textInput() ?>
				    <?=$form->field($model, 'end_int')->textInput() ?>
				    <?=$form->field($model, 'member_id')->widget(Select2::classname(), [
				        'data' => ArrayHelper::map(
				            \webvimark\modules\UserManagement\models\User::find()
				            ->all()
				            ,'id',
				            'username'
				        ),
				        'language' => 'en',
				        'options' => ['placeholder' => Yii::t('app','Select')],
				        'pluginOptions' => [
				            'allowClear' => true
				        ],
				    ]);?>
				    <?= $form->field($model, 'status')->hiddenInput(['value'=>0])->label(false) ?>
				    <?= $form->field($model, 'type')->hiddenInput(['value'=>0])->label(false) ?>
				    <?= $form->field($model, 'created_at')->hiddenInput(['value'=>time()])->label(false) ?>

				    <div class="form-group">
				        <?= Html::submitButton(Yii::t("app","Define"), ['class' => 'btn btn-primary']) ?>
				    </div>
				    <?php else: ?>
				    <?= $form->field($model, 'code')->textInput(['maxlength' => true])->label(Yii::t("app","Recipet code")) ?>
				    <?= $form->field($model, 'status')->dropDownList([0=>Yii::t("app","Free"),1=>Yii::t("app","Busy")]) ?>
				    <div class="form-group">
				        <?= Html::submitButton(Yii::t("app","Define receipts"), ['class' => 'btn btn-secondary']) ?>
				    </div>
				    <?php endif ?>
			    <?php ActiveForm::end(); ?>
		</div>
	</div>
</div>




<?php 

use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Json;
use kartik\select2\Select2;
use webvimark\modules\UserManagement\models\User;

$this->title = Yii::t("app","Adding a packet");
$siteConfig = \app\models\SiteConfig::find()->asArray()->one();
$currency = $siteConfig['currency'];
$langUrl = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";
 ?>
 

 <div class="request-order-form ">
    <?php 
        if(Yii::$app->request->get('service_id')){
                $model->service_id = Yii::$app->request->get('service_id');
            $this->registerJs('
                $.pjax.reload({
                url: "'.Url::to(['adding-packet']).'?id='.Yii::$app->request->get('id').'&service_id="+$(this).val(),
                container: "#pjax-adding-packet-form",
                timeout: 6000,

                });
                $(document).on("pjax:complete", function() {
                  $(".select_loader").hide();
                });
            ');
        }
     ?>
     <div class="row">
        <?php if ($model_user->status == 0 || $model_user->second_status == '5'): ?>
        <div class="col-lg-12 animatedParent animateOnce z-index-50">
            <div class="panel panel-default animated fadeInUp">
                <div class="panel-body">
                    <?php $form = ActiveForm::begin([
                            'id'=>"add-form",
                            'enableAjaxValidation' => true,
                            'validateOnSubmit'=> true,
                            'enableClientValidation'=>false,
                            'validationUrl' => $langUrl.'adding-packet-validate',
                            'options' => ['autocomplete' => 'off']
                        ]); 
                    ?>


                            <?= $form->field($model, 'user_id')->hiddenInput(['value' => $model_user->id])->label(false) ?>
                            <?php $selectedService = explode(",", $model_user->selected_services); ?>

                            <?= $form->field($model, 'service_id')->dropDownList(
                                ArrayHelper::map(
                                    app\models\Services::find()
                                    ->where(['id'=>$selectedService])
                                    ->all(),'id',
                                    'service_name'
                                ),
                                [
                                 'onchange'=>'
                                   
                                    $.pjax.reload({
                                        url: "'.Url::to(['adding-packet']).'?id='.Yii::$app->request->get('id').'&service_id="+$(this).val(),
                                        container: "#pjax-adding-packet-form",
                                        timeout: 5000
                                    });
                                    $(document).on("pjax:complete", function() {
                                      $(".select_loader").hide();
                                    });
                                ',
                                'prompt'=>Yii::t("app","Select")
                                 ]
                            ) ?>

                       <?php  Pjax::begin(['id'=>'pjax-adding-packet-form','enablePushState'=>true]);  ?>
                            <?php 
                                if (Yii::$app->request->get('service_id')) {
                                    $serviceGet  = \app\models\Services::find()->where(['id'=>Yii::$app->request->get('service_id')])->asArray()->one();
                                }else{
                                    $serviceGet['service_alias'] =[];
                                }

                            ?>
                            <?php if ($serviceGet['service_alias'] == "tv"): ?>    
                                <?= $form->field($model, 'packet_tags')->dropDownList(
                                    ArrayHelper::map(
                                        app\models\Packets::find()
                                        ->where(['service_id'=>Yii::$app->request->get('service_id')])
                                        ->orderBy(['packet_name'=>SORT_ASC])
                                        ->all(),
                                        'id',
                                        'packet_name'
                                    ),
                                    [
                                        'prompt'=>Yii::t('app','Select')
                                    ]
                                ) ?>
                               <?=$form->field($model, 'price')->textInput(['placeholder'=>Yii::t('app','if input will be empty pricing equalt to default packe price')])->label(Yii::t('app','Custom price'));?>

                            <?php endif ?>



                            <?php if ( $serviceGet['service_alias'] == "internet" ): ?>
                                
                                <?= $form->field($model, 'packet_tags')->dropDownList(
                                    ArrayHelper::map(
                                        app\models\Packets::find()
                                        ->orderBy(['packet_name'=>SORT_ASC])
                                        ->where(['service_id'=>Yii::$app->request->get('service_id')])
                                        ->all(),
                                        'id',
                                        'packet_name'
                                    ),
                                    [
                                        'prompt'=>Yii::t("app","Select")
                                    ]
                                ) ?>

                                <?=$form->field($model, 'price')->textInput(['placeholder'=>Yii::t('app','if input will be empty pricing equalt to default packe price')])->label(Yii::t('app','Custom price'));?> 


                                <?=$form->field($model, 'static_ip_address')->dropDownList(
                                        ArrayHelper::map(
                                            \app\models\IpAdresses::find()
                                        ->where(['type'=>'1'])
                                        ->andWhere(['status'=>'0'])
                                        ->all(),'id','public_ip'),['prompt'=>Yii::t("app","Select")])->label(Yii::t('app','Static ip'));?>

                                <?=$form->field($model, 'mac_address')->dropDownList(
                                        \app\components\MikrotikQueries::dhcpGetFakeUser( $routerModel['nas'],$routerModel['username'],$routerModel['password'] ),['prompt'=>Yii::t("app","Select")])->label();?>





                                    <?php 
                                        foreach ($form->attributes as $attribute) {
                                            $attribute = Json::htmlEncode($attribute);
                                            $this->registerJs("jQuery('form#add-form').yiiActiveForm('add', $attribute);");
                                        } 
                                    ?>

                                <?php if ( $serviceGet['service_alias'] == "internet" && $model_user->district->device_registration == "1" ): ?>

                                    <?= $form->field($model, 'port_type')->dropDownList(
                                        ArrayHelper::map(
                                           \app\models\UsersServicesPackets::getPortType($model_user->id),
                                            'device_type',
                                            'device_type'
                                        ),
                                        [
                                            'onchange'=>'
                                                $(".select_loader").show();
                                                $.pjax.reload({
                                                    url: "'.Url::to(['adding-packet']).'?id='.Yii::$app->request->get('id').'&service_id='.Yii::$app->request->get('service_id').'&packet_tags='.Yii::$app->request->get('packet_tags').'&port_type="+$(this).val(),
                                                    container: "#pjax-adding-packet-form-port_type",
                                                    timeout: 5000
                                                });



                                                $(document).on("pjax:complete", function() {
                                                  $(".select_loader").hide();
                                                });
                                            ',

                                            'prompt'=>Yii::t("app","Select")
                                        ]

                                    ) ?>

                                    <?php  Pjax::begin(['id'=>'pjax-adding-packet-form-port_type','enablePushState'=>true]);  ?>
                                            <?php if ( Yii::$app->request->get('port_type')  == "switch" ): ?>
                                               <?= $form->field($model, 'devices')->dropDownList(
                                                    ArrayHelper::map(
                                                       \app\models\UsersServicesPackets::getSwitches($model_user->id),
                                                        'device_id',
                                                        'device_name'
                                                    ),
                                                    [
                                                        'onchange'=>'
                                                            $(".select_loader").show();
                                                            $.pjax.reload({
                                                                url: "'.Url::to(['adding-packet']).'?id='.Yii::$app->request->get('id').'&service_id='.Yii::$app->request->get('service_id').'&packet_tags='.Yii::$app->request->get('packet_tags').'&port_type='.Yii::$app->request->get('port_type').' &switchValue="+$(this).val(),
                                                                container: "#pjax-adding-packet-form-switchValue",
                                                                timeout: 5000
                                                            });
                                                            $(document).on("pjax:complete", function() {
                                                              $(".select_loader").hide();
                                                            });
                                                        ',

                                                        'prompt'=>Yii::t("app","Select")
                                                    ]
                                                ) ?>
                                            <?php endif ?>

                                        <?php if ( Yii::$app->request->get('port_type')  != "switch" ): ?>
                                           <?= $form->field($model, 'devices')->dropDownList(
                                                ArrayHelper::map(
                                                   \app\models\UsersServicesPackets::getOlt($model_user->id,Yii::$app->request->get('port_type')),
                                                    'device_id',
                                                    'device_name'
                                                ),
                                                [
                                                    'onchange'=>'
                                                        $(".select_loader").show();
                                                        $.pjax.reload({
                                                            url: "'.Url::to(['adding-packet']).'?id='.Yii::$app->request->get('id').'&service_id='.Yii::$app->request->get('service_id').'&packet_tags='.Yii::$app->request->get('packet_tags').'&port_type='.Yii::$app->request->get('port_type').' &deviceValue="+$(this).val(),
                                                            container: "#pjax-adding-packet-form-switchValue",
                                                            timeout: 5000
                                                        });
                                                        $(document).on("pjax:complete", function() {
                                                          $(".select_loader").hide();
                                                        });
                                                    ',

                                                    'prompt'=>Yii::t("app","Select")
                                                ]
                                            ) ?>
                                        <?php endif ?>
                                        <?php 
                                        foreach ($form->attributes as $attribute) {
                                            $attribute = Json::htmlEncode($attribute);
                                            $this->registerJs("jQuery('form#add-form').yiiActiveForm('add', $attribute);");
                                        } 
                                        ?>

                                        <?php  Pjax::begin(['id'=>'pjax-adding-packet-form-switchValue','enablePushState'=>true]);  ?>
                                            <?php if ( Yii::$app->request->get('port_type')  == "switch" || Yii::$app->request->get('switchValue') != ""  ): ?>
                                                   <?= $form->field($model, 'switch_port')->dropDownList(
                                                        ArrayHelper::map(
                                                           \app\models\UsersServicesPackets::getSwitchPort(Yii::$app->request->get('switchValue')),
                                                            'id',
                                                            'port_number'
                                                        ),
                                                        [
                                                            'prompt'=>Yii::t("app","Select")
                                                        ]
                                                    ) ?>
                                                <?php 
                                                    foreach ($form->attributes as $attribute) {
                                                        $attribute = Json::htmlEncode($attribute);
                                                        $this->registerJs("jQuery('form#add-form').yiiActiveForm('add', $attribute);");
                                                    } 
                                                ?>
                                            <?php endif ?>
                                            <?php if (Yii::$app->request->get('port_type')  == "epon" || Yii::$app->request->get('port_type')  == "gpon" ||   Yii::$app->request->get('deviceValue') != "" ): ?>
                                               
                                                   <?= $form->field($model, 'box')->dropDownList(
                                                        ArrayHelper::map(
                                                           \app\models\UsersServicesPackets::getOltBox(Yii::$app->request->get('deviceValue'),$model_user->id),
                                                            'id',
                                                            'box_name'
                                                        ),
                                                        [
                                                        'onchange'=>'
                                                            $(".select_loader").show();
                                                            $.pjax.reload({
                                                                url: "'.Url::to(['adding-packet']).'?id='.Yii::$app->request->get('id').'&service_id='.Yii::$app->request->get('service_id').'&packet_tags='.Yii::$app->request->get('packet_tags').'&port_type='.Yii::$app->request->get('port_type').'&deviceValue='.Yii::$app->request->get('deviceValue').' &box="+$(this).val(),
                                                                container: "#pjax-adding-packet-form-box",
                                                                timeout: 5000
                                                            });
                                                            $(document).on("pjax:complete", function() {
                                                              $(".select_loader").hide();
                                                            });
                                                        ',


                                                            'prompt'=>Yii::t("app","Select")
                                                        ]
                                                    ) ?>
                                                <?php 
                                                    foreach ($form->attributes as $attribute) {
                                                        $attribute = Json::htmlEncode($attribute);
                                                        $this->registerJs("jQuery('form#add-form').yiiActiveForm('add', $attribute);");
                                                    } 
                                                ?>
                                            <?php endif ?>
                                            <?php  Pjax::begin(['id'=>'pjax-adding-packet-form-box','enablePushState'=>true]);  ?>
                                            <?php if ( Yii::$app->request->get('box')  != "" ): ?>
                                                   <?= $form->field($model, 'box_port')->dropDownList(
                                                        ArrayHelper::map(
                                                           \app\models\UsersServicesPackets::getOltBoxPort(Yii::$app->request->get('box')),
                                                            'id',
                                                            'port_number'
                                                        ),
                                                        [
                                                            'prompt'=>Yii::t("app","Select")
                                                        ]
                                                    ) ?>
                                                <?php 
                                                    foreach ($form->attributes as $attribute) {
                                                        $attribute = Json::htmlEncode($attribute);
                                                        $this->registerJs("jQuery('form#add-form').yiiActiveForm('add', $attribute);");
                                                    } 
                                                ?>                                  
                                            <?php endif ?>
                                            <?php Pjax::end(); ?>  


                                        <?php Pjax::end(); ?>  
                                    <?php Pjax::end(); ?>  
                                    <?php 
                                    foreach ($form->attributes as $attribute) {
                                        $attribute = Json::htmlEncode($attribute);
                                        $this->registerJs("jQuery('form#add-form').yiiActiveForm('add', $attribute);");
                                    } 
                                    ?>

                                    <?php endif ?>
                              <?php endif ?>
                                    <?php if ($serviceGet['service_alias'] == "tv"): ?>
                                <?php echo $form->field($model, 'property[card_number]')->textInput(['maxlength' => true])->label(Yii::t("app","Card number"))  ?>
                                    <?php 
                                        foreach ($form->attributes as $attribute) {
                                        $attribute = Json::htmlEncode($attribute);
                                        $this->registerJs("jQuery('form#add-form').yiiActiveForm('add', $attribute);");
                                        } 
                                    ?>
                            <?php endif ?>

                        <?php if ($serviceGet['service_alias'] == "wifi"): ?>

                            <?= $form->field($model, 'packet_tags')->dropDownList(
                                    ArrayHelper::map(
                                        app\models\Packets::find()
                                        ->orderBy(['packet_name'=>SORT_ASC])
                                        ->where(['service_id'=>Yii::$app->request->get('service_id')])->all()
                                        ,'id',
                                        'packet_name'
                                    ),
                                    ['prompt'=>Yii::t("app","Select")]
                                ) ?>
                                <?=$form->field($model, 'price')->textInput()->label(Yii::t('app','Price'));?>

                            <?php 
                                foreach ($form->attributes as $attribute) {
                                    $attribute = Json::htmlEncode($attribute);
                                    $this->registerJs("jQuery('form#add-form').yiiActiveForm('add', $attribute);");
                                } 
                             ?>
                        <?php endif ?>



                        <?php if ($serviceGet['service_alias'] == "voip"): ?>

                            <?= $form->field($model, 'packet_tags')->dropDownList(
                                    ArrayHelper::map(
                                        app\models\Packets::find()
                                        ->orderBy(['packet_name'=>SORT_ASC])
                                        ->where(['service_id'=>Yii::$app->request->get('service_id')])->all()
                                        ,'id',
                                        'packet_name'
                                    ),
                                    ['prompt'=>Yii::t("app","Select")]
                                ) ?>
                                <?=$form->field($model, 'phone_number')->textInput()->label();?>

                                <?=$form->field($model, 'price')->textInput()->label(Yii::t('app','Custom price'));?>

                            <?php 
                                foreach ($form->attributes as $attribute) {
                                    $attribute = Json::htmlEncode($attribute);
                                    $this->registerJs("jQuery('form#add-form').yiiActiveForm('add', $attribute);");
                                } 
                             ?>
                        <?php endif ?>





                    <?php Pjax::end(); ?>  

                    <?php echo $form->field($model, 'created_at')->hiddenInput(['value' =>time() ])->label(false)  ?>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t("app","Add a service"), ['class' => 'btn btn-success']) ?>
                    </div>

            <?php ActiveForm::end(); ?>
        </div>
            </div>
        </div>            
        <?php endif ?>

            <div class="col-lg-12 animatedParent animateOnce z-index-50">
                <div class="panel panel-default animated fadeInUp">
                    <div class="panel-body">
                        <?php if ( count( $UsersServicesPacketsModel ) > 0 ): ?>
                            <div class="table table-striped mb-0">
                                <table class="table">
                                    <thead> 
                                        <tr> 
                                            <th>#</th> 
                                           
                                            <th><?=Yii::t('app','Service') ?></th> 
                                            <th><?=Yii::t('app','Packet') ?></th> 
                                            <th><?=Yii::t('app','Tariff') ?></th> 
                                            <th><?=Yii::t('app','Status') ?></th> 
                                            <?php if (  User::canRoute('/request-order/service-packet-detail') ): ?>
                                            <th><?=Yii::t('app','Detail') ?></th> 
                                            <?php endif ?>
                                            <?php if ( User::canRoute('/users/packet-ajax-status') && $model_user->status != 3 ): ?>
                                            <th><?=Yii::t('app','Enable/Disable') ?></th> 
                                            <?php endif ?>
                                            <?php if ( User::canRoute('/request-order/change-packet') && User::canRoute('/request-order/change-packet-validate') && $model_user->status != 3  ): ?>
                                            <th><?=Yii::t('app','Change') ?></th> 
                                            <?php endif ?>
                                            <?php if ( User::hasPermission('order-packet-deleting') && $model_user->status != 3  ): ?>
                                            <th><?=Yii::t('app','Delete') ?></th> 
                                            <?php endif ?>
                                        </tr> 
                                    </thead> 
                                    <tbody> 
                                        <?php $c = 1;?>
                                        <?php foreach ($UsersServicesPacketsModel as $key => $userPacket): ?>
                                    <tr> 
                                            <td><?=$c++; ?></td> 
                                            
                                            <td><?=$userPacket->service->service_name ?></td> 
                                            <td><?=$userPacket->packet->packet_name ?></td> 
                                            <td><?php
                                                $packetTariff = ( $userPacket->price != 0 || $userPacket->price != null ) ? $userPacket->price : $userPacket->packet->packet_price;
                                          
                                           echo  $packetTariff; ?> <b><?=$currency ?></b></td> 
                                            <td>
                                                <?php 
                                                    if ($userPacket->status == 2) {
                                                    echo Yii::t("app","Deactive");
                                                    }elseif($userPacket->status == 1){
                                                    echo Yii::t("app","Old packets (Active)");
                                                    }elseif ($userPacket->status == 3) {
                                                    echo Yii::t("app","Archive");
                                                    }elseif ($userPacket->status == 0) {
                                                    echo Yii::t("app","New added (Pending)");
                                                    }
                                                ?> 
                                            </td> 
                                            <?php if (  User::canRoute('/request-order/service-packet-detail') ): ?>
                                                <td>
                                                    <?php if ($userPacket->service->service_alias == "internet"): ?>
                                                        <?php $user_inet = \app\models\UsersInet::find()->where(['u_s_p_i'=>$userPacket->id])->one(); ?>

                                                        <a  style=" display: block; text-align: center;" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>//users/check-user-internet?login=<?=$user_inet->login ?>" href="javascript:;" >
                                                          <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                                        </a>

                                                    <?php else: ?>
                                                        <a  style=" display: block; text-align: center;" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/request-order/service-packet-detail?id=<?=$userPacket->id ?>" href="javascript:;" >
                                                          <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                                        </a>
                                                    <?php endif ?>
                                                </td>   
                                            <?php endif ?>
                                            <?php if ( User::canRoute('/users/packet-ajax-status') && $model_user->status != 3 ): ?>
                                                <?php if ($userPacket->service->service_alias == "internet"): ?>
                                                    <?php $user_inet = \app\models\UsersInet::find()->where(['u_s_p_i'=>$userPacket->id])->one(); ?>
                                                    <td>
                                                    <?php  $isChecked = ($user_inet->status == '1') ? "checked" : ""; ?>
                                                        <input name="input_cj" class="stat_us" data-user_id="<?=$userPacket->user_id ?>" data-packet_id="<?=$userPacket->packet->id ?>"
                                                              data-service_id="<?=$userPacket->service_id  ?>"  data-usp-id= "<?=$user_inet->u_s_p_i ?>"
                                                            type="checkbox" <?=$isChecked ?> hidden="hidden"   id="packets_check<?=$user_inet->id ?>">
                                                       <label class="c-switch" for="packets_check<?=$user_inet->id ?>"></label>      
                                                    </td>
                                                <?php endif ?>

                                                <?php if ($userPacket->service->service_alias == "tv"): ?>
                                                    <td>-</td>
                                                <?php endif ?>

                                                <?php if ($userPacket->service->service_alias == "wifi"): ?>
                                                    <td>-</td>
                                                <?php endif ?>
                                            <?php endif ?>  

                                            <?php if ($userPacket->service->service_alias == "voip"): ?>
                                                    <td>-</td>
                                            <?php endif ?>
                                            <?php if ( User::canRoute('/request-order/change-packet') && User::canRoute('/request-order/change-packet-validate')  && $model_user->status != 3  ): ?>
                                                <td class="change-packet">
                                                    <a data-fancybox="" data-type="ajax" data-fancybox data-type="ajax" data-options='{"touch" : false}'  data-src="<?=$langUrl ?>/request-order/change-packet?id=<?=$userPacket['id'] ?>" href="javascript:;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                                    </a>
                                                </td>
                                            <?php endif ?>
                                            <?php if ( User::hasPermission('order-packet-deleting')  && $model_user->status != 3 ) : ?>
                                                <td>
                                                    <a data-fancybox data-src="#hidden-content-<?=$userPacket->id ?>" href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
                                                </td> 
                                            <?php endif ?>
                                        </tr> 

                                        <?php if ( User::hasPermission('order-packet-deleting')  && $model_user->status != 3 ): ?>
                                            <div style="display: none;" id="hidden-content-<?=$userPacket->id ?>">
                                                    <div class="fcc">
                                                       <h2 ><b><?=Yii::t("app","Delete packet") ?> - <?=$userPacket->user->fullname ?></b></h2>
                                                      <p ><?=Yii::t('app', 'Are you sure delete {packet_name} ?', ['packet_name' => $userPacket->packet->packet_name]) ?></p>
                                                      <button class="btn btn-success delete-packet" data-user_id="<?=$userPacket->user_id ?>"  data-service="<?=$userPacket->service_id ?>"  data-packet="<?=$userPacket->packet_id  ?>" data_packet_id_ser="<?=$userPacket->id ?>"   title="<?=Yii::t("app","Delete") ?>" ><?=Yii::t("app","Delete") ?></button>
                                                      <button data-fancybox-close="" class="btn btn-primary"  title="<?=Yii::t('app','Close') ?>" ><?=Yii::t("app","Close") ?></button>           
                                                    </div>
                                            </div> 
                                        <?php endif ?>
       
                                    <?php endforeach ?>
                                    </tbody> 
                                </table>
                            </div>
                        <?php else: ?>
                            <h5 class="text-center"><?=Yii::t('app','Customer doesn\'t have any service') ?></h5>
                        <?php endif ?>

                    </div>
                </div>
            </div>
    </div>
</div>

 <?php
$this->registerJs('




$(document).on("click",".stat_us",function(){

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
    url:"'.$langUrl .'/packet-ajax-status",
    type:"post",
    data:{checked:checked,_user_id:_user_id,_packet_id:_packet_id,_service_id:_service_id,_usp_id:_usp_id},
});
 e.preventDefault();
 return false;
})






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

.table > thead > tr > th, .table > thead > tr > td, .table > tbody > tr > th, .table > tbody > tr > td, .table > tfoot > tr > th, .table > tfoot > tr > td{
        text-align: center;

}


.field-requestorderservice-request_id{    margin-bottom: -15px;}
         .form-select{position: relative;}
.select_loader,.select_loader:after {
    width: 20px;
    height: 20px;
}


.select_loader,
.select_loader:after {
    width: 20px;
    height: 20px;
}
.select_loader{
    display: none;
    margin: 0;
    position: absolute;
    background: white;
    right: 4px;
    top: 7px;
    z-index: 99;
    border-width: 2px;
}

.ball-beat>div{
    background-color:#aaa;
    border-radius:100%
}

@-webkit-keyframes ball-beat{
    50%{
        opacity:.2;
        -webkit-transform:scale(.75);
        transform:scale(.75)
    }
    100%{
        opacity:1;
        -webkit-transform:scale(1);
        transform:scale(1)
    }
}
@keyframes ball-beat{
    50%{
        opacity:.2;
        -webkit-transform:scale(.75);
        transform:scale(.75)
    }
    100%{
        opacity:1;
        -webkit-transform:scale(1);
        transform:scale(1)
    }
}
.ball-beat>div{
    width:15px;
    height:15px;
    margin:2px;
    display:inline-block;
    -webkit-animation:ball-beat .7s 0s infinite linear;
    animation:ball-beat .7s 0s infinite linear
}
.ball-beat>div:nth-child(2n-1){
    -webkit-animation-delay:-.35s!important;
    animation-delay:-.35s!important
}

.animate {
    -webkit-animation-duration: 0.3s;
    animation-duration: 0.3s;
    -webkit-animation-fill-mode: both;
    animation-fill-mode: both;
}

@-webkit-keyframes fadeInUp {
  from {
    opacity: 0;
    -webkit-transform: translateY(20px);
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}
@keyframes fadeInUp {
  from {
    opacity: 0;
    -webkit-transform: translate3d(0, 20px, 0);
    transform: translate3d(0, 20px, 0);
  }

  to {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}
.fadeInUp {
  -webkit-animation-name: fadeInUp;
  animation-name: fadeInUp;
}
.custom-li{
    padding-left: 10px;
    padding-bottom: 5px; 
    font-size: 16px;
    line-height: 35px;
}
.custom-ul{
    padding: 0;
    margin: 0;
    list-style: none;    
}
.custom-span{
    float: right;
    padding-right: 15px;
}


</style>

<?php $this->registerJs('


$(document).on("click","#custom-lp",function(){
    if ( $(this).is(":checked") ){
        $(".lp-container").show()
    }else{
        $(".lp-container").hide()
    }
});

var clickAddingPacket = false;


var addingPacketId = '.Yii::$app->request->get("id").';
var xhrAddingPacket;
var xhrAddingPacketActive=false;
var formAddingPacket = $("form#add-form");

$(document).on("beforeSubmit", function (e) {
    if(!clickAddingPacket){
        clickAddingPacket = true;
        if( formAddingPacket.find(".btn-success").prop("disabled")){
            return false;
        }
        if(xhrAddingPacketActive) { xhrAddingPacket.abort(); }
        xhrAddingPacketActive=true;
        formAddingPacket.find(".btn-success").prop("disabled",true);
            
        xhrAddingPacket = $.ajax({
          url: "'.\yii\helpers\Url::to(["/request-order/adding-packet"]).'?id="+addingPacketId,
          type: "post",
          beforeSend:function(){
            $(".loader").show();
            $(".overlay").addClass("show");
          },
          data: formAddingPacket.serialize(),
          success: function (response) {
              $(".loader").hide();
              $(".overlay").removeClass("show");

            if(response.status == "error"){
                 alertify.set("notifier","position", "top-right");
                 alertify.error(response.message);
            }          

            if(response.status == "success"){
                 window.location.href = response.url;
            }else{
                xhrAddingPacketActive=false;
                formAddingPacket.find(".btn-success").prop("disabled",false);
            }

          }
        }).done(function(){ clickAddingPacket = false; });
        return false;
    }
}); 

$(document).on("click",".delete-packet",function(){
    var url = "/request-order/index";    
    var user_id = $(this).attr("data-user_id");
    var service_id = $(this).attr("data-service");
    var packet_id = $(this).attr("data-packet");
    var id_usrp = $(this).attr("data_packet_id_ser");
    var that = $(this);
   $.ajax({
        url:"'.$langUrl.Url::to('/users/service-delete').'",
        method:"POST",
        data:{user_id:user_id,service_id:service_id,packet_id:packet_id,id_usrp:id_usrp},
        success:function(res){
           if(res.code == "success"){
           window.location.href = url;
           }
        }
    });
 e.preventDefault();
 return false;
});

') ?>


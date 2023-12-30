

  <div class="dropdown grid-visiblity-container" >
    <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
    </button>
    <div class="dropdown-menu grid-visiblity" aria-labelledby="dropdownMenuButton">
      <form class="grid-view-form" >

        <?php foreach ($params as $key => $param): ?>
            <div class="custom-control custom-checkbox">
                <input <?= ( explode("@", $param)[0] == "true" ) ? "checked" : "" ?> name="<?=$key ?>" type="checkbox" value="<?= $param  ?>" class="custom-control-input" id="<?=$key ?>">
                <label class="custom-control-label" for="<?=$key ?>"><?=Yii::t("app",explode("@", $param)[1]) ?></label>
            </div>
        <?php endforeach ?>
        <button type="submit" class="btn btn-info" style="margin-top: 10px;"><?=Yii::t("app","Confrim") ?></button>
      </form>
    </div>
</div>

 <?php

$this->registerJs("

    $('.grid-view-form input').on('change',function(){
        var value = $(this).val();
        var label = value.split('@')[1];
        var checkboxValue = $(this).prop('checked');
        if( checkboxValue == true ){
            $(this).val('true@'+label)
        }else{
            $(this).val('false@'+label)
        }
    })

    let form = $('.grid-view-form');
    form.submit(function (e) {
        e.preventDefault();
        var formData = {};
        form.find(':input').each(function() {
            formData[this.name] = $(this).val();
        });
        $.ajax({
            url:'".$url."',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            method:'POST',
            data:formData,
            success:function(){
                $.pjax.reload({
                    container: '".$pjaxContainer."',
                    timeout: 5000
                });
            }
        });
    });
");
?>
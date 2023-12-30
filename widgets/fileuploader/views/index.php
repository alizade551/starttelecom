<?php
use yii\helpers\Html;
$ph = '';
if ($photos) {
    foreach ($photos as $key => $photo) {
        $ph .= $photo["photo_url"]."@";
    }
}


$lang = (Yii::$app->language == "en") ? "" : "/".Yii::$app->language."/";



?>

    <div class="fileuploader fileuploader-theme-thumbnails">
        <input type="hidden" name="<?=$name ?>" class="files_data" value="<?=$ph?>">
        <input accept="*" id="fileupload" type="file" name="files[]" data-url="<?=$url?>" multiple style="position: absolute; z-index: -9999; height: 0px; width: 0px; padding: 0px; margin: 0px; line-height: 0; outline: 0px; border: 0px; opacity: 0;">
        <div class="fileuploader-items">
            <ul class="fileuploader-items-list clearfix">
							 <li class="fileuploader-thumbnails-input">
                    <div class="fileuploader-thumbnails-input-inner">+</div>
                </li>
                <?php if ($photos): ?>
                    <?php foreach ($photos as $key => $photo): ?>
                        <li class="fileuploader-item" data-img="<?=$photo["photo_url"]?>">
                           <div class="fileuploader-item-inner">
                              <div class="thumbnail-holder">
                                 <div class="fileuploader-item-image"><img src="/uploads/user_photos/<?=$photo["photo_url"]?>?r=<?=time()?>"></div>
                              </div>
                              <div class="progress-holder" style="display: none;">
                                 <div class="fileuploader-progressbar">
                                    <div class="bar" aria-valuenow="100" style="width: 100%;"></div>
                                 </div>
                              </div>
                                <div class="fileuploader-percentage" style="display: none;">100%</div>
                                <div class="fileuploader-loader" style="display: none;"></div>
                              <div class="actions-holder">
                             
                                <a class="fileuploader-action fileuploader-action-fullscreen" href="/uploads/user_photos/<?=$photo["photo_url"]?>"  data-fancybox="images"><i></i></a>
    							<a class="fileuploader-action fileuploader-action-remove" href="<?=$lang ?>/photo-upload?file=<?=$photo["photo_url"]?>"><i class="remove"></i></a>
							  </div>
                           </div>
                        </li>
                    <?php endforeach ?>
                <?php endif ?>
                
               
            </ul>
            <div class="clearfix"></div>
        </div>
      <div style="margin-left:0 !important;margin-top:7px;color: black" class="help-block"></div>
    </div>


<?php

 $this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
 \app\widgets\fileuploader\FileuploaderAsset::register($this);


$this->registerJs('


$(document).on("click",".fileuploader-thumbnails-input-inner",function(){
    $("#fileupload").trigger("click");
});
$(document).on("click",".fileuploader-action-remove",function(e){
    e.preventDefault();
    var that = $(this);
		that.closest(".fileuploader-item").remove();
    $.ajax({
        url:$(this).attr("href"),
        type:"DELETE",
        success:function(){
            that.closest(".fileuploader-item").remove();
            var order = $(".fileuploader-items-list").sortable("toArray",{attribute: "data-img"});
            var positions = order.join("@");
            $(".fileuploader .files_data").attr("value",positions);
        }
    });
});





$(document).on("click",".fileuploader-action-rotate-right",function(e){
    e.preventDefault();
    var that = $(this);
		that.closest(".fileuploader-item").find(".fileuploader-loader").show();
    $.ajax({
        url:$(this).attr("href"),
        type:"GET",
        success:function(){
            that.closest(".fileuploader-item").find("img").attr("src",that.attr("data-thumbnail")+"?r"+(Math.random() * 10000000));
						that.closest(".fileuploader-item").find(".fileuploader-loader").hide();
        }
    });
});



  $( ".fileuploader-items-list" ).sortable({
      items: "li:not(.fileuploader-thumbnails-input)",
        handle: ".fileuploader-item-inner",
        cancel: ".fileuploader-action-remove, .fileuploader-action-rotate-left",
        placeholder: "fileuploader-item fileuploader-sorter-placeholder",
        start: function( event, ui ) {
            $("<div/>").appendTo(ui.placeholder[0]);
        
            $(ui.placeholder[0]).find("div").height(ui.item.height()).width(ui.item.width());

          
        },
        update: function(event,ui){

                 var order = $(this).sortable("toArray",{attribute: "data-img"});
            var positions = order.join("@");

            $(".fileuploader .files_data").attr("value",positions);
        },
    });

$( ".fileuploader-items-list" ).disableSelection();


    var fi = $("#fileupload"); 

    fi.fileupload({
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 999000,
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        previewMaxWidth: 120,
        previewMaxHeight: 95,
        previewCrop: true,
        previewThumbnail:false,
          previewOrientation: false,
				//sequentialUploads: true,
        dataType: "json",    
    });
    
    var maxNumberOfFiles = parseInt("'.Yii::$app->params['maxNumberOfFiles'].'");
    
    fi.on("fileuploadadd", function (e, data) {

          if(($("ul.fileuploader-items-list > li").length - 1) >= maxNumberOfFiles){
            $.fancybox.open({
                src  : "#fileupload-alert",
                type : "inline",
            });
            return false;
          }

            data.context = $("<li/>").addClass("fileuploader-item").appendTo(".fileuploader-items-list").attr("data-status","processing");
            var item_inner = $("<div/>").addClass("fileuploader-item-inner").appendTo(data.context);

            //img
            var item_thumb = $("<div/>").addClass("thumbnail-holder").appendTo(item_inner);
            var item_thumb_image = $("<div/>").addClass("fileuploader-item-image").appendTo(item_thumb);

            //progress
          //  var item_progress_holder = $("<div/>").addClass("progress-holder").appendTo(item_inner);
           // var item_progress_bar = $("<div/>").addClass("fileuploader-progressbar").appendTo(item_progress_holder);
           //var item_progress_bar = $("<div/>").addClass("bar").appendTo(item_progress_bar);
						
			//loader
            var loader = $("<div/>").addClass("fileuploader-loader").appendTo(item_inner);

            //percantage
          //  var percentage = $("<div/>").addClass("fileuploader-percentage").appendTo(item_inner);
			
            //actions
            var item_actions_holder = $("<div/>").addClass("actions-holder").appendTo(item_inner);

            // var item_actions_rotate_right = $("<a/>").addClass("fileuploader-action fileuploader-action-rotate-right").css({"display":"none"}).appendTo(item_actions_holder);
            // var item_actions_rotate_right_i = $("<i/>").appendTo(item_actions_rotate_right);

//             var item_actions_sort = $("<a/>").addClass("fileuploader-action fileuploader-action-sort").appendTo(item_actions_holder);
//             var item_actions_sort_i = $("<i/>").appendTo(item_actions_sort);
            var item_actions_remove = $("<a/>").addClass("fileuploader-action fileuploader-action-remove").appendTo(item_actions_holder);
            var item_actions_remove_i = $("<i/>").addClass("remove").appendTo(item_actions_remove);

    });
		
		fi.on("fileuploadprocessalways", function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]),
						img = $("<img/>");
        if (file.preview) {
		  		img.attr("src", file.preview.toDataURL());
        }
				node.find(".fileuploader-item-image").append(img);

    })


    fi.on("fileuploadprogress", function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        if (data.context) {
            data.context.each(function () {
               // if(progress >=99){progress = 99;}
               // $(this).find(".bar").attr("aria-valuenow", progress).css("width",progress + "%");               
               // $(this).find(".fileuploader-percentage").show().text(progress + "%");           
            });
        }
    });





    

    fi.on("fileuploaddone", function (e, data) {

           
        $.each(data.result.files, function (index, file) {
        $(data.context.children()[index]).closest(".fileuploader-item").attr("data-status","done");
            if (file.url) {
               $(data.context.children()[index]).closest(".fileuploader-item").attr("data-img",file.name);

            var order = $(".fileuploader-items-list").sortable("toArray",{attribute: "data-img"});
            var positions = order.join("@");
            $(".fileuploader .files_data").attr("value",positions);

        
        
                $(data.context.children()[index]).find(".fileuploader-action-rotate-left").attr("href","'.$lang.'"+file.deleteUrl+"&rotate=left").attr("data-thumbnail",file.thumbnailUrl).show();
                $(data.context.children()[index]).find(".fileuploader-action-rotate-right").attr("href","'.$lang.'"+file.deleteUrl+"&rotate=right").attr("data-thumbnail",file.thumbnailUrl).show();
           

                $(data.context.children()[index]).find(".fileuploader-action-remove").attr("href","'.$lang.'"+file.deleteUrl);
               // $(data.context.children()[index]).find(".fileuploader-item-image img").attr("src",file.thumbnailUrl);
                        // $(data.context.children()[index]).find(".progress-holder").hide();
                                $(data.context.children()[index]).find(".fileuploader-loader").hide();

            } else if (file.error) {
               var error = $("<span class=\"fileuploader-danger\"/>").html("<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" version=\"1.1\" id=\"Capa_1\" x=\"0px\" y=\"0px\" viewBox=\"0 0 485.811 485.811\" style=\" display: block; text-align: center; margin: 0px auto; margin-bottom: 5px;\" xml:space=\"preserve\" width=\"30px\" height=\"30\"> <g> <path d=\"M476.099,353.968l-170.2-294.8c-27.8-48.7-98.1-48.7-125.8,0l-170.3,294.8c-27.8,48.7,6.8,109.2,62.9,109.2h339.9 C468.699,463.168,503.899,402.068,476.099,353.968z M242.899,397.768c-14.8,0-27.1-12.3-27.1-27.1s12.3-27.1,27.1-27.1 c14.8,0,27.1,12.3,26.5,27.8C270.099,385.468,257.099,397.768,242.899,397.768z M267.599,222.568c-1.2,21-2.5,41.9-3.7,62.9 c-0.6,6.8-0.6,13-0.6,19.7c-0.6,11.1-9.3,19.7-20.4,19.7s-19.7-8-20.4-19.1c-1.8-32.7-3.7-64.8-5.5-97.5 c-0.6-8.6-1.2-17.3-1.9-25.9c0-14.2,8-25.9,21-29.6c13-3.1,25.9,3.1,31.5,15.4c1.9,4.3,2.5,8.6,2.5,13.6 C269.499,195.468,268.199,209.068,267.599,222.568z\" fill=\"#D80027\"></path> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>"+file.error); //error text
               $(data.context.children()[index]).append(error); //add to data context
               		$(data.context.children()[index]).find(".progress-holder").hide();
								$(data.context.children()[index]).find(".fileuploader-percentage").hide()
               
            }
        });
    });
    
    fi.on("fileuploadfail", function (e, data) {
     console.log(data.jqXHR.responseText);

    });

  var csi = true;
  setInterval(function(){
var btn_dis = false;
$( ".fileuploader-items li" ).each( function( index, element ){
       if(!$(this).hasClass(".fileuploader-thumbnails-input")){
        if($(this).attr("data-status") == "processing"){
            btn_dis = true; csi = true;
        }
       }
    });

if(btn_dis){
    $("#add-form").find("button").prop("disabled",true);
    $("#add-form").find("span.image-processing-label").fadeIn(200);
}else if(csi){
    csi = false;
     $("#add-form").find("button").prop("disabled",false);
     $("#add-form").find("span.image-processing-label").fadeOut(200);
}
    },200);
        


	');

?>
<div style="display: none;" id="fileupload-alert">
    <p><?=Yii::t('app','Şəkillərin maksimum sayı {count} olmalıdır!',['count'=>Yii::$app->params['maxNumberOfFiles']])?></p>
</div>

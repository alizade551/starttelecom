$('.scrollTop').click(function() {
    $("html, body").animate({scrollTop: 0});
});

$('.navbar .dropdown.notification-dropdown > .dropdown-menu, .navbar .dropdown.message-dropdown > .dropdown-menu ').click(function(e) {
    e.stopPropagation();
});

// $('.bs-tooltip').tooltip();

$('.bs-popover').popover();

// $('.t-dot').tooltip({
//     template: '<div class="tooltip status rounded-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
// })
$(".overlay ").on("click",function(){
    $(".loader").hide()
})

$(function(){
    $(document).on("click",".modal-d",function(){
       const modalTitle = $(this).attr('title');
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            beforeSend: function() {
                $(".loader").show();
                $(".overlay").addClass("show");
            },
            complete: function() {
                $(".loader").hide();
                $(".overlay").removeClass("show");
            },
            success: function(data) {
                 $('#modal').find('.modal-title').text(modalTitle);
                 $('#modal').modal('show').find('#modalContent').html(data)
            }
        });
       return false;       
    });
}); 
function openRightMenu() {
    document.getElementById("rightMenu").style.display = "block";
}
function closeRightMenu() {
    document.getElementById("rightMenu").style.display = "none";
}


function openLeftMenu() {
    document.getElementById("leftMenu").style.display = "block";
}
function closeLeftMenu() {
    document.getElementById("leftMenu").style.display = "none";
}

function openFilterSearch() {
    document.getElementById("filter_search").style.display = "block";
}
function closeFilterSearch() {
    document.getElementById("filter_search").style.display = "none";
}


(function($) {

    $('.profile').hover(function () {
        $('#action-account').show();
    })

    $('.profile').mouseleave(function () {
        $('#action-account').hide();
    })

    var fullName = $('.fullName').text().split(' ');

    var miniName = "";
    if(fullName.length >= 1){
        miniName = fullName[0].substr(0, 1);
    }

    if(fullName.length > 1){
        miniName += fullName[1].substr(0, 1);
    }
    $('.mini-name').html(miniName.toUpperCase());


    $('.tbl_cart_remove').on('click', function () {
        var $object  = $(this).parent().parent().parent();
        $object.remove();
    });


})(jQuery);
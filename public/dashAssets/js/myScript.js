(function($) {
    $('.deleteImg').click(function () {
        $(this).parent().remove();
    })
})(jQuery);


//---------------------------------------------------------------------------------------


//--------------- edit images ----------------------

function testCountImages() {
    var count = $('.item-image').length;

    if(count >= 5){
        $('.files').hide();
        return false;
    }else{
        $('.files').show();
        return true
    }
}
testCountImages();

var countPic = 0,reader = new FileReader(),currantFile;

function previewFile() {
    countPic++;
    reader.onloadend = function () {
        $('<div id="new_pic_' + countPic + '" class="item-image">' +
            '<button type="button" class="close deleteFile" onclick="deleteFile('+ countPic+ ')">&times;</button>' +
            '<img src="' + reader.result + '">' +
            '</div>').insertBefore($('.files'));

        testCountImages();
    }

    reader.readAsDataURL(currantFile.files[0]);

    currantFile.classList.remove("current-file");
    currantFile.setAttribute("id", "new_file_" + countPic);

    $('#divFiles').append('<input type="file" name="pic[]" class="current-file" onchange="previewFile()" accept="image/*">')

}

function deleteFile(del) {
    document.getElementById("new_pic_"+del).remove();
    document.getElementById("new_file_"+del).remove();
    testCountImages();
}

function inputClick() {
    if(testCountImages()){
        currantFile = document.getElementsByClassName("current-file")[0];
        currantFile.click()
    }
}

//--------------- edit images ----------------------


//-------- href delete -------------
function deleteAction(link) {
    document.getElementById("linkDelete").href = link;
}
//-------- href delete -------------


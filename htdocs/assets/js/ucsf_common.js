function getParameterByName(name, url) {
    if (!url) {
        url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
    results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function ShowBusy() {
    if ($('body').find('#resultLoading').attr('id') != 'resultLoading') {
        $('body').append('<div id="resultLoading" style="display:none" ><img src="./assets/images/ajax-loader.gif" class="imgloader" alt="loading"></div>');
    }

    $('#resultLoading .bg').height('100%');
    $('#resultLoading').fadeIn(300);
    $('body').css('cursor', 'wait');
}

function HideBusy() {
    $('#resultLoading .bg').height('100%');
    $('#resultLoading').fadeOut(300);
    $('body').css('cursor', 'default');
}


function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}



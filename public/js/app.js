function showLoader() {
    $("#app").css("opacity", 0.3);
    $('#loader').show();
    $('#app').css('pointer-events', 'none');
}

function hideLoader() {
    $("#app").css("opacity", 1.0);
    $('#loader').hide();
    $('#app').css('pointer-events', 'auto');
}

function moveToTop() {
    window.scrollTo(0, 0);
}

function openModal(id) {
    $('#' + id).modal('show');
}

function closeModal(id) {
    $('#' + id).modal('hide');
}

function columnToKey(array, key = 'id') {
    result = [];
    for (let item of array) {
        result[item[key]] = item;
    }
    return result;
}

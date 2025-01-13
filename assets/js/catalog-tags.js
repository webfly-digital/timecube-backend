document.addEventListener('DOMContentLoaded', function(){
    var tagsInputs = document.querySelectorAll('.tags-list input');
    for (var i = 0; i < tagsInputs.length; i++) {
        tagsInputs[i].addEventListener('change', changeTagsFilter);
    }
});

function changeTagsFilter(e) {
    var form = document.getElementById(e.target.dataset.parent),
        data = new FormData(form),
        query = getQueryString(data),
        currPage = location.href.split('?')[0];

    /*Добавляем выбранные опции из умного фильтра*/
    var smartFilterForm = document.querySelector('.smart-filter-form');
    if (false) {
        var sfData = new FormData(smartFilterForm),
            sfQuery = getQueryString(sfData);
        sfQuery = sfQuery.replace('?', '&');
        query += sfQuery;
    }
    location.href = currPage + query;
}

function getQueryString(formData){
    var pairs = [];
    for (var [key, value] of formData.entries()) {
        pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
    }
    return '?' + pairs.join('&');
}
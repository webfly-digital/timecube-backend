function addFavorite(event) {
    var btn = event.currentTarget;
    var pid = btn.dataset.pid;
    BX.ajax.runComponentAction('webfly:favorites', 'addFavorite', {
        mode: 'class',
        data: {pid: pid},
    }).then(function (response) {
        WF_FAVORITES = response.data.favorites;
        WF_FAVORITES_COUNTER = response.data.count;
        //console.log(WF_FAVORITES);
        updateCounter();
        btn.classList.toggle('active');
    }, function (response) {
        console.log(response);
    });
}

function updateCounter() {
    var counter = document.getElementById('wf_favorites_counter');
    var counterMobile = document.getElementById('wf_favorites_counter_mobile');
    if (counter) {
        counter.innerHTML = WF_FAVORITES_COUNTER;
        if (WF_FAVORITES_COUNTER > 0) {
            counter.style.display = '';
        } else {
            counter.style.display = 'none';
        }
    }
    if (counterMobile) {
        counterMobile.innerHTML = WF_FAVORITES_COUNTER;
        if (WF_FAVORITES_COUNTER > 0) {
            counterMobile.style.display = '';
        } else {
            counterMobile.style.display = 'none';
        }
    }
}
(function () {
    var script = document.currentScript;

    if (script && script.dataset.conflict === '1') {
        alert('Booking tidak dapat disetujui karena jadwal bentrok!');
        window.location = 'index.php?page=booking';
        return;
    }

    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });
})();

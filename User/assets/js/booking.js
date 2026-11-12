document.querySelectorAll('.jam-input').forEach(function (input) {

    input.addEventListener('input', function () {

        var angka = this.value.replace(/\D/g, '').slice(0, 4);

        if (angka.length > 2) {
            angka = angka.slice(0, 2) + ':' + angka.slice(2);
        }

        this.value = angka;
    });

});


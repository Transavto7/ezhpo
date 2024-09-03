$(document).ready(function () {
    const ui = {
        driverId: $('input[name="driver_id"]'),
        date: $('input[name="anketa[0][date]"]'),
        type: $('select[name="anketa[0][type_view]"]')
    }

    ui.driverId.keyup(function (e) {
        const driverId = $(this).val()

        if (driverId.length < 6) {
            return
        }

        isDuplicate()
    })

    ui.date.change(function () {
        isDuplicate()
    })

    ui.type.change(function () {
        isDuplicate()
    })

    function isDuplicate() {
        if (! isValid()) {
            return
        }

        axios
            .get('/api/sdpo/forms/medic/duplicates', {
                params: {
                    driverId: ui.driverId.val(),
                    date: ui.date.val(),
                    type: ui.type.val(),
                },
                headers: {
                    Authorization: 'Bearer ' + API_TOKEN
                }
            })
            .then(response => {
                const { data } = response

                console.log(data)
            })
    }

    function isValid() {
        return !!(ui.driverId.val().length >= 6 && ui.date.val() && ui.type.val())
    }
})

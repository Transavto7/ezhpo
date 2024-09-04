$(document).ready(function () {
    const ui = {
        driverId: $('input[name="driver_id"]'),
    }

    let timer

    ui.driverId.keyup(function (e) {
        clearTimeout(timer)

        timer = setTimeout(() => {
            const $dates = $('.anketa.anketa-fields').find('.inspection-date')
            let hasDuplicate = false
            for (let i = 0; i < $dates.length; i++) {
                const $date = $($dates[i])
                isDuplicate($date)
                hasDuplicate = window.hasDuplicates
            }
            window.hasDuplicates = hasDuplicate
        }, 500)
    })

    $(document).on('change', '.inspection-date', function () {
        const $date = $(this)
        isDuplicate($date)
    })

    $(document).on('change', '.type-view', function () {
        const $type = $(this)
        const $date = $type.closest('.cloning').find('.inspection-date')
        isDuplicate($date)
    })

    function isDuplicate($date) {
        const $type = $date.closest('.cloning').find('.type-view')

        if (! isValid($date, $type)) {
            $type.next().addClass('d-none')
            return
        }

        axios
            .get('/api/sdpo/forms/medic/duplicates', {
                params: {
                    driverId: ui.driverId.val(),
                    date: $date.val(),
                    type: $type.val(),
                },
                headers: {
                    Authorization: 'Bearer ' + API_TOKEN
                }
            })
            .then(response => {
                const { data } = response

                if (data.hasDuplicates) {
                    $type.next().removeClass('d-none')
                    window.hasDuplicates = true
                    return
                }
                window.hasDuplicates = false
                $type.next().addClass('d-none')
            })
    }

    function isValid($date, $type) {
        return !!(ui.driverId.val().length >= 6 && $date.val() && $type.val())
    }

    $('#ANKETA_FORM').submit(function (e) {
        if (! window.hasDuplicates) {
            return
        }
        e.preventDefault()
        document.querySelector('#page-preloader').classList.add('hide')

        window.swal.fire({
            title: 'Найдены дубликаты!',
            text: 'Обратите внимание, данные осмотров совпадают с уже существующими. Подтвердите действие.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Продолжить',
            cancelButtonText: "Отмена",
        }).then(function (result) {
            if (result.isConfirmed) {
                window.hasDuplicates = false
                $('#ANKETA_FORM').trigger('submit')
            } else {
                document.querySelector('#page-preloader').classList.add('hide')
            }
        })
    })
})

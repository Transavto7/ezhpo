$(document).ready(function () {
    const ui = {
        driverId: $('input[name="driver_id"]'),
    }
    window.duplicates = {}

    let timer

    ui.driverId.keyup(function (e) {
        clearTimeout(timer)

        timer = setTimeout(() => {
            const $dates = $('.anketa.anketa-fields').find('.inspection-date')

            for (let i = 0; i < $dates.length; i++) {
                const $date = $($dates[i])
                isDuplicate($date)
            }
        }, 500)
    })

    $(document).on('keyup', '.inspection-date', function () {
        changeDate(this)
    })

    $(document).on('change', '.inspection-date', function () {
        changeDate(this)
    })

    let dateTimer

    function changeDate(element) {
        clearTimeout(dateTimer)

        dateTimer = setTimeout(() => {
            const $date = $(element)
            isDuplicate($date)
        }, 500)
    }

    $(document).on('change', '.type-view', function () {
        const $type = $(this)
        const $date = $type.closest('.cloning').find('.inspection-date')
        isDuplicate($date)
    })

    function isDuplicate($date) {
        const $type = $date.closest('.cloning').find('.type-view')
        const formType = $('input[name="type_anketa"]').val()
        if (! isValid($date, $type)) {
            $type.next().addClass('d-none')
            return
        }

        axios
            .get('/api/sdpo/forms/duplicates', {
                params: {
                    driverId: ui.driverId.val(),
                    date: $date.val(),
                    type: $type.val(),
                    formType: formType,
                },
                headers: {
                    Authorization: 'Bearer ' + API_TOKEN
                }
            })
            .then(response => {
                const { data } = response

                if (data.hasDuplicates) {
                    $type.next().removeClass('d-none')
                    window.duplicates[$date.attr('name')] = true
                    return
                }
                delete window.duplicates[$date.attr('name')]
                $type.next().addClass('d-none')
            })
    }

    function isValid($date, $type) {
        return !!(ui.driverId.val().length >= 6 && $date.val() && $type.val())
    }

    $(document).on('click', '.anketa-delete', function () {
        const name = $(this).closest('.cloning').find('.inspection-date').attr('name')
        delete window.duplicates[name]
    })

    $('#ANKETA_FORM').submit(function (e) {
        if (! Object.keys(window.duplicates).length) {
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
                window.duplicates = {}
                $('#ANKETA_FORM').trigger('submit')
            } else {
                document.querySelector('#page-preloader').classList.add('hide')
            }
        })
    })
})

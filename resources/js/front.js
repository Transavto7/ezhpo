import axios from 'axios'
import swal from 'sweetalert2'
import Swal2 from 'sweetalert2'
import {v4 as uidv4} from 'uuid'
import {ApiController} from "./components/ApiController";

require('./init-plugins')
require('chosen-js')
require('croppie')
require('suggestions-jquery')

$.fn.select2.amd.require(['select2/selection/search'], function (Search) {
    Search.prototype.searchRemoveChoice = function (decorated, item) {
        this.trigger('unselect', {
            data: item
        });

        this.$search.val('');
        this.handleSearch();
    };
});

function toggleAnketaCloneButton(state) {
    let button = $('#ANKETA_CLONE_TRIGGER');
    if (state === true) {
        return button.hide();
    }
    return button.show();
}

$(document).ready(function () {

    $('div.form-group').each(function (i, el) {
        $(el).has(':input[required]').addClass('required')
    });

    const Toast = swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', swal.stopTimer)
            toast.addEventListener('mouseleave', swal.resumeTimer)
        }
    })

    let loading = false
    let pprResult = false
    let anketa_type = $('#ANKETA_FORM_VIEW').data('anketa');

    switch (anketa_type) {
        case ('profile.ankets.tech') :
            pprResult = ($('#point_reys_control').val() === 'Пройден');
            toggleAnketaCloneButton(!pprResult);
            $(document).on('change', '#point_reys_control', function (event) {
                pprResult = ($(event.target).val() === 'Пройден');
                toggleAnketaCloneButton(!pprResult);
            });
            break;
        case ('profile.ankets.medic') :
            pprResult = ($('#med_view').val() === 'В норме');
            toggleAnketaCloneButton(!pprResult);
            $(document).on('change', '#med_view', function (event) {
                pprResult = ($(event.target).val() === 'В норме');
                toggleAnketaCloneButton(!pprResult);
            });
            break;
    }


    $(document).on('click', '.reload-filters', function (event) {
        const btn = $(event.target);
        if (location.pathname.indexOf('home') > -1) {
            btn.disabled = true;
            btn.children('span.spinner').removeClass('d-none');
            $('#filter-group-2-tab').removeClass('active');
            $('#filter-group-1-tab').addClass('active');

            let path = location.origin + location.pathname;

            if (!path.endsWith('/')) {
                path += '/';
            }

            path += 'filters';
            $.get(path).done(response => {
                if(response) {
                    $('#filter-groupsContent').html(response)
                    LIBS.initChosen()
                }
            });
        }
    });

    $(document).on('input', '.select2-search__field', async function(event) {
        let search = event.target.value;
        let select = $(event.target).parents('.select2')?.parent()?.children('.filled-select2');

        if (select.length < 1) {
           const id = $(event.target).attr('aria-controls');
           select = $(`.select2-selection[aria-owns="${id}"]`).parents('.select2')?.parent()?.children('.filled-select2');
        }

        const model = select.attr('model');
        const concat = select.attr('field-concat') || false;
        const concatField = select.attr('field-concat-name') || 'hash_id';
        const field = select.attr('field');
        const key = select.attr('field-key');

        if (!model) {
            return;
        }

        if (loading) {
            loading = false;
            return;
        }

        loading = true;
        await API_CONTROLLER.getFindModel({
            model,
            params: {
                search,
                field,
                key,
            }
        }).then(({ data }) => {
            data.forEach((element => {
                const value = element[key];
                let text = element[field];

                if (concat) {
                    text = '[' + element[concatField] + '] ' + text;
                }

                const exist = select.children('option');
                for(let i = 0; i < exist.length; i++) {
                    if (exist[i].value == value) {
                        return;
                    }
                }

                select.append($('<option>', {
                    value,
                    text
                }));
            }));
        });

        $(event.target).trigger('input');
    })

    const API_CONTROLLER = new ApiController(),
        API = API_CONTROLLER.client

    const LIBS = {
        docFields: {
            'особенности_поведения': [
                'напряженность, раздражительность, суетливость',
                'иногда речь приобретает скандальный оттенок',
                'повышенная отвлекаемость',
                'попытки диссимуляции',
                'заторможенность, замкнутость, апатия',
                'нарушения ориентировки, непонимание смысла вопросов',
                'фрагментарность высказываний',
                'нарушение последовательности изложения мысли',
                'резкая заторможенность, сонливость',
                'агрессия'
            ],

            'жалобы': [
                'отсутствуют',
                'наличие следов от инъекций',
                'присутствие расчесов, ссадин'
            ],

            'кп_окраска': [
                'гиперемированы',
                'бледные',
                'без особенностей'
            ],

            'кп_наличие_повреждений': [
                'отсутствуют',
                'наличие следов от инъекций',
                'присутствие расчесов, ссадин'
            ],

            'слизистые': [
                'гиперемированы',
                'инъецированы',
                'желтушность',
                'без особенностей'
            ],

            'особенности_походки': [
                'шаткая',
                'разбрасывание при ходьбе',
                'походка ровная',
                'неустойчивость при стоянии и ходьбе',
                'отчетливые нарушения координации движений'
            ],

            'оп_точность': [
                'неточность выполнения движений и координарных проб',
                'точное выполнение движений и координарных проб'
            ],

            'оп_тремор_пальцев': [
                'присутствует',
                'отсутствует'
            ],

            'оп_тремор_век': [
                'присутствует',
                'отсутствует'
            ],

            'оп_наличие_запаха': [
                'запах алкоголя изо рта',
                'ацетона',
                'жженой резины или пластмассы',
                'уксуса',
                'серы',
                'отсутствует',
            ],

            'экспресс_тест_мочи': [
                'не проводились',
                'в приложении протокол тестирования наркотических веществ в моче №',
            ],

            'предв_закл': [
                'алкогольное опьянение',
                'установлен факт потребления алкоголя',
                'трезв',
            ],

            'примечания': [
                'особых отметок нет.',
                'водителю протокол контроля трезвости был зачитан вслух, от подписи отказался',
            ]
        },

        initChosen () {
            $('.filled-select2').select2({
                placeholder: 'Выберите значение',
                language: {
                    noResults: function (params) {
                        return "Совпадений не найдено";
                    }
                },
                allowClear: false
            });

            $('.js-chosen').chosen({
                width: '100%',
                search_contains: true,
                no_results_text: 'Совпадений не найдено',
                placeholder_text_single: 'Выберите значение',
                placeholder_text_multiple: 'Выберите значения',
                reset_search_field_on_update: false,
            });
        },

        initDoc () {
            $('.Doc textarea').each(function () {
                let name = this.name

                $(this).click(async function () {
                    if(LIBS.docFields[name]) {
                        let df = LIBS.docFields[name]

                        const { value: dataField } = await swal.fire({
                            input: 'select',
                            inputOptions: df,
                            inputPlaceholder: 'Выберите опцию',
                            showCancelButton: true
                        })

                        if(dataField) {
                            this.value = df[dataField]
                        }
                    }
                })
            })

            /**
             * Сохраняем документ
             */
            $('#DOC_FORM').each(function () {
                let protokol = $(this).data('protokol')

                if(protokol) {
                    for(let i in protokol) {
                        if(i !== 'id') {
                            let inp = $(this).find(`*[name="${i}"]`)

                            inp.val(protokol[i])
                        }
                    }
                }

                $(this).submit(function (e) {
                    e.preventDefault()

                    let data = {}

                    let id = this.id.value;

                    $(this).find('input,textarea').each(function () {
                        if(this.name) {
                            data[this.name] = this.value
                        }
                    })

                    API_CONTROLLER.updateModelProperty({
                        item_model: 'Anketa',
                        item_field: 'protokol_path',
                        item_id: id,
                        new_value: JSON.stringify(data)
                    }).then(response => {
                        window.print()
                    })
                })
            })
        },

        initAll () {
            LIBS.initDoc()
            LIBS.initChosen()
        }
    }

    function initCroppies ()
    {
        let Croppies = {}

        $('.croppie-demo').each(function () {
            let id = $(this).data('croppieId')

            Croppies[id] = $(this).croppie({
                enableOrientation: true,
                viewport: {
                    width: 170,
                    height: 170,
                    type: 'circle' // or 'square'
                },
                boundary: {
                    width: 200,
                    height: 200
                }
            })
        })

        $('[id*="croppie-input"]').on('change', function () {
            let reader = new FileReader(), croppId = this.id.replace('croppie-input', '')

            reader.onload = function (e) {
                $('#croppie-block' + croppId).show()

                Croppies[croppId].croppie('bind', {
                    url: e.target.result
                });
            }

            reader.readAsDataURL(this.files[0]);

            this.files = null
            this.value = ''
        });

        $('.croppie-save').click(function () {
            let id = $(this).data('croppieId')

            Croppies[id].croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(image => {
                //$('#croppie-block' + id).hide()

                // success message
                swal.fire({
                    title: 'Изображение обрезано',
                    icon: 'info'
                });

                $(`#croppie-result-base64${id}`).val(image)
                $('#croppie-block' + id).hide()
            })
        })

        $('.croppie-delete').click(function () {
            let id = $(this).data('croppieId')
            let input = $(`#croppie-input${id}`);

            if(input && id) {
                input[0].files = null;
                input[0].value = '';

                $(`#croppie-result-base64${id}`).val('')
                $('#croppie-block' + id).hide()
            }
        })
    }
    initCroppies()

    // Подгрузка в полей в Журналах: CHOSEN
    if(location.pathname.indexOf('home') > -1) {
        let path = location.origin + location.pathname;

        if (!path.endsWith('/')) {
            path += '/';
        }

        path += 'filters' + location.search;

        $.get(path).done(response => {
            if(response) {
                $('#filter-groupsContent').html(response)
                LIBS.initChosen()
            }
        });
    }


    /**
     * Показываем данные в сущностях в карточках Анкеты
     * @param model
     * @param data
     */
    const showAnketsCardDBitemData = async (model, data, fieldsValues, fullData) => {
        if(data) {
            let dbItemId = 'CARD_' + model.toUpperCase(), msg = `<form id="form_${model}">`,
                inputClass = model + '_' + 'input'

            $(`#${dbItemId}`).html('<b class="text-info">Загружаем данные...</b>')

            /**
             * Вставляем поля
             */
            for(let i in data) {
                let fvItem = fieldsValues[i]

                if(fvItem
                || i === 'contract'
                || i === 'contracts'
                ) {
                   if(i != 'id' && i != 'hash_id') {
                       let field = '',
                           otherHtmlItems = '',
                           fId = uidv4(),
                           isBlocked = fullData.blockedFields.includes(i) ? 'disabled' : ''

                       if(isBlocked === ''){
                           otherHtmlItems = `<a href="" style="font-size: 10px; color: #c2c2c2;" onclick="$('#${fId}').val('').trigger('change'); return false;"><i class="fa fa-trash"></i> Очистить</a>`
                       }
                       if(i === 'contract_id' || i === 'contract' || i === 'contracts'){
                           // fvItem['type'] = 'text';
                           continue;
                       }

                       if(i === 'products_id'){
                           // for company
                           // if(fieldsValues.contracts){
                           //     fieldsValues.contracts
                           // }

                       }

                       if(i === 'products_id' && data.contract){
                           // driver & auto
                           msg += `
                               <p class="text-small m-0">Договор:</p>`;
                           if(data.contract.length != 0){
                               msg +=  `
                            <ul class="list-group my-2">
                                <li style="padding: 0;" class="text-small list-group-item list-group-item-action list-group-item-success">
                                <b>${data.contract.name_with_dates}</b>

                            <ul class="list-group">`;
                               if(data.contract.services){

                                   data.contract.services.map((service) => {
                                       msg +=  `<li style="padding: 0; font-size: 0.8em" class="list-group-item text-small list-group-item-action list-group-item-secondary">${service.name}</li>`;
                                   })
                               }

                               msg +=  `
                            </ul></li>
                            </ul>`;
                           }else{
                               msg += `
                               <p class="text-small">-- Отсутствует --</p>`;
                           }

                           // continue;
                         }else if(i === 'products_id' && data.contracts){
                           // copmany
                           msg += `
                               <p class="text-small m-0">Договор:</p>`;
                           if(data.contracts.length != 0){

                               msg += `<ul class="list-group my-2">`;
                               data.contracts.map((contract) => {
                                   if(contract.services){

                                       msg += `

                                    <li style="padding: 0;" class=" text-small list-group-item list-group-item-action list-group-item-success"><ul class="list-group">
                                    <b>${contract.name_with_dates}</b>`;
                                       contract.services.map((service) => {
                                           msg += `
                                    <li style="padding: 0; font-size: 0.8em" class="list-group-item text-small list-group-item-action list-group-item-secondary">
                                    ${service.name}</li>`;
                                       })
                                       msg += `</ul></li>`;
                                   }

                               })
                               msg += `</ul>`;
                           }else{

                               msg += `
                               <p class="text-small">-- Отсутствует --</p>`;
                           }
                           // continue;
                       }
                       // else{
                           if(fvItem['type'] === 'select') {
                               await API_CONTROLLER.getFieldHTML({ field: i, model, default_value: encodeURIComponent(data[i]) }).then(response => {
                                   field = response.data
                               })
                           } else {
                               if(i === 'note' || i === 'comment') {
                                   field = `<textarea id="${fId}" ${isBlocked} data-model="${model}" class="ANKETAS_TEXTAREA form-control" name="${i}">${(data[i] ? data[i] : '').trim()}</textarea>`
                               } else if(i === 'photo') {
                                   otherHtmlItems = ''

                                   if(data[i]) {
                                       field = `<img src="/storage/${data[i]}" width="60%" />`
                                   }
                               } else {
                                   field = `<input id="${fId}" ${isBlocked} data-model="${model}" class="form-control" type="${fvItem['type']}" value='${data[i] ? data[i] : ''}' name="${i}" />`
                               }
                           }
                       // }

                       msg += `
                        <p style="${i === 'dismissed' ? data[i].toUpperCase() === 'ДА' ? 'color: red; font-weight: bold;' : '' : ''}" data-field-card="${model}_${i}" class="text-small m-0">${fvItem.label}:<br/>
                            ${otherHtmlItems}
                            <div class="form-group ${inputClass}">
                                ${field}
                            </div>
                        </p>`

                    }
                }
            }
            /**
             * Запрещаем Мед.сотр и Тех.сотр редактировать компанию
             */
            if((userRole() !== 1 || userRole() !== 2) && model == 'Company') {

            } else {
                msg += `<button type="submit" class="btn btn-sm btn-success">Сохранить</button></form>`
            }

            $(`#${dbItemId}`).html(msg)

            LIBS.initChosen()

            $(`#form_${model}`).submit(function (e) {
                e.preventDefault()

                if(confirm('Сохранить?')) {
                    $(this).find(`.${inputClass} input, .${inputClass} select, .${inputClass} textarea`).each(function () {
                        let val = this.value, name = this.name

                        if(name) {
                            API_CONTROLLER.updateModelProperty({
                                item_model: model,
                                item_field: name,
                                item_id: data['id'],
                                new_value: val
                            })
                        }
                    })
                }
            })

            $('.ANKETAS_TEXTAREA').each(function () {
                this.style.height = this.scrollHeight + 'px';
            })

            /**
             * Контроль дат (DDATE)
             */
            let redDates = fullData.redDates
            if(redDates && typeof redDates === "object" && !Array.isArray(redDates)) {
                for(let i in redDates) {
                    $(`*[data-field-card="${model}_${i}"]`).css({
                        'color': 'red',
                        'font-weight': 'bold'
                    })
                }
            }

        }
    }

    window.changeFormRequire = (element, className) => {
        if (element.value.length > 0) {
            $(element).parents('.cloning')?.find('.' + className).prop('required', false);
        } else {
            $(element).parents('.cloning')?.find('.' + className).prop('required', true);
        }
    };

    // Проверка свойства по модели на бэкенда
    window.checkInputProp = async (prop = '0', model = '0', val = '0', label, parent, is_dop) => {
        let PARENT_ELEM;
        console.log('---------------')
        if(parent){
            PARENT_ELEM = parent;
        }else{
            PARENT_ELEM = $(event.target).parent();
        }

        //check-prop-one
        if(!is_dop) {
            let answer = await $.ajax({
                url: `/api/check-prop-one/${prop}/${model}/${val}?dateAnketa=${$('[name="anketa[0][date]"]').val()}`,
                headers: {'Authorization': 'Bearer ' + API_TOKEN},
                success:  (data) => {
                    let element = PARENT_ELEM.find('.app-checker-prop')
                    if(data.status){
                        element.removeClass('text-danger').addClass('text-success').text(data.name);
                        PARENT_ELEM.closest('#ANKETA_FORM').find('.btn-success').prop('disabled', false);
                    }else{
                        element.removeClass('text-success').addClass('text-danger').text(`Не найдено`);
                        parent.prevObject.attr('company', null);
                        PARENT_ELEM.closest('#ANKETA_FORM').find('.btn-success').prop('disabled', true);
                    }
                }
            })

            if(!answer.status){
                return;
            }
        }


        $.ajax({
            url: `/api/check-prop/${prop}/${model}/${val}?dateAnketa=${$('[name="anketa[0][date]"]').val()}`,
            headers: {'Authorization': 'Bearer ' + API_TOKEN},
            success:  (data) => {
                const PROP_HAS_EXISTS = data.data.exists
                const DATA = data.data.message;

                if ((model === 'Driver' || model === 'Car') && DATA.company_hash_id) {
                    const form = parent.closest('#ANKETA_FORM');
                    form.find('input[name="company_id"]').val(DATA.company_hash_id);
                    const select = form.find('select[name="company_id"]');

                    if (select.length > 0) {
                        const exist = select.children('option').filter((id, element) => {
                            return element.value === DATA.company_hash_id;
                        });

                        if (exist.length < 1) {
                            select.append($('<option>', {
                                value: DATA.company_hash_id,
                                text: DATA.company_name
                            }));
                        }

                        select.select2().val(DATA.company_hash_id).trigger("change");
                    }
                }

                if ((model === 'Driver' || model === 'Car') && DATA.company_name) {
                    const form =  parent.closest('#ANKETA_FORM');
                    const select = form.find('select[name="company_name"]');

                    if (select.length > 0) {
                        const exist = select.children('option').filter((id, element) => {
                            return element.value === DATA.company_name;
                        });

                        if (exist.length < 1) {
                            select.append($('<option>', {
                                value: DATA.company_name,
                                text: DATA.company_name
                            }));
                        }

                        select.select2().val(DATA.company_name).trigger("change");
                    }
                }

                showAnketsCardDBitemData(model, DATA, data.data.fieldsValues, data.data)

                if(PARENT_ELEM.length) {
                    const APP_CHECKER_PARENT = PARENT_ELEM.find('.app-checker-prop')

                    if(PROP_HAS_EXISTS){
                        APP_CHECKER_PARENT.removeClass('text-danger').addClass('text-success').text(DATA[label])
                    } else {
                        // PARENT_ELEM.find('input, textarea, select').val('');
                        APP_CHECKER_PARENT.removeClass('text-success').addClass('text-danger').text(`Не найдено`)
                    }
                }

                if(DATA) {
                    if(!!DATA.company_id) {
                        $('#ANKETA_FORM').find('input[name="company_id"]').parent().find('.app-checker-prop').removeClass('text-danger').addClass('text-success').text(DATA.company_name);
                        PARENT_ELEM.closest('#ANKETA_FORM').find('.btn-success').prop('disabled', false);
                        checkInputProp('id', 'Company', DATA.company_id, 'name', $('#ANKETA_FORM').find('input[name="company_id"]').parent())
                    }
                }

                if (model === 'Driver' && DATA.company_hash_id) {
                    parent.prevObject.attr('company', DATA.company_hash_id);
                    const driverInput = parent.closest('#ANKETA_FORM').find('.car-input');
                    driverInput.each((id, input) => {
                        const attr = $(input).attr('company');
                       if (attr && attr !== DATA.company_hash_id) {
                           const mess = $(input).parent()?.find('.app-checker-prop');
                           if (mess?.find('#company')?.length < 1) {
                               mess.append(
                                   `<br><span id="company" class="text-danger">Компания автомобиля не соответствует компании водителя</span>`
                               );
                           }
                       } else if (attr && attr === DATA.company_hash_id) {
                           const mess = $(input).parent()?.find('.app-checker-prop');
                           if (mess?.find('#company')?.length > 0) {
                               mess.find('#company').remove();
                           }
                       }
                    });
                }

                if (model === 'Car' && DATA.company_hash_id) {
                    const driverInput = parent.closest('#ANKETA_FORM').find('input[name="driver_id"]');
                    parent.prevObject.attr('company', DATA.company_hash_id);

                    if (driverInput) {
                        const driverCompany = driverInput.attr('company');
                        if (driverCompany && driverCompany !== DATA.company_hash_id) {
                            parent.find('.app-checker-prop').append(
                                `<br><span id="company" class="text-danger">Компания автомобиля не соответствует компании водителя</span>`
                            );
                        }
                    }
                }

                return;
            }
        });
    }



    window.addFieldToHistory = (value, field) => {
        API.post('/api/field-history', {
            value, field
        })
    }

    // ЭКСПОРТ таблицы в xlsx
    window.exportTable = function(table, withNotExport = false) {
        table = document.getElementById(table)

        if(table) {
            table = table.cloneNode(true)

            if(!withNotExport) {
                $(table).find('.not-export').remove()
            }

            $(table).find('.modal').remove()

            table = table.innerHTML

            table = table.replace(/<(\/*)a[^>]*>/g, '<span>').replace('</a>', '</span>')

            var uri = 'data:application/vnd.ms-excel;base64,',
                template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
                base64 = function(s) {
                    return window.btoa(unescape(encodeURIComponent(s)))
                },
                format = function(s, c) {
                    return s.replace(/{(\w+)}/g, function(m, p) {
                        return c[p];
                    })
                }
            var toExcel = table;
            var ctx = {
                worksheet: name || '',
                table: toExcel
            };
            var link = document.createElement("a");
            link.download = "export.xls";
            link.href = uri + base64(format(template, ctx))
            link.setAttribute('target', '_blank')
            link.click();

            setTimeout(() => {
                let findStrtypePrikaz = '&exportPrikaz=1&typePrikaz=Dop'
                if(location.href.indexOf(findStrtypePrikaz) > -1) {
                    let newLocation = location.href.replace(findStrtypePrikaz, '')
                    newLocation = newLocation.replace(/(export\=1\&)|(\&export\=1)|(export\=1)/g, '')

                    location.href = newLocation
                }
            }, 1500)
        }
    };

    /*
    СТАРЫЙ ЭКСПОРТ==
    window.exportTable = (function() {
        let uri = 'data:application/vnd.ms-excel;base64,'
            , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>'
            , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
            , format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; })
        }
            , downloadURI = function(uri, name) {
            let link = document.createElement("a");
            link.download = name;
            link.href = uri;
            link.click();
        }

        //  exportTable('resultTable','Смета', 'Ремрайон_смета.xls');
        return function(table, name, fileName) {
            if (!table.nodeType) table = document.getElementById(table)
            let ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
            let resuri = uri + base64(format(template, ctx))
            downloadURI(resuri, fileName);
        }
    })();*/

    // Открытие/закртие элементов
    $('[data-toggle-show]').click(function (e) {
        e.preventDefault()

        let $this = $(this), el = $($this.data('toggle-show')), title = $this.find('.toggle-title'),
            hiddenClass = 'toggle-hidden', titleData = 'Показать'

        if(el.length && title.length) {

            titleData = (el.hasClass(hiddenClass)) ? 'Скрыть' : titleData

            title.text(titleData)
            el.toggleClass(hiddenClass);
        }
    })

    $('.field').each(function () {
        let $t = $(this),
            $i = $t.find('> i'),
            $input = $t.find('> input')

        if($t.hasClass('field--password')) {
            $i.click(function () {
                if($(this).hasClass('fa-eye')) {
                    $(this).removeClass('fa-eye').addClass('fa-eye-slash')
                    $input.attr('type', 'password')
                } else {
                    $(this).removeClass('fa-eye-slash').addClass('fa-eye')
                    $input.attr('type', 'text')
                }
            })
        }
    })

    // ЭКШЕНЫ
    $('*[class*="ACTION_"]').click(function (e) {
        e.preventDefault();

        let confirms = {
            'ACTION_DELETE': 'Точно хотите удалить?'
        }

        this.classList.forEach(cls=> {
            if(confirms[cls])
                if(confirm(confirms[cls]))
                    location.href = this.href;
        })
    })

    $('.TRIGGER_CLICK').trigger('click');

    // Клонируем анкету
    let count_anketa = 1;

    $('#ANKETA_CLONE_TRIGGER').click(e => {
        const CLONE_ID = 'cloning'
        if(count_anketa+1 === 31) {
            swal.fire({
                title: 'Нельзя добавлять более 30 осмотров',
                icon: 'warning'
            });

            return
        }

        let clone_first = $(`#${CLONE_ID}-first`), clone_to = $(`#${CLONE_ID}-append`)
        let clone = clone_first.clone()
        let randId = 'ANKETA_DOUBLE_' + count_anketa

        if(clone_to.find('.cloning-clone').length) {
            clone = clone_to.find('.cloning-clone').last().clone()
        }

        // Выставляем параметры
        clone.removeAttr('id').addClass('cloning-clone')

        clone.attr('id', randId)

        // Создаем клон
        clone_to.append(clone)

        clone.find('input,select').each(function () {
            this.name = this.name.replace('anketa['+(count_anketa-1)+']', 'anketa['+count_anketa+']')
        })

        count_anketa++

        clone.find('.anketa-delete').html('<a href="" onclick="'+randId+'.remove(); return false;" class="text-danger">Удалить</a>')
    })

    // Отправка данных с форма по CTRL + ENTER
    $('.anketa-fields input, .anketa-fields textarea').keydown(e => {
        if(e.ctrlKey & e.keyCode == 13) {
            $('.anketa-fields form').trigger('submit');
        }
    })


    /**
     * API:
     * ИЗМЕНЕНИЕ ПОЛЕЙ НА BACKEND
     */
    $('.JS_CHANGE_FIELD_MODEL').click(function (e) {
        e.preventDefault()

        let url = this.href, field = $(this).data('field')

        field = $(field)

        if(field.length) {
            let new_value = field.val()

            API.put(url.replace(location.origin, ''), { new_value }).then(response => {
                const data = response.data

                if(data.success)
                    alert('Данные успешно обновлены!')
            })
        }
    })

    // ------------------------------------------------------- //
    // Card Close
    // ------------------------------------------------------ //
    $('.card-close a.remove').on('click', function (e) {
        e.preventDefault();
        $(this).parents('.card').fadeOut();
    });

    // ------------------------------------------------------- //
    // Tooltips init
    // ------------------------------------------------------ //

    $('[data-toggle="tooltip"]').tooltip()
    $("[rel='tooltip'], .tooltip").tooltip();

    // ------------------------------------------------------- //
    // Adding fade effect to dropdowns
    // ------------------------------------------------------ //
    $('.dropdown').on('show.bs.dropdown', function () {
        $(this).find('.dropdown-menu').first().stop(true, true).fadeIn();
    });
    $('.dropdown').on('hide.bs.dropdown', function () {
        $(this).find('.dropdown-menu').first().stop(true, true).fadeOut();
    });

    // ------------------------------------------------------- //
    // Sidebar Functionality
    // ------------------------------------------------------ //
    $('#toggle-btn').on('click', function (e) {
        e.preventDefault();
        $(this).toggleClass('active');

        $('.side-navbar').toggleClass('shrinked');
        $('.content-inner').toggleClass('active');
        $(document).trigger('sidebarChanged');

        if ($(window).outerWidth() > 1183) {
            if ($('#toggle-btn').hasClass('active')) {
                $('.navbar-header .brand-small').hide();
                $('.navbar-header .brand-big').show();
            } else {
                $('.navbar-header .brand-small').show();
                $('.navbar-header .brand-big').hide();
            }
        }

        if ($(window).outerWidth() < 1183) {
            $('.navbar-header .brand-small').show();
        }
    })

    // Меню (dropdown)
    $('[data-btn-collapse]').click(function (e) {
        e.preventDefault()

        let t = $(this), menu = $(t.data('btn-collapse')), clsCollapse = 'collapse'

        t.attr('aria-expanded', menu.hasClass(clsCollapse))

        if(menu.length) {
            menu.toggleClass(clsCollapse)
        }
    })

    /**
     * Elements FORMS (edit, add)
     * Check Requireds / Validate
     */
    function checkElementsFormDataRequireds (form, event = null) {
        let $form = $(form),
            requireds = $form.find('[required]'), errors = []

        requireds.each(function () {
            let val = $(this).val(), name = $(this).data('label')

            if(typeof val === "object") {
                if(val.length === 0) {
                    errors.push(`Поле "${name}" не заполнено`)
                }
            } else {
                if(!val)
                    errors.push(`Поле "${name}" не заполнено`)
            }
        })

        if(errors.length) {
            if(event)
                event.preventDefault()

            Toast.fire({
                icon: 'error',
                text: errors[0]
            })

            return
        }
    }

    function initElementsModalCheckFields () {
        $('[id*="elements-modal"] form, [id="elements-modal-add"] form').submit(function (e) {
            checkElementsFormDataRequireds(this, e)
        })

        $('[id*="elements-modal"] button[type="submit"], [id="elements-modal-add"] button[type="submit"]').click(function (e) {
            let $form = $(this).parents('form')

            if($form.length)
                checkElementsFormDataRequireds($form)
        })
    }
    initElementsModalCheckFields()

    /**
     * АВТО-ОТПРАВКА ФОРМ
     */
    $('.API_FORM_SEND').submit(async function (e) {
        e.preventDefault()
        document.querySelector('#page-preloader').classList.remove('hide');
        let data = $(this).serialize(), action = this.action, method = this.method,
            formData = new FormData(this), successTitle = $(this).data('successTitle')

        successTitle = successTitle ? successTitle : 'Успешно!'

        await axios[method](action, data, {
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN
            }
        }).then(response => {
            swal.fire({
                title: successTitle,
                icon: 'info'
            });
        })

        document.querySelector('#page-preloader').classList.add('hide');
    })

    // Проверяем ссылки в меню
    $('a[data-btn-collapse] + ul a').each(function () {
        let $href = this.href.replace(location.origin, '')

        if(location.pathname.indexOf($href) > -1) {
            let $parent = $(this).parent().parent()

            $parent.prev().trigger('click')
        }
    })

    // Таймер


    $('.App-Timer[data-date]').each(function () {
        let $t = $(this),
            timerDateStart = new Date($t.data('date'))

        function showDiff(date1, date2){
            var diff = (date2 - date1)/1000
            diff = Math.abs(Math.floor(diff))

            var days = Math.floor(diff/(24*60*60))
            var leftSec = diff - days * 24*60*60
            var hrs = Math.floor(leftSec/(60*60))
            var leftSec = leftSec - hrs * 60*60

            var min = Math.floor(leftSec/(60))
            var leftSec = leftSec - min * 60

            $t.html("<b class='text-red'>" + (days < 10 ? '0' + days : days) + ":" + (hrs < 10 ? '0' + hrs : hrs) + ":" + (min < 10 ? '0' + min : min) + ":" + (leftSec < 10 ? '0' + leftSec : leftSec) + "</b>")
        }

        setInterval(() => { showDiff(timerDateStart, new Date()) }, 1000)
    })

    // ------------------------------------------------------- //
    // Material Inputs
    // ------------------------------------------------------ //

    var materialInputs = $('input.input-material');

    // activate labels for prefilled values
    materialInputs.filter(function() { return $(this).val() !== ""; }).siblings('.label-material').addClass('active');

    // move label on focus
    materialInputs.on('focus', function () {
        $(this).siblings('.label-material').addClass('active');
    });

    // remove/keep label on blur
    materialInputs.on('blur', function () {
        $(this).siblings('.label-material').removeClass('active');

        if ($(this).val() !== '') {
            $(this).siblings('.label-material').addClass('active');
        } else {
            $(this).siblings('.label-material').removeClass('active');
        }
    });

    // ------------------------------------------------------- //
    // Footer
    // ------------------------------------------------------ //

    var contentInner = $('.content-inner');

    $(document).on('sidebarChanged', function () {
        adjustFooter();
    });

    $(window).on('resize', function () {
        adjustFooter();
    })

    function adjustFooter() {
        var footerBlockHeight = $('.main-footer').outerHeight();
        contentInner.css('padding-bottom', footerBlockHeight + 'px');
    }

    // ------------------------------------------------------- //
    // External links to new window
    // ------------------------------------------------------ //
    $('.external').on('click', function (e) {

        e.preventDefault();
        window.open($(this).attr("href"));
    })

    // ADAPTIVE
    if(screen.width <= 900) {
        $('#toggle-btn').trigger('click')
    }

    $('input[type="checkbox"][data-connect-field]').each(function () {
        let connectField = $(this).data('connectField')
            connectField = $(`*[name="${connectField}"]`)

        let triggerField = () => {
            if(this.checked) {
                connectField.removeAttr('disabled')
            } else {
                connectField.attr('disabled', 'disabled')
                connectField.removeAttr('checked')
            }
        }

        $(this).change(function () {
            triggerField()
        })

        triggerField()
    })

    // $('*[data-field="Company_name"]').suggestions({
    //     token: "4de76a04c285fbbad3b2dc7bcaa3ad39233d4300",
    //     type: "PARTY",
    //     /* Вызывается, когда пользователь выбирает одну из подсказок */
    //     onSelect: function(suggestion) {
    //         if(suggestion.data) {
    //             const { inn } = suggestion.data
    //
    //             $('#elements-modal-add input[name="inn"]').val(inn)
    //         }
    //     }
    // });

    $('.header #toggle-btn').each(function () {
        let localStatusSidebar = () => {
                return localStorage.getItem('sidebar')
            },
            localStatusSet = status => {
                localStorage.setItem('sidebar', status)
            }

        if(localStatusSidebar() === "0") {
            $(this).trigger('click')
        }

        $(this).click(function () {
            if($(this).hasClass('active')) {
                localStatusSet(1)
            } else {
                localStatusSet(0)
            }
        })
    })

    LIBS.initAll()
    $('.MASK_ID_ELEM').trigger('input')


    $('#modalEditor').on('shown.bs.modal', function (event) {
        let route = $(event.relatedTarget).data('route')
        let modalContent = $("#modalEditor .modal-content")

        axios.get(route).then(({ data }) => {
            modalContent.text('').append(data);
            LIBS.initAll()
        })

    })
    let field = $("*[name=type_product]").chosen()
    field.change(function(e, { selected }){

        if(selected === 'Абонентская плата без реестров'){
            // field.closest('.modal-body').find('select[name=essence]').prop("disabled", false);

            field.closest('.modal-body').find('select[name=type_anketa]').prop( "disabled", true);
            field.closest('.modal-body').find('select[name="type_view[]"]').prop( "disabled", true);

            // field.closest('.modal-body').find('select[name=essence]').prop('required', true) // тип осмотра
            field.closest('.modal-body').find('select[name=type_anketa]').prop('required', false) // тип осмотра
            field.closest('.modal-body').find('select[name="type_view[]"]').prop('required', false) // Реестр
        }else{
            // field.closest('.modal-body').find('select[name=essence]').prop( "disabled", true);
            field.closest('.modal-body').find('select[name=type_anketa]').prop( "disabled", false);
            field.closest('.modal-body').find('select[name="type_view[]"]').prop( "disabled", false);

            // field.closest('.modal-body').find('select[name=essence]').prop('required', false) // тип осмотра
            field.closest('.modal-body').find('select[name=type_anketa]').prop('required', true) // тип осмотра
            field.closest('.modal-body').find('select[name="type_view[]"]').prop('required', true) // Реестр
        }
    });

    $(document).on('change', '.filled-select2.filled-select.type_product', function(event) {
        const field = $(event.target);
        let selected = field.val()

        if(selected === 'Абонентская плата без реестров'){
            // field.closest('.modal-body').find('select[name=essence]').prop( "disabled", false);
            field.closest('.modal-body').find('select[name=type_anketa]').prop( "disabled", true);
            field.closest('.modal-body').find('select[name="type_view[]"]').prop( "disabled", true);

            // field.closest('.modal-body').find('select[name=essence]').prop('required', true) // тип осмотра
            field.closest('.modal-body').find('select[name=type_anketa]').prop('required', false) // тип осмотра
            field.closest('.modal-body').find('select[name="type_view[]"]').prop('required', false) // Реестр
        }else{
            // field.closest('.modal-body').find('select[name=essence]').prop( "disabled", true);
            field.closest('.modal-body').find('select[name="type_view[]"]').prop( "disabled", false);
            field.closest('.modal-body').find('select[name=type_anketa]').prop( "disabled", false);

            // field.closest('.modal-body').find('select[name=essence]').prop('required', false) // тип осмотра
            field.closest('.modal-body').find('select[name=type_anketa]').prop('required', true) // тип осмотра
            field.closest('.modal-body').find('select[name="type_view[]"]').prop('required', true) // Реестр
        }
    });


    $(document).on('change ready', 'select[name="company_id"]', function (e) {
        //select_for_contract_driver_car 'input[name="company_id"]'
        let value = $(this).val();
        let targetSelect = $('select[name="company_id"]').parent().parent().parent().find('#select_for_contract_driver_car');
        // let targetSelect = $('#select_for_contract_driver_car');
        targetSelect.empty();

        axios.post('/contract/getAvailableForCompany', {
            company_id: value,
        }).then(({data}) => {
            console.log(data)
            if (data.status) {
                targetSelect.append($('<option>', {
                    value: '',
                    text: 'Не установлено'
                }));
                data.contracts.map((item) => {
                    targetSelect.append($('<option>', {
                        value: item.id,
                        text: item.name
                    }));
                })
            } else {
                Swal2.fire('Ошибка', data.message, 'warning');
            }

        });


        // $.ajax({
        //     type:     'post',
        //     url:      '/app/purchase/check-active-discount',
        //     dataType: 'json',
        //     data:     {
        //         current_category_id: select.value,
        //     },
        //     success:  function (response) {
        //         if (response.status) {
        //         } else {
        //             alert('Ошибка');
        //         }
        //     },
        //     error:    function () {
        //         alert('Ошибка на сервере при запросе на checkActiveDiscount');
        //     },
        // });
    })


    // let field = $('*[data-field="Product_type_product"]')
});

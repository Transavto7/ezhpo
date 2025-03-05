@extends('layouts.app')

@section('title', 'Журнал смен')
@section('sidebar', 1)

@section('custom-styles')
    <style>
        .hv-checkbox-mass-deletion {
            accent-color: #138496;
            cursor: pointer;
            width: 20px;
            height: 20px;
        }

        .hv-mass-deletion-alert-error {
            font-size: 12px;
        }

        .hv-mass-deletion-alert-error code {
            font-size: 13px;
            border-radius: 3px;
            background-color: #f4b2b0;
            padding: .21rem .4rem;
        }

        #hv-alert-error-close {
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        thead {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 10
        }

        .table-card {
            max-height: 80vh;
            overflow: hidden;
        }

        .table-card > .card-body {
            overflow: scroll;
            padding: 0 !important;
            margin: 15px !important;
            overscroll-behavior: contain;
        }
    </style>
@endsection

@section('custom-scripts')
    <script type="text/javascript">
        @if (user()->fields_visible)
        let fieldsVisible = {!! user()->fields_visible !!};
        @else
        let fieldsVisible = @json(config('fields.visible'));
        @endif

        console.log(fieldsVisible)

        const type = 'workdays'

        function setVisibleInputs() {
            $('.workdays-form input').each(function () {
                const name = $(this).attr('name');
                console.log(name)
                let checked = false;
                if (fieldsVisible[type] && fieldsVisible[type][name]) {
                    checked = true;
                }
                $(this).prop("checked", checked);
                $(this).trigger('change');
            });
        }

        $(document).ready(function () {
            setVisibleInputs();

            const showTableData = el => {
                if (el) {
                    const id = el.attr('name');
                    const prop_checked = el.prop('checked');

                    const workdaysTable = $(`.workdays-table thead th[data-field-key="${id}"], .workdays-table tbody tr td[data-field-key="${id}"]`)
                    const displayProp = !prop_checked ? 'none' : 'table-cell'

                    workdaysTable.attr('hidden', !prop_checked).css({'display': displayProp})

                    return
                }

                $('.workdays-form input').each(function () {
                    if (this.name !== '_token') {
                        showTableData($(this))
                    }
                })
            }

            showTableData()

            $('.workdays-form input').change(e => {
                let el = $(e.target)
                const id = el.attr('name');

                if (!fieldsVisible[type]) {
                    fieldsVisible[type] = {};
                }

                fieldsVisible[type][id] = el.prop('checked');

                showTableData(el)
            });

            $('#saveFieldsBtn').click(async function () {
                await saveFieldsVisible(fieldsVisible);
                $('.toast-save-checks').toast('show');
            });

            $('#resetFieldsBtn').click(async function () {
                fieldsVisible = JSON.parse(`{!! json_encode(config('fields.visible')) !!}`);

                setVisibleInputs();

                await saveFieldsVisible(null);
                $('.toast-reset-checks').toast('show');
            });
        });

        function saveFieldsVisible(params) {
            return axios.post('/api/fields/visible', {params}, {
                headers: {
                    Authorization: 'Bearer ' + API_TOKEN
                },
            });
        }
    </script>

    <script type="text/javascript">
        const SELECTED_WORKDAYS_ITEM = 'selectedWorkdays'
        const workdaysApi = {
            massTrash: '{{ route('employees.workdays.mass-trash') }}',
        }
        const data = {
            items: [],
            total: 0
        }

        function updateWorkdaysCheckbox() {
            const workdaysStorage = getWorkdaysStorage()

            $('.hv-checkbox-mass-deletion').prop('checked', false)
            workdaysStorage.items.forEach(function (item) {
                $(`.hv-checkbox-mass-deletion[data-id="${item}"]`).prop('checked', true)
            })
        }

        function getWorkdaysStorage() {
            if (data === null) {
                return {
                    items: [],
                    total: 0
                }
            }

            return data
        }

        function setWorkdaysStorage(value) {
            data.items = value.items
            data.total = value.total
        }

        function pronunciationWithNumber(number, one, two, eleven) {
            const lastTwo = Math.abs(number) % 100
            const lastOne = Math.abs(number) % 10

            if (lastTwo > 10 && lastTwo < 20) {
                return eleven
            }

            if (lastOne > 1 && lastOne < 5) {
                return two
            }

            if (lastOne === 1) {
                return one
            }

            return eleven
        }

        function updateWorkdaysControl() {
            const control = $('#selected-workdays-control')
            const controlBtnDelete = $('#selected-workdays-control-btn-delete')

            const workdaysStorage = getWorkdaysStorage()

            if (workdaysStorage.total) {
                const records = pronunciationWithNumber(workdaysStorage.total, 'смену', 'смены', 'смен')
                const label = "Удалить " + workdaysStorage.total + " " + records

                controlBtnDelete.html(label)
                control.addClass('d-flex')
                control.removeClass('d-none')
            } else {
                control.addClass('d-none')
                control.removeClass('d-flex')
            }
        }

        function clearWorkdaysStorage() {
            data.items = []
            data.total = 0
        }

        function pushWorkdayToStorage(id) {
            const workdaysStorage = getWorkdaysStorage()

            if (workdaysStorage.items.filter(item => item === id).length) {
                return
            }

            workdaysStorage.total++
            workdaysStorage.items.push(id)

            setWorkdaysStorage(workdaysStorage)
        }

        function removeWorkdayFromStorage(id) {
            const workdaysStorage = getWorkdaysStorage()

            workdaysStorage.items = workdaysStorage.items.filter(item => item !== id)
            workdaysStorage.total = workdaysStorage.items.length

            setWorkdaysStorage(workdaysStorage)
        }

        $(document).ready(function () {
            clearWorkdaysStorage()
            updateWorkdaysControl()
            updateWorkdaysCheckbox()

            $('.hv-checkbox-mass-deletion').click(function () {
                const id = $(this).attr('data-id')
                const checked = $(this).is(':checked')

                if (checked) {
                    pushWorkdayToStorage(id)
                } else {
                    removeWorkdayFromStorage(id)
                }

                updateWorkdaysControl()
            })

            $('#selected-workdays-control-btn-unset').click(function () {
                clearWorkdaysStorage()
                updateWorkdaysControl()
                updateWorkdaysCheckbox()
            })

            $('#selected-workdays-control-btn-delete').click(function () {
                const workdaysStorage = getWorkdaysStorage()

                axios
                    .get(workdaysApi.massTrash, {
                        params: {
                            action: '{{ request()->get('trash') ? 0 : 1 }}',
                            ids: workdaysStorage.items
                        }
                    })
                    .then(() => {
                        clearWorkdaysStorage()
                        window.location.reload()
                    })
                    .catch(() => {
                    })
            })

            $('#select-all').click(function () {
                $('.workdays-table input[type="checkbox"]').each(function () {
                    if (!$(this).prop('checked')) {
                        $(this).click();
                    }
                });
            })

            $('.hv-btn-trash').click(function (e) {
                e.stopPropagation()
                const id = $(this).attr('data-id')

                removeWorkdayFromStorage(id)
                updateWorkdaysControl()
            })

            $('#hv-alert-error-close').click(function () {
                $('#hv-alert-error').addClass('d-none')
                $('#hv-alert-error').removeClass('d-flex')
            })
        })
    </script>
@endsection

@php
    $permissionToView = user()->access('employees_workdays_read');
    $permissionToTrashView = $permissionToDelete = user()->access('employees_workdays_trash');
    $permissionToUpdate = user()->access('employees_workdays_update');

    $notDeletedItems = session('not_deleted_workdays');
@endphp

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div>
                    @include('Workdays::components.table-actions')

                    @if($permissionToView)
                        @include('Workdays::components.filters')
                    @endif

                    @if(session()->has('error'))
                        <div class="alert alert-danger" role="alert">{{ session()->get('error') }}</div>
                    @endif

                    @if(count($workdays) > 0 && $permissionToView)
                        <div id="selected-workdays-control" class="d-none align-items-center mt-4 mb-2">
                            @if($permissionToDelete)
                                <button id="selected-workdays-control-btn-delete"
                                        class="btn btn-danger btn-sm mr-2"></button>
                            @endif
                            <button id="select-all" class="btn btn-success btn-sm ml-2">Выделить все на странице
                            </button>
                            <button id="selected-workdays-control-btn-unset" class="btn btn-success btn-sm ml-2">Снять
                                выделение
                            </button>
                        </div>
                    @endif

                    @if($notDeletedItems)
                        <div id="hv-alert-error"
                             class="alert alert-danger hv-mass-deletion-alert-error d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <div>Не удалось удалить анкеты с ID:</div>
                                <div class="d-flex align-items-center flex-wrap" style="gap: 5px;">
                                    @foreach($notDeletedItems as $item)
                                        <code>{{ $item }}</code>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <div id="hv-alert-error-close">
                                    <i class="fa fa-times"></i>
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    {{-- TODO: что это и зачем --}}
                    @if(count($workdays) > 0)
                        {{ $workdays->appends($_GET)->render() }}
                    @endif
                </div>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-body">
                @if((count($workdays) > 0) && $permissionToView)
                    <table
                        id="workdays-table"
                        class="workdays-table table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                @foreach($fieldPrompts as $field)
                                    @include('Workdays::components.columns.column-header')
                                @endforeach

                                @if($permissionToDelete && request()->get('trash'))
                                    <th>Удаливший</th>
                                    <th>Время удаления</th>
                                @endif

                                <th class="not-export">
                                    #
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($workdays as $workdayKey => $workday)
                            <tr data-field="{{ $workdayKey }}">
                                <td>
                                    <input
                                        type="checkbox"
                                        data-id="{{ $workday->id }}"
                                        class="hv-checkbox-mass-deletion">
                                </td>

                                @foreach($fieldPrompts as $field)
                                    <td data-field-key="{{ $field->field }}">
                                        @if(($field->field === 'date' || strpos($field->field, '_at') > 0) && $workday[$field->field])
                                            {{ date('d-m-Y H:i:s', strtotime($workday[$field->field])) }}
                                        @elseif(($field->field === 'photo') && $workday[$field->field])
                                            @include('Workdays::components.columns.photo', compact($workday, $field))
                                        @elseif(($field->field === 'video') && $workday[$field->field])
                                            @include('Workdays::components.columns.video', compact($workday, $field))
                                        @elseif($field->field === 'employee_id' && user()->access('employee_read'))
                                            <a href="{{ route('users', ['id' => $workday[$field->field] ]) }}">
                                                {{ $workday['employee_fio'] }}
                                            </a>
                                        @elseif($field->field === 'type_anketa')
                                            {{ \Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum::create($workday[$field->field])->getTitle() }}
                                        @elseif($field->field === 'is_real')
                                            {{ $workday[$field->field] ? 'Да' : 'Нет' }}
                                        @elseif($field->field === 'admitted')
                                            {{ $workday[$field->field] ? 'Да' : 'Нет' }}
                                        @else
                                            {{ $workday[$field->field] }}
                                        @endif
                                    </td>
                                @endforeach

                                @if($permissionToDelete && request()->get('trash'))
                                    <td class="td-option">
                                        {{ ($workday->deleted_user_name) }}
                                    </td>
                                    <td class="td-option">
                                        {{ ($workday->deleted_at) }}
                                    </td>
                                @endif

                                <td class="td-option not-export d-flex justify-content-end">
                                    @if($permissionToDelete)
                                        @include('Workdays::components.buttons.delete-workday-btn', compact($workday))
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($permissionToView)
                    @include('templates.take_form')

                    <p class="text-success">Найдено записей: <b>{{ $count }}</b></p>
                @endif
            </div>
        </div>
    </div>
@endsection

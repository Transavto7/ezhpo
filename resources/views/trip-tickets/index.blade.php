@extends('layouts.app')

@section('title', 'Реестр путевых листов')
@section('sidebar', 1)
@section('class-page', 'trip-tickets-page')

@section('custom-styles')
    <style>
        .select2-search--inline {
            width: 100%;
        }

        .select2-search__field:placeholder-shown {
            width: 100% !important;
        }

        .show {
            display: block !important;
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

        const type = $('.ankets-form').attr('anketa');

        function setVisibleInputs() {
            $('.ankets-form input').each(function () {
                const name = $(this).attr('name');
                let checked = false;
                if (fieldsVisible[type] && fieldsVisible[type][name]) {
                    checked = true;
                }
                $(this).prop("checked", checked);
                $(this).trigger('change');
            });
        }

        $(document).ready(function () {
            $('#preloader-div').attr('style', 'display: none')
            $('#filters-div').attr('style', '')

            setVisibleInputs();

            const showTableData = el => {
                if (el) {
                    const id = el.attr('name');
                    const prop_checked = el.prop('checked');

                    const anketsTable = $(`.ankets-table thead th[data-field-key="${id}"], .ankets-table tbody tr td[data-field-key="${id}"]`)
                    const displayProp = !prop_checked ? 'none' : 'table-cell'

                    anketsTable.attr('hidden', !prop_checked).css({'display': displayProp})

                    return
                }

                $('.ankets-form input').each(function () {
                    if (this.name !== '_token') {
                        showTableData($(this))
                    }
                })
            }

            showTableData()

            $('.ankets-form input').change(e => {
                let el = $(e.target)
                const type = el.parents('.ankets-form').attr('anketa');
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
@endsection

@php
    use App\Models\TripTicket;
    //todo: permissions for trip tickets
    $permissionToView = true;
    $permissionToTrashView = true;
    $permissionToCreate = true;
    $permissionToDelete = true;
    $permissionToUpdate = true;
    $permissionToExport = true;
    $permissionToExportPrikaz = true;
@endphp

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div>
                    <div class="col-md-12">
                        <div class="row bg-light p-2">
                            <div class="col-md-6">
                                @if (!user()->hasRole('client'))
                                    <button type="button" data-toggle-show="#ankets-filters"
                                            class="btn btn-sm btn-info"><i class="fa fa-cog"></i> <span
                                            class="toggle-title">Настроить</span> колонки
                                    </button>
                                @endif

                                @if($permissionToTrashView)
                                    @if(request()->get('trash', 0))
                                        <a href="{{ route('trip-tickets.index') }}"
                                           class="btn btn-sm btn-warning">Назад</a>
                                    @else
                                        <a href="?trash=1" class="btn btn-sm btn-warning">Корзина <i
                                                class="fa fa-trash"></i></a>
                                    @endisset
                                @endif
                            </div>
                            <div class="col-md-6 text-right">
                                @if($permissionToExport)
                                    <a href="?export=1&{{ $queryString }}" class="btn btn-sm btn-default">Экспорт
                                        таблицы <i class="fa fa-download"></i></a>
                                @endif
                                @if($permissionToExportPrikaz)
                                    <a href="?export=1&{{ $queryString }}&exportPrikaz=1"
                                       class="btn btn-sm btn-default">Экспорт таблицы по приказу <i
                                            class="fa fa-download"></i></a>
                                @endif
                            </div>

                            @if (!user()->hasRole('client'))
                                <div class="toggle-hidden p-3" id="ankets-filters">
                                    <form class="ankets-form">
                                        @foreach($fieldPrompts as $key => $field)
                                            <label>
                                                <input
                                                    checked
                                                    type="checkbox" name="{{ $field->field }}"
                                                    data-value="{{ $key+1 }}"/>
                                                {{ $field->name }} &nbsp;
                                            </label>
                                        @endforeach
                                    </form>
                                    <button class="btn btn-success btn-sm mt-3" id="saveFieldsBtn">Сохранить
                                    </button>
                                    <button class="btn btn-danger btn-sm mt-3" id="resetFieldsBtn">Сбросить</button>
                                    <div class="toast mt-2 toast-save-checks position-absolute">
                                        <div class="toast-body bg-success text-white">
                                            Успешно сохранено
                                        </div>
                                    </div>

                                    <div class="toast mt-2 toast-reset-checks position-absolute">
                                        <div class="toast-body bg-danger text-white">
                                            Успешно сброшено
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center" id="preloader-div">
                        <img src="{{ asset('images/loader.gif') }}" width="30" class="mb-4"/>
                    </div>

                    <div id="filters-div" style="display: none">
                        <ul
                            class="nav nav-tabs"
                            id="filter-groups"
                            role="tablist">
                            <li class="nav-item">
                                <a
                                    class="nav-link active"
                                    id="registry-update-1-tab"
                                    data-toggle="tab"
                                    href="#registry-update"
                                    role="tab"
                                    aria-controls="registry-update"
                                    aria-selected="true">
                                    Обновление реестра
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    id="filter-group-2-tab"
                                    data-toggle="tab"
                                    href="#filter-group"
                                    role="tab"
                                    aria-controls="filter-group"
                                    aria-selected="false">
                                    <i class="fa fa-filter"></i>Фильтры
                                </a>
                            </li>
                        </ul>

                        @if($permissionToCreate)
                            <div class="tab-pane fade show active" id="registry-update" role="tabpanel"
                                 aria-labelledby="registry-update" style="display: none">
                                <form action="{{ route('trip-tickets.generate') }}" method="GET"
                                      class="tab-content ankets-form-filter p-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Компания</label>
                                                @include('templates.elements_field', [
                                                    'v' => [
                                                        'type' => 'select',
                                                        'values' => 'Company',
                                                        'getField' => 'name',
                                                        'getFieldKey' => 'hash_id',
                                                        'concatField' => 'hash_id',
                                                        'trashed' => true
                                                    ],
                                                    'model' => 'trip-tickets',
                                                    'k' => 'company_id',
                                                    'is_required' => 'required',
                                                    'default_value' => null
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Водитель</label>
                                                @include('templates.elements_field', [
                                                    'v' => [
                                                        'type' => 'select',
                                                        'values' => 'Driver',
                                                        'getField' => 'fio',
                                                        'getFieldKey' => 'hash_id',
                                                        'concatField' => 'hash_id',
                                                        'trashed' => true
                                                    ],
                                                    'model' => 'trip-tickets',
                                                    'k' => 'driver_id',
                                                    'is_required' => '',
                                                    'default_value' => null
                                                ])
                                            </div>
                                        </div>
                                        @php
                                            $date_from_filter = now()->subMonth()->startOfMonth()->format('Y-m-d');
                                            $date_to_filter = now()->subMonth()->endOfMonth()->format('Y-m-d');
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Дата ПЛ от</label>
                                                <input type="date" value="{{ $date_from_filter }}"
                                                       name="date_from"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Дата ПЛ до</label>
                                                <input type="date"
                                                       value="{{ $date_to_filter }}" name="date_to"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Вид сообщения</label>
                                                @include('templates.elements_field', [
                                                    'v' => [
                                                        'type' => 'select',
                                                        'values' => App\Enums\LogisticsMethodEnum::labels(),
                                                    ],
                                                    'model' => 'trip-tickets',
                                                    'k' => 'logistics_method',
                                                    'is_required' => 'required',
                                                    'default_value' => null
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Вид перевозки</label>
                                                @include('templates.elements_field', [
                                                    'v' => [
                                                        'type' => 'select',
                                                        'values' => App\Enums\TransportationTypeEnum::labels(),
                                                    ],
                                                    'model' => 'trip-tickets',
                                                    'k' => 'transportation_type',
                                                    'is_required' => 'required',
                                                    'default_value' => null
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Шаблон ПЛ</label>
                                                @include('templates.elements_field', [
                                                    'v' => [
                                                        'type' => 'select',
                                                        'values' => App\Enums\TripTicketTemplateEnum::labels(),
                                                    ],
                                                    'model' => 'trip-tickets',
                                                    'k' => 'template_code',
                                                    'is_required' => 'required',
                                                    'default_value' => null
                                                ])
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-info">Сформировать</button>
                                </form>
                            </div>
                        @endif

                        @if($permissionToView)
                            <div class="tab-pane fade" id="filter-group" role="tabpanel"
                                 aria-labelledby="filter-group" style="display: none">
                                <form onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
                                      action="" method="GET" class="tab-content ankets-form-filter p-3">
                                    <input type="hidden" name="filter" value="1">

                                    @if(request()->filled('trash'))
                                        <input type="hidden" name="trash" value="{{ request()->get('trash') }}">
                                    @endif

                                    <input type="hidden" name="take" value="{{ request()->get('take', '') }}">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Компания</label>
                                                @include('templates.elements_field', [
                                                    'v' => [
                                                        'type' => 'select',
                                                        'values' => 'Company',
                                                        'getField' => 'name',
                                                        'getFieldKey' => 'hash_id',
                                                        'multiple' => 1,
                                                        'concatField' => 'hash_id',
                                                        'trashed' => true
                                                    ],
                                                    'model' => 'trip-tickets',
                                                    'k' => 'company_id',
                                                    'is_required' => '',
                                                    'default_value' => null
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Водитель</label>
                                                @include('templates.elements_field', [
                                                    'v' => [
                                                        'type' => 'select',
                                                        'values' => 'Driver',
                                                        'getField' => 'fio',
                                                        'getFieldKey' => 'hash_id',
                                                        'multiple' => 1,
                                                        'concatField' => 'hash_id',
                                                        'trashed' => true
                                                    ],
                                                    'model' => 'trip-tickets',
                                                    'k' => 'driver_id',
                                                    'is_required' => '',
                                                    'default_value' => null
                                                ])
                                            </div>
                                        </div>
                                        @php
                                            $date_from_filter = now()->subMonth()->startOfMonth()->format('Y-m-d');
                                            $date_to_filter = now()->subMonth()->endOfMonth()->format('Y-m-d');
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Дата ПЛ от</label>
                                                <input type="date" value="{{ $date_from_filter }}"
                                                       name="date_from"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Дата ПЛ до</label>
                                                <input type="date"
                                                       value="{{ $date_to_filter }}" name="date_to"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-info">Поиск</button>
                                    <button type="button" class="btn btn-danger reload-filters">
                                        <span class="spinner spinner-border spinner-border-sm d-none" role="status"
                                              aria-hidden="true"></span>
                                        Сбросить
                                    </button>

                                </form>
                            </div>
                        @endif
                    </div>


                    @if(session()->has('error'))
                        <div class="alert alert-danger" role="alert">{{ session()->get('error') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-body">
                @if((count($tripTickets) > 0) && $permissionToView)
                    <table
                        id="trip-tickets-table"
                        class="trip-tickets-table table table-striped table-sm">
                        <thead>
                        <tr>
                            <th>#</th>

                            @foreach($fieldPrompts as $field)
                                <th
                                    data-field-key="{{ $field->field }}"
                                    @isset($blockedToExportFields[$field->field])
                                        class="not-export"
                                    @endisset>
                                <span class="user-select-none"
                                      @if ($field->content)
                                          data-toggle="tooltip"
                                      data-html="true"
                                      data-trigger="click hover"
                                      title="{{ $field->content }}"
                                      @endif>
                                    {{ $field->name }}
                                </span>
                                    <a class="not-export"
                                       href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey={{ $field->field }}&{{ $queryString }}">
                                        <i class="fa fa-sort"></i>
                                    </a>
                                </th>
                            @endforeach

                            @if(request()->get('trash'))
                                <th width="60">Удаливший</th>
                                <th width="60">Время удаления</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tripTickets as $tripTicketKey => $tripTicket)
                            <tr data-field="{{ $tripTicketKey }}">
                                <td>
                                    <input
                                        type="checkbox"
                                        data-id="{{ $tripTicket->id }}"
                                        class="hv-checkbox-mass-deletion">
                                </td>

                                @foreach($fieldPrompts as $field)
                                    <td data-field-key="{{ $field->field }}"
                                        @isset($blockedToExportFields[$field->field])
                                            class="not-export"
                                        @endisset>
                                        @if(($field->field === 'date' || strpos($field->field, '_at') > 0) && $tripTicket[$field->field])
                                            {{ date('d-m-Y', strtotime($tripTicket[$field->field])) }}
                                        @elseif($field->field === 'driver_fio' && user()->access('drivers_read'))
                                            <a href="{{ route('renderElements', ['model' => 'Driver', 'filter' => 1, 'fio' => $tripTicket[$field->field] ]) }}">
                                                {{ $tripTicket[$field->field] }}
                                            </a>
                                        @elseif($field->field === 'car_gos_number' && user()->access('cars_read'))
                                            <a href="{{ route('renderElements', ['model' => 'Car', 'filter' => 1, 'gos_number' => $tripTicket[$field->field] ]) }}">
                                                {{ $tripTicket[$field->field] }}
                                            </a>
                                        @elseif($field->field === 'logistics_method')
                                            {{ \App\Enums\LogisticsMethodEnum::getLabel($tripTicket[$field->field]) }}
                                        @elseif($field->field === 'transportation_type')
                                            {{ \App\Enums\TransportationTypeEnum::getLabel($tripTicket[$field->field]) }}
                                        @elseif($field->field === 'template_code')
                                            {{ \App\Enums\TripTicketTemplateEnum::getLabel($tripTicket[$field->field]) }}
                                        @else
                                            {{ $tripTicket[$field->field] }}
                                        @endif
                                    </td>
                                @endforeach

                                @if($permissionToDelete && request()->get('trash'))
                                    <td class="td-option">
                                        {{ ($tripTicket->deleted_user_name) }}
                                    </td>
                                    <td class="td-option">
                                        {{ ($tripTicket->deleted_at) }}
                                    </td>
                                @endif

                                <td class="td-option not-export d-flex justify-content-end">
                                    @if($permissionToUpdate)
                                        <a href="{{ route('trip-tickets.edit', $tripTicket->id) }}"
                                           class="btn btn-info btn-sm mr-1"><i class="fa fa-edit"></i></a>
                                    @endif

                                    @if($permissionToDelete)
                                        <a
                                            href="{{ route('trip-tickets.trash', ['id' => $tripTicket->id, 'action' => request()->get('trash') ? 0 : 1]) }}"
                                            class="btn btn-warning btn-sm hv-btn-trash mr-1"
                                            data-id="{{ $tripTicket->id }}">
                                            @if(request()->get('trash', 0))
                                                <i class="fa fa-undo"></i>
                                            @else
                                                <i class="fa fa-trash"></i>
                                            @endisset
                                        </a>
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

                    <p class="text-success">Найдено записей: <b>{{ $tripTicketsCountResult }}</b></p>
                @endif
            </div>
        </div>
    </div>
@endsection

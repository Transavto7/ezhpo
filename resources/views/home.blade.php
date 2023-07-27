@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)
@section('class-page', 'anketa-' . $type_ankets)

@section('custom-scripts')
    <script type="text/javascript">
        window.onload = function () {
            @if($filter_activated)
                $.get(location.href + '&getCounts=1').done(data => {
                    if(data) {
                        $('#COUNTS_ANKETAS').html(`
                            <p class="text-success">Кол-во Автомобилей: <b>${data.anketasCountCars}</b></p>
                            <p class="text-success">Кол-во Водителей: <b>${data.anketasCountDrivers}</b></p>
                            <p class="text-success">Кол-во Компаний: <b>${data.anketasCountCompany}</b></p>
                        `);
                    }
                })
            @endif
        };

        $(document).ready(function () {
            @if (user()->fields_visible)
                let fieldsVisible = JSON.parse(`{!! user()->fields_visible !!}`);
            @else
                let fieldsVisible = JSON.parse(`{!! json_encode(config('fields.visible')) !!}`);
            @endif

            $('.ankets-form input').each(function () {
                const type = $(this).parents('.ankets-form').attr('anketa');
                const name = $(this).attr('name');
                if (fieldsVisible[type] && fieldsVisible[type][name]) {
                    $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", false);
                }
            });

            const showTableData = el => {
                if(el) {
                    const id = el.attr('name');
                    const prop_checked = el.prop('checked');

                    let anketsTable = $(`.ankets-table thead th[data-field-key="${id}"], .ankets-table tbody tr td[data-field-key="${id}"]`),
                        displayProp = (!prop_checked ? 'none' : 'table-cell')

                    anketsTable.attr('hidden', !prop_checked).css({'display': displayProp })
                } else {
                    $('.ankets-form input').each(function () {
                        let $t = $(this)

                        if(this.name !== '_token') {
                            showTableData($t)
                        }

                    })
                }
            }

            showTableData()

            $('.ankets-form input').change(e => {
                let el = $(e.target)
                const type = el.parents('.ankets-form').attr('anketa');
                const id = el.attr('name');
                const prop_checked = el.prop('checked');

                if (!fieldsVisible[type]) {
                    fieldsVisible[type] = {};
                }

                fieldsVisible[type][id] = prop_checked;

                showTableData(el)
            });

            $('#saveFieldsBtn').click(async function () {
                await saveFieldsVisible(fieldsVisible);
                $('.toast-save-checks').toast('show');
            });

            $('#resetFieldsBtn').click(async function () {
                fieldsVisible = JSON.parse(`{!! json_encode(config('fields.visible')) !!}`);

                $('.ankets-form input').each(function () {
                    const type = $(this).parents('.ankets-form').attr('anketa');
                    const name = $(this).attr('name');
                    if (fieldsVisible[type] && fieldsVisible[type][name]) {
                        $(this).prop("checked", true);
                    } else {
                        $(this).prop("checked", false);
                    }
                });

                await saveFieldsVisible(null);
                $('.toast-reset-checks').toast('show');
            });
        });

        function saveFieldsVisible(params) {
            return axios.post('/api/fields/visible', { params }, {
                headers: {
                    Authorization: 'Bearer ' + API_TOKEN
                },
            });
        }
    </script>

@endsection

@php
function checkChangeResult($anketa) {
    if (!$anketa->is_dop) {
        return false;
    }

    if ($anketa->result_dop) {
        return false;
    }

    if (!$anketa->company_id || !$anketa->company_name || !$anketa->date) {
        return false;
    }

    if ($anketa->type_anketa === 'medic') {
        if (!$anketa->driver_id || !$anketa->driver_fio) {
            return false;
        }
    }

    if ($anketa->type_anketa === 'tech') {
        if (!$anketa->car_id || !$anketa->car_gos_number) {
            return false;
        }
    }

    return true;
}

//dd($type_ankets);
$permissionToView = (
    user()->access('medic_read') && $type_ankets == 'medic'
    || user()->access('tech_read') && $type_ankets == 'tech'
    || user()->access('journal_briefing_bdd_read') && $type_ankets == 'bdd'
    || user()->access('journal_pl_read') && $type_ankets == 'pechat_pl'
    || user()->access('map_report_read') && $type_ankets == 'report_cart'
    || user()->access('errors_sdpo_read') && $type_ankets == 'pak'
    || user()->access('approval_queue_view') && $type_ankets == 'pak_queue'
);


$permissionToTrashView = (
    user()->access('medic_trash') && $type_ankets == 'medic'
    || user()->access('tech_trash') && $type_ankets == 'tech'
    || user()->access('journal_briefing_bdd_trash') && $type_ankets == 'bdd'
    || user()->access('journal_pl_trash') && $type_ankets == 'pechat_pl'
    || user()->access('map_report_trash') && $type_ankets == 'report_cart'
    || user()->access('errors_sdpo_trash') && $type_ankets == 'pak'
);
$permissionToDelete = (
        $type_ankets == 'medic' && user()->access('medic_delete')
        || $type_ankets == 'tech' && user()->access('tech_delete')
        || $type_ankets == 'bdd' && user()->access('journal_briefing_bdd_delete')
        || user()->access('journal_pl_delete') && $type_ankets == 'pechat_pl'
        || user()->access('map_report_delete') && $type_ankets == 'report_cart'
        || user()->access('errors_sdpo_delete') && $type_ankets == 'pak'
);

$permissionToUpdate = (
            $type_ankets == 'medic' && user()->access('medic_update')
            || $type_ankets == 'tech' && user()->access('tech_update')
            || $type_ankets == 'bdd' && user()->access('journal_briefing_bdd_update')
        || user()->access('journal_pl_update') && $type_ankets == 'pechat_pl'
        || user()->access('map_report_update') && $type_ankets == 'report_cart'
        || user()->access('errors_sdpo_update') && $type_ankets == 'pak'
);


$permissionToExport = (
    $type_ankets == 'tech' && user()->access('tech_export')
    || $type_ankets == 'medic' && user()->access('medic_export')
    || $type_ankets == 'bdd' && user()->access('journal_briefing_bdd_export')
    || $type_ankets == 'pechat_pl' && user()->access('journal_pl_export')
    || $type_ankets == 'report_cart' && user()->access('map_report_export')
);

$permissionToExportPrikaz = (
    $type_ankets == 'tech' && user()->access('tech_export_prikaz')
    || $type_ankets == 'medic' && user()->access('medic_export_prikaz')
    || $type_ankets == 'bdd' && user()->access('journal_briefing_bdd_export_prikaz')
    || $type_ankets == 'pechat_pl' && user()->access('journal_pl_export_prikaz')
    || $type_ankets == 'report_cart' && user()->access('map_report_export_prikaz')
);

$permissionToExportPrikazPL = (
    $type_ankets == 'tech' && user()->access('tech_export_prikaz_pl')
);


@endphp





@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div>
                    @if($type_ankets !== 'pak_queue')
                        <div class="col-md-12">
                            <div class="row bg-light p-2">
                                <div class="col-md-4">
                                    @if (!user()->hasRole('client'))
                                        <button type="button" data-toggle-show="#ankets-filters" class="btn btn-sm btn-info"><i class="fa fa-cog"></i> <span class="toggle-title">Настроить</span> колонки</button>
                                    @endif

                                    @if($permissionToTrashView)
                                    @isset($_GET['trash'])
                                        <a href="{{ route('home', $type_ankets) }}" class="btn btn-sm btn-warning">Назад</a>
                                    @else
                                        <a href="?trash=1" class="btn btn-sm btn-warning">Корзина <i class="fa fa-trash"></i></a>
                                    @endisset
                                    @endif
                                </div>


{{--                                @if(user()->hasRole('manager'))--}}
                                    @if($type_ankets === 'tech')
                                        <div class="col-md-8 text-right">
                                            @if($permissionToExport)
                                            <a href="?export=1{{ $queryString }}" class="btn btn-sm btn-default">Экспорт таблицы <i class="fa fa-download"></i></a>
                                            @endif
                                            @if($permissionToExportPrikaz)
                                                <a href="?export=1{{ $queryString }}&exportPrikaz=1" class="btn btn-sm btn-default">Экспорт таблицы по приказу ТО <i class="fa fa-download"></i></a>
                                            @endif
                                            @if($permissionToExportPrikazPL)
                                                <a href="?export=1{{ $queryString }}&exportPrikazPL=1" class="btn btn-sm btn-default">Экспорт таблицы по приказу ПЛ <i class="fa fa-download"></i></a>
                                            @endif
                                        </div>
                                    @else
                                        <div class="col-md-8 text-right">
                                            <!--                                    <button type="button" onclick="exportTable('ankets-table', true)" class="btn btn-default">Экспорт результатов <i class="fa fa-download"></i></button>-->
                                            <!--                                    <button type="button" onclick="exportTable('ankets-table')" class="btn btn-default">Экспорт результатов по приказу <i class="fa fa-download"></i></button>-->
                                            @if($permissionToExport)
                                                <a href="?export=1{{ $queryString }}" class="btn btn-sm btn-default">Экспорт таблицы <i class="fa fa-download"></i></a>
                                            @endif
                                            @if($permissionToExportPrikaz)
                                                <a href="?export=1{{ $queryString }}&exportPrikaz=1" class="btn btn-sm btn-default">Экспорт таблицы по приказу <i class="fa fa-download"></i></a>
                                            @endif
                                        </div>
                                    @endif
{{--                                @endif--}}

                                @if (!user()->hasRole('client'))
                                    <div class="toggle-hidden p-3" id="ankets-filters">
                                        <form class="ankets-form" anketa="{{ $type_ankets }}">
                                            @foreach($fieldPrompts as $key => $field)
                                                    <label>
                                                        <input
                                                            checked
                                                            type="checkbox" name="{{ $field->field }}" data-value="{{ $key+1 }}" />
                                                        {{ $field->name }} &nbsp;
                                                    </label>
                                            @endforeach
                                        </form>
                                        <button class="btn btn-success btn-sm mt-3" id="saveFieldsBtn">Сохранить</button>
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

                        @if($permissionToView)
                        <ul class="nav nav-tabs" id="filter-groups" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="filter-group-1-tab" data-toggle="tab" href="#filter-group-1" role="tab" aria-controls="filter-group-1" aria-selected="true"><i class="fa fa-filter"></i> Первая группа фильтров</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="filter-group-2-tab" data-toggle="tab" href="#filter-group-2" role="tab" aria-controls="filter-group-2" aria-selected="false"><i class="fa fa-filter"></i> Вторая группа фильтров</a>
                            </li>
                        </ul>

                        <form onsubmit="document.querySelector('#page-preloader').classList.remove('hide')" action="" method="GET" class="tab-content ankets-form-filter mb-3 pt-3" id="filter-groupsContent">
                            <div class="text-center">
                                <img src="{{ asset('images/loader.gif') }}" width="30" class="mb-4" />
                            </div>
                        </form>
                        @endif
                    @else
                        {{-- ОЧИСТКА ОЧЕРЕДИ СДПО --}}
                        @if($type_ankets === 'pak_queue')
                            @if(user()->access('approval_queue_clear'))
                                <a href="?clear=1&type_anketa={{ $type_ankets }}" class="btn btn-warning mb-2">Очистить очередь</a>
                            @endif
                        @endif
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger" role="alert">{{ session()->get('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        @if((count($ankets) > 0) && $permissionToView)
                            <table id="ankets-table" class="ankets-table table table-striped table-sm">
                                <thead>
                                    <tr>
                                        @if($type_ankets === 'pak_queue')
                                            <th class="not-export">
                                                Таймер
                                            </th>
                                        @endif

                                        @foreach($fieldPrompts as $field)
                                            @if($field->field == 'hour_from' || $field->field == 'hour_to')
                                                @continue
                                            @endif

                                            <th @isset($blockedToExportFields[$field->field])
                                                class="not-export"
                                                @endisset
                                                data-field-key="{{ $field->field }}"
                                            >
                                                <span class="user-select-none"
                                                    @if ($field->content)
                                                        data-toggle="tooltip"
                                                        data-html="true"
                                                        data-trigger="click hover"
                                                        title="{{ $field->content }}"
                                                    @endif
                                                >
                                                    {{ $field->name }}
                                                </span>

                                                <a class="not-export" href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey={{ $field->field }}{{ $queryString }}">
                                                    <i class="fa fa-sort"></i>
                                                </a>
                                            </th>
                                        @endforeach

                                        @if(request()->get('trash'))
                                            <th width="60">Удаливший</th>
                                            <th width="60">Время удаления</th>
                                        @endif

                                        <!-- ОЧЕРЕДЬ ОСМОТРОВ -->
                                        @if($type_ankets === 'pak_queue')
                                            <th class="not-export">#</th>
                                            <th class="not-export">#</th>
                                        @endif
                                        <!-- /ОЧЕРЕДЬ ОСМОТРОВ -->

                                        {{-- redactirovanie --}}
                                        @if($permissionToUpdate)
                                            <th class="not-export">#</th>
                                        @endif

                                        {{-- УДАЛЕНИЕ--}}
    {{--                                    @if($permissionToDelete)--}}
    {{--                                        <th class="not-export">#</th>--}}
    {{--                                    @endif--}}

                                        @if($type_ankets !== 'pak_queue')
                                                @if($permissionToDelete)
                                                    <th class="not-export">#</th>
                                                @endif
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ankets as $anketaKey => $anketa)
                                        <tr data-field="{{ $anketaKey }}">

                                            @if($type_ankets === 'pak_queue')
                                                <td class="not-export">
                                                    <div class="App-Timer" data-date="{{ $anketa->created_at }}"></div>
                                                </td>
                                            @endif


                                            @foreach($fieldPrompts as $field)
                                                <td
                                                    @isset($blockedToExportFields[$field->field])
                                                        class="not-export"
                                                    @endisset
                                                    data-field-key="{{ $field->field }}"
                                                >
                                                    @if($field->field === 'date' || strpos($field->field, '_at') > 0)
                                                        @if($anketa[$field->field])
                                                            {{ date('d-m-Y H:i:s', strtotime($anketa[$field->field])) }}
                                                        @endif
                                                    @elseif($field->field === 'photos')

                                                        @if($anketa[$field->field])
                                                            @php $photos = explode(',', $anketa[$field->field]); @endphp

                                                            @foreach($photos as $phI => $ph)
                                                                @php $isUri = strpos($ph, 'sdpo.ta-7'); @endphp

                                                                @if($phI == 0)
                                                                    <a href="{{ $isUri ? $ph : Storage::url($ph) }}" data-fancybox="gallery_{{ $anketa->id }}"><i class="fa fa-camera"></i> ({{ count($photos) }})</a>
                                                                @else
                                                                    <a href="{{ $isUri ? $ph : Storage::url($ph) }}" data-fancybox="gallery_{{ $anketa->id }}"></a>
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                    @elseif($field->field === 'videos')

                                                        @if($anketa[$field->field])
                                                            @php $videos = explode(',', $anketa[$field->field]); @endphp

                                                            @foreach($videos as $vK => $vV)
                                                                @if($vK == 0)
                                                                    <a data-type="iframe" href="{{ route('showVideo', ['url' => $vV]) }}" data-fancybox="video_{{ $anketa->id }}">
                                                                        <i class="fa fa-video-camera"></i>

                                                                        ({{ count($videos) }})
                                                                    </a>
                                                                @else
                                                                    <a data-type="iframe" href="{{ $vV }}" data-fancybox="video_{{ $anketa->id }}"></a>
                                                                @endif

                                                            @endforeach
                                                        @endif

                                                    @elseif($field->field === 'company_name' && user()->access('company_read'))
                                                        <a href="{{ route('renderElements', ['model' => 'Company', 'filter' => 1, 'name' => $anketa[$field->field] ]) }}">
                                                            {{ $anketa[$field->field] }}
                                                        </a>
                                                    @elseif($field->field === 'user_name' && user()->access('employee_read'))
                                                        <a href="{{ route('users', ['name' => $anketa[$field->field] ]) }}">
                                                            {{ $anketa[$field->field] }}
                                                        </a>
                                                    @elseif($field->field === 'operator_id' && user()->access('employee_read'))
                                                        @if($anketa->operator_id)
                                                            <a href="{{ route('users', ['id' => $anketa[$field->field] ]) }}">
                                                                {{ $anketa->operator ? $anketa->operator->name : 'Отсутствует' }}
                                                            </a>
                                                        @else
                                                            Отсутствует
                                                        @endif
                                                    @elseif($field->field === 'driver_fio' && user()->access('drivers_read'))
                                                        <a href="{{ route('renderElements', ['model' => 'Driver', 'filter' => 1, 'fio' => $anketa[$field->field] ]) }}">
                                                            {{ $anketa[$field->field] }}
                                                        </a>
                                                    @elseif($field->field === 'car_gos_number' && user()->access('cars_read'))
                                                        <a href="{{ route('renderElements', ['model' => 'Car', 'filter' => 1, 'gos_number' => $anketa[$field->field] ]) }}">
                                                            {{ $anketa[$field->field] }}
                                                        </a>
                                                    @elseif($field->field === 'protokol_path')
                                                        @if ($anketa->proba_alko === 'Положительно' && user()->access('medic_protokol_view'))
                                                            @if(user()->access('medic_protokol_edit'))
                                                                <a class="btn primary btn-sm btn-table"
                                                                   href="{{ route('docs.get', ['type' => 'protokol', 'anketa_id' => $anketa->id]) }}">
                                                                    Протокол отстранения
                                                                </a>
                                                            @else
                                                                <a class="btn primary btn-sm btn-table" target="_blank"
                                                                   href="{{ route('docs.get.pdf', ['type' => 'protokol', 'anketa_id' => $anketa->id]) }}">
                                                                    Протокол отстранения
                                                                </a>
                                                            @endif
                                                        @else
                                                            Отсутствует
                                                        @endif
                                                    @else

                                                        {{ $anketa[$field->field] }}

                                                        @if($type_ankets === 'medic' && $field->field === 'admitted' && $anketa[$field->field] === 'Не допущен' && user()->access('medic_closing_view'))
                                                            @if(user()->access('medic_closing_edit'))
                                                                <div class="row d-flex" style="gap: 3px">
                                                                    <a class="btn primary btn-sm btn-table"
                                                                       href="{{ route('docs.get', ['type' => 'closing', 'anketa_id' => $anketa->id]) }}">
                                                                        Мед. заключение
                                                                    </a>

                                                                    @if($anketa['closing_path'])
                                                                        <a target="_blank" class="btn primary btn-sm btn-table"
                                                                           href="{{ route('docs.get.pdf', ['type' => 'closing', 'anketa_id' => $anketa->id]) }}">
                                                                            <i class="fa fa-eye"></i>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <div class="row d-flex" style="gap: 3px">
                                                                    <a target="_blank" class="btn primary btn-sm btn-table"
                                                                       href="{{ route('docs.get.pdf', ['type' => 'closing', 'anketa_id' => $anketa->id]) }}">
                                                                        Мед. заключение
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </td>
                                            @endforeach


                                            @if($permissionToDelete)
                                            @if(request()->get('trash'))
                                                <td class="td-option">
                                                    {{ ($anketa->deleted_user->name) }}
                                                </td>
                                                <td class="td-option">
                                                    {{ ($anketa->deleted_at) }}
                                                </td>
                                            @endif
                                            @endif

                                            <!-- ОЧЕРЕДЬ ОСМОТРОВ -->
                                            @if($type_ankets === 'pak_queue')
                                                <td class="td-option not-export d-flex">
                                                    <a href="{{ route('forms.get', $anketa->id) }}" class="btn btn-info btn-sm mr-1"><i class="fa fa-search"></i></a>
                                                    <a href="{{ route('changePakQueue', ['admitted' => 'Допущен', 'id' => $anketa->id]) }}" class="btn btn-sm btn-success"><i class="fa fa-check"></i></a>
                                                </td>

                                                <td class="td-option not-export">
                                                    <a href="{{ route('changePakQueue', ['admitted' => 'Не допущен', 'id' => $anketa->id]) }}" class="btn btn-sm btn-danger"><i class="fa fa-close"></i></a>
                                                </td>
                                            @endif
                                            <!-- /ОЧЕРЕДЬ ОСМОТРОВ -->

                                                @if($permissionToUpdate)
                                                <td class="td-option not-export">
                                                    <a href="{{ route('forms.get', $anketa->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                                    @if($anketa->is_dop && !$anketa->result_dop)
                                                        @if (checkChangeResult($anketa))
                                                            <a
                                                                href="{{ route('changeResultDop', ['result_dop' => 'Утвержден', 'id' => $anketa->id]) }}"
                                                                class="btn btn-sm btn-success">
                                                                <i class="fa fa-check"></i>
                                                            </a>
                                                        @else

                                                            <button disabled
                                                                class="btn btn-sm btn-success">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endif

                                            @if($type_ankets === 'medic')
                                                    <td class="td-option not-export">
                                                        <a href="{{ route('forms.print', [
                                                            'id' => $anketa->id,
                                                        ]) }}" target="_blank" class="btn btn-success btn-sm">
                                                            <i class="fa fa-print"></i>
                                                        </a>
                                                    </td>
                                            @endif

                                            @if($type_ankets !== 'pak_queue')
                                                <td class="td-option not-export">
                                                    @if($permissionToDelete)
                                                    <a href="{{ route('forms.trash', [
                                                        'id' => $anketa->id,
                                                        'action' => isset($_GET['trash']) ? 0 : 1
                                                    ]) }}" class="btn btn-warning btn-sm">
                                                        @isset($_GET['trash'])
                                                            <i class="fa fa-undo"></i>
                                                        @else
                                                            <i class="fa fa-trash"></i>
                                                        @endisset
                                                    </a>
                                                    @endif
                                                </td>
                                            @endif

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr>

                        @if(count($ankets) > 0)
                            {{ $ankets->appends($_GET)->render() }}
                        @endif
                    @endif

                @if($permissionToView)

                @include('templates.take_form')

                <p class="text-success">Найдено записей: <b>{{ $anketasCountResult }}</b></p>
                @endif

                <div id="COUNTS_ANKETAS">

                </div>
            </div>

        </div>
    </div>

    {{--@include('templates.dashboard')--}}

@endsection

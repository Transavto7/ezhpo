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
    </script>

@endsection

@php
//dd($type_ankets);
    $permissionToView = (
        user()->access('medic_read') && $type_ankets == 'medic'
        || user()->access('tech') && $type_ankets == 'tech_read'
        || user()->access('journal_briefing_bdd_read') && $type_ankets == 'bdd'
        || user()->access('journal_pl_read') && $type_ankets == 'pechat_pl'
        || user()->access('map_report_read') && $type_ankets == 'report_cart'
        || user()->access('journal_pl_accounting') && $type_ankets == 'Dop'
        || user()->access('errors_sdpo_read') && $type_ankets == 'pak'
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
                                    <button type="button" data-toggle-show="#ankets-filters" class="btn btn-sm btn-info"><i class="fa fa-cog"></i> <span class="toggle-title">Настроить</span> колонки</button>

                                    @isset($_GET['trash'])
                                        <a href="{{ route('home', $type_ankets) }}" class="btn btn-sm btn-warning">Назад</a>
                                    @else
                                        <a href="?trash=1" class="btn btn-sm btn-warning">Корзина <i class="fa fa-trash"></i></a>
                                    @endisset
                                </div>


                                @if(user()->hasRole('manager'))
                                    @if($type_ankets === 'tech')
                                        <div class="col-md-8 text-right">
                                            <a href="?export=1{{ $queryString }}" class="btn btn-sm btn-default">Экспорт таблицы <i class="fa fa-download"></i></a>
                                            <a href="?export=1{{ $queryString }}&exportPrikaz=1" class="btn btn-sm btn-default">Экспорт таблицы по приказу ТО <i class="fa fa-download"></i></a>
                                            <a href="?export=1{{ $queryString }}&exportPrikazPL=1" class="btn btn-sm btn-default">Экспорт таблицы по приказу ПЛ <i class="fa fa-download"></i></a>
                                        </div>
                                    @else
                                        <div class="col-md-8 text-right">
                                            <!--                                    <button type="button" onclick="exportTable('ankets-table', true)" class="btn btn-default">Экспорт результатов <i class="fa fa-download"></i></button>-->
                                            <!--                                    <button type="button" onclick="exportTable('ankets-table')" class="btn btn-default">Экспорт результатов по приказу <i class="fa fa-download"></i></button>-->
                                            <a href="?export=1{{ $queryString }}" class="btn btn-sm btn-default">Экспорт таблицы <i class="fa fa-download"></i></a>
                                            <a href="?export=1{{ $queryString }}&exportPrikaz=1" class="btn btn-sm btn-default">Экспорт таблицы по приказу <i class="fa fa-download"></i></a>
                                        </div>
                                    @endif
                                @endif

                                <div class="toggle-hidden p-3" id="ankets-filters">
                                    <form action="{{ route('home.save-fields', $type_ankets) }}" method="POST" class="ankets-form">
                                        @csrf

                                        @foreach($anketsFields as $fieldKey => $fieldValue)
                                            @isset($fieldsKeys[$fieldValue])
                                                <label>
                                                    <input
                                                        @if(session()->exists("fields_$type_ankets"))
                                                            @isset(session()->get("fields_$type_ankets")[$fieldValue])
                                                            checked
                                                            @endisset
                                                        @else
                                                            checked
                                                        @endif

                                                        type="checkbox" name="{{ $fieldValue }}" data-value="{{ $fieldKey+1 }}" />
                                                    {{ (isset($fieldsKeys[$fieldValue]['name'])) ? $fieldsKeys[$fieldValue]['name'] : $fieldsKeys[$fieldValue] }} &nbsp;
                                                </label>
                                            @endisset
                                        @endforeach

                                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i> Сохранить в сессию</button>
                                    </form>
                                </div>
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

                        <form action="" method="GET" class="tab-content ankets-form-filter mb-3 pt-3" id="filter-groupsContent">
                            <div class="text-center">
                                <img src="{{ asset('images/loader.gif') }}" width="30" class="mb-4" />
                            </div>
                        </form>
                        @endif
                    @else
                        {{-- ОЧИСТКА ОЧЕРЕДИ СДПО --}}
                        @if($type_ankets === 'pak_queue')
                            @if(user()->access('approval_queue_clear'))
                                <a href="?clear=1&type_anketa={{ $type_ankets }}" class="btn btn-warning">Очистить очередь</a>
                            @endif
                        @endif
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger" role="alert">{{ session()->get('error') }}</div>
                    @endif

                    @if((count($ankets) > 0) && $permissionToView)
                        <table id="ankets-table" class="ankets-table table table-striped table-sm">
                            <thead>
                                <tr>
                                    @if($type_ankets === 'pak_queue')
                                        <th class="not-export">
                                            Таймер
                                        </th>
                                    @else
                                        <th width="60" class="not-export">ID записи</th>
                                    @endif

                                    @foreach($anketsFieldsTable as $field)
                                        @if($field == 'hour_from' || $field == 'hour_to')
                                            @continue
                                        @endif
                                        @isset($fieldsKeys[$field])
                                            <th @isset($blockedToExportFields[$field]) class="not-export" @endif data-field-key="{{ $field }}">
                                                {{ (isset($fieldsKeys[$field]['name'])) ? $fieldsKeys[$field]['name'] : $fieldsKeys[$field] }}

                                                <a class="not-export" href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey={{ $field . $queryString }}">
                                                    <i class="fa fa-sort"></i>
                                                </a>
                                            </th>
                                        @endisset
                                    @endforeach

                                    @accessSetting('id_auto', 'medic')
                                        <th class="not-export">ID автомобиля</th>
                                    @endaccessSetting


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

                                    {{-- УДАЛЕНИЕ--}}
                                    @if(
                                            $type_ankets == 'medic' && user()->access('medic_delete')
                                            || $type_ankets == 'tech' && user()->access('tech_delete')
                                            || $type_ankets == 'bdd' && user()->access('journal_briefing_bdd_delete')
                                        )
                                        <th class="not-export">#</th>
                                    @endif

                                    @if($type_ankets !== 'pak_queue')
                                            @if(
                                                $type_ankets == 'medic' && user()->access('medic_trash')
                                                || $type_ankets == 'tech' && user()->access('tech_trash')
                                                || $type_ankets == 'bdd' && user()->access('journal_briefing_bdd_trash')
                                            )
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
                                        @else
                                            <td class="not-export">{{ $anketa->id }}</td>
                                        @endif


                                        @foreach($anketsFieldsTable as $anketaKey)
                                            @if(isset($fieldsKeys[$anketaKey]))
                                                <td @isset($blockedToExportFields[$anketaKey]) class="not-export" @endisset data-field-key="{{ $anketaKey }}">
                                                    @if($anketaKey === 'date' || strpos($anketaKey, '_at') > 0)
                                                        @if($anketa[$anketaKey])
                                                            {{ date('d-m-Y H:i:s', strtotime($anketa[$anketaKey])) }}
                                                        @endif
                                                    @elseif($anketaKey === 'photos')

                                                        @if($anketa[$anketaKey])
                                                            @php $photos = explode(',', $anketa[$anketaKey]); @endphp

                                                            @foreach($photos as $phI => $ph)
                                                                @php $isUri = strpos($ph, 'sdpo.ta-7'); @endphp

                                                                @if($phI == 0)
                                                                    <a href="{{ $isUri ? $ph : Storage::url($ph) }}" data-fancybox="gallery_{{ $anketa->id }}"><i class="fa fa-camera"></i> ({{ count($photos) }})</a>
                                                                @else
                                                                    <a href="{{ $isUri ? $ph : Storage::url($ph) }}" data-fancybox="gallery_{{ $anketa->id }}"></a>
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                    @elseif($anketaKey === 'videos')

                                                        @if($anketa[$anketaKey])
                                                            @php $videos = explode(',', $anketa[$anketaKey]); @endphp

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

                                                    @else

                                                        {{ $anketa[$anketaKey] }}

                                                        @if($type_ankets === 'medic' && $anketaKey === 'admitted' && $anketa[$anketaKey] === 'Не допущен')
                                                            @if ($anketa->proba_alko === 'Положительно')
                                                                <a href="{{ route('docs.get', ['type' => 'protokol', 'anketa_id' => $anketa->id]) }}">Протокол отстранения</a>
                                                            @else
                                                                <a href="{{ route('docs.get', ['type' => 'other', 'anketa_id' => $anketa->id]) }}">Протокол отстранения</a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach


                                        @if(request()->get('trash'))
                                            <td class="td-option">
                                                {{ ($anketa->deleted_user->name) }}
                                            </td>
                                            <td class="td-option">
                                                {{ ($anketa->deleted_at) }}
                                            </td>
                                        @endif

                                        @accessSetting('id_auto', 'medic')
                                            <td class="td-option not-export">
                                                {{ $anketa->car_id }}
                                            </td>
                                        @endaccessSetting

                                        <!-- ОЧЕРЕДЬ ОСМОТРОВ -->
                                        @if($type_ankets === 'pak_queue')
                                            <td class="td-option not-export">
                                                <a href="{{ route('changePakQueue', ['admitted' => 'Допущен', 'id' => $anketa->id]) }}" class="btn btn-sm btn-success"><i class="fa fa-check"></i></a>
                                            </td>

                                            <td class="td-option not-export">
                                                <a href="{{ route('changePakQueue', ['admitted' => 'Не допущен', 'id' => $anketa->id]) }}" class="btn btn-sm btn-danger"><i class="fa fa-close"></i></a>
                                            </td>
                                        @endif
                                        <!-- /ОЧЕРЕДЬ ОСМОТРОВ -->

                                            @if(
                                                $type_ankets == 'medic' && user()->access('medic_update')
                                                || $type_ankets == 'tech' && user()->access('tech_update')
                                                || $type_ankets == 'bdd' && user()->access('journal_briefing_bdd_update')
                                            )
                                            <td class="td-option not-export">
                                                <a href="{{ route('forms.get', $anketa->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                                @if($anketa->is_dop && !$anketa->result_dop)
                                                    @if ($anketa->date)
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

                                        @if($type_ankets !== 'pak_queue')
                                            <td class="td-option not-export">

                                                {{--  poka ne nado--}}
{{--                                                @if(false)--}}
{{--                                                    @if(--}}
{{--                                                        $type_ankets == 'medic' && user()->access('medic_delete')--}}
{{--                                                        || $type_ankets == 'tech' && user()->access('tech_delete')--}}
{{--                                                        || $type_ankets == 'bdd' && user()->access('journal_briefing_bdd_delete')--}}
{{--                                                        )--}}
{{--                                                    <form action="{{ route('forms.delete', $anketa->id) }}" onsubmit="if(!confirm('Хотите удалить?')) return false;" method="POST">--}}
{{--                                                        @csrf--}}
{{--                                                        {{ method_field('DELETE') }}--}}
{{--                                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-remove"></i></button>--}}
{{--                                                    </form>--}}
{{--                                                    @endif--}}
{{--                                                @endif--}}


                                                @if(
                                                    $type_ankets == 'medic' && user()->access('medic_trash')
                                                    || $type_ankets == 'tech' && user()->access('tech_trash')
                                                    || $type_ankets == 'bdd' && user()->access('journal_briefing_bdd_trash')
                                                )
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

                @include('templates.take_form')

                @if($permissionToView)
                <p class="text-success">Найдено записей: <b>{{ $anketasCountResult }}</b></p>
                @endif

                <div id="COUNTS_ANKETAS">

                </div>
            </div>

        </div>
    </div>

    {{--@include('templates.dashboard')--}}

@endsection

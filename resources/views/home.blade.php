@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)
@section('class-page', 'anketa-' . $type_ankets)

@section('custom-scripts')
    @if($isExport)
        <script type="text/javascript">
            window.onload = function () {
                setTimeout(function () {
                    exportTable('ankets-table', {{ isset($_GET['exportPrikaz']) ? false : true }})
                }, 1500)
            };
        </script>
    @endif
@endsection

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">

                    @if($type_ankets !== 'pak_queue')
                        <div class="col-md-12">
                            <div class="row bg-light p-2">
                                <div class="col-md-6">
                                    <button type="button" data-toggle-show="#ankets-filters" class="btn btn-info"><i class="fa fa-cog"></i> <span class="toggle-title">Настроить</span> колонки</button>

                                    @isset($_GET['trash'])
                                        <a href="{{ route('home', $type_ankets) }}" class="btn btn-warning">Назад</a>
                                    @else
                                        <a href="?trash=1" class="btn btn-warning">Корзина <i class="fa fa-trash"></i></a>
                                    @endisset
                                </div>


                                @manager
                                    <div class="col-md-6 text-right">
    <!--                                    <button type="button" onclick="exportTable('ankets-table', true)" class="btn btn-default">Экспорт результатов <i class="fa fa-download"></i></button>-->
    <!--                                    <button type="button" onclick="exportTable('ankets-table')" class="btn btn-default">Экспорт результатов по приказу <i class="fa fa-download"></i></button>-->
                                        <a href="?export=1{{ $queryString }}" class="btn btn-default">Экспорт таблицы <i class="fa fa-download"></i></a>
                                        <a href="?export=1{{ $queryString }}&exportPrikaz=1" class="btn btn-default">Экспорт таблицы по приказу <i class="fa fa-download"></i></a>
                                    </div>
                                @endmanager

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
                    @else
                        {{-- ОЧИСТКА ОЧЕРЕДИ СДПО --}}
                        @if($type_ankets === 'pak_queue')
                            @role(['admin'])
                                <a href="?clear=1&type_anketa={{ $type_ankets }}" class="btn btn-warning">Очистить очередь</a>
                            @endrole
                        @endif
                    @endif

                    @if(count($ankets) > 0)
                        <table id="ankets-table" class="ankets-table table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th width="60" class="not-export">ID</th>

                                    @foreach($anketsFields as $field)
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

                                    @role(['admin', 'manager', 'medic', 'tech', $currentRole])
                                        <th class="not-export">#</th>
                                    @endrole

                                    @role(['admin', 'manager', 'medic', 'tech'])
                                        <th class="not-export">#</th>
                                    @endrole
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ankets as $anketaKey => $anketa)
                                    <tr data-field="{{ $anketaKey }}">
                                        <td class="not-export">{{ $anketa->id }}</td>
                                        @foreach($anketsFields as $anketaTDkey)
                                            @if(isset($fieldsKeys[$anketaTDkey]))
                                                <td @isset($blockedToExportFields[$anketaTDkey]) class="not-export" @endisset data-field-key="{{ $anketaTDkey }}">
                                                    @if($anketaTDkey === 'date' || strpos($anketaTDkey, '_at') > 0)
                                                        {{ date('d-m-Y H:i:s', strtotime($anketa[$anketaTDkey])) }}

                                                        <!-- Очередь ПАК -->
                                                        @if($type_ankets === 'pak_queue' && $anketaTDkey === 'created_at')
                                                            <div class="App-Timer" data-date="{{ $anketa['created_at'] }}"></div>
                                                        @endif

                                                    @elseif($anketaTDkey === 'photos')

                                                        @if($anketa[$anketaTDkey])
                                                            @php $photos = explode(',', $anketa[$anketaTDkey]); @endphp

                                                            @foreach($photos as $phI => $ph)
                                                                @if($phI == 0)
                                                                    <a href="{{ Storage::url($ph) }}" data-fancybox="gallery_{{ $anketa->id }}"><i class="fa fa-camera"></i> ({{ count($photos) }})</a>
                                                                @else
                                                                    <a href="{{ Storage::url($ph) }}" data-fancybox="gallery_{{ $anketa->id }}"></a>
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                    @else
                                                        {{ $anketa[$anketaTDkey] }}

                                                        @if($type_ankets === 'medic' && $anketaTDkey === 'admitted' && $anketa[$anketaTDkey] === 'Не допущен')
                                                            <a href="{{ route('docs.get', ['type' => 'protokol', 'anketa_id' => $anketa->id]) }}">Протокол отстранения</a>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach

                                        @accessSetting('id_auto', 'medic')
                                            <td class="td-option not-export">
                                                {{ $anketa->car_id }}
                                            </td>
                                        @endaccessSetting

                                        @role(['admin', 'manager', 'medic', 'tech', $currentRole])
                                            <td class="td-option not-export">
                                                <a href="{{ route('forms.get', $anketa->id) }}" class="btn btn-info"><i class="fa fa-edit"></i></a>
                                            </td>
                                        @endrole

                                        <td class="td-option not-export">
                                            @manager
                                                <form action="{{ route('forms.delete', $anketa->id) }}" onsubmit="if(!confirm('Хотите удалить?')) return false;" method="POST">
                                                    @csrf
                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="btn btn-danger"><i class="fa fa-remove"></i></button>
                                                </form>
                                            @endmanager

                                            @role(['admin', 'manager', 'medic', 'tech'])
                                                <a href="{{ route('forms.trash', [
                                                    'id' => $anketa->id,
                                                    'action' => isset($_GET['trash']) ? 0 : 1
                                                ]) }}" class="btn btn-warning">
                                                    @isset($_GET['trash'])
                                                        <i class="fa fa-undo"></i>
                                                    @else
                                                        <i class="fa fa-trash"></i>
                                                    @endisset
                                                </a>
                                            @endrole

                                        </td>

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

                <p class="text-success">Найдено осмотров: <b>{{ $anketasCountResult }}</b></p>

                @if($CountCompanies > 0 && $filter_activated)
                    <p class="text-success">Кол-во компаний: <b>{{ $CountCompanies }}</b></p>
                @endif

                @if($CountDrivers > 0 && $filter_activated && $type_ankets !== 'tech')
                    <p class="text-success">Кол-во Водителей: <b>{{ $CountDrivers }}</b></p>
                @endif

                @if($CountCars > 0 && $filter_activated && $type_ankets !== 'medic')
                    <p class="text-success">Кол-во Автомобилей: <b>{{ $CountCars }}</b></p>
                @endif
            </div>

        </div>
    </div>

    {{--@include('templates.dashboard')--}}

@endsection

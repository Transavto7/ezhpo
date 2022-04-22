@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@section('content')

<!-- Добавление элемента -->
<div id="elements-modal-add" tabindex="-1" role="dialog" aria-labelledby="elements-modal-label" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавление {{ $popupTitle }}</h4>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>

            <form action="{{ route('addElement', $model) }}" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="modal-body">
                    <p>Заполните форму внимательно и нажмите кнопку "Добавить"</p>

                    @foreach ($fields as $k => $v)
                        @php $is_required = isset($v['noRequired']) ? '' : 'required' @endphp
                        @php $default_value = isset($v['defaultValue']) ? $v['defaultValue'] : '' @endphp

                        @if($k !== 'id' && !isset($v['hidden']))
                            <div class="form-group" data-field="{{ $k }}">
                                <label>
                                    @if($is_required) <b class="text-danger text-bold">*</b> @endif

                                    {{ $v['label'] }}</label>

                                @include('templates.elements_field')
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Добавить</button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Закрыть</button>
                </div>
            </form>

        </div>
    </div>
</div>

{{--<div id="elements-modal-import" tabindex="-1" role="dialog" aria-labelledby="elements-modal-import" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Импорт</h4>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>

            <form action="{{ route('importElements', $model) }}" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="modal-body">
                    <p>Выберите файл импорта:</p>
                    <input required type="file" name="file" />
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Импорт</button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Закрыть</button>
                </div>
            </form>

        </div>
    </div>
</div>--}}

{{--NAVBAR--}}
@if(!(count($elements) >= $max) || !$max)
    <div class="col-md-12">
        <div class="row bg-light p-2">
            @role(['manager', 'admin', 'tech', 'medic', 'client'])
                @if($model === 'Company' && (auth()->user()->hasRole('tech', '==') || auth()->user()->hasRole('medic', '==')))
                @else
                    <div class="col-md-2">
                        <button type="button" data-toggle="modal" data-target="#elements-modal-add" class="@isset($_GET['continue']) TRIGGER_CLICK @endisset btn btn-success">Добавить <i class="fa fa-plus"></i></button>
                    </div>
                @endif
            @endrole

            <div class="col-md-5">
                <button type="button" data-toggle-show="#elements-filters" class="btn btn-info"><i class="fa fa-filter"></i> <span class="toggle-title">Показать</span> фильтры</button>
            </div>

            {{--<div class="col-md-3 text-right">
                <div class="row">
                    <button type="button" data-toggle="modal" data-target="#elements-modal-import" class="btn btn-primary">Импорт <i class="fa fa-download"></i></button>
                    <button type="button" onclick="exportTable('export-elements-table', '{{ $title }}', '{{ $title }}.xls')" class="btn btn-default">Шаблон .xls <i class="fa fa-download"></i></button>
                </div>

                <table style="display:none;" id="export-elements-table" class="table table-striped table-sm">
                    <thead>
                        <tr>
                            @foreach ($fields as $k => $v)
                                <th>{{ $k }}</th>
                            @endforeach
                        </tr>
                    </thead>
                </table>
            </div>--}}

            <div class="col-md-2 text-right">
{{--                <button type="button" onclick="exportTable('elements-table', '{{ $title }}', '{{ $title }}.xls')" class="btn btn-dark">Экспорт <i class="fa fa-download"></i></button>--}}
            </div>

            <div class="toggle-hidden col-md-12" id="elements-filters">
                <form action="" method="GET" class="elements-form-filter">
                    <input type="hidden" name="filter" value="1">
                    <hr>
                    <div class="row">
                        {{--DEFAULT FIELDS--}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>ID <small><i>(hash_id)</i></small></label>
                                <input
                                    type="number"
                                    value="{{ request()->get('hash_id') }}" name="hash_id" class="form-control" />
                            </div>
                        </div>

                        @foreach($fields as $fk => $fv)
                            @php $fv['multiple'] = true; @endphp

                            @if(!in_array($fk, ['photo']) && !isset($fv['hidden']))
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ $fv['label'] }}</label>

                                        @if($model === 'Company' && $fk === 'name')
                                            @include('templates.elements_field', [
                                                'v' => [
                                                    'label' => 'Компания',
                                                    'type' => 'select',
                                                    'values' => 'Company',
                                                    'noRequired' => 1,
                                                    'getFieldKey' => 'name'
                                                ],
                                                'k' => $fk,
                                                'is_required' => '',
                                                'default_value' => request()->get($fk)
                                            ])
                                        @else
                                            @include('templates.elements_field', [
                                                'v' => $fv,
                                                'k' => $fk,
                                                'is_required' => '',
                                                'default_value' => request()->get($fk)
                                            ])
                                        @endif

                                    </div>
                                </div>
                            @endif
                        @endforeach

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info">Поиск</button>
                            <a href="?" class="btn btn-danger">Сбросить</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<div class="card">
    @error('errors')
        <div class="text-red">
            <b>{{ $errors->first('errors') }}</b>
        </div>
    @enderror

    <table id="elements-table" class="table table-striped table-sm">
        <thead>
            <tr>
                {{--HASH_ID--}}
                @if(!isset($notShowHashId))
                    <th title="Ключ: id" width="60">
                        ID
                        <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey=id{{ $queryString }}">
                            <i class="fa fa-sort"></i>
                        </a>
                    </th>
                @endif

                @foreach ($fields as $k => $v)
                    @if(!isset($v['hidden']))
                        <th title="Ключ: {{ $k }}" data-key="{{ $k }}">
                            {{ $v['label'] }}

                            <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey={{ $k . $queryString }}">
                                <i class="fa fa-sort"></i>
                            </a>
                        </th>
                    @endif
                @endforeach

                @role(['manager', 'admin'])
                {{--УДАЛЕНИЕ--}}
                    <th width="60">#</th>
                @endrole
            </tr>
        </thead>
        <tbody>
            @if(count($elements) > 0)

                @foreach ($elements as $el)
                    <tr>
                        @foreach ($el->fillable as $elIndex => $elK)
                            @if(!(isset($notShowHashId) && $elK === 'hash_id') && $elK !== 'autosync_fields')
                                <td class="td-option">
                                    @if($elK === $editOnField)
                                        {{-- Если пользователь Менеджер --}}
                                        @role($otherRoles)
                                            <a href="" data-toggle="modal" data-target="#elements-modal-{{ $el->id }}-add" class="text-info">
                                        @endrole
                                    @endif

                                    @if($elK === 'products_id' || $elK === 'company_id' || $elK === 'req_id' || $elK === 'pv_id' || $elK === 'user_id' || $elK === 'town_id')
                                        @if($elK === 'company_id')

                                            @if(($model === 'Driver' || $model === 'Car') && $el->$elK && auth()->user()->hasRole('client', '!=') && auth()->user()->hasRole('operator_pak', '!='))
                                                <div>
                                                    <a href="{{ route('renderElements', ['model' => 'Company', 'filter' => 1, 'id' => $el->company_id ]) }}">{{ app('App\Company')->getName($el->company_id) }}</a>

                                                    <p>
                                                        <a class="btn btn-sm btn-default" href="{{ route('renderElements', ['model' => 'Car', 'filter' => 1, 'company_id' => $el->company_id ]) }}"><i class="fa fa-car"></i></a>
                                                        <a class="btn btn-sm btn-default" href="{{ route('renderElements', ['model' => 'Driver', 'filter' => 1, 'company_id' => $el->company_id ]) }}"><i class="fa fa-user"></i></a>
                                                    </p>
                                                </div>
                                            @else
                                                 {{ app('App\Company')->getName($el->company_id) }}
                                            @endif

                                        @elseif ($elK === 'user_id')
                                            {{ app('App\User')->getName($el->user_id, false) }}
                                        @elseif ($elK === 'req_id')
                                            {{ app('App\Req')->getName($el->req_id) }}
                                        @elseif ($elK === 'town_id')
                                            {{ app('App\Town')->getName($el->town_id) }}
                                        @elseif ($elK === 'pv_id' && $type !== 'Point')
                                            {{ app('App\Point')->getPointText($el->pv_id) }}
                                        @elseif ($elK === 'pv_id' && $type === 'Point')
                                            {{ app('App\Town')->getName($el->pv_id) }}
                                        @elseif ($elK == 'products_id')
                                            {{ app('App\Product')->getName($el->$elK) }}
                                        @endif
                                    @else
                                        @if(Storage::disk('public')->exists($el[$elK]) && $el[$elK] !== '<' && $el[$elK] !== '>')
                                            <a href="{{ Storage::url($el[$elK]) }}" data-fancybox="gallery_{{ $el->id }}">
                                                <b>
                                                    <i class="fa fa-camera"></i>
                                                </b>
                                            </a>
                                        @else
                                            {{--ПРОВЕРКА ДАТЫ--}}
                                            @if($elK === 'date' || strpos($elK, '_at') > 0)
                                                {{ date('d-m-Y H:i:s', strtotime($el[$elK])) }}
                                            @elseif($elK === 'autosync_fields')
                                                @foreach(explode(',', $el[$elK]) as $aSyncData)
                                                    <div class="text-bold text-success"><i class="fa fa-refresh"></i> {{ __($aSyncData) }}</div>
                                                @endforeach
                                            @else
                                                @foreach(explode(',', htmlspecialchars($el[$elK])) as $keyElK => $valElK)
                                                    @if($keyElK !== 0), @endif
                                                    {{ htmlspecialchars_decode(__($valElK)) }}
                                                @endforeach
                                            @endif
                                        @endif

                                        @if($model === 'Company' && $elK === 'name')

                                            <p>
                                                @php
                                                    $pre_month = (date('m')-1);
                                                    $date_from_company = date('Y') . '-' . ($pre_month <= 9 ? '0' . $pre_month : $pre_month) . '-' . '01';
                                                    $date_to_company = date('Y') . '-' . ($pre_month <= 9 ? '0' . $pre_month : $pre_month) . '-' . cal_days_in_month(CAL_GREGORIAN, $pre_month, date('Y'));
                                                @endphp

                                                <a class="btn btn-sm btn-outline-success"
                                                   href="{{ route('report.get', ['type' => 'journal', 'date_field' => 'date', 'filter' => 1, 'is_finance' => 1, 'company_id' => $el->hash_id, 'date_from' => $date_from_company, 'date_to' => $date_to_company]) }}">
                                                    ₽
                                                </a>
                                                <a class="btn btn-sm btn-default"
                                                   href="{{ route('renderElements', ['model' => 'Car', 'filter' => 1, 'company_id' => $el->id ]) }}">
                                                    <i class="fa fa-car"></i>
                                                </a>
                                                <a class="btn btn-sm btn-default"
                                                   href="{{ route('renderElements', ['model' => 'Driver', 'filter' => 1, 'company_id' => $el->id ]) }}">
                                                    <i class="fa fa-user"></i>
                                                </a>
                                            </p>
                                        @endif

                                        @isset($fields[$elK]['filterJournalLinkKey'])
                                            <div>
                                                <a class="btn btn-sm btn-danger" href="{{ route('home', 'medic') }}/?filter=1&{{ $fields[$elK]['filterJournalLinkKey'] }}={{ $el['hash_id'] }}">МЕД</a>
                                                <a class="btn btn-sm btn-info" href="{{ route('home', 'tech') }}/?filter=1&{{ $fields[$elK]['filterJournalLinkKey'] }}={{ $el['hash_id'] }}">ТЕХ</a>
                                                <a class="btn btn-sm btn-dark" href="{{ route('home', 'Dop') }}/?filter=1&{{ $fields[$elK]['filterJournalLinkKey'] }}={{ $el['hash_id'] }}">ПЛ</a>
                                            </div>
                                        @endisset
                                    @endif

                                    @if($elK === $editOnField)
                                        {{-- Если пользователь Менеджер --}}
                                        @role($otherRoles)
                                            </a>

                                            <div id="elements-modal-{{ $el->id }}-add" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
                                                <div role="document" class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Редактирование {{ $popupTitle }}</h4>
                                                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                                        </div>

                                                        <form action="{{ route('updateElement', ['type' => $model, 'id' => $el->id ]) }}" enctype="multipart/form-data" method="POST">
                                                            @csrf

                                                            <div class="modal-body">
                                                                @foreach ($fields as $k => $v)
                                                                    @php $is_required = isset($v['noRequired']) ? '' : 'required' @endphp

                                                                    @if($k !== 'id' && !isset($v['hidden']))
                                                                        <div class="form-group" data-field="{{ $k }}">
                                                                            <label>
                                                                                @if($is_required) <b class="text-danger text-bold">*</b> @endif
                                                                                {{ $v['label'] }}</label>

                                                                            @include('templates.elements_field', [
                                                                                'v' => $v,
                                                                                'k' => $k,
                                                                                'default_value' => $el[$k],
                                                                                'element_id' => $el['id']
                                                                            ])

                                                                            {{--Синхронизация полей--}}
                                                                            @if(isset($v['syncData']) && $model !== 'Company')
                                                                                @foreach($v['syncData'] as $syncData)
                                                                                    <a href="{{ route('syncDataElement', [
                                                                                        'model' => $syncData['model'],
                                                                                        'fieldFind' => $syncData['fieldFind'],
                                                                                        'fieldFindId' => $el['id'],
                                                                                        'fieldSync' => $k,
                                                                                        'fieldSyncValue' => $el[$k]
                                                                                    ]) }}" target="_blank" class="text-info btn-link"><i class="fa fa-spinner"></i> Синхронизация с: {{ $syncData['text'] }}</a>
                                                                                @endforeach
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-success">Сохранить</button>
                                                                <button type="button" data-dismiss="modal" class="btn btn-secondary">Закрыть</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>

                                        @endrole
                                    @endif

                                </td>
                            @endif
                        @endforeach

                        @role(['manager', 'admin'])
                            <td class="td-option">
                                <a href="{{ route('removeElement', ['type' => $model, 'id' => $el->id ]) }}" class="ACTION_DELETE btn btn-danger"><i class="fa fa-trash"></i></a>
                            </td>
                        @endrole

                    </tr>
                @endforeach

            @else
                <tr class="text-center">
                    <td colspan="{{ count($fields)+4 }}">Элементы не найдены</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="col-md-12">
        @if(count($elements) > 1)
            {{ $elements->appends($_GET)->render() }}
        @endif

        @include('templates.take_form')

        @role(['client'])
            <p>Элементов найдено: {{ method_exists($elements, 'total') ? $elements->total() : '' }}</p>
        @else
            <p>Элементов всего: {{ $elements_count_all }}</p>
            <p>Элементов найдено: {{ method_exists($elements, 'total') ? $elements->total() : $elements_count_all }}</p>
        @endrole
    </div>
</div>

@if(count($elements) <= 0)
    @section('custom-scripts')
        <script>
            setTimeout(function () {
                $('[data-toggle-show*="-filters"]').trigger('click')
            }, 500)
        </script>
    @endsection
@endif

@endsection

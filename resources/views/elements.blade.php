@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@section('content')

<!-- Модалка для редактирования см front.js  -->
<div class="modal fade editor-modal" id="modalEditor" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<!-- Добавление элемента -->
@if($model !== 'Product')
<div id="elements-modal-add" role="dialog" aria-labelledby="elements-modal-label" aria-hidden="true" class="modal fade text-left">
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
                            @if($model === 'Instr' && $k === 'sort' && (!auth()->user()->hasRole('engineer_bdd', '==') && !auth()->user()->hasRole('admin', '==')))
                                <!-- Сортировка инструктажей доступна админу или инженеру -->
                            @elseif($model === 'Instr' && $k === 'signature')
                            @else
                                <div class="form-group" data-field="{{ $k }}">
                                    <label>
                                        @if($is_required) <b class="text-danger text-bold">*</b> @endif

                                        {{ $v['label'] }}</label>

                                    @include('templates.elements_field')
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success">Добавить</button>
                    <button type="button" data-dismiss="modal" class="btn btn-sm btn-secondary">Закрыть</button>
                </div>
            </form>

        </div>
    </div>
</div>
@else
{{--    @php--}}
{{--        dd($fields)--}}

{{--    @endphp--}}
<div id="elements-modal-add" tabindex="-1" role="dialog" aria-labelledby="elements-modal-label"
     class="modal fade text-left" style="display: none;" aria-modal="true">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавление Услуги</h4>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="{{ route('addElement', $model) }}" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Заполните форму внимательно и нажмите кнопку "Добавить"</p>
                    <div data-field="name" class="form-group">
                        <label><b class="text-danger text-bold">*</b>Название</label>
                        <input value="" type="text" required="required" name="name"
                                                    data-label="Название" placeholder="Название"
                                                    data-field="Product_name" class="form-control ">
                    </div>
                    <div data-field="type_product" class="form-group">
                        <label><b class="text-danger text-bold">*</b>Тип</label>
                        <select name="type_product"
                                required="required"
                                data-label="Тип"
                                data-field="Product_type_product"
                                class="js-chosen"
                                style="display: none;"
                        >
                            <option value="" selected>Не установлено</option>
                            @foreach($fields['type_product']['values'] as $nameOfTypeProduct)
                            <option value="{{ $nameOfTypeProduct }}">
                                {{ $nameOfTypeProduct }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div data-field="unit" class="form-group">
                        <label><b class="text-danger text-bold">*</b>
                            Ед.изм.</label>
                        <input value="" type="text" required="required" name="unit"
                                                   data-label="Ед.изм." placeholder="Ед.изм." data-field="Product_unit"
                                                   class="form-control ">
                    </div>
                    <div data-field="price_unit" class="form-group">
                        <label><b class="text-danger text-bold">*</b>
                            Стоимость за единицу</label>
                        <input value="" type="number" required="required"
                                                                name="price_unit" data-label="Стоимость за единицу"
                                                                placeholder="Стоимость за единицу"
                                                                data-field="Product_price_unit" class="form-control ">
                    </div>
                    <div data-field="type_anketa" class="form-group">
                        <label><b class="text-danger text-bold">*</b> Реестр</label>
                        <select name="type_anketa" required="required" data-label="Реестр"
                                                   data-field="Product_type_anketa" class="js-chosen"
                                                   style="display: none;">
                            <option value="">Не установлено</option>
                            <option value="bdd">
                                БДД
                            </option>
                            <option value="medic">
                                Медицинский
                            </option>
                            <option value="tech">
                                Технический
                            </option>
                            <option value="Dop">
                                Учет ПЛ
                            </option>
                            <option value="pechat_pl">
                                Печать ПЛ
                            </option>
                            <option value="report_cart">
                                Отчеты с карт
                            </option>
                        </select>
                    </div>
                    <div data-field="type_view" class="form-group">
                        <label><b class="text-danger text-bold">*</b>Тип осмотра</label>
                        <select multiple="multiple" name="type_view[]" required="required"
                                                        data-label="Тип осмотра" data-field="Product_type_view"
                                                        class="js-chosen" style="display: none;">
                            <option value="">Не установлено</option>
                            <option value="Предрейсовый">
                                Предрейсовый
                            </option>
                            <option value="Послерейсовый">
                                Послерейсовый
                            </option>
                            <option value="Предсменный">
                                Предсменный
                            </option>
                            <option value="Послесменный">
                                Послесменный
                            </option>
                            <option value="БДД">
                                БДД
                            </option>
                            <option value="Отчёты с карт">
                                Отчёты с карт
                            </option>
                            <option value="Учет ПЛ">
                                Учет ПЛ
                            </option>
                            <option value="Печать ПЛ">
                                Печать ПЛ
                            </option>
                        </select>
                    </div>

                    <div data-field="essence" class="form-group" style="display: none">
                        <label><b class="text-danger text-bold">*</b>Сущности</label>
                        <select name="essence"
                                required="required"
                                data-label="Сущности"
                                data-field="Product_type_view"
                                class="js-chosen"
                                style="display: none;"
                        >
                            <option value="null">Не установлено</option>
                            @foreach(\App\Product::$essence as $essenceKey => $essenceName)
                            <option value="{{ $essenceKey }}">
                                {{ $essenceName }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success">Добавить</button>
                    <button type="button" data-dismiss="modal" class="btn btn-sm btn-secondary">Закрыть</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
                        <button type="button" data-toggle="modal" data-target="#elements-modal-add" class="@isset($_GET['continue']) TRIGGER_CLICK @endisset btn btn-sm btn-success">Добавить <i class="fa fa-plus"></i></button>
                    </div>
                @endif
            @endrole

            <div class="col-md-5">
                <button type="button" data-toggle-show="#elements-filters" class="btn btn-sm btn-info"><i class="fa fa-filter"></i> <span class="toggle-title">Показать</span> фильтры</button>
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
                                        @elseif($model === 'Instr' && $fk === 'sort' && (!auth()->user()->hasRole('engineer_bdd', '==') && !auth()->user()->hasRole('admin', '==')))
                                            <!-- Сортировка доступна только инженеру БДД и Админу -->
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
                            <button type="submit" class="btn btn-sm btn-info">Поиск</button>
                            <a href="?" class="btn btn-sm btn-danger">Сбросить</a>
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

                        @if($model === 'Instr' && $k === 'sort' && (!auth()->user()->hasRole('engineer_bdd', '==') && !auth()->user()->hasRole('admin', '==')))
                           <!-- Только админам -->
                        @elseif($model === 'Instr' && $k === 'signature')
                        @else
                            <th title="Ключ: {{ $k }}" data-key="{{ $k }}">
                                {{ $v['label'] }}

                                <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey={{ $k . $queryString }}">
                                    <i class="fa fa-sort"></i>
                                </a>
                            </th>
                            @if($model === 'Company' && $k == 'name')
                                <th>
                                    Реестры
                                </th>
                            @endif
                            @isset($fields[$k]['filterJournalLinkKey'])
                                <th>
                                    Справочники
                                </th>
                            @endisset
                        @endif
                    @endif
                @endforeach

                @if($model === 'Company')
                    @role(['admin'])
                    {{--УДАЛЕНИЕ--}}
                    <th width="60">#</th>
                    @endrole
                @endif

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
                                @php
                                    if (!isset($fields[$elK]) && $elK !== 'hash_id' && $elK !== 'id') {
                                        continue;
                                    }
                                @endphp

                                <td class="td-option">
                                    @if($elK === $editOnField)
                                        @role($otherRoles)
{{--                                           <a data-type="iframe" data-fancybox href="{{ route('showEditElementModal', ['id' => $el->id, 'model' => $model]) }}">--}}
                                               <a href="#" class="showEditModal" data-route="{{ route('showEditElementModal', ['id' => $el->id, 'model' => $model]) }}" data-toggle="modal" data-target="#modalEditor">
{{--                                            <a href="{{ route('showEditElementModal', ['id' => $el->id, 'model' => $model]) }}" data-toggle="modal" data-target="#elements-modal-{{ $el->id }}-add" class="text-info">--}}

                                        @endrole
                                    @endif

                                    @if($elK === 'products_id' || $elK === 'company_id' || $elK === 'req_id' || $elK === 'pv_id' || $elK === 'user_id' || $elK === 'town_id' || $elK === 'essence')
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
                                        @elseif ($elK == 'essence')
                                            {{ \App\Product::$essence[$el->$elK] ?? ''  }}
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
                                            @elseif ($elK === 'date_of_employment')
                                                {{ $el[$elK] ? \Carbon\Carbon::parse($el[$elK])->format('d.m.Y') : '' }}
                                            @elseif($model === 'Instr' && $elK === 'sort' && (!auth()->user()->hasRole('engineer_bdd', '==') && !auth()->user()->hasRole('admin', '==')))
                                                <!-- Сортировка инструктажей доступна ролям БДД и Админу -->
                                            @else
                                                @foreach(explode(',', htmlspecialchars($el[$elK])) as $keyElK => $valElK)
                                                    @if($keyElK !== 0), @endif
                                                    {{ htmlspecialchars_decode(__($valElK)) }}
                                                @endforeach
                                            @endif
                                        @endif

                                        @if($model === 'Company' && $elK === 'name')
                                            </td>
                                            <td class="td-option">
                                            <nobr>

                                                <a class="btn btn-sm btn-outline-success"
                                                   href="{{ route('report.get', ['type' => 'journal', 'company_id' => $el->hash_id]) }}">
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
                                            </nobr>
                                        @endif

                                        @isset($fields[$elK]['filterJournalLinkKey'])
                                            </td>
                                            <td class="td-option">
                                            <nobr>
                                                <a class="btn btn-sm btn-danger" href="{{ route('home', 'medic') }}/?filter=1&{{ $fields[$elK]['filterJournalLinkKey'] }}={{ $el['name'] }}">МЕД</a>
                                                <a class="btn btn-sm btn-info" href="{{ route('home', 'tech') }}/?filter=1&{{ $fields[$elK]['filterJournalLinkKey'] }}={{ $el['name'] }}">ТЕХ</a>
                                                <a class="btn btn-sm btn-dark" href="{{ route('home', 'Dop') }}/?filter=1&{{ $fields[$elK]['filterJournalLinkKey'] }}={{ $el['name'] }}">ПЛ</a>
                                            </nobr>
                                        @endisset
                                    @endif

                                    @if($elK === $editOnField)
                                        {{-- Если пользователь Менеджер --}}
                                        @role($otherRoles)
                                            </a>

                                            <!-- ЗДЕСЬ БЫЛА МОДАЛКА РЕДАКТИРОВАНИЯ -->

                                        @endrole
                                    @endif

                                </td>
                            @endif
                        @endforeach

                        @if($model === 'Company')
                            @role(['admin'])
                                <td class="td-option" title="При синхронизации все услуги компании будут присвоены водителям и автомобилям компании.">
                                    <a href="{{ route('syncElement', ['type' => $model, 'id' => $el->id ]) }}" class="btn btn-sm btn-success"><i class="fa fa-refresh"></i></a>
                                </td>
                            @endrole
                        @endif

                        @role(['manager', 'admin'])
                            <td class="td-option">
                                <a href="{{ route('removeElement', ['type' => $model, 'id' => $el->id ]) }}" class="ACTION_DELETE btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
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

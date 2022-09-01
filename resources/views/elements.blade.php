@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@section('content')

    <!-- Модалка для редактирования см front.js  -->
    <div class="modal fade editor-modal" id="modalEditor" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>

    <!-- Добавление элемента -->
    @if($model !== 'Product')
        <div id="elements-modal-add" role="dialog" aria-labelledby="elements-modal-label" aria-hidden="true"
             class="modal fade text-left">
            <div role="document" class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Добавление {{ $popupTitle }}</h4>
                        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                aria-hidden="true">×</span></button>
                    </div>

                    <form action="{{ route('addElement', $model) }}" enctype="multipart/form-data" method="POST">
                        @csrf

                        <div class="modal-body">
                            <p>Заполните форму внимательно и нажмите кнопку "Добавить"</p>

                        @foreach ($fields as $k => $v)
                            @php $is_required = isset($v['noRequired']) ? '' : 'required' @endphp
                            @php $default_value = isset($v['defaultValue']) ? $v['defaultValue'] : '' @endphp

                            @if($k !== 'id' && !isset($v['hidden']))
                                @if($model === 'Instr' && $k === 'sort')
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
                                    <option value="Предрейсовый/Предсменный">
                                        Предрейсовый/Предсменный
                                    </option>
                                    <option value="Послерейсовый/Послесменный">
                                        Послерейсовый/Послесменный
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
    @php
        // DDates
        // Req
        // DDates
        // DDates
        // DDates
        //dd($model);
        $permissionToCreate = (
            user()->access('drivers_create') && $model == 'Driver'
            || user()->access('cars_create') && $model == 'Car'
            || user()->access('company_create') && $model == 'Company'
            || user()->access('discount_create') && $model == 'Discount'
            || user()->access('service_create') && $model == 'Product'
            || user()->access('briefings_create') && $model == 'Instr'
            || user()->access('city_create') && $model == 'Town'
            || user()->access('requisites_create') && $model == 'Req'
            || user()->access('date_control_create') && $model == 'DDates'
            || user()->access('story_field_create') && $model == 'FieldHistory'
            || user()->access('pv_create') && $model == 'Point'
            || (($model == 'Car' || $model == 'Driver' || $model == 'Company') && user()->access('client_create'))
        ) && !request()->get('deleted');

        $permissionToDelete = (
            user()->access('drivers_delete') && $model == 'Driver'
            || user()->access('cars_delete') && $model == 'Car'
            || user()->access('company_delete') && $model == 'Company'
            || user()->access('discount_delete') && $model == 'Discount'
            || user()->access('service_delete') && $model == 'Product'
            || user()->access('briefings_delete') && $model == 'Instr'
            || user()->access('city_delete') && $model == 'Town'
            || user()->access('requisites_delete') && $model == 'Req'
            || user()->access('date_control_delete') && $model == 'DDates'
            || user()->access('system_delete') && $model == 'Settings'
            || user()->access('story_field_delete') && $model == 'FieldHistory'
            || user()->access('pv_delete') && $model == 'Point'
        ) && !request()->get('deleted');

        $permissionToEdit = (
            user()->access('drivers_update') && $model == 'Driver'
            || user()->access('cars_update') && $model == 'Car'
            || user()->access('company_update') && $model == 'Company'
            || user()->access('discount_update') && $model == 'Discount'
            || user()->access('service_update') && $model == 'Product'
            || user()->access('briefings_update') && $model == 'Instr'
            || user()->access('city_update') && $model == 'Town'
            || user()->access('requisites_update') && $model == 'Req'
            || user()->access('date_control_update') && $model == 'DDates'
            || user()->access('system_update') && $model == 'Settings'
            || user()->access('story_field_update') && $model == 'FieldHistory'
            || user()->access('pv_update') && $model == 'Point'
        ) && !request()->get('deleted');

        $permissionToView = (
            user()->access('drivers_read') && $model == 'Driver'
            || user()->access('cars_read') && $model == 'Car'
            || user()->access('company_read') && $model == 'Company'
            || user()->access('discount_read') && $model == 'Discount'
            || user()->access('service_read') && $model == 'Product'
            || user()->access('briefings_read') && $model == 'Instr'
            || user()->access('city_read') && $model == 'Town'
            || user()->access('requisites_read') && $model == 'Req'
            || user()->access('date_control_read') && $model == 'DDates'
            || user()->access('system_read') && $model == 'Settings'
            || user()->access('story_field_read') && $model == 'FieldHistory'
            || user()->access('pv_read') && $model == 'Point'
        );

        $permissionToTrashView = (
            user()->access('drivers_trash_read') && $model == 'Driver'
            || user()->access('cars_trash_read') && $model == 'Car'
            || user()->access('company_trash_read') && $model == 'Company'
            || user()->access('discount_trash_read') && $model == 'Discount'
            || user()->access('service_trash_read') && $model == 'Product'
            || user()->access('briefings_trash_read') && $model == 'Instr'
                || user()->access('system_trash') && $model == 'Settings'
        || user()->access('city_trash_read') && $model == 'Town'
            || user()->access('requisites_trash_read') && $model == 'Req'
            || user()->access('pv_trash_read') && $model == 'Point'
        );

        $permissionToSyncCompany = ($model === 'Company' && user()->access('company_sync'));

        $date_from_filter = now()->subMonth()->startOfMonth()->format('Y-m-d');
        $date_to_filter = now()->subMonth()->endOfMonth()->format('Y-m-d');
        //dd($permissionToTrash)
       //dd($elements);
    // $permissionToCreate = true;
    @endphp
    {{--NAVBAR--}}
    @if(!(count($elements) > $max) || !$max)
        <div class="col-md-12">
            <div class="row bg-light p-2">
                @if($permissionToCreate && !request()->get('deleted'))
                    <div class="m-2">
                        <button type="button" data-toggle="modal" data-target="#elements-modal-add"
                                class="@isset($_GET['continue']) TRIGGER_CLICK @endisset btn btn-sm btn-success">
                            Добавить <i class="fa fa-plus"></i>
                        </button>
                    </div>
                @endif

                @if($permissionToView)
                        @if(!(count($elements) >= $max) || !$max)
                    <div class=" m-2">
                        <button type="button" data-toggle-show="#elements-filters" class="btn btn-sm btn-info">
                            <i class="fa fa-filter"></i> <span class="toggle-title">Показать</span> фильтры
                        </button>
                    </div>
                        @endif
                @endif

                @if($permissionToTrashView)
                    <div class="m-2">
                        @if(!request()->get('deleted'))
                            <a href="?deleted=1" class="btn btn-sm btn-warning">
                                Корзина <i class="fa fa-trash"></i>
                            </a>
                        @else
                            <a href="{{ route('renderElements', ['model' => $model]) }}" class="btn btn-sm btn-warning">
                                Назад <i class="fa fa-trash"></i>
                            </a>
                        @endif
                    </div>
                @endif
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
                        @if(request()->get('deleted'))
                            <input type="hidden" name="deleted" value="1">
                        @endif

                        <hr>
                        <div class="row">
                            {{--DEFAULT FIELDS--}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>ID <small><i>(hash_id)</i></small></label>
                                    <input
                                        type="number"
                                        value="{{ request()->get('hash_id') }}" name="hash_id" class="form-control"/>
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
                                        @elseif($model === 'Instr' && $fk === 'sort')
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
    @if(!$permissionToView)
        @dump('Нет доступа для просмотра')
    @else
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

                            {{--                        // ($model === 'Instr' && $k === 'sort') тут какие то права были todo--}}
                            @if(false)
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

                    @if($permissionToSyncCompany && !request()->get('deleted'))
                        <th width="60">#</th>
                    @endif
                    @if($permissionToDelete)
                        {{--УДАЛЕНИЕ--}}
                        <th width="60">#</th>
                    @endif
                    @if(request()->get('deleted'))
                        <th width="60">Удаливший</th>
                        <th width="60">Время удаления</th>
                        <th width="60">#</th>
                    @endif

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
                                            @if($permissionToEdit)
                                                {{--                                        @role($otherRoles)--}}
                                                {{--                                           <a data-type="iframe" data-fancybox href="{{ route('showEditElementModal', ['id' => $el->id, 'model' => $model]) }}">--}}
                                                <a href="#" class="showEditModal"
                                                   data-route="{{ route('showEditElementModal', ['id' => $el->id, 'model' => $model]) }}"
                                                   data-toggle="modal" data-target="#modalEditor">
                                                    {{--                                            <a href="{{ route('showEditElementModal', ['id' => $el->id, 'model' => $model]) }}" data-toggle="modal" data-target="#elements-modal-{{ $el->id }}-add" class="text-info">--}}
                                                    @endif
                                                    {{--                                        @endrole--}}
                                                    @endif

                                                    @if($elK === 'products_id' || $elK === 'company_id' || $elK === 'req_id' || $elK === 'pv_id' || $elK === 'user_id' || $elK === 'town_id' || $elK === 'essence')
                                                        @if($elK === 'company_id')

                                                            @if(($model === 'Driver' || $model === 'Car') && $el->$elK && auth()->user()->hasRole('client') && auth()->user()->hasRole('operator_sdpo'))
                                                                <div>
                                                                    <a href="{{ route('renderElements', ['model' => 'Company', 'filter' => 1, 'id' => $el->company_id ]) }}">{{ app('App\Company')->getName($el->company_id) }}</a>

                                                                    <p>
                                                                        <a class="btn btn-sm btn-default"
                                                                           href="{{ route('renderElements', ['model' => 'Car', 'filter' => 1, 'company_id' => $el->company_id ]) }}"><i
                                                                                class="fa fa-car"></i></a>
                                                                        <a class="btn btn-sm btn-default"
                                                                           href="{{ route('renderElements', ['model' => 'Driver', 'filter' => 1, 'company_id' => $el->company_id ]) }}"><i
                                                                                class="fa fa-user"></i></a>
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
                                                        @if($elK == 'photo')
                                                            @if(Storage::disk('public')->exists($el[$elK]) && $el[$elK] !== '<' && $el[$elK] !== '>')

                                                                <a href="{{ Storage::url($el[$elK]) }}"
                                                                   data-fancybox="gallery_{{ $el->id }}">
                                                                    <b>
                                                                        <i class="fa fa-camera"></i>
                                                                    </b>
                                                                </a>
                                                            @endif
                                                        @else
                                                            {{--ПРОВЕРКА ДАТЫ--}}
                                                            @if($elK === 'date' || strpos($elK, '_at') > 0)
                                                                {{ date('d-m-Y H:i:s', strtotime($el[$elK])) }}
                                                            @elseif($elK === 'autosync_fields')
                                                                @foreach(explode(',', $el[$elK]) as $aSyncData)
                                                                    <div class="text-bold text-success"><i
                                                                            class="fa fa-refresh"></i> {{ __($aSyncData) }}
                                                                    </div>
                                                                @endforeach
                                                            @elseif ($elK === 'date_of_employment')
                                                                {{ $el[$elK] ? \Carbon\Carbon::parse($el[$elK])->format('d.m.Y') : '' }}
                                                                {{--                                            @elseif($model === 'Instr' && $elK === 'sort')--}}
                                                            @elseif ($elK === 'trigger')
                                                                {{ $el[$elK] === '<' ? 'меньше' : 'больше'  }}
                                                            @elseif($model === 'Instr' && $elK === 'sort')
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

                                            @if(user()->access('report_service_company_read'))
                                                <a class="btn btn-sm btn-outline-success"
                                                   href="{{ route('report.get', ['type' => 'journal', 'company_id' => $el->hash_id]) }}">
                                                    ₽
                                                </a>
                                            @endif
                                            @if(user()->access('cars_read'))
                                                <a class="btn btn-sm btn-default"
                                                   href="{{ route('renderElements', ['model' => 'Car', 'filter' => 1, 'company_id' => $el->id ]) }}">
                                                    <i class="fa fa-car"></i>
                                                </a>
                                            @endif
                                            @if(user()->access('drivers_read'))
                                                <a class="btn btn-sm btn-default"
                                                   href="{{ route('renderElements', ['model' => 'Driver', 'filter' => 1, 'company_id' => $el->id ]) }}">
                                                    <i class="fa fa-user"></i>
                                                </a>
                                            @endif
                                        </nobr>
                                        @endif

                                        @isset($fields[$elK]['filterJournalLinkKey'])
                                    </td>
                                    <td class="td-option">
                                        <nobr>
                                            @if(user()->access('medic_read'))
                                                <a class="btn btn-sm btn-danger"
                                                   href="{{ route('home', 'medic') }}/?filter=1&{{ $fields[$elK]['filterJournalLinkKey'] }}={{ $el['hash_id'] }}&date={{ $date_from_filter }}&TO_date={{ $date_to_filter }}">
                                                    МЕД
                                                </a>
                                            @endif
                                            @if(user()->access('tech_read'))
                                                <a class="btn btn-sm btn-info"
                                                   href="{{ route('home', 'tech') }}/?filter=1&{{ $fields[$elK]['filterJournalLinkKey'] }}={{ $el['hash_id'] }}&date={{ $date_from_filter }}&TO_date={{ $date_to_filter }}">
                                                    ТЕХ
                                                    @endif
                                                </a>

                                                @if(user()->access('journal_pl_accounting'))
                                                    <a class="btn btn-sm btn-dark"
                                                       href="{{ route('home', 'Dop') }}/?filter=1&{{ $fields[$elK]['filterJournalLinkKey'] }}={{ $el['hash_id'] }}&date={{ $date_from_filter }}&TO_date={{ $date_to_filter }}">
                                                        ПЛ
                                                    </a>
                                                @endif
                                        </nobr>
                                        @endisset
                                        @endif

                                        @if($elK === $editOnField)
                                        {{-- Если пользователь Менеджер --}}
                                        {{--                                        @role($otherRoles)--}}
                                        @if($permissionToEdit)
                                        </a>
                                    @endif

                                    <!-- ЗДЕСЬ БЫЛА МОДАЛКА РЕДАКТИРОВАНИЯ -->

                                        {{--                                        @endrole--}}
                                        @endif

                                    </td>
                                @endif
                            @endforeach

                            @if($permissionToSyncCompany && !request()->get('deleted'))
                                <td class="td-option"
                                    title="При синхронизации все услуги компании будут присвоены водителям и автомобилям компании.">
                                    <a href="{{ route('syncElement', ['type' => $model, 'id' => $el->id ]) }}"
                                       class="btn btn-sm btn-success"><i class="fa fa-refresh"></i></a>
                                </td>
                            @endif


                            @if($permissionToDelete)
                                {{--УДАЛЕНИЕ--}}
                                <td class="td-option">
                                    <a href="{{ route('removeElement', ['type' => $model, 'id' => $el->id ]) }}"
                                       class="ACTION_DELETE btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                </td>
                            @endif
                            @if(request()->get('deleted'))
                                <td class="td-option">
                                    {{ ($el->deleted_user->name) }}
                                </td>
                                <td class="td-option">
                                    {{ ($el->deleted_at) }}
                                </td>
                                <td class="td-option">
                                    <a href="{{ route('removeElement', ['type' => $model, 'id' => $el->id , 'undo' => 1]) }}"
                                       class="btn btn-sm btn-warning"><i class="fa fa-undo"></i></a>
                                </td>
                            @endif

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

                @if(user()->hasRole('client'))
                    <p>Элементов найдено: {{ method_exists($elements, 'total') ? $elements->total() : '' }}</p>
                @else
                    <p>Элементов всего: {{ $elements_count_all }}</p>
                    <p>Элементов
                        найдено: {{ method_exists($elements, 'total') ? $elements->total() : $elements_count_all }}</p>
                @endif
            </div>
        </div>
    @endif

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

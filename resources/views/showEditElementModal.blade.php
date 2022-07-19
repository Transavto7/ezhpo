<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Редактирование элемента "{{ $title }}"</h4>
    </div>
@php
//dd($fields, $el->toArray())
@endphp
    <form action="{{ route('updateElement', ['type' => $model, 'id' => $id ]) }}" enctype="multipart/form-data" method="POST">
        @csrf

        <div class="modal-body">
            @foreach ($fields as $k => $v)
                @php $is_required = isset($v['noRequired']) ? '' : 'required' @endphp

            @if($k == 'essence')
                    <div data-field="essence" class="form-group" @if(($el->type_product ?? '') != 'Абонентская плата без реестров')  style="display: none" @endif>
                        <label>Сущности</label>
                        <select name="essence"
                                data-label="Сущности"
                                data-field="Product_type_view"
                                class="js-chosen"
                                style="display: none;"
                        >
                            <option value="">Не установлено</option>
                            @foreach(\App\Product::$essence as $essenceKey => $essenceName)
                                <option value="{{ $essenceKey }}">
                                    {{ $essenceName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @continue
            @endif

                @if($k !== 'id' && !isset($v['hidden']))
                    <div class="form-group" data-field="{{ $k }}" @if(($el->type_product ?? '') == 'Абонентская плата без реестров' && ($k == 'type_view'|| $k == 'type_anketa' ))  style="display: none" @endif>
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
        </div>
    </form>
</div>

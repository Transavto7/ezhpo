<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Редактирование элемента "{{ $title }}"</h4>
    </div>
    <form action="{{ route('updateElement', ['type' => $model, 'id' => $id ]) }}"
          enctype="multipart/form-data"
          method="POST">
        @csrf
        <div class="modal-body">
            @foreach ($fields as $k => $v)
                @if(isset($v['hidden']) || in_array($k, $fieldsToSkip))
                    @continue
                @endif
                @if(($k === 'name') && $model === 'Company')
                    <div class="form-group" data-field="name">
                        <label>
                            {{ $v['label'] }}
                        </label>
                        <textarea name="name"
                                  data-label="{{ $v['label'] }}"
                                  placeholder="{{ $v['label'] }}"
                                  data-field="Company_name"
                                  class="form-control">
                        {{ $el[$k] ?? '' }}
                    </textarea>
                    </div>
                    @continue
                @endif
                @if(($k === 'note') && $model === 'Company')
                    <div class="form-group" data-field="comment">
                        <label>
                            {{ $v['label'] }}
                        </label>
                        <textarea {{ user()->access('companies_access_field_note') ? '' : 'disabled' }}
                                  name="note"
                                  data-label="{{ $v['label'] }}"
                                  placeholder="{{ $v['label'] }}"
                                  data-field="Company_note"
                                  class="form-control">
                            {{ $el[$k] ?? '' }}
                        </textarea>
                    </div>
                    @continue
                @endif
                @if(( $k === 'comment') && $model === 'Company')
                    <div class="form-group" data-field="comment">
                        <label>
                            {{ $v['label'] }}
                        </label>
                        <textarea
                            name="comment"
                            data-label="{{ $v['label'] }}"
                            placeholder="{{ $v['label'] }}"
                            data-field="Company_comment"
                            class="form-control">
                            {{ $el[$k] ?? '' }}
                        </textarea>
                    </div>
                    @continue
                @endif
                @if($k == 'procedure_pv' && user()->hasRole(['medic', 'tech']))
                    <div class="form-group" data-field="comment">
                        <label for="disabledProcedureInput">{{$v['label']}}</label>
                        <input type="text"
                               id="disabledProcedureInput"
                               class="form-control"
                               placeholder="{{$el[$k]}}"
                               disabled>
                    </div>
                    @continue
                @endif
                @if($k == 'unit' && ($model === 'Service' || $model === 'Product'))
                    <div data-field="essence" class="form-group">
                        <label><b class="text-danger text-bold">* </b>Сущности</label>
                        <select
                            name="essence"
                            data-label="Сущности"
                            class="filled-select2 filled-select"
                            required="required"
                        >
                            <option value="" selected>Не установлено</option>
                            @foreach(\App\Product::$essence as $essenceKey => $essenceName)
                                <option value="{{ $essenceKey }}"
                                        @if ($el->essence !== null && $el->essence == $essenceKey) selected @endif>
                                    {{ $essenceName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if($k == 'contracts')
                    @php
                        $contractCollect = collect($el->contracts);
                    @endphp

                    <div>
                        <ul class="list-group">
                            @foreach($el->contracts as $contract)
                                <li style="padding: 0;"
                                    class=" text-small list-group-item list-group-item-action list-group-item-success">
                                    <b>{{ $contract->name }}</b>
                                    @foreach($contract->services as $new_service)
                                        <ul class="list-group">
                                            <li style="padding: 0; font-size: 0.8em"
                                                class="list-group-item text-small list-group-item-action list-group-item-secondary">
                                                {{ $new_service->name }}
                                            </li>
                                        </ul>
                                    @endforeach
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @continue
                @endif
                @if($k == 'contract_id' && ($model == 'Driver' || $model == 'Car'))
                    @php
                        $contractCollect = \App\Models\Contract::query()
                            ->where('company_id', $el->company_id)
                            ->get(['id', 'name']);

                        if ($model === 'Car') {
                            $contractForDriverOrCar = \App\Car::query()
                                ->with('contracts')
                                ->find($el->id);
                        }

                        if ($model === 'Driver') {
                            $contractForDriverOrCar = \App\Driver::query()
                                ->with('contracts')
                                ->find($el->id);
                        }
                    @endphp
                    @if(( $model == 'Driver' && user()->access('contract_edit_driver')) || ($model == 'Car' && user()->access('contract_edit_car')) )
                        <div data-field="contract" class="form-group">
                            <label>Договор</label>
                            <select name="contract_ids[]"
                                    data-label="Договор"
                                    id="select_for_contract_driver_car"
                                    class="js-chosen"
                                    style="display: none;"
                                    multiple="multiple"
                            >
                                @foreach($contractCollect as $contract)
                                    <option value="{{ $contract->id }}"
                                            @if($contractForDriverOrCar->contracts->where('id', $contract->id)->first()) selected @endif
                                    >
                                        {{ $contract->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <select name="contract_ids[]" hidden>
                            @foreach($contractForDriverOrCar->contracts as $contract)
                                <option value="{{ $contract->id }}"></option>
                            @endforeach
                        </select>
                    @endif
                    <div>
                        <ul class="list-group">
                            @foreach($el->contracts as $contract)
                                <li style="padding: 0;"
                                    class=" text-small list-group-item list-group-item-action list-group-item-success">
                                    <b>{{ $contract->name }}</b>
                                    @foreach($contract->services as $new_service)
                                        <ul class="list-group">
                                            <li style="padding: 0; font-size: 0.8em"
                                                class="list-group-item text-small list-group-item-action list-group-item-secondary">
                                                {{ $new_service->name }}
                                            </li>
                                        </ul>
                                    @endforeach
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @continue
                @endif
                <div class="form-group" data-field="{{ $k }}">
                    @php
                        $is_required = !isset($v['noRequired']);
                    @endphp

                    <label>
                        @if($is_required)
                            <b class="text-danger text-bold">* </b>
                        @endif
                        {{ $v['label'] }}
                    </label>

                    @include('templates.elements_field', [
                        'v' => $v,
                        'k' => $k,
                        'default_value' => $el[$k],
                        'element_id' => $el['id'],
                        'disabled' => in_array($k, $disabledFields)
                    ])

                    @if(isset($v['syncData']) && $model !== 'Company')
                        @foreach($v['syncData'] as $syncData)
                            <a href="{{ route('syncDataElement', [
                                    'model' => $syncData['model'],
                                    'fieldFind' => $syncData['fieldFind'],
                                    'fieldFindId' => $el['id'],
                                    'fieldSync' => $k,
                                    'fieldSyncValue' => $el[$k]
                                ]) }}" target="_blank" class="text-info btn-link">
                                <i class="fa fa-spinner"></i> Синхронизация с: {{ $syncData['text'] }}
                            </a>
                        @endforeach
                    @endif
                </div>
            @endforeach
            @if($model == "Company")
                <input type="checkbox"
                       name="required_type_briefing"
                       id="required_type_briefing_checkbox"
                       @if(App\Company::find($id)->required_type_briefing == 1) checked @endif>
                <label for="required_type_briefing_checkbox">
                    Автоматическое проведение инструктажа водителям
                </label>
            @endif
            @if($model == "Instr")
                <input type="checkbox"
                       name="is_default"
                       id="is_default_checkbox"
                       @if(App\Instr::find($id)->is_default) checked @endif>
                <label for="is_default_checkbox">
                    Базовый инструктаж
                </label>
            @endif
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Сохранить</button>
        </div>
    </form>
</div>

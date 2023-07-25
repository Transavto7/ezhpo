<input type="hidden" name="filter" value="1">

@isset($_GET['trash'])
    <input type="hidden" name="trash" value="{{ request()->get('trash', 0) }}">
@endisset

<input type="hidden" name="take" value="{{ request()->get('take', '') }}">

<div class="tab-pane fade show active" id="filter-group-1" role="tabpanel" aria-labelledby="filter-group-1">
    {{--ОТКРЫТЫЕ ПО УМОЛЧАНИЮ ГРУППЫ ПОЛЕЙ--}}

    @if($fieldsGroupFirst)
        <div class="row">
            @isset ($fieldsGroupFirst['id'])
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ $fieldsGroupFirst['id'] }}</label>

                        <input type="number" name="id" class="form-control" value="{{ request()->get('id') ? request()->get('id') : '' }}">
                    </div>
                </div>
            @endisset

            @php
                // если есть дата в _GET запросе, но пустая, то оставляем пустую
                if(array_key_exists('date', request()->all())) {
                    $date_from_filter = '';
                    $date_to_filter = '';
                }else{ // иначе берём начало и конец прошлого месяца
                    $date_from_filter = now()->subMonth()->startOfMonth()->format('Y-m-d');;
                    $date_to_filter = now()->subMonth()->endOfMonth()->format('Y-m-d');
                }
            @endphp

            @foreach($anketsFields as $field)
                @isset($fieldsGroupFirst[$field])
                    @if (auth()->user()->hasRole('client') && in_array($field, $exclude))
                        @continue
                    @endif

                    <div class="col-md-3">
                        <div class="form-group">
                            @if($field === 'date' || strpos($field, '_at') > 0)
                                <label>{{ (isset($fieldsGroupFirst[$field]['name'])) ? $fieldsGroupFirst[$field]['name'] : $fieldsGroupFirst[$field] }} от</label>
                            @else
                                <label>{{ (isset($fieldsGroupFirst[$field]['name'])) ? $fieldsGroupFirst[$field]['name'] : $fieldsGroupFirst[$field] }}</label>
                            @endif

                            @php $field_view_key = join('_', explode('.', $field)); @endphp
                            @php $field_view = 'profile.ankets.fields.' . $field_view_key; @endphp

                            @if(View::exists($field_view))
                                @include($field_view, [
                                    'field_default_value' => !empty(request()->get($field_view_key)) ? request()->get($field_view_key) : 'Не установлено'
                                ])
                            @else
                                {{--ИЗНАЧАЛЬНОЕ ПОЛЕ ФИЛЬТР--}}
                                <input
                                        @if($field === 'date' || $field === 'date_prto' || strpos($field, '_at') > 0)
                                            type="date"
                                        @else
                                            type="search"
                                        @endif

                                        value="{{ request()->get($field) ? request()->get($field) : (($field === 'date' || strpos($field, '_at')) ? $date_from_filter : '') }}" name="{{ $field }}" class="form-control" />
                            @endif

                        </div>
                    </div>

                    {{--ФИЛЬТР ДО--}}
                    @if($field === 'date' || strpos($field, '_at') > 0)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ (isset($fieldsGroupFirst[$field]['name'])) ? $fieldsGroupFirst[$field]['name'] : $fieldsGroupFirst[$field] }} до</label>
                                <input
                                        type="date"
                                        value="{{ request()->get('TO_'.$field) ? request()->get('TO_'.$field) : $date_to_filter }}"
                                        name="TO_{{ $field }}" class="form-control" />
                            </div>
                        </div>
                    @endif
                @endisset
            @endforeach
        </div>
    @endif
</div>
<div class="tab-pane fade" id="filter-group-2" role="tabpanel" aria-labelledby="filter-group-2">
    <!-- filter-group-2 -->
    <div class="row">
        @if (!isset($fieldsGroupFirst['id']))
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ $fieldsKeys['id'] ?? 'ID' }}</label>

                    <input type="number" name="id" class="form-control" value="{{ request()->get('id') ? request()->get('id') : '' }}">
                </div>
            </div>
        @endif

        @foreach($anketsFields as $field)
            @if(!isset($fieldsGroupFirst[$field]))
                @isset($fieldsKeys[$field])
                    @php
                        if ($field === 'company_name') continue;
                    @endphp

                    @if (auth()->user()->hasRole('client') && in_array($field, $exclude))
                        @continue
                    @endif

                    <div class="col-md-3">
                        <div class="form-group">
                            @if($field === 'date' || strpos($field, '_at') > 0)
                                <label>{{ (isset($fieldsKeys[$field]['name'])) ? $fieldsKeys[$field]['name'] : $fieldsKeys[$field] }} от</label>
                            @else
                                <label>{{ (isset($fieldsKeys[$field]['name'])) ? $fieldsKeys[$field]['name'] : $fieldsKeys[$field] }}</label>
                            @endif

                            @php $field_view_key = join('_', explode('.', $field)); @endphp
                            @php $field_view = 'profile.ankets.fields.' . $field_view_key; @endphp

                            @if(View::exists($field_view))
                                @include($field_view, [
                                    'field_default_value' => !empty(request()->get($field_view_key)) ? request()->get($field_view_key) : 'Не установлено'
                                ])
                            @else
                                {{--ИЗНАЧАЛЬНОЕ ПОЛЕ ФИЛЬТР--}}
                                <input
                                        @if($field === 'date' || strpos($field, '_at') > 0)
                                            type="date"
                                        @else
                                            type="search"
                                        @endif

                                        value="{{ request()->get($field) }}" name="{{ $field }}" class="form-control" />
                            @endif

                        </div>
                    </div>

                    {{--ФИЛЬТР ДО--}}
                    @if($field === 'date' || strpos($field, '_at') > 0)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ (isset($fieldsKeys[$field]['name'])) ? $fieldsKeys[$field]['name'] : $fieldsKeys[$field] }} до</label>
                                <input
                                        type="date"
                                        value="{{ request()->get('TO_'.$field) }}"
                                        name="TO_{{ $field }}" class="form-control" />
                            </div>
                        </div>
                    @endif

                @endisset
            @endif
        @endforeach

        @if($type_ankets == 'tech' || $type_ankets == 'medic')
            <div class="col-md-3">
                <div class="form-group">
                    <label><b>Время проведения осмотра с:</b></label>
                    <input
                            type="time"
                            value="{{ request()->get('hour_from') }}"
                            name="hour_from" class="form-control" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label><b>Время проведения осмотра до:</b></label>
                    <input
                            type="time"
                            value="{{ request()->get('hour_to') }}"
                            name="hour_to" class="form-control" />
                </div>
            </div>
        @endif
    </div>
</div>


<button type="submit" class="btn btn-info">Поиск</button>
<button type="button" class="btn btn-danger reload-filters">
    <span class="spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    Сбросить
</button>

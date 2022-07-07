<input type="hidden" name="filter" value="1">

@isset($_GET['trash'])
    <input type="hidden" name="trash" value="{{ request()->get('trash', 0) }}">
@endisset

<input type="hidden" name="take" value="{{ request()->get('take', '') }}">

<div class="tab-pane fade show active" id="filter-group-1" role="tabpanel" aria-labelledby="filter-group-1">
    {{--ОТКРЫТЫЕ ПО УМОЛЧАНИЮ ГРУППЫ ПОЛЕЙ--}}

    @if($fieldsGroupFirst)
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>ID</label>

                    <input type="number" name="id" class="form-control" value="{{ request()->get('id') ? request()->get('id') : '' }}">
                </div>
            </div>

            @php
                // если есть дата в _GET запросе, но пустая, то оставляем пустую
                if(array_key_exists('date', request()->all())){
                    if(is_null(request()->get('date'))){
                        $date_from_filter = '';
                        $date_to_filter = '';
                    }
                }else{ // иначе берём начало и конец прошлого месяца
                    $date_from_filter = now()->subMonth()->startOfMonth()->format('Y-m-d H:i');;
                    $date_to_filter = now()->subMonth()->endOfMonth()->format('Y-m-d H:i');
                }
            @endphp

            @foreach($anketsFields as $field)
                @isset($fieldsGroupFirst[$field])
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ (isset($fieldsGroupFirst[$field]['name'])) ? $fieldsGroupFirst[$field]['name'] : $fieldsGroupFirst[$field] }}</label>

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
                                    type="datetime-local"
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
                                <label><b>ДО</b></label>
                                <input
                                    type="datetime-local"
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
        @foreach($anketsFields as $field)
            @if(!isset($fieldsGroupFirst[$field]))
                @isset($fieldsKeys[$field])
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ (isset($fieldsKeys[$field]['name'])) ? $fieldsKeys[$field]['name'] : $fieldsKeys[$field] }}</label>

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
                                    type="datetime-local"
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
                                <label><b>ДО</b></label>
                                <input
                                    type="datetime-local"
                                    value="{{ request()->get('TO_'.$field) }}"
                                    name="TO_{{ $field }}" class="form-control" />
                            </div>
                        </div>
                    @endif

                @endisset
            @endif
        @endforeach
    </div>
</div>


<button type="submit" class="btn btn-info">Поиск</button>
<button type="button" class="btn btn-danger reload-filters">
    <span class="spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    Сбросить
</button>

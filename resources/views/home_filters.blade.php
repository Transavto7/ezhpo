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
                                    type="date"
                                    @else
                                    type="search"
                                    @endif

                                    value="{{ request()->get($field) ? request()->get($field) : (($field === 'date' || strpos($field, '_at')) ? date('Y-m-01') : '') }}" name="{{ $field }}" class="form-control" />
                            @endif

                        </div>
                    </div>

                    {{--ФИЛЬТР ДО--}}
                    @if($field === 'date' || strpos($field, '_at') > 0)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><b>ДО</b></label>
                                <input
                                    type="date"
                                    value="{{ request()->get('TO_'.$field) ? request()->get('TO_'.$field) : date('Y-m-d') }}"
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
                                <label><b>ДО</b></label>
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
    </div>
</div>


<button type="submit" class="btn btn-info">Поиск</button>
<a href="{{ route('home', $type_ankets) }}" class="btn btn-danger">Сбросить</a>

<input type="hidden" name="filter" value="1">

@if(request()->filled('trash'))
    <input type="hidden" name="trash" value="{{ request()->get('trash') }}">
@endif

@if(request()->filled('duplicates'))
    <input type="hidden" name="duplicates" value="{{ request()->get('duplicates') }}">
@endif

<input type="hidden" name="take" value="{{ request()->get('take', '') }}">

<div class="tab-pane fade show active" id="filter-group-1" role="tabpanel" aria-labelledby="filter-group-1">
    {{--ОТКРЫТЫЕ ПО УМОЛЧАНИЮ ГРУППЫ ПОЛЕЙ--}}

    @if($fieldsGroupFirst)
        <div class="row">
            @isset($fieldsGroupFirst['id'])
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ $fieldsGroupFirst['id'] }}</label>
                        <input type="number" name="id" class="form-control" value="{{ request()->get('id') ?? '' }}">
                    </div>
                </div>
            @endisset

            @php
                $date_from_filter = '';
                $date_to_filter = '';
                if (!request()->filled('date') && !request()->filled('filter')) {
                    $date_from_filter = now()->subMonth()->startOfMonth()->format('Y-m-d');;
                    $date_to_filter = now()->subMonth()->endOfMonth()->format('Y-m-d');
                }
            @endphp

            @foreach($anketsFields as $field)
                @if(!isset($fieldsGroupFirst[$field]))
                    @continue
                @endisset

                @if(auth()->user()->hasRole('client') && in_array($field, $exclude))
                    @continue
                @endif

                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ $fieldsGroupFirst[$field]['name'] ?? $fieldsGroupFirst[$field] }}
                            @if($field === 'date' || strpos($field, '_at') > 0)
                                от
                            @endif
                        </label>

                        @php
                            $field_view_key = join('_', explode('.', $field));
                            $field_view = 'profile.ankets.fields.' . $field_view_key;
                        @endphp

                        @if(View::exists($field_view))
                            @include($field_view, [
                                'field_default_value' => request()->get($field_view_key, null)
                            ])
                        @else
                            {{--ИЗНАЧАЛЬНОЕ ПОЛЕ ФИЛЬТР--}}
                            <input
                                @if(in_array($field, ['date', 'date_prto', 'date_prmo']) || strpos($field, '_at') > 0)
                                    type="date"
                                @else
                                    type="search"
                                @endif

                                value="{{ request()->get($field, $field === 'date' ? $date_from_filter : '')  }}"
                                name="{{ $field }}" class="form-control"/>
                        @endif

                    </div>
                </div>

                {{--ФИЛЬТР ДО--}}
                @if($field === 'date' || strpos($field, '_at') > 0)
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ $fieldsGroupFirst[$field]['name'] ?? $fieldsGroupFirst[$field] }} до</label>
                            <input
                                type="date"
                                value="{{ request()->get('TO_'.$field, $field === 'date' ? $date_to_filter : '') }}"
                                name="TO_{{ $field }}" class="form-control"/>
                        </div>
                    </div>
                @endif
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

                    <input type="number" name="id" class="form-control"
                           value="{{ request()->get('id') ? request()->get('id') : '' }}">
                </div>
            </div>
        @endif

        @foreach($anketsFields as $field)
            @if(isset($fieldsGroupFirst[$field]))
                @continue;
            @endif

            @if(!isset($fieldsKeys[$field]))
                @continue;
            @endif

            @if($field === 'company_name')
                @continue;
            @endif

            @if (auth()->user()->hasRole('client') && in_array($field, $exclude))
                @continue
            @endif

            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ $fieldsKeys[$field]['name'] ?? $fieldsKeys[$field] }}
                        @if($field === 'date' || strpos($field, '_at') > 0)
                            от
                        @endif
                    </label>

                    @php
                        $field_view_key = join('_', explode('.', $field));
                        $field_view = 'profile.ankets.fields.' . $field_view_key;
                    @endphp

                    @if(View::exists($field_view))
                        @include($field_view, [
                            'field_default_value' => request()->get($field_view_key, null)
                        ])
                    @else
                        {{--ИЗНАЧАЛЬНОЕ ПОЛЕ ФИЛЬТР--}}
                        <input
                            @if($field === 'date' || strpos($field, '_at') > 0)
                                type="date"
                            @else
                                type="search"
                            @endif

                            value="{{ request()->get($field) }}" name="{{ $field }}" class="form-control"/>
                    @endif

                </div>
            </div>

            {{--ФИЛЬТР ДО--}}
            @if($field === 'date' || strpos($field, '_at') > 0)
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ $fieldsKeys[$field]['name'] ?? $fieldsKeys[$field] }} до</label>
                        <input
                            type="date"
                            value="{{ request()->get('TO_'.$field) }}"
                            name="TO_{{ $field }}" class="form-control"/>
                    </div>
                </div>
            @endif
        @endforeach

        @if($type_ankets == \App\Enums\FormTypeEnum::TECH || $type_ankets == \App\Enums\FormTypeEnum::MEDIC)
            <div class="col-md-3">
                <div class="form-group">
                    <label><b>Время проведения осмотра с:</b></label>
                    <input
                        type="time"
                        value="{{ request()->get('hour_from') }}"
                        name="hour_from" class="form-control"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label><b>Время проведения осмотра до:</b></label>
                    <input
                        type="time"
                        value="{{ request()->get('hour_to') }}"
                        name="hour_to" class="form-control"/>
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

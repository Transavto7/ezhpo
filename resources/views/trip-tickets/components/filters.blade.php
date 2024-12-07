<div class="tab-pane fade" id="filter-group" role="tabpanel"
     aria-labelledby="filter-group" style="display: none">
    <form onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
          action="" method="GET" class="tab-content p-3">
        <input type="hidden" name="filter" value="1">

        @if(request()->filled('trash'))
            <input type="hidden" name="trash" value="{{ request()->get('trash') }}">
        @endif

        <input type="hidden" name="take" value="{{ request()->get('take', '') }}">

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Компания</label>
                    @include('templates.elements_field', [
                        'v' => [
                            'type' => 'select',
                            'values' => 'Company',
                            'getField' => 'name',
                            'getFieldKey' => 'hash_id',
                            'multiple' => 1,
                            'concatField' => 'hash_id',
                            'trashed' => true
                        ],
                        'model' => 'trip-tickets',
                        'k' => 'company_id',
                        'is_required' => '',
                        'default_value' => null
                    ])
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Водитель</label>
                    @include('templates.elements_field', [
                        'v' => [
                            'type' => 'select',
                            'values' => 'Driver',
                            'getField' => 'fio',
                            'getFieldKey' => 'hash_id',
                            'multiple' => 1,
                            'concatField' => 'hash_id',
                            'trashed' => true
                        ],
                        'model' => 'trip-tickets',
                        'k' => 'driver_id',
                        'is_required' => '',
                        'default_value' => null
                    ])
                </div>
            </div>
            @php
                $date_from_filter = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $date_to_filter = now()->subMonth()->endOfMonth()->format('Y-m-d');
            @endphp
            <div class="col-md-3">
                <div class="form-group">
                    <label>Дата ПЛ от</label>
                    <input type="date" value="{{ $date_from_filter }}"
                           name="date_from"
                           class="form-control"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Дата ПЛ до</label>
                    <input type="date"
                           value="{{ $date_to_filter }}" name="date_to"
                           class="form-control"/>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-info">Поиск</button>
        <button type="button" class="btn btn-danger reload-filters">
            <span class="spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            Сбросить
        </button>

    </form>
</div>

<div class="tab-pane fade" id="registry-update" role="tabpanel"
     aria-labelledby="registry-update" style="display: none">
    <form action="{{ route('trip-tickets.generate') }}" method="POST"
          class="tab-content p-3">
        @csrf
        <input type="hidden" name="create" value="1">

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
                            'concatField' => 'hash_id',
                            'trashed' => true
                        ],
                        'model' => 'trip-tickets',
                        'k' => 'company_id',
                        'is_required' => 'required',
                        'default_value' => ! request()->filled('filter', null)
                            ? request()->get('company_id', null)
                            : null
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
                            'concatField' => 'hash_id',
                            'trashed' => true
                        ],
                        'model' => 'trip-tickets',
                        'k' => 'driver_id',
                        'is_required' => '',
                        'default_value' => ! request()->filled('filter', null)
                            ? request()->get('driver_id', null)
                            : null
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
                    <input type="date" value="{{ ! request()->filled('filter')
                            ? request()->get('date_from', $date_from_filter)
                            : $date_from_filter }}"
                           name="date_from"
                           class="form-control"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Дата ПЛ до</label>
                    <input type="date"
                           value="{{ ! request()->filled('filter')
                            ? request()->get('date_to', $date_to_filter)
                            : $date_to_filter }}" name="date_to"
                           class="form-control"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Вид сообщения</label>
                    @include('templates.elements_field', [
                        'v' => [
                            'type' => 'select',
                            'values' => App\Enums\LogisticsMethodEnum::labels(),
                        ],
                        'model' => 'trip-tickets',
                        'k' => 'logistics_method',
                        'is_required' => 'required',
                        'default_value' => ! request()->filled('filter', null)
                            ? request()->get('logistics_method', null)
                            : null
                    ])
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Вид перевозки</label>
                    @include('templates.elements_field', [
                        'v' => [
                            'type' => 'select',
                            'values' => App\Enums\TransportationTypeEnum::labels(),
                        ],
                        'model' => 'trip-tickets',
                        'k' => 'transportation_type',
                        'is_required' => 'required',
                        'default_value' => ! request()->filled('filter', null)
                            ? request()->get('transportation_type', null)
                            : null
                    ])
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Шаблон ПЛ</label>
                    @include('templates.elements_field', [
                        'v' => [
                            'type' => 'select',
                            'values' => App\Enums\TripTicketTemplateEnum::labels(),
                        ],
                        'model' => 'trip-tickets',
                        'k' => 'template_code',
                        'is_required' => 'required',
                        'default_value' => ! request()->filled('filter', null)
                            ? request()->get('template_code', null)
                            : null
                    ])
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Срок действия, дней</label>
                    <input class="form-control" type="number" value="1" name="validity_period" min="1"/>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-info">Сформировать</button>
        <a class="btn btn-danger reload-filters" href="{{ route('trip-tickets.index') }}">
            <span class="spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            Сбросить
        </a>
    </form>
</div>

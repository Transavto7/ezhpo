<div class="tab-pane fade show active" id="registry-update" role="tabpanel"
     aria-labelledby="registry-update" style="display: none">
    <form action="{{ route('trip-tickets.generate') }}" method="GET"
          class="tab-content p-3">
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
                        'default_value' => null
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
                        'default_value' => null
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
                        'default_value' => null
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
    </form>
</div>

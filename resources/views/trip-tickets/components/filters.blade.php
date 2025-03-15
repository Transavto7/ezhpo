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
        <label>Номер ПЛ</label>
        <input class="form-control" type="text" value="{{ request()->get('ticket_number', null) }}"
               name="ticket_number"/>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Фото загружено?</label>
        @include('templates.elements_field', [
            'v' => [
                'type' => 'select',
                'values' => [
                    'true' => 'Да',
                    'false' => 'Нет'
                ]
            ],
            'model' => 'trip-tickets',
            'k' => 'has_photos',
            'is_required' => '',
            'default_value' => request()->get('has_photos', null) !== [null]
              ? request()->get('has_photos', null)
              : null
        ])

      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Тип осмотра</label>
        @include('templates.elements_field', [
            'v' => [
                'type' => 'select',
                'values' => \App\Enums\TripTicket\TripTicketType::labels(),
            ],
            'model' => 'trip-tickets',
            'k' => 'type',
            'is_required' => '',
            'default_value' => request()->get('type', null) !== [null]
                ? request()->get('type', null)
                : null
        ])

      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Осмотр утвержден?</label>
        @include('templates.elements_field', [
            'v' => [
                'type' => 'select',
                'values' => [
                    'true' => 'Да',
                    'false' => 'Нет'
                ]
            ],
            'model' => 'trip-tickets',
            'k' => 'is_approved',
            'is_required' => '',
            'default_value' => request()->get('is_approved', null) !== [null]
              ? request()->get('is_approved', null)
              : null
        ])

      </div>
    </div>
  </div>

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
            'default_value' => request()->get('company_id', null) !== [null]
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
                'multiple' => 1,
                'concatField' => 'hash_id',
                'trashed' => true
            ],
            'model' => 'trip-tickets',
            'k' => 'driver_id',
            'is_required' => '',
            'default_value' => request()->get('driver_id', null) !== [null]
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
        <input type="date" value="{{ request()->filled('filter')
                            ? request()->get('date_from', null)
                            : $date_from_filter }}"
               name="date_from"
               class="form-control"/>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Дата ПЛ до</label>
        <input type="date"
               value="{{ request()->filled('filter')
                            ? request()->get('date_to', null)
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
                'values' => \App\Enums\TripTicket\LogisticsMethodEnum::labels(),
            ],
            'model' => 'trip-tickets',
            'k' => 'logistics_method',
            'is_required' => '',
            'default_value' => request()->get('logistics_method', null)
        ])
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Вид перевозки</label>
        @include('templates.elements_field', [
            'v' => [
                'type' => 'select',
                'values' => \App\Enums\TripTicket\TransportationTypeEnum::labels(),
            ],
            'model' => 'trip-tickets',
            'k' => 'transportation_type',
            'is_required' => '',
            'default_value' => request()->get('transportation_type', null)
        ])
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Шаблон ПЛ</label>
        @include('templates.elements_field', [
            'v' => [
                'type' => 'select',
                'values' => \App\Enums\TripTicket\TripTicketTemplateEnum::labels(),
            ],
            'model' => 'trip-tickets',
            'k' => 'template_code',
            'is_required' => '',
            'default_value' => request()->get('template_code', null)
        ])
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Срок действия, дней</label>
        <input class="form-control" type="number" value="{{ request()->get('validity_period', null) }}"
               name="validity_period" min="1"/>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3">
      <div class="form-group">
        <label>Дата выдачи от</label>
        <input type="date" value="{{ request()->filled('filter')
                            ? request()->get('created_date_from', null)
                            : null }}"
               name="created_date_from"
               class="form-control"/>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Дата выдачи до</label>
        <input type="date"
               value="{{ request()->filled('filter')
                            ? request()->get('created_date_to', null)
                            : null }}" name="created_date_to"
               class="form-control"/>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>ФИО ответственного</label>
        @include('templates.elements_field', [
            'v' => [
                'type' => 'select',
                'values' => 'User',
                'getField' => 'name',
                'getFieldKey' => 'id',
                'multiple' => 1,
                'concatField' => 'hash_id',
                'trashed' => true
            ],
            'model' => 'trip-tickets',
            'k' => 'user_id',
            'is_required' => '',
            'default_value' => request()->get('user_id', null) !== [null]
                ? request()->get('user_id', null)
                : null
        ])
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-info">Поиск</button>
  <a class="btn btn-danger reload-filters" href="{{ route('trip-tickets.index') }}">
    <span class="spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    Сбросить
  </a>
</form>

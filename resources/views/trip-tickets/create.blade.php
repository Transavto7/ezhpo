@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@php
    use Carbon\Carbon;
    $created = \Illuminate\Support\Facades\Session::get('created', []);
    $createByForms = $createByForms ?? request()->get('createByForms', '0') === '1';
@endphp

@section('custom-scripts')
    <script type="text/javascript">
        if (screen.width <= 700) {
            ANKETA_FORM_VIEW.insertBefore(ANKETA_FORM_ROOT, ANKETA_FORM_VIEW_FIRST)
        }

        $(document).ready(function () {
            const datesSelector = $("input[name='additional_dates']")
            initDatePicker(datesSelector)

            let ctr = 1;

            $('#trip-ticket-clone-btn').click(e => {
                const CLONE_ID = 'clone'

                if (ctr + 1 === 32) {
                    swal.fire({
                        title: 'Нельзя добавлять более 31 путевого листа',
                        icon: 'warning'
                    });

                    return
                }

                let firstClone = $(`#first-${CLONE_ID}`)
                let cloneTo = $('#clone-stack')
                let clone = firstClone.clone()
                let randId = 'trip_ticket_' + ctr

                if (cloneTo.find('.cloning-clone').length) {
                    clone = cloneTo.find('.cloning-clone').last().clone()
                }

                clone.removeAttr('id').addClass('cloning-clone')
                clone.attr('id', randId)
                cloneTo.append(clone)

                clone.find('input,select').each(function () {
                    this.name = this.name.replace('trip_ticket[' + (ctr - 1) + ']', 'trip_ticket[' + ctr + ']')
                })

                ctr++

                clone.find('.trip-ticket-delete').html('<a href="" onclick="' + randId + '.remove(); return false;" class="text-danger">Удалить</a>')
            })

            $('#trip-ticket-print-btn').click(function () {
                const spinner = $('.spinner-btn')
                const created = JSON.parse('@json($created)')
                const ids = created.map(item => item.uuid)

                spinner.attr('style', '')
                $(this).attr('style', 'display:none')

                axios({
                    method: 'post',
                    url: '{{ route('trip-tickets.mass-print') }}',
                    data: {
                        ids: ids,
                    },
                    responseType: 'blob',
                })
                    .then((response) => {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');

                        link.href = url;
                        link.setAttribute('download', 'Путевой лист.xlsx');

                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                    })
                    .catch((error) => {
                        if (error.response && error.response.data instanceof Blob) {
                            const blob = error.response.data;

                            blob.text().then((text) => {
                                try {
                                    const errorData = JSON.parse(text);
                                    let message = errorData.error ?? '';

                                    swal.fire({
                                        title: 'При формировании файла произошла ошибка',
                                        text: message,
                                        icon: 'error'
                                    });
                                } catch (e) {
                                    swal.fire({
                                        title: 'При формировании файла произошла ошибка',
                                        text: text,
                                        icon: 'error'
                                    });
                                }
                            });
                        } else {
                            swal.fire({
                                title: 'При формировании файла произошла ошибка',
                                icon: 'error'
                            });
                        }
                    })
                    .finally(() => {
                        spinner.attr('style', 'display:none')
                        $(this).attr('style', '')
                    })
            })
        })
    </script>
@endsection

@section('content')
    @include('profile.ankets.components.fast-scroll')

    <div class="row" id="ANKETA_FORM_VIEW">
        <div class="col-lg-3" id="ANKETA_FORM_VIEW_FIRST">
            <div class="card">
                <div class="card-body">
                    <p><b>Карточка автомобиля</b></p>

                    <div id="CARD_CAR">
                        Не найдено
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3" id="ANKETA_FORM_ROOT">
            <div class="card">
                <div class="card-body">
                    <p><b>{{ $title }}</b></p>

                    <article class="anketa anketa-fields">
                        @foreach($errors ?? [] as $error)
                            <div class="alert alert-danger" role="alert">{{ $error }}</div>
                        @endforeach

                        @if(count($created ?? []))
                            @if(count($created ?? []) <= 15)
                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-center">
                                        <button type="button" id="trip-ticket-print-btn" class="btn btn-sm btn-success">
                                            <i class="fa fa-print"></i> Распечатать путевые листы
                                        </button>
                                        <button type="button" class="btn btn-success spinner-btn" style="display: none" disabled>
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                            Загрузка...
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                @foreach($created ?? [] as $tripTicket)
                                    <div class="col-md-12">
                                        <div class="card p-2 text-xsmall">
                                            <b>Путевой лист успешно создан!</b>
                                            <span>Номер путевого листа: <b>{{ $tripTicket->ticket_number }}</b></span>

                                            @if($tripTicket->company && $tripTicket->company->name)
                                                <span>Компания: <b>{{ $tripTicket->company->name }}</b></span>
                                            @endif

                                            @if($tripTicket->driver && $tripTicket->driver->fio)
                                                <span>Водитель: <b>{{ $tripTicket->driver->fio }}</b></span>
                                            @endif

                                            @if($tripTicket->car && $tripTicket->car->gos_number)
                                                <span>Госномер автомобиля: <b>{{ $tripTicket->car->gos_number }}</b></span>
                                            @endif

                                            @if($tripTicket->start_date)
                                                <span><i>Дата начала действия:<b> {{ Carbon::parse($tripTicket->start_date)->format('d.m.Y') }}</b></i></span>
                                            @endif

                                            @if($tripTicket->start_date === null && $tripTicket->period_pl)
                                                <span><i>Период выдачи ПЛ:<b> {{ Carbon::parse($tripTicket->period_pl)->format('m.Y') }}</b></i></span>
                                            @endif

                                            @if($tripTicket->validity_period)
                                                <span><i>Срок действия:<b> {{ $tripTicket->validity_period }}</b></i></span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(\Illuminate\Support\Facades\Session::has('message'))
                            <div class="alert alert-success">
                                <b>{{ \Illuminate\Support\Facades\Session::get('message') }}</b>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ $createByForms
                                  ? route('trip-tickets.generate')
                                  : route('trip-tickets.store') }}"
                              class="form-horizontal"
                              onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
                              enctype="multipart/form-data" id="ANKETA_FORM">
                            @csrf

                            <div class="form-group d-flex">
                                <a class="text-small"
                                   href="{{ route('trip-tickets.create', ['createByForms' => ! $createByForms]) }}">
                                    <input onchange="this.parentNode.click()" type="checkbox"
                                           @if($createByForms) checked @endif id="create_by_forms">
                                </a>
                                <label class="form-control-label mb-0 ml-2" for="create_by_forms">Создать на основе
                                    МО/ТО</label>
                                <input type="hidden" name="createByForms" value="{{ $createByForms ?? 0 }}">
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">ID компании:</label>
                                <article>
                                    <input value="{{ $company_id ?? '' }}"
                                           required
                                           type="number"
                                           oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Company', event.target.value, 'name', $(event.target).parent(), {{ !($id ?? false) ? 'true' : 'false' }})"
                                           min="5"
                                           name="company_id"
                                           class="MASK_ID_ELEM form-control">
                                    <p class="app-checker-prop"></p>
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">ID водителя:</label>
                                <article>
                                    <div class="d-flex">
                                        <input value="{{ $driver_id ?? '' }}" type="number"
                                               oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent().parent(), {{ 'false' }})"
                                               min="6"
                                               name="driver_id"
                                               class="MASK_ID_ELEM form-control">
                                    </div>
                                    <p class="app-checker-prop"></p>
                                </article>
                            </div>

                            @if(! $createByForms)
                                <div class="form-group">
                                    <label class="form-control-label">ID автомобиля:</label>
                                    <article>
                                        <div class="d-flex">
                                            <input value="{{ $car_id ?? '' }}" type="number"
                                                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent().parent(), {{ 'false' }})"
                                                   min="6"
                                                   name="car_id"
                                                   class="MASK_ID_ELEM form-control car-input">
                                        </div>
                                        <p class="app-checker-prop"></p>
                                    </article>
                                </div>
                            @endif

                            <div class="clone" id="first-clone">
                                <div class="form-group">
                                    <label class="form-control-label">
                                        @if($createByForms)
                                            Дата первого осмотра:
                                        @else
                                            Дата начала действия:
                                        @endif
                                    </label>
                                    <article>
                                        <input min="1970-01-01"
                                               max="2100-01-01"
                                               oninput="$(this).closest('.clone').find('.period_pl').prop('required', !(this.value.length > 0))"
                                               type="date"
                                               value="{{ $default_current_date ?? '' }}"
                                               class="form-control date_from"
                                               required
                                               @if($createByForms)
                                                   name="date_from"
                                               @else
                                                   name="trip_ticket[0][date_from]"
                                               @endif
                                        >
                                    </article>
                                </div>

                                @if($createByForms)
                                    <div class="form-group">
                                        <label class="form-control-label">Дата последнего осмотра (не более 31 дня от
                                            первого):</label>
                                        <article>
                                            <input min="1970-01-01"
                                                   max="2100-01-01"
                                                   type="date"
                                                   value="{{ $default_current_date ?? '' }}"
                                                   name="date_to"
                                                   class="form-control"
                                                   required>
                                        </article>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label class="form-control-label">Период выдачи ПЛ:</label>
                                        <article>
                                            <input type="month"
                                                   oninput="$(this).closest('.clone').find('.date_from').prop('required', !(this.value.length > 0))"
                                                   name="trip_ticket[0][period_pl]"
                                                   class="form-control period_pl">
                                        </article>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="form-control-label">Срок действия, дней:</label>
                                    <input type="number" value="{{ $validity_period ?? '1' }}" required
                                           name="trip_ticket[0][validity_period]" class="form-control" min="1">
                                </div>

                                @if(! $createByForms)
                                    <div class="form-group">
                                        <label class="form-control-label">Номер путевого листа:</label>
                                        <input type="text" value="{{ $number_list_road ?? '' }}"
                                               name="trip_ticket[0][ticket_number]"
                                               class="form-control">
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="form-control-label">Вид сообщения:</label>
                                    <article>
                                        <select name="trip_ticket[0][logistics_method]" required class="form-control type-view">
                                            @foreach(\App\Enums\LogisticsMethodEnum::labels() as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </article>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Вид перевозки:</label>
                                    <article>
                                        <select name="trip_ticket[0][transportation_type]" required class="form-control type-view">
                                            @foreach(\App\Enums\TransportationTypeEnum::labels() as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </article>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Код бумажного шаблона:</label>
                                    <article>
                                        <select name="trip_ticket[0][template_code]" required class="form-control type-view">
                                            @foreach(\App\Enums\TripTicketTemplateEnum::labels() as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </article>
                                </div>

                                <div class="trip-ticket-delete"></div>
                            </div>

                            @if(! $createByForms)
                                <div id="clone-stack"></div>

                                <button type="button" id="trip-ticket-clone-btn" class="anketa__addnew">
                                    <i class="fa fa-plus"></i>
                                </button>
                            @endif

                            <div class="form-group row mb-0">
                                <button type="submit"
                                        class="m-center btn btn-sm btn-success submit-btn">
                                    @if($createByForms)
                                        Отправить задание
                                    @else
                                        Сохранить
                                    @endif
                                </button>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <p><b>Карточка компании</b></p>

                    <div id="CARD_COMPANY">
                        Не найдено
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <p><b>Карточка водителя</b></p>

                    <div id="CARD_DRIVER">
                        Не найдено
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

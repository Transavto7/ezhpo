@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@php
    use Carbon\Carbon;
    $created = \Illuminate\Support\Facades\Session::get('created', []);
@endphp

@section('custom-scripts')
    <script type="text/javascript">
        if (screen.width <= 700) {
            ANKETA_FORM_VIEW.insertBefore(ANKETA_FORM_ROOT, ANKETA_FORM_VIEW_FIRST)
        }

        $(document).ready(function () {
            const datesSelector = $("input[name='additional_dates']")
            initDatePicker(datesSelector)
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
                            <div class="row">
                                @foreach($created ?? [] as $tripTicket)
                                    <div class="col-md-12">
                                        <div class="card p-2 text-xsmall">
                                            <b>Путевой лист успешно создан!</b>
                                            <br/> Номер путевого листа: {{ $tripTicket->ticket_number }}

                                            @if($tripTicket->company && $tripTicket->company->name)
                                                <br/>
                                                <b>Компания: {{ $tripTicket->company->name }}</b>
                                            @endif

                                            @if($tripTicket->driver && $tripTicket->driver->fio)
                                                <br/>
                                                <b>Водитель: {{ $tripTicket->driver->fio }}</b>
                                            @endif

                                            @if($tripTicket->car && $tripTicket->car->gos_number)
                                                <br/>
                                                <b>Госномер автомобиля: {{ $tripTicket->car->gos_number }}</b>
                                            @endif

                                            @if($tripTicket->start_date)
                                                <div>
                                                    <i>Дата оформления:
                                                    <br/><b>{{ Carbon::parse($tripTicket->start_date)->format('d.m.Y') }}</b></i>
                                                </div>
                                            @endif

                                            @if($tripTicket->validity_period)
                                                <div>
                                                    <i>Срок действия:<b> {{ $tripTicket->validity_period }}</b></i>
                                                </div>
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
                              action="{{ route('trip-tickets.store') }}"
                              class="form-horizontal"
                              onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
                              enctype="multipart/form-data">
                            @csrf

                            @if(isset($anketa_route) && $id)
                                <input type="hidden" name="REFERER" value="{{ url()->previous() }}">
                            @endif

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

                            <div class="form-group">
                                <label class="form-control-label">Дата начала действия:</label>
                                <article>
                                    <input min="1970-01-01"
                                           max="2100-01-01"
                                           type="date"
                                           value="{{ $default_current_date ?? '' }}"
                                           name="start_date"
                                           class="form-control">
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Доп. даты:</label>
                                <article>
                                    <input type="date"
                                           name="additional_dates"
                                           class="form-control date-range">
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Срок действия, дней:</label>
                                <input type="number" value="{{ $validity_period ?? '1' }}"
                                       name="validity_period" class="form-control" min="1">
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Номер путевого листа:</label>
                                <input type="text" value="{{ $number_list_road ?? '' }}"
                                       name="ticket_number"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Вид сообщения:</label>
                                <article>
                                    <select name="logistics_method" required class="form-control type-view">
                                        @foreach(\App\Enums\LogisticsMethodEnum::labels() as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Вид перевозки:</label>
                                <article>
                                    <select name="transportation_type" required class="form-control type-view">
                                        @foreach(\App\Enums\TransportationTypeEnum::labels() as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Код бумажного шаблона:</label>
                                <article>
                                    <select name="template_code" required class="form-control type-view">
                                        @foreach(\App\Enums\TripTicketTemplateEnum::labels() as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </article>
                            </div>

                            <div class="form-group row mb-0">
                                @if(isset($anketa_route))
                                    <a href="{{ url()->previous() }}"
                                       class="m-center btn btn-info">
                                        Вернуться в журнал
                                    </a>
                                @endif
                                <button type="submit"
                                        class="m-center btn btn-sm btn-success submit-btn">
                                    Сохранить
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

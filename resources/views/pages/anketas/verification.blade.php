@php
    /** @var $details \App\ViewModels\AnketaVerificationDetails */

    if($details->isVerified()) {
        $title = 'Осмотр верифицирован';
    }
    else {
        $title = 'Осмотр не верифицирован';
    }

    $permissionToDelete = user() && (
        $details->getAnketaType()->value() == \App\Enums\AnketLabelingType::MEDIC && user()->access('medic_delete') ||
        $details->getAnketaType()->value() == \App\Enums\AnketLabelingType::TECH && user()->access('tech_delete')
    );
@endphp

@extends('pages.anketas.base')

@section('title', $title)

@push('custom_styles')
    <style>
        .page-content {
            height: calc(100vh - 50px);
        }

        .status-icon {
            font-size: 70px;
        }

        .status-icon-success {
            color: #2fa360;
        }

        .status-icon-wrong {
            color: #a70912;
        }

        .status-title {
            margin-top: 10px;
            font-size: 18px;
        }

        .verified-item {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px;
        }

        .verified-item + .verified-item {
            margin-top: 5px;
        }

        .verification-history-list p {
            font-size: 14px;
            font-weight: 400;
        }
    </style>
@endpush

@section('content')
    <main class="page-content d-flex align-items-stretch">
        <div class="container text-center">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-12">
                    <div class="flex justify-content-center align-items-center">
                        <div>
                            @if($details->isVerified())
                                <div>
                                    <i class="fa fa-check-circle status-icon status-icon-success"
                                       aria-hidden="true"></i>
                                </div>
                                <div class="status-title">Осмотр верифицирован</div>
                            @else
                                <div>
                                    <i class="fa fa-times-circle status-icon status-icon-wrong" aria-hidden="true"></i>
                                </div>
                                <div class="status-title">Осмотр не верифицирован</div>
                            @endif

                            <div id="verification-alert-body" class="d-none alert alert-danger mt-2">
                                <b>Вы уже проверяли данный осмотр <span id="verification-alert-count"></span></b><br>
                                <div>Дата последней проверки: <span id="verification-alert-date"></span></div>
                            </div>

                            @if($details->isVerified())
                                <div class="mt-2 d-flex flex-column align-items-center">
                                    @if($details->getAnketaNumber())
                                        <div class="verified-item">
                                            <b>Номер осмотра:</b>
                                            <span>{{ $details->getAnketaNumber() }}</span>
                                        </div>
                                    @endif

                                    @if($details->getCompanyName())
                                        <div class="verified-item">
                                            <b>Наименование компании:</b>
                                            <span>{{ $details->getCompanyName() }}</span>
                                        </div>
                                    @endif

                                    @if($details->getAnketaDate())
                                        <div class="verified-item">
                                            <b>Дата осмотра:</b>
                                            <span>{{ $details->getAnketaDate()->format('d.m.Y') }}</span>
                                        </div>
                                    @endif

                                    @if($details->getDriverName())
                                        <div class="verified-item">
                                            <b>ФИО водителя:</b>
                                            <span>{{ $details->getDriverName() }}</span>
                                        </div>
                                    @endif

                                    @if($details->getCarGosNumber())
                                        <div class="verified-item">
                                            <b>Гос. номер автомобиля:</b>
                                            <span>{{ $details->getCarGosNumber() }}</span>
                                        </div>
                                    @endif
                                </div>

                                @auth
                                    @if($permissionToDelete)
                                        <div class="mt-2">
                                            <a
                                                href="{{ route('forms.trash', ['id' => $details->getAnketaId(), 'action' => 1]) }}"
                                                class="btn btn-warning btn-sm hv-btn-trash mr-1"
                                                data-id="{{ $details->getAnketaId() }}">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    @endif
                                @endauth
                            @else
                                @auth
                                    @if($permissionToDelete)
                                        <div class="mt-2">
                                            <a
                                                href="{{ route('forms.trash', ['id' => $details->getAnketaId(), 'action' => 0]) }}"
                                                class="btn btn-warning btn-sm hv-btn-trash mr-1"
                                                data-id="{{ $details->getAnketaId() }}">
                                                <i class="fa fa-undo"></i>
                                            </a>
                                        </div>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @if(count($details->getVerificationDates()))
        <div class="d-flex justify-content-center">
            <a class="text-info" data-toggle="collapse" href="#collapseVerificationHistory" role="button"
               aria-expanded="false" aria-controls="collapseVerificationHistory">
                История проверок осмотра
            </a>
        </div>
        <div>
            <div class="collapse p-2" id="collapseVerificationHistory">
                <div class="alert alert-info verification-history-list">
                    <div class="d-flex justify-content-center mb-2"><b>Проверок
                            всего: {{ count($details->getVerificationDates()) }}</b></div>
                    @foreach($details->getVerificationDates() as $date)
                        <p class="text-center"><b>{{ $loop->index + 1 }}.</b> {{ $date->format('d.m.Y h:i:s') }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection

@push('custom_scripts')
    <script>
        const SS_KEY_SIGN = 'anketLabelingVerificationSign';
        const SS_KEY_SESSION_KEY = 'anketLabelingVerificationSessionKey';
        const LS_KEY = 'anketLabelingVerificationItems';

        const ui = {
            verificationAlertBody: $('#verification-alert-body'),
            verificationAlertCount: $('#verification-alert-count'),
            verificationAlertDate: $('#verification-alert-date'),
        };

        const currentUuid = '{{ $details->getAnketaUuid() }}';

        function getVerificationItems() {
            const items = localStorage.getItem(LS_KEY);

            if (!items) {
                localStorage.setItem(LS_KEY, JSON.stringify({}))
                return {};
            }

            return JSON.parse(items);
        }

        function getVisitedSign() {
            const rawData = sessionStorage.getItem(SS_KEY_SIGN)

            if (rawData) {
                const data = JSON.parse(rawData)

                return data.hasOwnProperty('visited') && data.visited
            }

            return false
        }

        function getSessionKey() {
            let sessionKey = sessionStorage.getItem(SS_KEY_SESSION_KEY)

            if (!sessionKey) {
                sessionKey = (new Date()).toISOString()
                sessionStorage.setItem(SS_KEY_SESSION_KEY, sessionKey)
            }

            return sessionKey
        }

        function formatDate(isoString) {
            const date = new Date(isoString);

            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Месяцы начинаются с 0
            const year = date.getFullYear();

            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');

            return `${day}.${month}.${year} ${hours}:${minutes}:${seconds}`;
        }

        function formatCount(count) {
            const value = Math.abs(count) % 100;
            const num = value % 10;

            if (value > 10 && value < 20) return `${count} раз`;
            if (num > 1 && num < 5) return `${count} раза`;
            if (num === 1) return `${count} раз`;

            return `${count} раз`;
        }

        function checkVerified() {
            const sessionKey = getSessionKey()
            const allItems = getVerificationItems();

            if (allItems.hasOwnProperty(currentUuid)) {
                const items = allItems[currentUuid].filter(function (item) {
                    return !item?.sessionKey || item.sessionKey !== sessionKey
                });

                if (items.length) {
                    const item = items[items.length - 1];

                    ui.verificationAlertBody.removeClass('d-none');
                    ui.verificationAlertCount.html(formatCount(items.length));
                    ui.verificationAlertDate.html(formatDate(item.date));
                }
            }
        }

        function storeVerification() {
            const allItems = getVerificationItems();

            if (!allItems.hasOwnProperty(currentUuid)) {
                allItems[currentUuid] = [];
            }

            allItems[currentUuid].push({
                date: (new Date()).toISOString(),
                sessionKey: getSessionKey()
            });

            localStorage.setItem(LS_KEY, JSON.stringify(allItems));
        }

        $(document).ready(function () {
            const visitedSign = getVisitedSign()

            checkVerified();

            if (!visitedSign) {
                storeVerification();
                sessionStorage.setItem(SS_KEY_SIGN, JSON.stringify({visited: true}))
            }
        })
    </script>

    <script>
        let isExpanded = false
        $('[href="#collapseVerificationHistory"]').click(function () {
            if (!isExpanded) {
                setTimeout(() => {
                    window.scrollBy(0, 50);
                }, 100)
            }

            isExpanded = !isExpanded
        })
    </script>
@endpush

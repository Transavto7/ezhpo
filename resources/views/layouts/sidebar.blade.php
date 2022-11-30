@php
    $user_role_text = \App\Http\Controllers\ProfileController::getUserRole();
    $user_role = \App\Http\Controllers\ProfileController::getUserRole(false);
@endphp

    <!-- Side Navbar -->
<nav class="side-navbar">
    <!-- Sidebar Header-->
    <div class="sidebar-header d-flex align-items-center">
        <div class="title">
            <article>
                @foreach(user()->roles as $role)
                    <h3>
                        <span class="badge badge-success">
                            {{ $role->guard_name }}
                        </span>
                    </h3>
                @endforeach
            </article>
        </div>
    </div>

    <ul class="list-unstyled">
        <li>
            <a class="bg-info text-white">МЕНЮ</a>
        </li>

        @if(user()->access('medic_create'))
            <li>
                <a href="{{ route('forms', ['type' => 'medic']) }}" class="bg-red text-white"><i
                        class="icon-padnote"></i>Провести мед. осмотр</a>
            </li>
        @endif

        @if(user()->access('tech_create'))
            <li>
                <a href="{{ route('forms', ['type' => 'tech']) }}" class="bg-blue text-white"><i
                        class="icon-padnote"></i>Провести тех. осмотр</a>
            </li>
        @endif

        @if(user()->access('map_report_create'))
            <li>
                <a href="{{ route('forms', ['type' => 'report_cart']) }}" class="bg-gray"><i
                        class="icon-padnote"></i>Внести Отчёт с карты</a>
            </li>
        @endif

        @if(user()->access('print_register_pl_create'))
            <li>
                <a href="{{ route('forms', ['type' => 'pechat_pl']) }}" class="bg-gray"><i
                        class="icon-padnote"></i>Внести запись в Реестр печати ПЛ</a>
            </li>
        @endif

        @if(user()->access('journal_briefing_bdd_create'))
            <li>
                <a href="{{ route('forms', ['type' => 'bdd']) }}" class="bg-gray"><i
                        class="icon-padnote"></i>Внести Инструктаж БДД</a>
            </li>
        @endif

        @if(user()->access('approval_queue_view', 'approval_queue_clear'))
            @php
                $countPakQueue = \App\Anketa::where('type_anketa', 'pak_queue')->count();
            @endphp
            <li><a href="{{ route('home', 'pak_queue') }}"><i class="fa fa-users"></i>Очередь утверждения <span
                        class="badge bg-primary text-white">{{ $countPakQueue < 99 ? $countPakQueue : '99+' }}</span></a>
            </li>
        @endif


        @if(user()->access('client_create'))
            <li>
                <a href="{{ route('pages.add_client') }}" class="bg-info text-white"><i class="icon-user"></i>Добавить
                    клиента</a>
            </li>
        @endif

        @if(user()->access('medic_read', 'tech_read', 'journal_briefing_bdd_read', 'journal_pl_read',
               'map_report_read', 'errors_sdpo_read', 'errors_sdpo_create'))
            <li>
                <a href="#" data-btn-collapse="#views" role="button"> <i class="icon-grid"></i>Журналы осмотров</a>
                <ul id="views" class="collapse list-unstyle">
                    @if(user()->access('medic_read'))
                        <li><a href="{{ route('home', 'medic') }}"><i class="fa fa-plus"></i>Журнал МО</a></li>
                    @endif

                    @if(user()->access('tech_read'))
                        <li><a href="{{ route('home', 'tech') }}"><i class="fa fa-wrench"></i>Журнал ТО</a></li>
                    @endif

                    @if(user()->access('journal_briefing_bdd_read'))
                        <li>
                            <a href="{{ route('home', 'bdd') }}">
                                <i class="fa fa-book"></i>Журнал инструктажей по БДД
                            </a>
                        </li>
                    @endif

                    @if(user()->access('journal_pl_read'))
                        <li>
                            <a href="{{ route('home', 'pechat_pl') }}"><i class="fa fa-book"></i>Журнал печати
                                ПЛ</a>
                        </li>
                    @endif

                    @if(user()->access('map_report_read'))
                        <li>
                            <a href="{{ route('home', 'report_cart') }}"><i class="fa fa-book"></i>Реестр снятия
                                отчетов
                                с карт</a>
                        </li>
                    @endif


                    @if(user()->access('errors_sdpo_read', 'errors_sdpo_create'))
                        @php
                            $countErrorsPak = \Illuminate\Support\Facades\Cache::remember('countErrorsPak', 3600,
                                            function (){
                                                return \App\Anketa::where('type_anketa', 'pak')->count();
                                            });

                        @endphp
                        <li>
                            <a href="{{ route('home', 'pak') }}"><i class="fa fa-close"></i>Реестр ошибок СДПО <span
                                    class="badge bg-primary text-white">{{ $countErrorsPak < 99 ? $countErrorsPak : '99+' }}
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if(user()->access('report_service_company_read', 'report_schedule_pv_read'))
            <li>
                <a href="#" data-btn-collapse="#reports" role="button"><i class="fa fa-area-chart"></i> Отчеты</a>
                <ul id="reports" class="collapse list-unstyle">

                    {{--                        @excludeRole(['client'])--}}
                    @if(user()->access('report_schedule_pv_read'))
                        <li>
                            <a href="{{ route('report.get', 'graph_pv') }}">
                                <i class="fa fa-book"></i>График работы пунктов выпуска
                            </a>
                        </li>
                    @endif

                    @if(user()->access('report_service_company_read', 'report_service_company_export'))
                        <li>
                            <a href="{{ route('report.journal') }}">
                                <i class="fa fa-book"></i>Отчет по услугам компании
                            </a>
                        </li>
                    @endif
                    @if(user()->access('report_service_company_read', 'report_service_company_export'))
                        <li>
                            <a href="{{ route('report.journal_contract') }}">
                                <i class="fa fa-book"></i>Отчет по услугам компании
                                <span class="
                                    start-100
                                    translate-middle
                                    badge
                                    text-white
                                    rounded-pill
                                    bg-success"
                                >
                                    new
                                </span>
                            </a>
                        </li>
                    @endif


                    @if(user()->access('report_schedule_dynamic_mo'))
                        <li>
                            <a href="{{ route('report.dynamic.medic') }}">
                                <i class="fa fa-book"></i>Отчет по количеству осмотров
                                <span class="start-100 translate-middle badge text-white rounded-pill bg-success">
                                    new
                                </span>
                            </a>
                        </li>
                    @endif
{{--                    @if(user()->access('report_schedule_dynamic_mo'))--}}
                        <li>
                            <a href="{{ route('report.company_service') }}">
                                <i class="fa fa-book"></i>Отчет по услугам компании
                            </a>
                        </li>
{{--                    @endif--}}
                </ul>
            </li>
        @endif

        @if(user()->access('drivers_read', 'cars_read', 'company_read', 'service_read', 'discount_read', 'briefings_read', 'drivers_create'
, 'cars_create', 'company_create'
, 'service_create', 'discount_create'
, 'briefings_create','contract_read', 'contract_create'))
            <li>
                <a href="#" data-btn-collapse="#phoenic" role="button"> <i
                        class="icon-interface-windows"></i>CRM</a>
                <ul id="phoenic" class="collapse list-unstyle">

                    @if(user()->access('contract_read', 'contract_create'))
                        <li>
                            <a href="/contract">
                                Договор
                                <span class="
                                    start-100
                                    translate-middle
                                    badge
                                    text-white
                                    rounded-pill
                                    bg-success"
                                >
                                    new
                                </span>
                            </a>
                        </li>
                    @endif
                    @if(user()->access('drivers_read', 'drivers_create'))
                        <li><a href="{{ route('renderElements', 'Driver') }}">Водители</a></li>
                    @endif
                    @if(user()->access('cars_read', 'cars_create'))
                        <li><a href="{{ route('renderElements', 'Car') }}">Автомобили</a></li>
                    @endif
                    @if(user()->access('company_read', 'company_create'))
                        <li><a href="{{ route('renderElements', 'Company') }}">Компании</a></li>
                    @endif
                    @if(user()->access('service_read', 'service_create'))
                        <li><a href="{{ route('renderElements', 'Product') }}">Услуги</a></li>
{{--                        <li><a href="{{ route('renderElements', 'Service') }}">--}}
{{--                                Услуги--}}
{{--                                <span class="--}}
{{--                                start-100--}}
{{--                                translate-middle--}}
{{--                                badge--}}
{{--                                text-white--}}
{{--                                rounded-pill--}}
{{--                                bg-success">--}}
{{--                                    new--}}
{{--                                </span>--}}
{{--                            </a></li>--}}
                    @endif
                    @if(user()->access('discount_read', 'discount_create'))
                        <li><a href="{{ route('renderElements', 'Discount') }}">Скидки</a></li>
                    @endif
                    @if(user()->access('briefings_read', 'briefings_create'))
                        <li><a href="{{ route('renderElements', 'Instr') }}">Виды инструктажей</a></li>
                    @endif
                </ul>
            </li>
        @endif

        @if(user()->access('system_read', 'settings_system_read',
            'city_read', 'pv_read', 'employee_read','employee_create','group_create',
            'city_create', 'group_read', 'story_field_read', 'story_field_create', 'pv_create', 'date_control_create',
            'date_control_read', 'pak_sdpo_read', 'pak_sdpo_create', 'pak_sdpo_update', 'requisites_read', 'requisites_create', 'releases_read'))
            <li>
                <a href="#" data-btn-collapse="#spis-pol" role="button"><i class="fa fa-cog"></i> Настройки</a>
                <ul id="spis-pol" class="collapse list-unstyle">

                    @if(user()->access('settings_system_read'))
                        <li><a href="{{ route('settings.index') }}">Системные настройки</a></li>
                    @endif

                    @if(user()->access('city_read', 'city_create'))
                        <li><a href="{{ route('renderElements', 'Town') }}">Города</a></li>
                    @endif

                    @if(user()->access('pv_read', 'pv_create'))
                        <li><a href="{{ route('renderElements', 'Point') }}">Пункты выпуска</a></li>
                    @endif

                    {{--                    @if(user()->access('employee_read'))--}}
                    {{--                        <li><a href="{{ route('adminUsers') }}">Сотрудники</a></li>--}}
                    {{--                    @endif--}}

                    @if(user()->access('employee_read', 'employee_create'))
                        <li><a href="{{ route('users') }}">Сотрудники</a></li>
                    @endif

                    @if(user()->access('group_read', 'group_create'))
                        <li><a href="{{ route('roles.index') }}">Роли</a></li>
                    @endif

                    @if(user()->access('pak_sdpo_read', 'pak_sdpo_create'))
                        <li><a href="{{ route('adminUsers', [
                                'filter' => 1,
                                'pak_sdpo' => 1
                            ]) }}">ПАК СДПО</a></li>
                    @endif

                    @if(user()->access('date_control_read', 'date_control_create'))
                        <li><a href="{{ route('renderElements', 'DDates') }}">Контроль дат</a></li>
                    @endif

                    @if(user()->access('story_field_read', 'story_field_create'))
                        <li><a href="{{ route('renderElements', 'FieldHistory') }}">История изменения полей</a>
                        </li>
                    @endif

                    @if(user()->access('requisites_read', 'requisites_create'))
                        <li><a href="{{ route('renderElements', 'Req') }}">Реквизиты нашей компании</a></li>
                    @endif

                    @if(user()->access('field_prompt_read'))
                        <li><a href="{{ route('prompt.index') }}">Подсказки полей</a></li>
                    @endif


                </ul>
            </li>
        @endif

    </ul>
</nav>

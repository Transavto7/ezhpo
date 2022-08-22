@php $user_role_text = \App\Http\Controllers\ProfileController::getUserRole(); @endphp
@php $user_role = \App\Http\Controllers\ProfileController::getUserRole(false); @endphp

@if($user_role != 3)
    <!-- Side Navbar -->
    <nav class="side-navbar">
        <!-- Sidebar Header-->
        <div class="sidebar-header d-flex align-items-center">
            <div class="title">
                <article>
                    <div class="badge badge-rounded bg-green">
                        {{ $user_role_text }}
                    </div>

                    @manager
                    <div class="badge badge-rounded bg-blue">Менеджер</div>
                    @endmanager
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
                    <a href="{{ route('forms', ['type' => 'report_cart']) }}" class="bg-yellow"><i
                            class="icon-padnote"></i>Внести Отчёт с карты</a>
                </li>
            @endif

            @if(user()->access('print_register_pl_create'))
                <li>
                    <a href="{{ route('forms', ['type' => 'pechat_pl']) }}" class="bg-yellow"><i
                            class="icon-padnote"></i>Внести запись в Реестр печати ПЛ</a>
                </li>
            @endif

            @if(user()->access('journal_pl_create'))
                <li>
                    <a href="{{ route('forms', ['type' => 'Dop']) }}" class="bg-yellow"><i
                            class="icon-padnote"></i>Внести запись в Журнал ПЛ</a>
                </li>
            @endif

            @if(user()->access('journal_briefing_bdd_create'))
                <li>
                    <a href="{{ route('forms', ['type' => 'bdd']) }}" class="bg-yellow"><i
                            class="icon-padnote"></i>Внести Инструктаж БДД</a>
                </li>
            @endif

            @if(user()->access('approval_queue_view'))
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

                    @if(user()->access('journal_briefing_bdd_read'))
                        <li>
                            <a href="{{ route('home', 'pechat_pl') }}"><i class="fa fa-book"></i>Журнал печати ПЛ</a>
                        </li>
                    @endif

                    @if(user()->access('map_report_read'))
                        <li>
                            <a href="{{ route('home', 'report_cart') }}"><i class="fa fa-book"></i>Реестр снятия отчетов
                                с карт</a>
                        </li>
                    @endif
                        <li><a href="{{ route('home', 'bdd') }}"><i class="fa fa-book"></i>Журнал инструктажей по БДД </a></li>
                        <li><a href="{{ route('home', 'pechat_pl') }}"><i class="fa fa-book"></i>Журнал печати ПЛ</a></li>
                    <li><a href="{{ route('home', 'report_cart') }}"><i class="fa fa-book"></i>Реестр снятия отчетов с карт</a></li>
                        <li><a href="{{ route('home', 'Dop') }}"><i class="fa fa-book"></i>Журнал учета ПЛ</a></li>
{{--                    <li><a href="{{ route('home', 'vid_pl') }}"><i class="fa fa-book"></i>Реестр выданных ПЛ</a></li>--}}

                    @if(user()->access('journal_pl_read'))
                        <li>
                            <a href="{{ route('home', 'Dop') }}"><i class="fa fa-book"></i>Журнал учета ПЛ</a>
                        </li>
                    @endif

                    @if(user()->access('errors_sdpo_read'))
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

                    {{--                    <li><a href="{{ route('home', 'vid_pl') }}"><i class="fa fa-book"></i>Реестр выданных ПЛ</a></li>--}}

                </ul>
            </li>

{{--            // все кроме 'medic', 'tech'--}}
            @if(user()->access('report_service_company_read', 'report_schedule_pv_read'))
                <li>
                    <a href="#" data-btn-collapse="#reports" role="button"><i class="fa fa-area-chart"></i> Отчеты</a>
                    <ul id="reports" class="collapse list-unstyle">
                        <li><a href="{{ route('report.get', 'journal') }}"><i class="fa fa-book"></i>Отчет по услугам компании</a></li>
                        @excludeRole(['client'])
                        <li><a href="{{ route('report.get', 'graph_pv') }}"><i class="fa fa-book"></i>График работы пунктов выпуска</a></li>
                        @endexcludeRole
                        @if(user()->access('report_service_company_read'))
                            <li>
                                <a href="{{ route('report.get', 'journal') }}"><i class="fa fa-book"></i>Отчет по
                                    услугам компании</a>
                            </li>
                        @endif

                        @if(user()->access('report_schedule_pv_read'))
                            <li>
                                <a href="{{ route('report.get', 'graph_pv') }}"><i class="fa fa-book"></i>График работы
                                    пунктов выпуска</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

{{--            // У всех--}}
            <li>
                <a href="#" data-btn-collapse="#phoenic" role="button"> <i class="icon-interface-windows"></i>CRM</a>
                <ul id="phoenic" class="collapse list-unstyle">
                    <li><a href="{{ route('renderElements', 'Driver') }}">Водители</a></li>
                    <li><a href="{{ route('renderElements', 'Car') }}">Автомобили</a></li>

                    @role(['admin', 'manager', 'medic', 'tech', 'terminal'])
                        <li><a href="{{ route('renderElements', 'Company') }}">Компании</a></li>
                    @endrole

                    @manager

                        @excludeRole(['client'])
                            <li><a href="{{ route('renderElements', 'Product') }}">Услуги</a></li>
                            <li><a href="{{ route('renderElements', 'Discount') }}">Скидки</a></li>

                        @endexcludeRole

                    @endmanager
                    @if(auth()->user()->role === 777 || auth()->user()->role == 12)
                        <li><a href="{{ route('renderElements', 'Instr') }}">Инструктажи</a></li>
                    @endif
                    @if(user()->access('drivers_read'))
                        <li><a href="{{ route('renderElements', 'Driver') }}">Водители</a></li>
                    @endif
                    @if(user()->access('cars_create'))
                            <li><a href="{{ route('renderElements', 'Car') }}">Автомобили</a></li>
                    @endif
                    @if(user()->access('company_read'))
                            {{--                    @role(['admin', 'manager', 'medic', 'tech', 'terminal'])--}}
                            <li><a href="{{ route('renderElements', 'Company') }}">Компании</a></li>
                            {{--                    @endrole--}}
                    @endif
                        {{--                    @manager--}}
                    @if(user()->access('service_read'))
                            <li><a href="{{ route('renderElements', 'Product') }}">Услуги</a></li>
                    @endif
                    @if(user()->access('discount_read'))
                            <li><a href="{{ route('renderElements', 'Discount') }}">Скидки</a></li>
                    @endif
                    @if(user()->access('briefings_create'))
                            <li><a href="{{ route('renderElements', 'Instr') }}">Инструктажи</a></li>
                    @endif
                        {{--                    @endmanager--}}


                </ul>
            </li>

            {{-- Если пользователь Админ --}}
{{--            @manager--}}
            <li>
                <a href="#" data-btn-collapse="#spis-pol" role="button"><i class="fa fa-cog"></i> Настройки</a>
                <ul id="spis-pol" class="collapse list-unstyle">
{{--                    @admin--}}
                    @if(user()->access('system_read'))
                        <li><a href="{{ route('renderElements', 'Settings') }}">Система</a></li>
                    @endif

                    @if(user()->access('settings_system_read'))
                        <li><a href="{{ route('systemSettings') }}">Системные настройки</a></li>
                    @endif

                    @if(user()->access('city_read'))
                        <li><a href="{{ route('renderElements', 'Town') }}">Города</a></li>
                    @endif

                    @if(user()->access('pv_read'))
                        <li><a href="{{ route('renderElements', 'Point') }}">Пункты выпуска</a></li>
                    @endif

                    @if(user()->access('employee_read'))
                        <li><a href="{{ route('adminUsers') }}">Сотрудники</a></li>
                    @endif

                    <li><a href="{{ route('users') }}">Сотрудники v2</a></li>

                    <li><a href="{{ route('groups') }}">Группы</a></li>

                    @if(user()->access('pak_sdpo_read'))
                        <li><a href="{{ route('adminUsers', [
                                    'filter' => 1,
                                    'role' => 778
                                ]) }}">ПАК СДПО</a></li>
                    @endif
            @manager
                @excludeRole(['client'])
                    <li>
                        <a href="#" data-btn-collapse="#spis-pol" role="button"><i class="fa fa-cog"></i> Настройки</a>
                        <ul id="spis-pol" class="collapse list-unstyle">
                            @admin
                                <li><a href="{{ route('renderElements', 'Settings') }}">Система</a></li>
                                <li><a href="{{ route('systemSettings') }}">Системные настройки</a></li>
                                <li><a href="{{ route('renderElements', 'Town') }}">Города</a></li>
                                <li><a href="{{ route('renderElements', 'Point') }}">Пункты выпуска</a></li>
                                <li><a href="{{ route('adminUsers') }}">Сотрудники</a></li>
                                <li><a href="{{ route('adminUsers', [
                                        'filter' => 1,
                                        'role' => 778
                                    ]) }}">ПАК СДПО</a></li>

                    @if(user()->access('date_control_read'))
                        <li><a href="{{ route('renderElements', 'DDates') }}">Контроль дат</a></li>
                    @endif

                    @if(user()->access('story_field_read'))
                        <li><a href="{{ route('renderElements', 'FieldHistory') }}">История изменения полей</a></li>
                    @endif
                                <li><a href="{{ route('renderElements', 'DDates') }}">Контроль дат</a></li>
                                <li><a href="{{ route('renderElements', 'FieldHistory') }}">История изменения полей</a></li>
                            @endadmin

                    @if(user()->access('requisites_read'))
                        <li><a href="{{ route('renderElements', 'Req') }}">Реквизиты нашей компании</a></li>
                    @endif

                    @if(user()->access('releases_read'))
                        <li><a href="{{ route('releases') }}">Релизы</a></li>
                    @endif

{{--                    @endadmin--}}

                </ul>
            </li>
{{--            @endmanager--}}
                            <li><a href="{{ route('renderElements', 'Req') }}">Реквизиты нашей компании</a></li>
                            <li><a href="{{ route('releases') }}">Релизы</a></li>
                        </ul>
                    </li>
                @endexcludeRole
            @endmanager

        </ul>
    </nav>
@endif

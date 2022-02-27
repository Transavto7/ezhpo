@php $user_role_text = \App\Http\Controllers\ProfileController::getUserRole(); @endphp
@php $user_role = \App\Http\Controllers\ProfileController::getUserRole(false); @endphp
@php $user_avatar = \App\Http\Controllers\ProfileController::getAvatar(); @endphp

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
        <!-- Sidebar Navidation Menus-->
        <span class="heading">
        <div class="client">
            <div class="client-avatar">
                <img src="{{ $user_avatar }}" width="100%" alt="avatar">
            </div>
        </div>

    </span>
        <ul class="list-unstyled">
            <li>
                <a class="bg-info text-white">МЕНЮ</a>
            </li>

            @if(($user_role < 11) || $user_role >= 777)
                @role(['manager', 'medic', 'client', 'terminal'])
                    <li>
                        <a href="{{ route('forms', ['type' => 'medic']) }}" class="bg-red text-white"><i class="icon-padnote"></i>Провести мед. осмотр</a>
                    </li>
                @endrole

                @role(['manager', 'tech', 'client', 'terminal'])
                    <li>
                        <a href="{{ route('forms', ['type' => 'tech']) }}" class="bg-blue text-white"><i class="icon-padnote"></i>Провести тех. осмотр</a>
                    </li>
                @endrole

                <li>
                    <a href="{{ route('forms', ['type' => 'bdd']) }}" class="bg-yellow"><i class="icon-padnote"></i>Внести Инструктаж БДД</a>
                </li>

                <li>
                    <a href="{{ route('forms', ['type' => 'report_cart']) }}" class="bg-yellow"><i class="icon-padnote"></i>Внести Отчёт с карты</a>
                </li>

                <li>
                    <a href="{{ route('forms', ['type' => 'pechat_pl']) }}" class="bg-yellow"><i class="icon-padnote"></i>Внести запись в Реестр печати ПЛ</a>
                </li>

                <li>
                    <a href="{{ route('forms', ['type' => 'vid_pl']) }}" class="bg-yellow"><i class="icon-padnote"></i>Внести запись в Реестр выданных ПЛ</a>
                </li>

                <li>
                    <a href="{{ route('forms', ['type' => 'Dop']) }}" class="bg-yellow"><i class="icon-padnote"></i>Внести запись в Журнал ПЛ</a>
                </li>

                @role(['manager', 'operator_pak', 'terminal'])
                    @php
                        $countPakQueue = \App\Anketa::where('type_anketa', 'pak_queue')->count();
                    @endphp
                    <li><a href="{{ route('home', 'pak_queue') }}"><i class="fa fa-users"></i>Очередь СДПО <span class="badge bg-primary text-white">{{ $countPakQueue < 99 ? $countPakQueue : '99+' }}</span></a></li>
                @endrole
            @endif

            @role(['admin', 'manager', 'client', 'terminal'])
                <li>
                    <a href="{{ route('pages.add_client') }}" class="bg-info text-white"><i class="icon-user"></i>Добавить клиента</a>
                </li>
            @endrole

            <li>
                <a href="#" data-btn-collapse="#views" role="button"> <i class="icon-grid"></i>Журналы осмотров</a>
                <ul id="views" class="collapse list-unstyle">
                    @if($user_role === 2 || @manager)
                        <li><a href="{{ route('home', 'medic') }}"><i class="fa fa-plus"></i>Медосмотры</a></li>
                    @endif

                    @if($user_role === 1 || @manager)
                        <li><a href="{{ route('home', 'tech') }}"><i class="fa fa-wrench"></i>Техосмотры</a></li>
                    @endif

                    <li><a href="{{ route('home', 'Dop') }}"><i class="fa fa-book"></i>Журнал учета ПЛ</a></li>

                    <li><a href="{{ route('home', 'bdd') }}"><i class="fa fa-book"></i>Журнал инструктажей по БДД </a></li>
                    <li><a href="{{ route('home', 'report_cart') }}"><i class="fa fa-book"></i>Журнал снятия отчетов с карт</a></li>
                    <li><a href="{{ route('home', 'pechat_pl') }}"><i class="fa fa-book"></i>Журнал печати ПЛ</a></li>
                    <li><a href="{{ route('home', 'vid_pl') }}"><i class="fa fa-book"></i>Реестр выданных ПЛ</a></li>
                    <li><a href="{{ route('home', 'pak') }}"><i class="fa fa-close"></i>Реестр ошибок СДПО</a></li>
                </ul>
            </li>

            <li>
                <a href="#" data-btn-collapse="#reports" role="button"><i class="fa fa-area-chart"></i> Отчеты</a>
                <ul id="reports" class="collapse list-unstyle">
                    <li><a href="{{ route('report.get', 'journal') }}"><i class="fa fa-book"></i>Отчет по услугам компании</a></li>
                    <li><a href="{{ route('report.get', 'graph_pv') }}"><i class="fa fa-book"></i>График работы пунктов выпуска</a></li>
                </ul>
            </li>

            <li>
                <a href="#" data-btn-collapse="#phoenic" role="button"> <i class="icon-interface-windows"></i>CRM</a>
                <ul id="phoenic" class="collapse list-unstyle">
                    <li><a href="{{ route('renderElements', 'Driver') }}">Водители</a></li>
                    <li><a href="{{ route('renderElements', 'Car') }}">Автомобили</a></li>
                    <li><a href="{{ route('renderElements', 'Company') }}">Компании</a></li>

                    @manager
                        <li><a href="{{ route('renderElements', 'Product') }}">Услуги</a></li>
                        <li><a href="{{ route('renderElements', 'Discount') }}">Скидки</a></li>
                        <li><a href="{{ route('renderElements', 'Instr') }}">Инструктажи</a></li>
                    @endmanager
                </ul>
            </li>

            {{-- Если пользователь Админ --}}
            @manager
            <li>
                <a href="#" data-btn-collapse="#spis-pol" role="button"><i class="fa fa-cog"></i> Настройки</a>
                <ul id="spis-pol" class="collapse list-unstyle">
                    @admin
                        <li><a href="{{ route('renderElements', 'Settings') }}">Система</a></li>
                        <li><a href="{{ route('renderElements', 'Town') }}">Города</a></li>
                        <li><a href="{{ route('renderElements', 'Point') }}">Пункты выпуска</a></li>
                        <li><a href="{{ route('adminUsers') }}">Сотрудники</a></li>
                        <li><a href="{{ route('adminUsers', [
                                'filter' => 1,
                                'role' => 778
                            ]) }}">ПАК СДПО</a></li>
                        <li><a href="{{ route('renderElements', 'DDate') }}">Контроль дат</a></li>
                        <li><a href="{{ route('renderElements', 'FieldHistory') }}">История изменения полей</a></li>
                    @endadmin

                    <li><a href="{{ route('renderElements', 'Req') }}">Реквизиты нашей компании</a></li>

                </ul>
            </li>
            @endmanager

        </ul>
    </nav>
@endif

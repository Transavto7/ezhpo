@php
    use App\Enums\FormTypeEnum;
    use Illuminate\Support\Facades\Cache;
    use App\Models\Forms\Form;
    use App\Actions\Terminals\GetTerminalsToCheck\GetTerminalsToCheckQuery;

    /** @var \App\User $user */
    $user = \Illuminate\Support\Facades\Auth::user();
    $accessToJournals = $user->access(
            'medic_read',
            'tech_read',
            'journal_briefing_bdd_read',
            'journal_pl_read',
            'map_report_read',
            'errors_sdpo_read',
            'errors_sdpo_create',
            'trip_tickets_read'
        );

    $accessToSettings = $user->access(
            'system_read',
            'settings_system_read',
            'city_read',
            'pv_read',
            'employee_read',
            'employee_create',
            'group_create',
            'city_create',
            'group_read',
            'story_field_read',
            'story_field_create',
            'pv_create',
            'date_control_create',
            'date_control_read',
            'pak_sdpo_read',
            'pak_sdpo_create',
            'pak_sdpo_update',
            'requisites_read',
            'requisites_create',
            'releases_read',
            'users_read',
        );

    $accessToElements = $user->access(
            'drivers_read',
            'cars_read',
            'company_read',
            'service_read',
            'discount_read',
            'briefings_read',
            'drivers_create',
            'cars_create',
            'company_create',
            'service_create',
            'discount_create',
            'briefings_create',
            'contract_read',
            'contract_create'
        );
@endphp

    <!-- Side Navbar -->
<nav class="side-navbar">
    <!-- Sidebar Header-->
    <div class="sidebar-header d-flex align-items-center">
        <div class="title">
            <article>
                @foreach($user->roles as $role)
                    <h3>
                        <span class="badge badge-success text-wrap">
                            {{ $role->guard_name }}
                        </span>
                    </h3>
                @endforeach
            </article>
        </div>
    </div>

    <ul class="list-unstyled">
        @if($user->access('approval_queue_view', 'approval_queue_clear'))
            @php
                $countPakQueue = Form::pakQueueCount($user);
            @endphp
            <li>
                <a href="{{ route('pak.index') }}">
                    <i class="fa fa-users"></i>Очередь утверждения
                    <span class="badge bg-primary text-white">
                        {{ $countPakQueue < 99 ? $countPakQueue : '99+' }}
                    </span>
                </a>
            </li>
        @endif


        @if($user->access('client_create'))
            <li>
                <a href="{{ route('pages.add_client') }}" class="bg-info text-white"><i class="icon-user"></i>Добавить
                    клиента</a>
            </li>
        @endif

        @if($user->access('medic_read'))
            <li><a href="{{ route('home', FormTypeEnum::MEDIC) }}"><i class="fa fa-plus"></i>Журнал
                    МО</a></li>
        @endif

        @if($accessToElements)
            <li>
                <a href="#" data-btn-collapse="#phoenic" role="button"> <i
                        class="icon-interface-windows"></i>CRM</a>
                <ul id="phoenic" class="collapse list-unstyle">
                    @if($user->access('drivers_read', 'drivers_create'))
                        <li><a href="{{ route('renderElements', 'Driver') }}">Водители</a></li>
                    @endif
                    @if($user->access('company_read', 'company_create'))
                        <li><a href="{{ route('renderElements', 'Company') }}">Компании</a></li>
                    @endif
                </ul>
            </li>
        @endif

        @if($accessToSettings)
            <li>
                <a href="#" data-btn-collapse="#spis-pol" role="button"><i class="fa fa-cog"></i> Настройки</a>
                <ul id="spis-pol" class="collapse list-unstyle">

                    @if($user->access('settings_system_read'))
                        <li><a href="{{ route('settings.index') }}">Системные настройки</a></li>
                    @endif

                    @if($user->access('city_read', 'city_create'))
                        <li><a href="{{ route('renderElements', 'Town') }}">Города</a></li>
                    @endif

                    @if($user->access('pv_read', 'pv_create'))
                        <li><a href="{{ route('renderElements', 'Point') }}">Пункты выпуска</a></li>
                    @endif

                    @if($user->access('employee_read', 'employee_create'))
                        <li><a href="{{ route('employees.index') }}">Сотрудники</a></li>
                    @endif

                    @if($user->access('group_read', 'group_create'))
                        <li><a href="{{ route('roles.index') }}"> Роли </a></li>
                    @endif

                    @if($user->access('pak_sdpo_read', 'pak_sdpo_create'))
                        @php
                            $query = new GetTerminalsToCheckQuery();
                            $terminalsToCheckViewModel = $query->get();

                            $lessMonthCount = count($terminalsToCheckViewModel->getLessMonth());
                            $expiredCount = count($terminalsToCheckViewModel->getExpired());
                        @endphp
                        <li>
                            <a href="{{ route('terminals.index') }}">
                                Терминалы
                                @if($lessMonthCount)
                                    <span
                                        class="badge bg-warning text-white">{{ $lessMonthCount < 99 ? $lessMonthCount : '99+' }}</span>
                                @endif
                                @if($expiredCount)
                                    <span
                                        class="badge bg-primary text-white">{{ $expiredCount < 99 ? $expiredCount : '99+' }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                     @if($user->access('users_read'))
                         <li><a href="{{ route('users.management.list-page') }}">Пользователи</a></li>
                     @endif

                    @if($user->access('stamp_read'))
                        <li><a href="{{ route('stamp.index') }}">Штампы</a></li>
                    @endif

                    @if($user->access('date_control_read', 'date_control_create'))
                        <li><a href="{{ route('renderElements', 'DDates') }}">Контроль дат</a></li>
                    @endif

                    @if($user->access('requisites_read', 'requisites_create'))
                        <li><a href="{{ route('renderElements', 'Req') }}">Реквизиты нашей компании</a></li>
                    @endif

                    @if($user->access('field_prompt_read'))
                        <li><a href="{{ route('prompt.index') }}">Подсказки полей</a></li>
                    @endif

                    @if($user->access('logs_read'))
                        <li><a href="{{ route('logs.index') }}">Журнал действий</a></li>
                        <li><a href="{{ route('form-logs.index') }}">Журнал действий с осмотрами</a></li>
                    @endif
                </ul>
            </li>
        @endif

    </ul>
</nav>

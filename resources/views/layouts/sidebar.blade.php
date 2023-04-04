@php
    $user_role_text = \App\Http\Controllers\ProfileController::getUserRole();
    $user_role = \App\Http\Controllers\ProfileController::getUserRole(false);
    [$pakQueueCnt, $pakErrorCnt, $sidebarItems, ] = \App\Services\SidebarService::renderItems();
@endphp

    <!-- Side Navbar -->
<nav class="side-navbar">
    <!-- Sidebar Header-->
    <div class="sidebar-header d-flex align-items-center">
        <div class="title">
            <article>
                @foreach(user()->roles as $role)
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
        <li>
            <a class="bg-info text-white">МЕНЮ</a>
        </li>
        @foreach($sidebarItems as $k => $item)
            @if(user()->access(sanitize_explode_by_commas($item->access_permissions)) or user()->hasRole('admin'))
                @if($item->is_header === 1)
                    <li>
                        <a data-btn-collapse="#items-{{$k}}"
                           data-toggle="tooltip"
                           data-placement="right"
                           title="{{$item->tooltip_prompt}}"
                           role="button" href="#">
                            <i class="{{$item->icon_class}}"></i>
                            {{$item->title}}
                        </a>
                        <ul id="items-{{$k}}" class="collapse list-unstyle">
                            @foreach($item->children as $child)
                                <li>
                                    <a href="{{$child->route_name}}"
                                       data-toggle="tooltip"
                                       data-placement="right"
                                       title="{{$item->tooltip_prompt}}"
                                    >
                                        <i class="{{$child->icon_class}}"></i>
                                        {{$child->title}}
                                        @if($child->slug === 'errors_pak_log')
                                            <span class="badge bg-primary text-white">{{$pakErrorCnt}}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @elseif($item->parent()->count() === 0)
                    <li>
                        <a data-toggle="tooltip"
                           data-placement="right"
                           title="{{$item->tooltip_prompt}}"
                           class="{{$item->css_class}}"
                           href="{{$item->route_name}}"
                        >
                            <i class="{{$item->icon_class}}"></i>
                            {{$item->title}}
                            @if ($item->slug === 'pak_queue')
                                <span class="badge bg-primary text-white">{{$item->parent}}{{$pakQueueCnt}}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endif
        @endforeach
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

                    @if(user()->access('employee_read', 'employee_create'))
                        <li><a href="{{ route('users') }}">Сотрудники</a></li>
                    @endif

                    @if(user()->access('group_read', 'group_create'))
                        <li><a href="{{ route('roles.index') }}"> Роли </a></li>
                    @endif

                    @if(user()->access('pak_sdpo_read', 'pak_sdpo_create'))
                        <li><a href="{{ route('terminals') }}">Терминалы</a></li>
                    @endif

                    @if(user()->access('date_control_read', 'date_control_create'))
                        <li><a href="{{ route('renderElements', 'DDates') }}">Контроль дат</a></li>
                    @endif

                    @if(user()->access('requisites_read', 'requisites_create'))
                        <li><a href="{{ route('renderElements', 'Req') }}">Реквизиты нашей компании</a></li>
                    @endif

                    @if(user()->access('field_prompt_read'))
                        <li><a href="{{ route('prompt.index') }}">Подсказки полей</a></li>
                    @endif
                    <li><a href="/sidebar/index">Настройки левого меню</a></li>

                </ul>
            </li>
        @endif

    </ul>
</nav>

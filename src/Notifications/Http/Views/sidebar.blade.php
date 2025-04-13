@php
    $countUnreadNotifications = 1;
    $expiredNotificationsCount = 2;
@endphp
<li>
    <a href="#" data-btn-collapse="#notifications" role="button">
        <i class="fa fa-comment"></i>
        Уведомления
        {{-- TODO: Нужно динамически обновлять --}}
        @if($countUnreadNotifications)
            <span
                class="badge bg-primary text-white">{{ $countUnreadNotifications < 99 ? $countUnreadNotifications : '99+' }}</span>
        @endif
        @if($expiredNotificationsCount)
            <span
                class="badge bg-warning text-white">{{ $expiredNotificationsCount < 99 ? $expiredNotificationsCount : '99+' }}</span>
        @endif
    </a>
    <ul id="notifications" class="collapse list-unstyle">
        <li>
            <a href="{{ route('notifications.list-page') }}">
                Список уведомлений
            </a>
            {{-- TODO: убрать после теста --}}
            @if(true || user()->access('notifications_logs'))
                <a href="{{ route('notifications.logs.list-page') }}">Журнал действий с уведомлениями</a>
            @endif
        </li>
    </ul>
</li>

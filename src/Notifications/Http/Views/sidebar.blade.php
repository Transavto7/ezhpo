<li>
    <a href="#" data-btn-collapse="#notifications" role="button">
        <i class="fa fa-comment"></i>
        Уведомления
        <span class="badge bg-primary text-white" id="countUnreadNotifications" style="display: none">123</span>
        <span class="badge bg-warning text-white" id="expiredNotificationsCount" style="display: none">321</span>
    </a>
    <ul id="notifications" class="collapse list-unstyle">
        <li>
            <a href="{{ route('notifications.list-page') }}">
                Список уведомлений
            </a>
            @if(user()->access('notifications_logs'))
                <a href="{{ route('notifications.logs.list-page') }}">Журнал действий с уведомлениями</a>
            @endif
        </li>
    </ul>
</li>

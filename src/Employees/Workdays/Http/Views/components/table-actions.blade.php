<div class="col-md-12">
    <div class="row bg-light p-2">
        <div class="col-md-6">
            <button type="button" data-toggle-show="#workdays-filters"
                    class="btn btn-sm btn-info"><i class="fa fa-cog"></i> <span
                    class="toggle-title">Настроить</span> колонки
            </button>

            @if($permissionToTrashView)
                @if(request()->get('trash', 0))
                    <a href="{{ route('employees.workdays.index') }}" class="btn btn-sm btn-warning">Назад</a>
                @else
                    <a href="?trash=1" class="btn btn-sm btn-warning">
                        Корзина <i class="fa fa-trash"></i>
                    </a>
                @endif
            @endif

        </div>

        @include('Workdays::components.visible-columns')
    </div>
</div>

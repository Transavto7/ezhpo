<div class="col-md-12">
    <div class="row bg-light p-2">
        <div class="col-md-6">
            <button type="button" data-toggle-show="#ankets-filters"
                    class="btn btn-sm btn-info"><i class="fa fa-cog"></i> <span
                    class="toggle-title">Настроить</span> колонки
            </button>

            @if($permissionToTrashView)
                @if(request()->get('trash', 0))
                    <a href="{{ route('workdays.list.index') }}" class="btn btn-sm btn-warning">Назад</a>
                @else
                    <a href="?trash=1" class="btn btn-sm btn-warning">
                        Корзина <i class="fa fa-trash"></i>
                    </a>
                @endif
            @endif

        </div>

        <div class="toggle-hidden p-3" id="ankets-filters">
            <form class="ankets-form" anketa="workdays">
                @foreach($fieldPrompts as $key => $field)
                    <label>
                        <input
                            checked
                            type="checkbox" name="{{ $field->field }}"
                            data-value="{{ $key+1 }}"/>
                        {{ $field->name }} &nbsp;
                    </label>
                @endforeach
            </form>
            <button class="btn btn-success btn-sm mt-3" id="saveFieldsBtn">Сохранить
            </button>
            <button class="btn btn-danger btn-sm mt-3" id="resetFieldsBtn">Сбросить</button>
            <div class="toast mt-2 toast-save-checks position-absolute">
                <div class="toast-body bg-success text-white">
                    Успешно сохранено
                </div>
            </div>

            <div class="toast mt-2 toast-reset-checks position-absolute">
                <div class="toast-body bg-danger text-white">
                    Успешно сброшено
                </div>
            </div>
        </div>
    </div>
</div>

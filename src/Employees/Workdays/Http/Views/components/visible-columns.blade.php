<div class="toggle-hidden p-3" id="workdays-filters">
    <form class="workdays-form">
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

    <button class="btn btn-success btn-sm mt-3" id="saveFieldsBtn">Сохранить</button>

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

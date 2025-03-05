<form method="POST"
      action="{{ route('employees.workdays.store') }}"
      class="form-horizontal"
      enctype="multipart/form-data"
      id="WORKDAY_FORM">

    @csrf

    @include('profile.ankets.components.pvs')

    <div class="form-group">
        <label class="form-control-label">ID сотрудника:</label>
        <article>
            <input value="{{ old('employee_id') }}"
                   type="number"
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'User', event.target.value, 'name', $(event.target).parent(), {{ 'false' }})"
                   required min="6"
                   name="employee_id"
                   class="MASK_ID_ELEM form-control">
            <div class="app-checker-prop"></div>
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Дата и время осмотра:</label>
        <article>
            <input min="1900-02-20T20:20"
                   max="2999-02-20T20:20"
                   type="datetime-local"
                   required
                   value="{{ old('employee_id', $default_current_date ?? '') }}"
                   name="date"
                   class="form-control inspection-date">
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Тип осмотра:</label>
        <article>
            <select name="type_anketa"
                    class="form-control">
                <option value="{{ \Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum::OPEN }}" selected>
                    Открытие
                </option>
                <option value="{{ \Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum::CLOSE }}">
                    Закрытие
                </option>
            </select>
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Проба на алкоголь:</label>
        <article>
            <select name="proba_alko"
                    class="form-control"
                    disabled>
                <option selected value="Отрицательно">
                    Отрицательно
                </option>
            </select>
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Уровень алкоголя в выдыхаемом воздухе:</label>
        <article>
            <input type="number"
                   step="0.01"
                   min="0"
                   value="0"
                   name="alcometer_result"
                   class="form-control"
                   disabled>
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Тест на наркотики:</label>
        <article>
            <select name="test_narko" class="form-control">
                <option selected value="Не проводился">
                    Не проводился
                </option>
                <option value="Отрицательно">
                    Отрицательно
                </option>
            </select>
        </article>
    </div>

    <div class="form-group row">
        <button type="submit" class="m-center btn btn-sm btn-success submit-btn">
            Добавить
        </button>
    </div>
</form>

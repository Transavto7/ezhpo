<form onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
      action="" method="GET" class="tab-content p-3">
    <input type="hidden" name="filter" value="1">

    @if(request()->filled('trash'))
        <input type="hidden" name="trash" value="{{ request()->get('trash') }}">
    @endif

    <input type="hidden" name="take" value="{{ request()->get('take', '') }}">

    <div class="row">
        <div class="col-md-6">
            @include('Workdays::components.filters.points')
        </div>
        <div class="col-md-6">
            @include('Workdays::components.filters.employees')
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            @include('Workdays::components.filters.date-from')
        </div>
        <div class="col-md-3">
            @include('Workdays::components.filters.date-to')
        </div>
        <div class="col-md-3">
            @include('Workdays::components.filters.flag-sdpo')
        </div>
        <div class="col-md-3">
            @include('Workdays::components.filters.is-real')
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            @include('Workdays::components.filters.admitted')
        </div>
        <div class="col-md-3">
            @include('Workdays::components.filters.roles')
        </div>
        <div class="col-md-3">
            @include('Workdays::components.filters.type-anketa')
        </div>
    </div>

    <button type="submit" class="btn btn-info">Поиск</button>
    <a class="btn btn-danger reload-filters" href="{{ route('employees.workdays.index') }}">
        <span class="spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        Сбросить
    </a>
</form>

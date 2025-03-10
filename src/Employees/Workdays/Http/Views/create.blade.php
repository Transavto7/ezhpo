@extends('layouts.app')

@section('title', 'Закрытие смены')
@section('sidebar', 1)

@section('custom-scripts')
    <script type="text/javascript">
        if (screen.width <= 700) {
            ANKETA_FORM_VIEW.insertBefore(WORKDAY_FORM_ROOT, WORKDAY_FORM_VIEW_FIRST)
        }
    </script>
@endsection

@section('content')
    @include('profile.ankets.components.fast-scroll')

    <div class="row" id="WORKDAY_FORM_VIEW">
        <div class="col-lg-3" id="WORKDAY_FORM_VIEW_FIRST">
            <div class="card">
                <div class="card-body">
                    <p><b>Карточка сотрудника</b></p>

                    <div id="CARD_EMPLOYEE">
                        Не найдено
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3" id="WORKDAY_FORM_ROOT">
            <div class="card">
                <div class="card-body">
                    <p><b>Закрытие смены</b></p>

                    <article class="workday workday-fields">
                        @foreach($errors ?? [] as $error)
                            <div class="alert alert-danger" role="alert">{{ $error }}</div>
                        @endforeach

                        @if(count($created ?? []))
                            <div class="row">
                                @foreach($created ?? [] as $workday)
                                    @include('Workdays::components.created', ['workday' => $workday])
                                @endforeach
                            </div>
                        @endif

                        @if(\Illuminate\Support\Facades\Session::has('message'))
                            <div class="alert alert-success">
                                <b>{{ \Illuminate\Support\Facades\Session::get('message') }}</b>
                            </div>
                        @endif

                            @include('Workdays::components.create-form')
                    </article>
                </div>
            </div>
        </div>
    </div>

@endsection

@extends('layouts.app')
@section('title', 'ЛК Водителя')
@section('content')
    <div class="container text-center">
        <h1>БДД</h1>
        <hr>

        <div class="row">
            <div class="col-md-12 mb-5">
                <h2>Выберите инструктаж</h2>
                <hr>

                <div class="row">
                    <div class="col-md-4 nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                         aria-orientation="vertical">

                        @foreach($instrs as $instr)
                            <a class="nav-link driver-bdd-link {{ $loop->first ? 'active' : '' }}"
                               id="instr-{{ $instr->id }}"
                               data-toggle="pill"
                               href="#instr-{{ $instr->id }}-tab"
                               role="tab"
                               aria-controls="instr-{{ $instr->id }}"
                               aria-selected="false">
                                {{ $instr->name }} [{{ $instr->type_briefing }}]
                            </a>
                        @endforeach
                    </div>
                    <div class="col-md-8 tab-content text-left" id="v-pills-tabContent">
                        @foreach($instrs as $instr)
                            <div class="tab-pane fade {{ $loop->first ? 'active show' : '' }}"
                                 id="instr-{{ $instr->id }}-tab"
                                 role="tabpanel"
                                 aria-labelledby="instr-{{ $instr->id }}-tab">
                                <h3>{{ $instr->name }}</h3>
                                <h6>Тип инструктажа: {{ $instr->type_briefing }}</h6>

                                @if($instr->photos)
                                    <p>
                                        <img src="{{ Storage::url($instr->photos) }}" width="200">
                                    </p>
                                @endif

                                @include('pages.driver-bdd.components.frame', ['url' => $instr->youtube])

                                <br><br>
                                <p>{{ $instr->descr }}</p>

                                <hr>
                                <h4>Вам был понятен инструктаж?</h4>

                                <div class="row">
                                    <div class="col-md-1">
                                        <form id="API_FORM_ANKETS_{{ $instr->id }}"
                                              data-success-title="Инструктаж пройден!"
                                              class="API_FORM_SEND"
                                              method="post"
                                              action="{{ route('driver.bdd.store') }}">
                                            @csrf
                                            <input type="hidden"
                                                   name="type_anketa"
                                                   value="bdd"/>
                                            <input type="hidden"
                                                   name="pv_id"
                                                   value="{{ $pv_id }}"/>
                                            <input value="{{ auth()->user()->login }}"
                                                   type="hidden"
                                                   name="driver_id">
                                            <input value="{{ $instr->type_briefing }}"
                                                   type="hidden"
                                                   name="anketa[0][type_briefing]">
                                            <input type="hidden"
                                                   value="{{ date('Y-m-d\TH:i', time()) }}"
                                                   name="anketa[0][date]">
                                            <input type="hidden"
                                                   value="Подписано простой электронной подписью"
                                                   name="anketa[0][signature]">
                                            <input type="hidden"
                                                   value="{{ $instr->name }}"
                                                   name="anketa[0][briefing_name]">

                                            <button type="submit" class="btn btn-success">Да</button>
                                        </form>
                                    </div>

                                    <div class="col-md-1">
                                        <a href="{{ route('driver.index') }}"
                                           onclick="return confirm('Точно хотите уйти?')"
                                           class="btn btn-danger">
                                            Нет
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

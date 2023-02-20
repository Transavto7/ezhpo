@extends('layouts.app')

@section('title', 'ЛК Водителя')
@php

    $nullable = $instrs->where('sort','===', null);
    $full = $instrs->where('sort','!==' ,null)->sortBy('sort');
    $instrs = collect();
    $instrs = $instrs->merge($full);
    $instrs = $instrs->merge($nullable);
@endphp
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
                        @php $number = 0; @endphp
                        @foreach($instrs as $k => $instr)
                            <a class="nav-link driver-bdd-link {{ $number === 0 ? 'active' : '' }}"
                               id="instr-{{ $instr->id }}"
                               data-toggle="pill"
                               href="#instr-{{ $instr->id }}-tab"
                               role="tab"
                               aria-controls="instr-{{ $instr->id }}"
                               aria-selected="false">
                                {{ $instr->name }} [{{ $instr->type_briefing }}]
                            </a>

                            @php $number++; @endphp
                        @endforeach
                    </div>
                    <div class="col-md-8 tab-content text-left" id="v-pills-tabContent">
                        @php $number = 0; @endphp
                        @foreach($instrs as $k => $instr)
                            <div class="tab-pane fade {{ $number === 0 ? 'active show' : '' }}"
                                 id="instr-{{ $instr->id }}-tab"
                                 role="tabpanel"
                                 aria-labelledby="instr-{{ $instr->id }}-tab"
                            >
                                <h3>{{ $instr->name }}</h3>
                                <h6>Тип инструктажа: {{ $instr->type_briefing }}</h6>

                                @if($instr->photos)
                                    <p>
                                        <img src="{{ Storage::url($instr->photos) }}" width="200">
                                    </p>
                                @endif

                                {{--                                <iframe width="100%" height="300" src="https://www.youtube.com/embed/{{ str_replace('https://www.youtube.com/watch?v=', '', $instr->youtube) }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>--}}

                                <iframe  width="100%" height="350"
                                    srcdoc="<style>html, body, a, img {display: block; width: 100%; height: 100%; margin: 0; padding: 0;}
                                    a:before, a:after {position: absolute; content: ''; left: 50%; top: 50%;} a:before {margin: -4.9% 0 0 -7%;
                                    background: rgba(31,31,30,.8); padding-top: 9.8%; width: 14%; border-radius: 16% / 27%;} a:after {margin: -1.9vw 0 0 -1vw;
                                    border: 2vw solid transparent; border-left: 3.8vw solid #fff;} a:hover:before {background: #c0171c;}</style>
                                    <a href='http://www.youtube-nocookie.com/embed/{{ str_replace('https://www.youtube.com/watch?v=', '', $instr->youtube) }}?autoplay=1'>
                                    <img src='//img.youtube.com/vi/{{ str_replace('https://www.youtube.com/watch?v=', '', $instr->youtube) }}/maxresdefault.jpg'
                                    srcset='//img.youtube.com/vi/{{ str_replace('https://www.youtube.com/watch?v=', '', $instr->youtube) }}/mqdefault.jpg 320w, //img.youtube.com/vi/{{ str_replace('https://www.youtube.com/watch?v=', '', $instr->youtube) }}/hqdefault.jpg 480w,
                                    //img.youtube.com/vi/{{ str_replace('https://www.youtube.com/watch?v=', '', $instr->youtube) }}/maxresdefault.jpg 1307w'></a>"
                                    allowfullscreen></iframe>


                                <a href="{{ $instr->youtube }}">Посмотреть видео на YouTube</a>
                                <br><br>
                                <p>{{ $instr->descr }}</p>

                                <hr>
                                <h4>Вам был понятен инструктаж?</h4>

                                <div class="row">
                                    <div class="col-md-1">
                                        <form id="API_FORM_ANKETS_{{ $instr->id }}"
                                              data-success-title="Инструктаж пройден!" class="API_FORM_SEND"
                                              method="post" action="{{ route('api.addform') }}">
                                            @csrf

                                            <input type="hidden" name="type_anketa" value="bdd"/>
                                            <input type="hidden" name="pv_id" value="{{ $pv_id }}"/>
                                            <input value="{{ auth()->user()->login }}" type="hidden" name="driver_id">
                                            <input value="{{ $instr->type_briefing }}" type="hidden"
                                                   name="anketa[0][type_briefing]">
                                            <input type="hidden" value="{{ date('Y-m-d\TH:i', time()) }}"
                                                   name="anketa[0][date]">
                                            <input type="hidden" value="Подписано простой ЭЦП"
                                                   name="anketa[0][signature]">
                                            <input type="hidden" value="{{ $instr->name }}"
                                                   name="anketa[0][briefing_name]">
                                            <input type="hidden" value="{{ $instr->name }}"
                                                   name="anketa[0][briefing_name]">

                                            <button type="submit" class="btn btn-success">Да</button>
                                        </form>

                                    </div>

                                    <div class="col-md-1">
                                        <a href="{{ route('page.driver') }}"
                                           onclick="return confirm('Точно хотите уйти?')" class="btn btn-danger">Нет</a>
                                    </div>
                                </div>
                            </div>
                            @php $number++; @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

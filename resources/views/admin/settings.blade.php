@extends('layouts.app')

@section('title','Системные настройки')
@section('sidebar', 1)

@section('content')

    <div class="row">
        <!-- Form Elements -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('systemSettings.update') }}" method="POST" class="form-horizontal">
                        @csrf

                        @if(count($settings))
                            @foreach($settings as $setting)
                                <div class="line"></div>
                                <label class="form-group row">
                                    <div class="col-sm-3 form-control-label">{{ $setting->label }}</div>
                                    <div class="col-sm-9">
                                        <input type="{{ $setting->input_type }}"

                                               @if($setting->input_type === 'checkbox')
                                                    {{ $setting->val ? 'checked' : '' }}
                                               @else
                                                   value="{{ $setting->val }}"
                                               @endif

                                               @isset($setting->connect_field)
                                                   data-connect-field="{{ $setting->connect_field }}"
                                               @endisset

                                               name="{{ $setting->param }}">
                                    </div>
                                </label>
                            @endforeach
                        @endif

                        <button type="submit" class="btn btn-success">Сохранить</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

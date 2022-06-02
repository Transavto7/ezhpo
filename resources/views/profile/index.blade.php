@extends('layouts.app')

@section('title','Профиль')
@section('sidebar', 1)

@section('content')

<div class="row">
    <!-- Form Elements -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
            <form action="{{ route('updateProfile') }}" enctype="multipart/form-data" method="POST" class="form-horizontal">
                @csrf

                <p class="text"><strong>#{{ $user->id }}</strong><br/></p>

                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">Фото:</label>
                    <div class="col-sm-9">
                        <input type="file" name="photo">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">ФИО:</label>
                    <div class="col-sm-9">
                        <input disabled type="text" value="{{ $user->name }}" name="name" class="form-control">
                    </div>
                </div>

                <div class="line"></div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">E-mail:</label>
                    <div class="col-sm-9">
                        <input disabled type="text" value="{{ $user->email }}" name="email" class="form-control">
                    </div>
                </div>

                <div class="line"></div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">ЭЦП:</label>
                    <div class="col-sm-9">
                        <input disabled type="text" value="{{ $user->eds }}" name="eds" class="form-control">
                    </div>
                </div>

                <div class="line"></div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">Часовой пояс:</label>
                    <div class="col-sm-9">
                        <input type="text" disabled value="{{ $user->timezone }}" name="timezone" class="form-control">
                    </div>
                </div>

                <div class="line"></div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">Пункт выпуска <b>по умолчанию</b>:</label>
                    <div class="col-sm-9">
                        <input type="text" disabled value="{{ $point }}" class="form-control">
                    </div>
                </div>

                @manager
                    <div class="line"></div>
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">API TOKEN</label>
                        <div class="col-sm-9">
                            <input type="text" disabled value="{{ $user->api_token }}" class="form-control">
                        </div>
                    </div>
                @endmanager

                <div class="line"></div>
                <div class="form-group row">
                    <div class="col-sm-4 offset-sm-3">
                        <a href="{{ route('home') }}" class="btn btn-info"><i class="fa fa-arrow-left"></i> Назад</a>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                    </div>
                </div>

            </form>
            </div>
        </div>
    </div>
</div>

@endsection

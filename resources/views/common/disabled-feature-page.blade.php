@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger mb-0" role="alert">
                        <ul class="mb-0">
                            Данная страница недоступна по одной из причин
                            <li>
                                она находится в разработке
                            </li>
                            <li>
                                она отключена на вашем домене
                            </li>
                            <li>
                                ведутся технические работы
                            </li>
                            <li>
                                она недоступна для вашего пользователя
                            </li>
                            Если вы уверены, что это ошибка - обратитесь к администратору
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

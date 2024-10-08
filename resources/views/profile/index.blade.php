@extends('layouts.app')

@section('title','Профиль')
@section('sidebar', 1)

@section('content')

    <div class="row d-flex justify-content-center">
        <!-- Form Elements -->

        <div class="col-12 col-lg-10">
            <div class="card p-2 py-0 py-lg-5 driver-card overflow-hidden">
                <form action="{{ route('profile.updateAvatar') }}" enctype="multipart/form-data" method="POST"
                      class="form-horizontal">
                    @csrf
                    <div style="display: none;" id="croppie-blockPHOTO"
                         class="croppie-block text-center croppie-block-profile"
                    >
                        <input type="hidden" name="photo_base64" id="croppie-result-base64PHOTO">
                        <div class="croppie-demo" data-croppie-id="PHOTO">
                            <div class="my-3">
                                <button type="button" data-croppie-id="PHOTO"
                                        class="btn croppie-save btn-sm btn-success">Сохранить обрезку
                                </button>
                                <button type="button" data-croppie-id="PHOTO"
                                        class="btn croppie-delete btn-sm btn-danger">Удалить фото
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-lg-4 p-2 d-flex align-items-center justify-content-center flex-column">
                            <img class="card-img-top driver-card-img"
                                 src="{{ asset($user->photo ? "storage/".$user->photo : "img/default_profile.jpg") }}"
                                 alt="Card image cap">
                            <div class="card-img-buttons">
                                <div class="input-group mt-4">
                                    <div>
                                        <a class="btn btn-danger border-radius-0 py-1 mr-1"
                                           data-toggle="tooltip" title="Удалить текущий аватар"
                                           href="{{ route('profile.deleteAvatar') }}">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                    <div class="custom-file">
                                        <input
                                            type="file"
                                            class="custom-file-input"
                                            id="croppie-inputPHOTO"
                                            name="photo"
                                            accept="image/*"
                                            data-label="photo"
                                            data-field="User_photo"
                                            aria-describedby="inputPHOTO">
                                        <label class="custom-file-label" for="inputPHOTO">Выберите файл</label>
                                    </div>
                                </div>

                                <div class="mt-2 d-flex justify-content-between">
                                    <a href="{{ route('home') }}" class="btn btn-info btn-sm"><i
                                            class="fa fa-arrow-left"></i> Назад</a>
                                    <button type="submit" class="btn btn-success btn-sm">Сохранить</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="input-group my-3 d-flex align-items-center">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="line-height: 1.3"
                                          id="inputGroup-sizing-default">ID:</span>
                                </div>
                                <input
                                    @if(user()->hasRole('driver'))
                                        value="{{ $user->login }}"
                                    @elseif(user()->hasRole('client'))
                                        value="{{ $user->company->hash_id }}"
                                    @else
                                        value="{{ $user->hash_id }}"
                                    @endif
                                    disabled
                                    type="text"
                                    class="form-control p-3 fw-bold"
                                    aria-label="Default"
                                    aria-describedby="inputGroup-sizing-default">
                            </div>

                            <div class="input-group my-3 d-flex align-items-center">
                                <div class="input-group-prepend">
                                <span class="input-group-text" style="line-height: 1.3" id="inputGroup-sizing-default">
                                 {{ user()->hasRole('client') ? 'Название компании' : 'ФИО'  }}:
                                </span>
                                </div>
                                <input value="{{ user()->name }}" disabled type="text" class="form-control p-3 fw-bold"
                                       aria-label="Default" aria-describedby="inputGroup-sizing-default">
                            </div>

                            @if (!user()->hasRole('driver') && !user()->hasRole('client'))
                                <div class="input-group mb-3 d-flex align-items-center">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="line-height: 1.3">E-mail:</span>
                                    </div>
                                    <input value="{{ $user->email }}" disabled type="text"
                                           class="form-control p-3 fw-bold" aria-label="Default"
                                           aria-describedby="inputGroup-sizing-default">
                                </div>

                                <div class="input-group mb-3 d-flex align-items-center">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="line-height: 1.3">ЭЦП:</span>
                                    </div>
                                    <input value="{{ $user->eds }}" disabled type="text"
                                           class="form-control p-3 fw-bold" aria-label="Default"
                                           aria-describedby="inputGroup-sizing-default">
                                </div>

                                <div class="input-group mb-3 d-flex align-items-center">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="line-height: 1.3">Часовой пояс:</span>
                                    </div>
                                    <input value="{{ $user->timezone }}" disabled type="text"
                                           class="form-control p-3 fw-bold" aria-label="Default"
                                           aria-describedby="inputGroup-sizing-default">
                                </div>
                            @endif

                            @if (!$user->hasRole('client'))
                                <div class="input-group mb-3 d-flex align-items-center">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="line-height: 1.3">Должность:</span>
                                    </div>
                                    <input value="{{ implode(', ',  $user->roles->pluck('guard_name')->toArray()) }}"
                                           disabled type="text" class="form-control p-3 fw-bold" aria-label="Default"
                                           aria-describedby="inputGroup-sizing-default">
                                </div>

                                <div class="input-group mb-3 d-flex align-items-center">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="line-height: 1.3">Пункт выпуска:</span>
                                    </div>
                                    <input value="{{ user()->pv->name ?? 'Неизвестно' }}" disabled type="text"
                                           class="form-control p-3 fw-bold" aria-label="Default"
                                           aria-describedby="inputGroup-sizing-default">
                                </div>
                            @endif

                            @if ($user->hasRole('client'))
                                <div class="input-group mb-3 d-flex align-items-center flex-nowrap">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="line-height: 1.3">Ссылка на таблицу с документами БДД:</span>
                                    </div>
                                    @if($user->company->document_bdd)
                                        <a href="{{ $user->company->document_bdd }}" class="form-profile-link pl-3">
                                            {{ $user->company->document_bdd }}
                                        </a>
                                    @else
                                        <div class="form-profile-link pl-3">
                                            Отсутствует
                                        </div>
                                    @endif
                                    @endif
                                </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@extends('layouts.app')

@section('title','Системные настройки')
@section('sidebar', 1)

@section('content')
<div class="row">
    <!-- Form Elements -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('settings.update') }}"
                      method="POST" class="form-horizontal"
                      enctype="multipart/form-data"
                >
                    @csrf
                    <div class="row px-4">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <div class="logo d-flex justify-content-center pb-2"
                                    style="max-height: 80px; overflow: hidden"
                                >
                                    <img src="{{ Storage::url($logo->value) }}"
                                         alt=""
                                         width="300px"
                                         style="height: 100%;"
                                    >
                                </div>
                                <label for="slogo-input" class="ml-1 mt-2">API ключ sms.ru</label>
                                <div class="custom-file">
                                    <input
                                        type="file"
                                        class="custom-file-input "
                                        accept="image/*"
                                        id="logo-input"
                                        name="logo"
                                    >
                                    <label class="custom-file-label" for="logo-input">Выберите файл</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-9 row">
                            <div class="form-group col-lg-6">
                                <label for="sms_api_key" class="mb-1">API ключ sms.ru</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="sms_api_key_prepend">
                                            <i class="fa fa-key"></i>
                                        </span>
                                    </div>
                                    <input type="text"
                                           class="form-control"
                                           id="sms_api_key"
                                           style="height: auto"
                                           placeholder="Введите API ключ"
                                           name="sms_api_key"
                                           aria-describedby="sms_api_key_prepend"
                                           value="{{ $sms_api_key->value }}"
                                    >
                                </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="sms_text_phone" class="mb-1">Телефон, куда звонить в случае вопросов</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="sms_api_key_prepend">
                                            <i class="fa fa-key"></i>
                                        </span>
                                    </div>
                                    <input type="text"
                                           class="form-control"
                                           id="sms_api_key"
                                           style="height: auto"
                                           placeholder="Введите телефон"
                                           name="sms_text_phone"
                                           aria-describedby="sms_text_phone_prepend"
                                           value="{{ $sms_text_phone->value }}"
                                    >
                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <label for="sms_text_driver" class="mb-1">Текст SMS для Водителя при непрохождении осмотра</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="sms_text_driver_prepend">
                                            <i class="fa fa-key"></i>
                                        </span>
                                    </div>
                                    <textarea
                                        class="form-control"
                                        id="sms_text_driver"
                                        style="height: auto"
                                        placeholder="Введите Текст SMS"
                                        name="sms_text_driver"
                                        aria-describedby="sms_text_driver_prepend"
                                    >{{ $sms_text_driver->value }}</textarea>
                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <label for="sms_text_car" class="mb-1">Текст SMS для Авто при непрохождении осмотра</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="sms_text_car_prepend">
                                            <i class="fa fa-key"></i>
                                        </span>
                                    </div>
                                    <textarea
                                        class="form-control"
                                        id="sms_text_car"
                                        style="height: auto"
                                        placeholder="Введите Текст SMS"
                                        name="sms_text_car"
                                        aria-describedby="sms_text_car_prepend"
                                    >{{ $sms_text_car->value }}</textarea>
                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <label for="sms_text_default" class="mb-1">Текст сообщения по умолчанию</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="sms_text_default_prepend">
                                            <i class="fa fa-key"></i>
                                        </span>
                                    </div>
                                    <textarea
                                        class="form-control"
                                        id="sms_text_default"
                                        style="height: auto"
                                        name="sms_text_default"
                                        placeholder="Введите Текст SMS"
                                        aria-describedby="sms_text_default_prepend"
                                    >{{ $sms_text_default->value }}</textarea>
                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <div class="row px-3">
                                    <div class="custom-control custom-checkbox col-lg-3">
                                        <input name="id_auto"
                                               type="checkbox"
                                               class="custom-control-input"
                                               id="id_auto"
                                            {{ $id_auto->value === '1' ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" style="padding-top: 2px" for="id_auto">
                                            Поле "ID авто" в МО
                                        </label>
                                    </div>

                                    <div class="custom-control custom-checkbox col-lg-3">
                                        <input name="id_auto_required"
                                               type="checkbox"
                                               class="custom-control-input"
                                               id="id_auto_required"
                                            {{ $id_auto_required->value === '1' ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" style="padding-top: 2px" for="id_auto_required">
                                            Обязательное поле ID авто в МО
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <button type="submit" class="btn btn-success float-right">Сохранить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

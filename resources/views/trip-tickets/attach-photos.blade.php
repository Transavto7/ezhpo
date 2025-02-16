@extends('layouts.app')

@section('title', 'Загрузка фото ПЛ')

@php

@endphp

@section('custom-styles')
    <style>
        label {
            font-size: 18px;
            margin-right: 10px;
        }
        input[type="file"] {
            display: none;
        }
    </style>
@endsection

@section('custom-scripts')
    <script>
        $(document).ready(function () {
            const $imageUpload = $('#imageUpload');
            const $previewImage = $('#previewImage');
            const $openCameraButton = $('#openCamera');

            $imageUpload.on('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $previewImage.attr('src', e.target.result);
                        $previewImage.show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $openCameraButton.on('click', function () {
                $imageUpload.click();
            });
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6 offset-3">
            <div class="card">
                <div class="card-body">
                    <p><b>{{ 'Загрузка фото ПЛ' }}</b></p>

                    @if(\Illuminate\Support\Facades\Session::has('message'))
                        <div class="alert alert-success">
                            <b>{{ \Illuminate\Support\Facades\Session::get('message') }}</b>
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('trip-tickets.attach-photos-page', ['id' => $id]) }}"
                          class="form-horizontal"
                          onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
                          enctype="multipart/form-data">
                        @csrf

                        <label for="imageUpload">Выберите изображение:</label>
                        <input type="file" id="imageUpload" name="imageUpload" accept="image/*" capture="environment">
                        <p>Или <button type="button" id="openCamera">Открыть камеру</button></p>

                        <div class="form-group row mb-0">
                            <a href="/" class="m-center btn btn-sm btn-info">Главная</a>
                            <button type="submit" class="m-center btn btn-sm btn-success submit-btn">Сохранить
                            </button>
                        </div>
                    </form>

                    <div id="preview">
                        <h2>Предпросмотр:</h2>
                        <img id="previewImage" src="#" alt="Предпросмотр изображения" style="max-width: 300px; display: none;">
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

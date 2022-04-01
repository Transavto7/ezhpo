@php $photoInputId = sha1(time()); @endphp

<!-- Добавление элемента -->
<div id="users-modal-add" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавление сотрудника</h4>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>

            <form method="POST" enctype="multipart/form-data" action="{{ route('adminCreateUser') }}">
                @csrf

                <div class="modal-body">
                    <p>Заполните форму внимательно и нажмите кнопку "Добавить"</p>

                    <div class="form-group">
                        <label>
                            Фотография
                            <input type="file" id="croppie-input{{ $photoInputId }}" name="photo">

                            <div style="display: none;" id="croppie-block{{ $photoInputId }}" class="croppie-block text-center">
                                <input type="hidden" name="photo_base64" id="croppie-result-base64{{ $photoInputId }}">
                                <div class="croppie-demo" data-croppie-id="{{ $photoInputId }}"></div>
                                <button type="button" data-croppie-id="{{ $photoInputId }}" class="btn croppie-save btn-sm btn-success">Сохранить обрезку</button>
                                <button type="button" data-croppie-id="{{ $photoInputId }}" class="btn croppie-delete btn-sm btn-danger">Удалить фото</button>
                            </div>

                        </label>
                    </div>

                    <div class="form-group">
                        <input type="text" required name="name" placeholder="Ваше имя" class="form-control">
                    </div>

                    <div class="form-group">
                        <input type="text" required name="login" placeholder="Login" class="form-control">
                        <i>(Пользователь авторизуется по введеному Вами логину)</i>
                    </div>

                    <div class="form-group">
                        <input type="email" required name="email" placeholder="E-mail" class="form-control">
                    </div>

                    <div class="form-group">
                        <div class="field field--password">
                            <i class="fa fa-eye-slash"></i>
                            <input data-toggle="password" id="password" type="password" placeholder="Пароль..." class="form-control" name="password"  autocomplete="current-password">
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="text" name="eds" placeholder="ЭЦП" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Часовой пояс <i>(UTC+3)</i></label>
                        <input type="number" name="timezone" value="3" placeholder="Часовой пояс" class="form-control">
                    </div>

                    <div class="form-group">
                        @include('admin.users.show_pvs')
                    </div>

                    <div class="form-group">
                        <label>Роль</label>
                        <select name="role" required class="form-control">
                            @if(!$is_pak)
                                <option value="12">Клиент</option>
                                <option value="4">Оператор СДПО</option>
                                <option value="1">Контролёр ТС</option>
                                <option selected value="2">Медицинский сотрудник</option>
                                <option value="11">Менеджер</option>
                                <option value="777">Администратор</option>
                            @endif
                            <option @if($is_pak) checked @endif value="778">Терминал</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Сделать менеджером</label>
                        <select name="role_manager">
                            <option value="1">Да</option>
                            <option selected value="0">Нет</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Заблокирован</label>
                        <select name="blocked">
                            <option value="1">Да</option>
                            <option selected value="0">Нет</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Добавить</button>
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Закрыть</button>
                </div>
            </form>

        </div>
    </div>
</div>

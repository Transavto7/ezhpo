@php $photoInputId = sha1(time()); @endphp

<!-- Добавление элемента -->
<div id="users-modal-add" role="dialog" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавление {{ $is_pak ? 'терминала' : 'сотрудника' }}</h4>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                        aria-hidden="true">×</span></button>
            </div>

            <form method="POST" autocomplete="off" enctype="multipart/form-data"
                  action="{{ route('adminCreateUser') }}">
                @csrf

                <div class="modal-body">
                    <p>Заполните форму внимательно и нажмите кнопку "Добавить"</p>

                    @if(!$is_pak)
                        <div class="form-group">
                            <label>
                                Фотография
                                <input type="file" id="croppie-input{{ $photoInputId }}" name="photo">

                                <div style="display: none;" id="croppie-block{{ $photoInputId }}"
                                     class="croppie-block text-center">
                                    <input type="hidden" name="photo_base64"
                                           id="croppie-result-base64{{ $photoInputId }}">
                                    <div class="croppie-demo" data-croppie-id="{{ $photoInputId }}"></div>
                                    <button type="button" data-croppie-id="{{ $photoInputId }}"
                                            class="btn croppie-save btn-sm btn-success">Сохранить обрезку
                                    </button>
                                    <button type="button" data-croppie-id="{{ $photoInputId }}"
                                            class="btn croppie-delete btn-sm btn-danger">Удалить фото
                                    </button>
                                </div>

                            </label>
                        </div>
                    @endif

                    @if(!$is_pak)
                        <div class="form-group">
                            <input type="text" required name="name" placeholder="Ваше ФИО" class="form-control">
                        </div>
                    @else
                        <input type="hidden" name="name" value="{{ random_int(5000,99999) }}">
                    @endif

                    <div class="form-group">
                        <input type="text" required name="login" placeholder="Login" class="form-control"
                               autocomplete="off">
                        <i>(Пользователь авторизуется по введеному Вами логину)</i>
                    </div>

                    <div class="form-group">
                        <input type="email" required name="email" placeholder="E-mail" class="form-control"
                               autocomplete="off">
                    </div>

                    <div class="form-group">
                        <div class="field field--password">
                            <i class="fa fa-eye-slash"></i>
                            <input data-toggle="password" id="password" type="password" placeholder="Пароль..."
                                   class="form-control" name="password" autocomplete="off">
                        </div>
                    </div>

                    @if(!$is_pak)
                        <div class="form-group">
                            <input type="text" name="eds" placeholder="ЭЦП" class="form-control">
                        </div>
                    @endif

                    <div class="form-group ">
                        <label>Часовой пояс <i>(UTC+3)</i></label>
                        <input type="number" name="timezone" value="3" placeholder="Часовой пояс" class="form-control">
                    </div>

                    <div class="form-group users_show_pvs">
                        @include('admin.users.show_pvs')
                    </div>

                    <div class="form-group users_show_city" style="display: none">
                        <label>Город</label>
                        <select name="city_id" required class="form-control">
                            <option value="0">--none--</option>
                            @foreach(\App\Town::get() as $cityInfo)
                                <option value="{{$cityInfo->id}}">{{$cityInfo->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(!$is_pak)
                        <div class="form-group">
                            <label>Роль</label>
                            <select name="role" required class="form-control">
                                @foreach(\App\User::$userRolesText as $roleKey =>  $roleName)
                                    <option value="{{$roleKey}}">{{$roleName}}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="role" value="778">
                    @endif

                    @if(!$is_pak)
                        <div class="form-group">
                            <label>Сделать менеджером</label>
                            <select name="role_manager">
                                <option value="1">Да</option>
                                <option selected value="0">Нет</option>
                            </select>
                        </div>
                    @endif

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

@section('custom-scripts')
    <script>
        $(document).ready(function () {
            $(document).on('change', 'select[name="role"]', function(event) {
                const field = $(event.target);
                let selected = field.val()

                if(selected == 12){
                    field.closest('.modal-body').find('.users_show_city').show()
                    field.closest('.modal-body').find('.users_show_pvs').hide()
                    field.closest('.modal-body').find('select[name="pv_id"]').val(0)

                }else{
                    field.closest('.modal-body').find('.users_show_pvs').show()
                    field.closest('.modal-body').find('.users_show_city').hide()
                    field.closest('.modal-body').find('select[name="city_id"]').val(0)
                }
            });
        })
    </script>
@endsection

<table id="elements-table" class="table table-striped table-sm">
    <thead>
    <tr>
        <th width="60">ID
            <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey=id">
                <i class="fa fa-sort"></i>
            </a>
        </th>

        @if(!$is_pak)
            <th width="60">Фото</th>
            <th>ФИО
                <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey=name">
                    <i class="fa fa-sort"></i>
                </a>
            </th>
            <th>ЭЦП
                <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey=eds">
                    <i class="fa fa-sort"></i>
                </a>
            </th>
        @else
            <th>Token</th>
        @endif

        <th>Login</th>
        <th>E-mail
            <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey=email">
                <i class="fa fa-sort"></i>
            </a>
        </th>
        <th>ПВ
            <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey=pv_id">
                <i class="fa fa-sort"></i>
            </a>
        </th>
        <th>GMT
            <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey=timezone">
                <i class="fa fa-sort"></i>
            </a>
        </th>
        <th>Заблокирован
            <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey=blocked">
                <i class="fa fa-sort"></i>
            </a>
        </th>
        <th width="60">Роль
            <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey=role">
                <i class="fa fa-sort"></i>
            </a>
        </th>
        <th>#</th>
        <th>#</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>

            @if(!$is_pak)
                <td>
                    <a href="{{ \App\Http\Controllers\ProfileController::getAvatar($user->id) }}" data-fancybox="gallery_{{ $user->id }}">
                        <b>
                            <i class="fa fa-camera"></i>
                        </b>
                    </a>
                </td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->eds }}</td>
            @else
                <td>{{ $user->api_token }}</td>
            @endif

            <td>{{ $user->login }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ \App\Point::getPointText($user->pv_id) }}</td>
            <td>{{ $user->timezone }}</td>
            <td>{{ $user->blocked ? 'Да' : 'Нет' }}</td>
            <td>{{ \App\Http\Controllers\ProfileController::getUserRole(true, $user->id) }}</td>
            <td class="td-option">
                <a href="" data-toggle="modal" data-target="#users-modal-edit-{{ $user->id }}" class="btn btn-info"><i class="fa fa-edit"></i></a>


                <!-- Редактирование элемента -->
                <div id="users-modal-edit-{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true" class="modal fade text-left">
                    <div role="document" class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Редактирование сотрудника "{{ $user->name }}"</h4>
                                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                            </div>

                            <form method="POST" enctype="multipart/form-data" action="{{ route('adminUpdateUser', $user->id) }}">
                                @csrf

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>
                                            Фотография
                                            <input type="file" id="croppie-input{{ $user->id }}" name="photo">

                                            <div style="display: none;" id="croppie-block{{ $user->id }}" class="croppie-block text-center">
                                                <input type="hidden" name="photo_base64" id="croppie-result-base64{{ $user->id }}">
                                                <div class="croppie-demo" data-croppie-id="{{ $user->id }}"></div>
                                                <button type="button" data-croppie-id="{{ $user->id }}" class="btn croppie-save btn-sm btn-success">Сохранить обрезку</button>
                                                <button type="button" data-croppie-id="{{ $user->id }}" class="btn croppie-delete btn-sm btn-danger">Удалить фото</button>
                                            </div>

                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" value="{{ $user->name }}" name="name" placeholder="Ваше имя" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" value="{{ $user->login }}" name="login" placeholder="Login" class="form-control">
                                        <i>(Пользователь авторизуется по введеному Вами логину)</i>
                                    </div>

                                    <div class="form-group">
                                        <input type="email" value="{{ $user->email }}" name="email" placeholder="E-mail" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <div class="field field--password">
                                            <i class="fa fa-eye-slash"></i>
                                            <input data-toggle="password" id="password" type="password" placeholder="Пароль..." class="form-control" name="password"  autocomplete="current-password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" value="{{ $user->eds }}" name="eds" placeholder="ЭЦП" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Часовой пояс <i>(UTC+3)</i></label>
                                        <input type="number" value="{{ $user->timezone }}" name="timezone" placeholder="Часовой пояс" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        @include('admin.users.show_pvs', [
                                            'pv_id' => $user->pv_id
                                        ])
                                    </div>

                                    <div class="form-group">
                                        <label>Роль</label>
                                        <select name="role" required class="form-control">
                                            <option disabled selected value="{{ $user->role }}">{{ \App\Http\Controllers\ProfileController::getUserRole(true, $user->id) }}</option>
                                            <option value="12">Клиент</option>
                                            <option value="1">Контролёр ТС</option>
                                            <option value="2">Медицинский сотрудник</option>
                                            <option value="4">Оператор СДПО</option>
                                            <option value="778">Терминал</option>
                                            <option value="11">Менеджер</option>
                                            <option value="777">Администратор</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Сделать менеджером</label>
                                        <select name="role_manager">
                                            <option
                                                @if($user->role_manager === 1) selected @endif
                                                value="1">Да</option>
                                            <option
                                                @if($user->role_manager === 0) selected @endif
                                                 value="0">Нет</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Заблокирован</label>
                                        <select name="blocked">
                                            <option
                                                @if($user->blocked === 1) selected @endif
                                                value="1">Да</option>
                                            <option
                                                @if($user->blocked === 0) selected @endif
                                                value="0">Нет</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Сохранить</button>
                                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Закрыть</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </td>
            <td class="td-option"><a href="{{ route('adminDeleteUser', $user->id) }}" class="ACTION_DELETE btn btn-danger"><i class="fa fa-trash"></i></a></td>
        </tr>
    @endforeach
    </tbody>
</table>

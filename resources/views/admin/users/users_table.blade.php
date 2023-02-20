<div class="table-responsive">
    <table id="elements-table" class="table table-striped table-sm">
        <thead>
        <tr>
            @foreach($fields as $field)
                <th>
                <span class="user-select-none"
                      @if ($field->content)
                          data-toggle="tooltip"
                      data-html="true"
                      data-trigger="click hover"
                      title="{{ $field->content }}"
                    @endif
                >
                    {{ $field->name }}
                </span>
                    <a href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey={{ $field->field }}">
                        <i class="fa fa-sort"></i>
                    </a>
                </th>
            @endforeach

            @if($permissionEdit)
                <th>#</th>
            @endif

            @if($permissionDelete)
                <th>#</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach ($users as $user)
            <tr>
                @foreach($fields as $field)
                    <td>
                        @if($field->field === 'pv_id')
                            {{ \App\Point::getPointText($user->pv_id) }}
                        @elseif($field->field === 'company_id')
                            {{ $user->company['name'] ?? '' }}
                        @elseif($field->field === 'blocked')
                            {{ $user->blocked ? 'Да' : 'Нет' }}
                        @elseif($field->field === 'roles')
                            @foreach($user->roles as $role)
                                <h2>
                            <span class="badge badge-success">
                                {{ $role['guard_name'] }}
                            </span>
                                </h2>
                            @endforeach
                        @else
                            {{ $user[$field->field] }}
                        @endif
                    </td>
                @endforeach

                @if($permissionEdit)
                    <td class="td-option">
                        <a href="" data-toggle="modal" data-target="#users-modal-edit-{{ $user->id }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>

                        <!-- Редактирование элемента -->
                        <div id="users-modal-edit-{{ $user->id }}" role="dialog" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Редактирование сотрудника "{{ $user->name }}"</h4>
                                        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                                    </div>

                                    <form method="POST" enctype="multipart/form-data" action="{{ route('adminUpdateUser', $user->id) }}">
                                        @csrf

                                        <div class="modal-body">

                                            @if(!$is_pak)
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
                                            @endif

                                            @if(!$is_pak)
                                                <div class="form-group">
                                                    <input type="text" value="{{ $user->name }}" name="name" placeholder="Ваше ФИО" class="form-control">
                                                </div>
                                            @else
                                                <input type="hidden" name="name" value="{{ $user->name }}">
                                            @endif

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

                                            @if(!$is_pak)
                                                <div class="form-group">
                                                    <input type="text" value="{{ $user->eds }}" name="eds" placeholder="ЭЦП" class="form-control">
                                                </div>
                                            @endif

                                            <div class="form-group">
                                                <label>Часовой пояс <i>(UTC+3)</i></label>
                                                <input type="number" value="{{ $user->timezone }}" name="timezone" placeholder="Часовой пояс" class="form-control">
                                            </div>

                                            <div class="form-group users_show_pvs"  @if( user()->hasRole('driver') ) style="display: none" @endif>
                                                @include('admin.users.show_pvs', [
                                                    'pv_id' => $user->pv_id
                                                ])
                                            </div>


                                            <div class="form-group users_show_company"  @if( $user->role != 12 ) style="display: none" @endif>
                                                <label>Компания</label>
                                                @php
                                                    $iController = new \App\Http\Controllers\IndexController();

                                                    $company_fields = $iController->elements['Driver']['fields']['company_id'];
                                                    $company_fields['getFieldKey'] = 'id';
                                                    //dd($company_fields)
                                                @endphp
                                                @include('templates.elements_field', [
                                                    'v' => $company_fields,
                                                    'k' => 'company_id',
                                                    'is_required' => '',
                                                    'model' => 'Company',
                                                    'default_value' => $user->company['id'] ?? null
                                                ])
                                            </div>

                                            @if(!$is_pak)
                                                <div class="form-group">
                                                    <label>Роль</label>
                                                    <select name="role" required class="form-control">
                                                        @foreach(\App\User::$userRolesText as $roleKey =>  $roleName)
                                                            <option value="{{$roleKey}}" @if($roleKey == $user->role) selected @endif>{{$roleName}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <input type="hidden" value="778" name="role">
                                            @endif

                                            @if(!$is_pak)
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
                                            @endif

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

                @endif
                @if($permissionDelete)
                    <td class="td-option">
                        <a href="{{ route('adminDeleteUser', $user->id) }}" class="ACTION_DELETE btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@section('custom-scripts')
    <script>
        $(document).ready(function () {
            $(document).on('change', 'select[name="role"]', function(event) {
                const field = $(event.target);
                let selected = field.val()

                if(selected == 12){
                    field.closest('.modal-body').find('.users_show_company').show()
                    field.closest('.modal-body').find('.users_show_pvs').hide()
                    field.closest('.modal-body').find('select[name="pv_id"]').val(0)

                }else{
                    field.closest('.modal-body').find('.users_show_pvs').show()
                    field.closest('.modal-body').find('.users_show_company').hide()
                    field.closest('.modal-body').find('select[name="company_id"]').val(0)
                }
            });
        })
    </script>
@endsection

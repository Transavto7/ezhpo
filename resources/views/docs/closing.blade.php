@extends('layouts.app')
@section('class-page', 'page-protokol')

@section('content')
    <div id="DOC">
        <form class="container Doc" id="DOC_FORM" data-protokol="{{ $protokol_path }}">
            <input type="hidden" name="id" value="{{ $id }}">
            <input type="hidden" name="field" value="closing">

            <table class="protokol">
                <tr>
                    <th class="head" colspan="4">
                        Заключение по результатам прохождения предсменного, предрейсового и послесменного, послерейсового медицинского осмотра
                    </th>
                </tr>
                <tr>
                    <td class="head">Дата</td>
                    <td class="head">Время</td>
                    <td class="head" colspan="2">Пункт осмотра</td>
                </tr>
                <tr>
                    <td rowspan="2">
                        <textarea rows="1" class="doc-input" name="date_str">от {{ date('d.m.Y', strtotime($date)) }} года</textarea>
                    </td>
                    <td rowspan="2">
                        <textarea rows="1" class="doc-input" name="time">{{ date('Hч iмин', strtotime($date)) }}</textarea>
                    </td>

                    <td class="head" style="width: 50px">Город</td>
                    <td class="head">Адрес</td>
                </tr>
                <tr>
                    <td>
                        <textarea rows="1" class="doc-input" name="town">{{ $town }}</textarea>
                    </td>
                    <td>
                        <textarea rows="1" class="doc-input large" name="point">{{ $point }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="head" colspan="3">ФИО</td>
                    <td class="head">Год рождения</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <textarea rows="1" class="doc-input" name="driver_fio">{{ $driver_fio }}</textarea>
                    </td>
                    <td>
                        <textarea rows="1" class="doc-input" name="driver_year_birthday">{{ $driver_year_birthday }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="head" colspan="3">Место работы</td>
                    <td class="head">Должность</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <textarea rows="1" class="doc-input" name="company_name">{{ $company_name }}</textarea>
                    </td>
                    <td>
                        <textarea rows="1" class="doc-input" name="post">Водитель</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="head">Жалобы</td>
                    <td class="head">Артериальное давление</td>
                    <td class="head">Пульс</td>
                    <td class="head">Температура</td>
                </tr>
                <tr>
                    <td>
                        <textarea rows="1" class="doc-input" name="status">{{ $status }}</textarea>
                    </td>
                    <td>
                        <textarea rows="1" class="doc-input" name="tonometer">{{ $tonometer }}</textarea>
                    </td>
                    <td>
                        <textarea rows="1" class="doc-input" name="pulse">{{ $pulse }}</textarea>
                    </td>
                    <td>
                        <textarea rows="1" class="doc-input" name="t_people">{{ $t_people }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        Проба на алкоголь
                    </td>
                    <td colspan="3">
                        <textarea rows="1" name="alko" class="doc-input">{{ $alcometer_result }} мг\л</textarea>
                    </td>
                </tr>
                <tr>
                    <td>Медицинское заключение</td>
                    <td colspan="3">
                        <textarea rows="5" name="closing" class="doc-input">{{ $closing }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>Рекомендации</td>
                    <td colspan="3">
                        <textarea rows="1" name="recommendations" class="doc-input">{{ $recommendations }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        ФИО медицинского работника
                    </td>
                    <td colspan="3">
                        <textarea rows="1" name="user_name" class="doc-input">{{ $user_name }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>Медицинский работник</td>
                    <td colspan="3">
                        <textarea rows="2" name="user_eds" class="doc-input">{{ $user_eds }}</textarea>
                    </td>
                </tr>
                @if($comment_rows)
                <tr>
                    <td colspan="4" style="border:0;">
                        <textarea rows="{{ $comment_rows }}" name="comment" class="doc-input">{{ $comment }}</textarea>
                    </td>
                </tr>
                @endif
            </table>
            <div class="mt-3">
                @if($closing_path)
                    <h3 class="no-print text-center text-success">Данные подгружены из ранее созданного мед. заключения</h3>
                    <div class="d-flex mt-2" style="gap: 10px;">
                        <a target="_blank" href="{{ route('docs.get.pdf', ['type' => 'closing', 'anketa_id' => $id]) }}" class="btn btn-info w-100">Открыть</a>
                        <a href="{{ route('docs.delete', ['type' => 'closing', 'anketa_id' => $id]) }}" class="btn btn-danger w-100">Удалить</a>
                    </div>
                @else
                    <h3 class="no-print text-center text-danger">Мед. заключение ранее не сохранялось</h3>
                    <button type="submit" class="no-print btn btn-success w-100">Сохранить</button>
                @endif
            </div>
        </form>
    </div>
@endsection

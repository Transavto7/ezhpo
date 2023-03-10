@extends('layouts.app')
@section('class-page', 'page-protokol')

@section('content')
    @if($protokol_path)
        <h3 class="no-print text-center text-success">Данные подгружены из ранее созданного протокола</h3>
    @else
        <h3 class="no-print text-center text-danger">Протокол ранее не сохранен</h3>
    @endif

    <div id="DOC">
        <form class="container Doc" id="DOC_FORM" data-protokol="{{ $protokol_path }}">
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
                    <td>
                        от {{ date('d.m.Y', strtotime($date)) }} года
                    </td>
                    <td>
                        {{ date('Hч iмин', strtotime($date)) }}
                    </td>

                    <td class="head">Город</td>
                    <td class="head">Адрес</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>{{ $town }}</td>
                    <td>{{ $pv_id }}</td>
                </tr>
                <tr>
                    <td class="head" colspan="3">ФИО</td>
                    <td class="head">Год рождения</td>
                </tr>
                <tr>
                    <td colspan="3">{{ $driver_fio }}</td>
                    <td>{{ $driver_year_birthday }}</td>
                </tr>
                <tr>
                    <td class="head" colspan="3">Место работы</td>
                    <td class="head">Должность</td>
                </tr>
                <tr>
                    <td colspan="3">{{ app('app\Company')->getName($company_id, 'hash_id') }}</td>
                    <td>
                        <textarea rows="1" class="doc-input">Водитель</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="head">Результат осмотра</td>
                    <td colspan="3">
                        <textarea rows="1" class="doc-input">{{$conclusionStr}}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="head" colspan="4">Контроль трезвости</td>
                </tr>
                <tr>
                    <td>Проба на алкоголь</td>
                    <td colspan="3">
                        @if($alko)
                            <textarea rows="1" class="doc-input">ПОЛОЖИТЕЛЬНА   _____  МГ/Л</textarea>
                        @else
                            <textarea rows="1" class="doc-input">ОТРИЦАТЕЛЬНА</textarea>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Проба на наркотические средства и психотропные вещества</td>
                    <td colspan="3">
                        @if($drugs)
                            <textarea rows="4" class="doc-input center">ПОЛОЖИТЕЛЬНА

результат иммунохроматографического экспресс теста на наличие наркотического средства/психотропного вещества</textarea>
                        @else
                            <textarea rows="1" class="doc-input">ОТРИЦАТЕЛЬНА</textarea>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Заключение</td>
                    <td colspan="3">
                        <textarea rows="4" class="doc-input">наличие признаков воздействия вредных и (или) опасных производственных факторов, состояний и заболеваний, препятствующих выполнению трудовых обязанностей, в том числе алкогольного, наркотического или иного токсического опьянения и остаточных явлений такого опьянения</textarea>
                    </td>
                </tr>
                <tr>
                    <td>Примечание</td>
                    <td colspan="3">
                        <textarea rows="2" class="doc-input"></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <textarea rows="2" class="doc-input"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>Медицинский работник</td>
                    <td colspan="4">{{ $user_eds }}</td>
                </tr>
            </table>
            <button type="submit" class="no-print btn btn-success">Сохранить</button>
        </form>
    </div>
@endsection

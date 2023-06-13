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
                    <td>
                        <textarea rows="1" class="doc-input" name="date">от {{ date('d.m.Y', strtotime($date)) }} года</textarea>
                    </td>
                    <td>
                        <textarea rows="1" class="doc-input" name="time">{{ date('Hч iмин', strtotime($date)) }}</textarea>

                    </td>

                    <td class="head" style="width: 50px">Город</td>
                    <td class="head">Адрес</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <textarea rows="1" class="doc-input" name="town">{{ $town }}</textarea>
                    </td>
                    <td>
                        <textarea rows="1" class="doc-input large" name="pv_id">{{ $pv_id }}</textarea>
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
                        <textarea rows="1" class="doc-input" name="company_id">{{ app('app\Company')->getName($company_id, 'hash_id') }}</textarea>
                    </td>
                    <td>
                        <textarea rows="1" class="doc-input" name="post">Водитель</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="head">Результат осмотра</td>
                    <td colspan="3">
                        @php
                            $result = 'Наличие признаков заболевания';

                            $tonometer = explode('/', $tonometer);
                            $pressure_systolic = $driver->getPressureSystolic();
                            $pressure_diastolic = $driver->getPressureDiastolic();

                            if ($t_people < 38 && $med_view === 'В норме' && $driver
                                && $tonometer[0] < $pressure_systolic && $tonometer[1] < $pressure_diastolic) {
                                $result = 'Здоров';
                            } else if ($proba_alko === 'Положительно') {
                                $result = 'Здоров';
                            }
                        @endphp
                        <textarea rows="1" class="doc-input" name="result">{{ $result }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="head" colspan="4">Контроль трезвости</td>
                </tr>
                <tr>
                    <td>Проба на алкоголь</td>
                    <td colspan="3">
                        @if($alko)
                            <textarea rows="1" class="doc-input" name="alco">ПОЛОЖИТЕЛЬНА   _____  МГ/Л</textarea>
                        @else
                            <textarea rows="1" class="doc-input" name="alco">ОТРИЦАТЕЛЬНА</textarea>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Проба на наркотические средства и психотропные вещества</td>
                    <td colspan="3">
                        @if($drugs)
                            <textarea rows="4" class="doc-input center" name="drugs">ПОЛОЖИТЕЛЬНА

результат иммунохроматографического экспресс теста на наличие наркотического средства/психотропного вещества</textarea>
                        @else
                            <textarea rows="1" class="doc-input" name="drugs">НЕ ПРОВОДИЛАСЬ</textarea>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Заключение</td>
                    <td colspan="3">
                        <textarea rows="4" class="doc-input" name="closing">наличие признаков воздействия вредных и (или) опасных производственных факторов, состояний и заболеваний, препятствующих выполнению трудовых обязанностей, в том числе алкогольного, наркотического или иного токсического опьянения и остаточных явлений такого опьянения</textarea>
                    </td>
                </tr>
                <tr>
                    <td>Примечание</td>
                    <td colspan="3">
                        <textarea rows="2" class="doc-input" name="note"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>Сопроводительные документы</td>
                    <td colspan="3" style="height: 0;">
                        <select rows="2" class="doc-input" name="docs">
                            <option value="0" selected>
                                Отсутствуют
                            </option>
                            <option value="1">
                                Выдана справка для обращения к врачу в медицинскую организацию для решения вопроса о
                                наличии у работника признаков временной нетрудоспособности
                            </option>
                            <option value="2">
                                Составлен протокол контроля трезвости
                            </option>
                            <option value="3">
                                На основании клинических признаков опьянения направлен на медицинского освидетельствование
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Медицинский работник</td>
                    <td colspan="3"><textarea rows="1" name="user_eds" class="doc-input">{{ $user_eds }}</textarea></td>
                </tr>
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

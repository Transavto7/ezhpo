@extends('layouts.app')
@section('class-page', 'page-protokol')

@section('content')
    <div id="DOC">
        <form class="container Doc" id="DOC_FORM" data-protokol="{{ $protokol_path }}">
            <div class="text-center">
                <h2>ПРОТОКОЛ<br/>
                    КОНТРОЛЯ ТРЕЗВОСТИ ВОДИТЕЛЯ АВТОТРАНСПОРТНОГО СРЕДСТВА</h2>
                <h3>
                    № <input style="min-width: 50px;width: 100px;" type="number" name="doc_id" value="{{ $id }}" class="doc-input doc-input--row">
                    от {{ date('d.m.Y', strtotime($date)) }} года
                </h3>
            </div>

            <input type="hidden" name="id" value="{{ $id }}">

            <p>1. Фамилия, имя, отчество, год рождения, место работы, должность:</p>

            <textarea rows="1" class="doc-input" name="driver_info">{{ $driver_fio ? $driver_fio . ',' : '' }} {{ $driver_year_birthday }} г.р., {{ $driver_pv }}, {{ app('app\Company')->getName($company_id, 'hash_id') }}</textarea>
            <br>
            кем и когда (точное время) произведен контроль трезвости
            <textarea rows="1" readonly class="doc-input" name="control_info">{{ $user_post ? $user_post . ',' : '' }} {{ $user_name }}, {{ $date }}</textarea>

            <p>2. Особенности поведения обследуемого:</p>
            <textarea rows="1" class="doc-input open-modal" name="features"></textarea>

            <p>3. Жалобы:</p>
            <textarea rows="1" name="complaints" class="doc-input open-modal">{{ $complaint }}</textarea>

            <p>4. Кожные покровы:</p>
            а) окраска: <textarea rows="1" name="coloring" class="doc-input doc-input--row open-modal"></textarea>
            <br/>
            б) наличие повреждений, расчесов, следов от инъекций , "дорожек" по ходу поверхности вен:
            <textarea rows="1" name="damage" class="doc-input open-modal">{{ $condition_koj_pokr }}</textarea>

            <p>5. Состояние слизистых глаз:</p>
            <textarea rows="1" name="mucous" class="doc-input open-modal">{{ $condition_visible_sliz }}</textarea>

            <p>6. Частота дыхательных движений:</p>
            <textarea name="respiratory" rows="1" class="doc-input">__ движ/мин, пульс: {{ $pulse }} уд/мин, артериальное давление: {{ $tonometer }} мм.рт.ст.</textarea>

            <p>7. Особенности походки:	</p>
            <textarea rows="1" name="gait" class="doc-input open-modal"></textarea>

            <p>точность движения:
                <textarea name="accuracy" rows="1" class="doc-input open-modal"></textarea></p>
            <p>тремор пальцев рук:
                <textarea name="tremor_fingers" rows="1" class="doc-input open-modal"></textarea></p>
            <p>тремор век:
                <textarea name="tremor_eyelid" rows="1" class="doc-input open-modal"></textarea></p>

            <p>8.Наличие запаха алкоголя или другого вещества изо рта:	</p>
            <textarea name="smell" rows="1" class="doc-input open-modal"></textarea>

            <p>9. Данные лабораторного исследования:</p>
            <p>а) на алкоголь</p>
            <p>- выдыхаемый воздух (алкометр)</p>
            <textarea name="alcometer" rows="1" class="doc-input doc-input--row open-modal">DRIVESAFE II</textarea>
            <p>время проведения контроля трезвости: <textarea name="time" rows="1" class="doc-input doc-input--row open-modal">{{ \Carbon\Carbon::parse($date)->format('H:i') }}</textarea></p>
            <p>результат: <textarea name="result" rows="1" class="doc-input doc-input--row open-modal">0,00 мг/л</textarea></p>

            <p>б) на наркотические средства</p>
            <p>- экспресс-тесты мочи</p>
            <textarea name="urine_test" rows="1" class="doc-input open-modal"></textarea>

            <p>10. Предварительное заключение:</p>
            <textarea name="closing" rows="1" class="doc-input open-modal"></textarea>

            <p>11. Запись тестируемого об ознакомлении с результатами тестирования:</p>
            <textarea name="record" rows="1" class="doc-input open-modal"></textarea>

            <br>

            <p style="margin-top: 0px; text-align: center;"><i>(с результатами ознакомлен, согласен)</i></p>

            <table style="width: 100%;">
                <tbody>
                <tr>
                    <td class="text-center">
                        _______________________________<br/>
                        <i>(дата)</i>
                    </td>
                    <td class="text-center">
                        _______________________________<br/>
                        <i>(время)</i>
                    </td>
                    <td class="text-center">
                        _______________________________<br/>
                        <i>(подпись обследуемого)</i>
                    </td>
                </tr>
                </tbody>
            </table>

            <p>12. Примечания</p>
            <textarea name="notice" class="doc-input open-modal">особых отметок нет.</textarea>
            <br><br>
            <table>
                <tbody>
                    <tr>
                        <td>
                            Подпись медицинского работника &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td class="text-center">
                            ________________ /<br/>
                            <i>(подпись)</i>
                        </td>
                        <td class="text-center">
                            ____________________________________ /<br/>
                            <i>(расшифровка подписи медработника)</i>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br/>
                            <br/>
                            _________________________<br/>
                            М.П.
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-3">
                @if($protokol_path)
                    <h3 class="no-print text-center text-success">Данные подгружены из ранее созданного протокола</h3>
                    <div class="d-flex mt-2" style="gap: 10px;">
                        <a target="_blank" href="{{ route('docs.get.pdf', ['type' => 'protokol', 'anketa_id' => $id]) }}" class="btn btn-info w-100">Открыть</a>
                        <a href="{{ route('docs.delete', ['type' => 'protokol', 'anketa_id' => $id]) }}" class="btn btn-danger w-100">Удалить</a>
                    </div>
                @else
                    <h3 class="no-print text-center text-danger">Протокол ранее не сохранен</h3>
                    <button type="submit" class="no-print btn btn-success w-100">Сохранить</button>
                @endif
            </div>
        </form>

        <form action="{{ route('docs.add.pdf', ['type' => 'protokol', 'anketa_id' => $id]) }}" method="POST" class="container Doc" enctype="multipart/form-data">
            @csrf
            Загрузка PDF файла
            <div class="d-flex align-items-center" style="gap: 10px;">
                <div class="custom-file w-100">
                    <input
                        type="file"
                        class="custom-file-input w-100"
                        name="pdf"
                        accept="application/pdf"
                        id="pdf">
                    <label class="custom-file-label w-100" for="pdf">Выберите файл</label>
                </div>

                <button class="btn btn-success" type="submit" style="max-height: 34px">Сохранить</button>
            </div>
        </form>
    </div>
@endsection

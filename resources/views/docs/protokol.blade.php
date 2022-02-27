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
            <div class="text-center">
                <h2>ПРОТОКОЛ<br/>
                    КОНТРОЛЯ ТРЕЗВОСТИ ВОДИТЕЛЯ АВТОТРАНСПОРТНОГО СРЕДСТВА</h2>
                <h3>№ {{ $id }} от {{ date('d.m.Y', strtotime($date)) }} года</h3>
            </div>

            <input type="hidden" name="id" value="{{ $id }}">

            <p>1. Фамилия, имя, отчество, год рождения, место работы, должность:</p>

            <textarea rows="1" class="doc-input">{{ $driver_fio ? $driver_fio . ',' : '' }} {{ $driver_year_birthday }} г.р., {{ $driver_pv }}</textarea>
            <br>
            кем и когда (точное время) произведен контроль трезвости
            <textarea rows="1" readonly class="doc-input">{{ $user_post ? $user_post . ',' : '' }} {{ $user_name }}, {{ $date }}</textarea>

            <p>2. Особенности поведения обследуемого:</p>
            <textarea rows="1" class="doc-input" name="особенности_поведения"></textarea>

            <p>3. Жалобы:</p>
            <textarea rows="1" name="жалобы" class="doc-input">{{ $complaint }}</textarea>

            <p>4. Кожные покровы:</p>
            а) окраска: <textarea rows="1" name="кп_окраска" class="doc-input doc-input--row"></textarea>
            <br/>
            б) наличие повреждений, расчесов, следов от инъекций , "дорожек" по ходу поверхности вен:
            <textarea rows="1" name="кп_наличие_повреждений" class="doc-input">{{ $condition_koj_pokr }}</textarea>

            <p>5. Состояние слизистых глаз:</p>
            <textarea rows="1" name="слизистые" class="doc-input">{{ $condition_visible_sliz }}</textarea>

            <p>6. Частота дыхательных движений: 	</p>
            <textarea name="частота_дых_движений" rows="1" class="doc-input">__ движ/мин, пульс: {{ $pulse }} уд/мин, артериальное давление: {{ $tonometer }} мм.рт.ст.</textarea>

            <p>7. Особенности походки:	</p>
            <textarea rows="1" name="особенности_походки" class="doc-input"></textarea>

            <p>точность движения:
                <textarea name="оп_точность" rows="1" class="doc-input"></textarea></p>
            <p>тремор пальцев рук:
                <textarea name="оп_тремор_пальцев" rows="1" class="doc-input"></textarea></p>
            <p>тремор век:
                <textarea name="оп_тремор_век" rows="1" class="doc-input"></textarea></p>

            <p>8.Наличие запаха алкоголя или другого вещества изо рта:	</p>
            <textarea name="оп_наличие_запаха" rows="1" class="doc-input"></textarea>

            <p>9. Данные лабораторного исследования:</p>
            <p>а) на алкоголь</p>
            <p>- выдыхаемый воздух (алкометр)</p>
            <textarea name="алкометр" rows="1" class="doc-input doc-input--row">DRIVESAFE II</textarea>
            <p>время проведения контроля трезвости: <textarea name="данные_ли_время" rows="1" class="doc-input doc-input--row">{{ date('H:i', time($date)) }}</textarea></p>
            <p>результат: <textarea name="данные_ли_результат" rows="1" class="doc-input doc-input--row">0,00 мг/л</textarea></p>

            <p>б) на наркотические средства</p>
            <p>- экспресс-тесты мочи</p>
            <textarea name="экспресс_тест_мочи" rows="1" class="doc-input"></textarea>

            <p>10. Предварительное заключение:</p>
            <textarea name="предв_закл" rows="1" class="doc-input">{{ $admitted }}</textarea>

            <p>11. Запись тестируемого об ознакомлении с результатами тестирования:</p>
            <textarea name="запись_тестируемого" rows="1" class="doc-input"></textarea>

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
            <textarea name="примечания" class="doc-input">особых отметок нет.</textarea>
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

            <button type="submit" class="no-print btn btn-success">Сохранить</button>
        </form>
    </div>
@endsection

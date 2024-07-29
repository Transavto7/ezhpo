<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Экспорт протокола отстранения</title>
    <style>
        body {
            font-family: "DejaVu Sans";
            font-size: 16px;
        }

        h2, h3 {
            font-size: 12px;
            margin: 0;
        }

        .Doc {
            padding: 25px;
            background: white;
            font-size: 10px;
        }

        b {
            font-weight: normal;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
<div id="DOC">
        <div class="container Doc" id="DOC_FORM">
            <div class="text-center">
                <h2>ПРОТОКОЛ<br/>
                    КОНТРОЛЯ ТРЕЗВОСТИ ВОДИТЕЛЯ АВТОТРАНСПОРТНОГО СРЕДСТВА</h2>
                <h3>№ {{ $id }} от {{ date('d.m.Y', strtotime($date)) }} года</h3>
            </div>
            <br>
            1. Фамилия, имя, отчество, год рождения, должность:<br>
            <b>{{ $driver_fio ? $driver_fio . ',' : '' }} {{ $driver_year_birthday }} г.р., {{ app('app\Company')->getName($company_id, 'hash_id') }}</b>
            <br>

            кем, когда (точное время) и где произведен контроль трезвости<br>
            <b>{{ $user_post ? $user_post . ',' : '' }} {{ $user_name }}, {{ $date }}, {{ $point }}</b>
            <br><br>

            2. Особенности поведения обследуемого:<br>
            <b>{{ $features ?? '-' }}</b>
            <br><br>

            3. Жалобы:<br>
            <b>{{ $complaint }}</b>
            <br><br>

            4. Кожные покровы:<br>
            а) окраска: <b>{{ $coloring ?? '-' }}</b><br>
            б) наличие повреждений, расчесов, следов от инъекций , "дорожек" по ходу поверхности вен:
            <b>{{ $condition_koj_pokr }}</b>
            <br><br>
            5. Состояние слизистых глаз:<br>
            <b>{{ $condition_visible_sliz }}</b>
            <br><br>

            6. Частота дыхательных движений:<br>
            <b>__ движ/мин, пульс: {{ $pulse }} уд/мин, артериальное давление: {{ $tonometer }} мм.рт.ст.</b>
            <br><br>

            7. Особенности походки:<br>
            <b>{{ $gait ?? '-' }}</b>
            <br><br>

            точность движения: <b>{{ $accuracy ?? '-' }}</b><br>
            тремор пальцев рук: <b>{{ $tremor_fingers ?? '-' }}</b><br>
            тремор век: <b>{{ $tremor_eyelid ?? '-' }}</b>
            <br><br>
            8.Наличие запаха алкоголя или другого вещества изо рта:<br>
            <b>{{ $smell ?? '-' }}</b>
            <br><br>

            9. Данные лабораторного исследования:<br>
            а) на алкоголь<br>
            - выдыхаемый воздух (алкометр): <b>DRIVESAFE II</b>
            <br>
            - время проведения контроля трезвости: <b>{{ \Carbon\Carbon::parse($date)->format('H:i') }}</textarea></b>
            <br>
            - результат: <b>{{ $alcometer_result }} мг\л<</b>
            <br><br>

            б) на наркотические средства<br>
            - экспресс-тесты мочи: <b>{{ $urine_test ?? '-' }}</b>
            <br><br>

            10. Предварительное заключение:<br>
            <b>{{ $admitted }}</b>
            <br><br>

            11. Запись тестируемого об ознакомлении с результатами тестирования:<br>
            <b>{{ $record ?? '-' }}</b>
            <br><br>

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
            <br>
            12. Примечания<br>
            <b>{{ $notice ?? '-' }}</b>
            <br>
            <table>
                <tbody>
                    <tr>
                        <td>
                            Подпись медицинского работника
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
        </div>
    </div>
</body>
</html>

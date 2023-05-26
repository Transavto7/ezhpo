<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Экспорт мед. заключения</title>
    <style>
        body {
            font-family: "DejaVu Sans";
            font-size: 16px;
        }

        .Doc {
            padding: 25px;
            background: white;
            font-size: 12px;
        }

        table, th, td {
            border-collapse: collapse;
            border: 1px solid;
        }

        td {
            padding: 5px;
        }

        td.head, th.head {
            font-weight: bold;
            background: #e1e1e1;
        }
    </style>
</head>
<body>
<div class="Doc">
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
            <td>
                {{ $town }}
            </td>
            <td>
                {{ $pv_id }}
            </td>
        </tr>
        <tr>
            <td class="head" colspan="3">ФИО</td>
            <td class="head">Год рождения</td>
        </tr>
        <tr>
            <td colspan="3">
                {{ $driver_fio }}
            </td>
            <td>
                {{ $driver_year_birthday }}
            </td>
        </tr>
        <tr>
            <td class="head" colspan="3">Место работы</td>
            <td class="head">Должность</td>
        </tr>
        <tr>
            <td colspan="3">{{ app('app\Company')->getName($company_id, 'hash_id') }}</td>
            <td>
                Водитель
            </td>
        </tr>
        <tr>
            <td class="head">Результат осмотра</td>
            <td colspan="3">
                Здоров
            </td>
        </tr>
        <tr>
            <td class="head" colspan="4">Контроль трезвости</td>
        </tr>
        <tr>
            <td>Проба на алкоголь</td>
            <td colspan="3">
                @if($alko)
                    ПОЛОЖИТЕЛЬНА   _____  МГ/Л
                @else
                    ОТРИЦАТЕЛЬНА
                @endif
            </td>
        </tr>
        <tr>
            <td>Проба на наркотические средства и психотропные вещества</td>
            <td colspan="3">
                @if($drugs)
                    ПОЛОЖИТЕЛЬНА<br><br>
                    результат иммунохроматографического экспресс теста на наличие наркотического средства/психотропного вещества
                @else
                    НЕ ПРОВОДИЛАСЬ
                @endif
            </td>
        </tr>
        <tr>
            <td>Заключение</td>
            <td colspan="3">
                наличие признаков воздействия вредных и (или) опасных производственных факторов, состояний и заболеваний,
                препятствующих выполнению трудовых обязанностей, в том числе алкогольного, наркотического или иного токсического опьянения и
                остаточных явлений такого опьянения
            </td>
        </tr>
        <tr>
            <td>Примечание</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="2">
                Выдана справка для обращения к врачу в медицинскую организацию для решения вопроса
                о наличии у работника признаков временной нетрудоспособности
            </td>
            <td>
                Составлен протокол контроля трезвости.
            </td>
            <td>
                На основании клинических признаков опьянения направлен на медицинского освидетельствование
            </td>
        </tr>
        <tr>
            <td>Медицинский работник</td>
            <td colspan="3">{{ $user_eds }}</td>
        </tr>
    </table>
</div>
</body>
</html>

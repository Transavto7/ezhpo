<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наклейка осмотра {{ $anketa->id }}</title>
    <style>
        @font-face {
            font-family: 'Arial';
            src: url({{ storage_path('fonts//ArialMT.eot') }}) format('embedded-opentype'),
            url({{ storage_path('fonts/ArialMT.woff') }}) format('woff'),
            url({{ storage_path('fonts/ArialMT.ttf') }}) format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page { margin: 0; }

        body {
            font-family: "Arial";
            margin: 0;
        }

        h2, h3 {
            font-size: 12px;
            margin: 0;
        }

        b {
            font-weight: normal;
        }

        .print {
            text-align: center;
            font-size: 7.7px;
            width: 215px;
            padding-top: 5.5px;
            line-height: 6px;
        }

        .title {
            font-size: 10.5px;
            padding: 0;
            margin: 0;
            display: inline;
        }

        .licence {
            font-size: 5.1px;
            margin-bottom: 1.5px;
            display: inline-block;
        }

        .validity {
            font-size: 7px;
            line-height: 5px;
            display: inline-block;
        }

        .name {
            display: inline-block;
        }

    </style>
</head>
<body>

    <div class="print">
        <span class="title">{{ $stamp ? $stamp->company_name : 'ООО "Трансавто-7"' }}</span><br>
        <span class="licence">
            {{ $stamp ? $stamp->licence : 'Бессрочная лицензия от 09.12.2020 № Л041-1177-91/00366739' }}
        </span><br>
        <span class="name">{{ $anketa->driver_fio }}</span><br>
        прошел {{ $anketa->type_view }}<br>
        Медицинский осмотр<br>
        @if(Str::contains(Str::lower($anketa->type_view), 'пред'))
            к исполнению трудовых обязаностей<br>
            допущен
        @endif

        <br><br>
        {{ $anketa->date ?? '0000-00-00 00:00:00' }}<br>
        {{ $anketa->user_name ?? 'неизвестный сотрудник' }}<br>
        ЭЦП {{ $anketa->user_eds ?? 'неизвестная-подпись' }}<br>
        @if ($user->validity_eds_start && $user->validity_eds_end)
            <span class="validity">
                Срок действия: c
                {{ \Carbon\Carbon::parse($user->validity_eds_start)->format('d.m.Y') }}
                по
                {{ \Carbon\Carbon::parse($user->validity_eds_end)->format('d.m.Y') }}
            </span>
        @endif
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 8px 4px 4px 8px;
        }
        @page { margin: 8px 4px 4px 8px; }
        .content-block {
            margin: 0 auto;
            font-weight: bold;
            text-align: center;
        }
        .title {
            font-size: @if($type === 'D') 18pt @else 26pt @endif;
            line-height: 1.1;
            margin-bottom: 5px;
        }
        .domain {
            font-size: 18pt;
            line-height: 1.2;
            margin: 5px auto 0;
        }
        .id-type {
            font-size: 26pt;
            line-height: 1.1;
            margin: 2px auto 0;
        }
    </style>
    <title></title>
</head>
<body>
<div>
    <div class="content-block title">{{ $title }}</div>
    <img src="{{ $qrCode }}" alt="QR Code" style="display: block; margin: 0 auto;"/>
    <div class="content-block domain">{{ $domain }}</div>
    <div class="content-block id-type">{{ $id.' - '.$type }}</div>
</div>
</body>
</html>

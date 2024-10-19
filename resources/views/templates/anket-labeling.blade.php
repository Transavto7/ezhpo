<style>
    @page {
        margin: 8px;
    }

    body {
        margin: 8px;
    }

    .page-break {
        page-break-after: always;
    }

    .wrapper {
        background-color: #fff;
        position: relative;
        width: 100%;
        height: 100%;
    }

    /** T7 **/
    .logo {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        height: 95px;
        width: 165px;
    }

    .arrows {
        position: absolute;
        top: 170px;
        right: 185px;
        transform: translateX(-50%);
        height: 60px;
        width: auto;
    }

    .intro {
        position: absolute;
        left: 20px;
        top: 115px;
        font-size: 32px;
        text-transform: uppercase;
        font-family: "DejaVu Sans", sans-serif;
        line-height: 1;
    }

    .qr {
        position: absolute;
        top: 115px;
        right: 20px;
        width: 170px;
        height: 170px;
    }

    .domain {
        position: absolute;
        top: 235px;
        left: 20px;
        width: 145px;
        height: 145px;
        font-family: "DejaVu Sans", sans-serif;
        font-size: 18px;
    }

    .id {
        position: absolute;
        top: 260px;
        left: 20px;
        font-family: "DejaVu Sans", sans-serif;
        font-size: 18px;
    }
</style>

@foreach($pages as $page)
    <div class="page">
        <div class="wrapper">
            <div class="logo">
                <img src="{{ $logoImage }}" width="100%" height="100%" alt=""/>
            </div>
            <div class="arrows">
                <img src="{{ $arrowsImage }}" width="100%" height="100%" alt=""/>
            </div>
            <div class="intro">
                Проверь<br />
                подлинность<br />
                путевого листа<br />
            </div>
            <div class="qr">
                <img src="{{ $page['qrCode'] }}" width="100%" height="100%" alt="QR Code"/>
            </div>
            <div class="domain">
                {{ $domain }}
            </div>
            <div class="id">
                {{ $page['id'] }}
            </div>
        </div>
    </div>
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach


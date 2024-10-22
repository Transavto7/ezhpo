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
        left: 20px;
        height: 70px;
        width: 160px;
    }

    .intro {
        position: absolute;
        left: 20px;
        top: 100px;
        font-size: 36px;
        text-transform: uppercase;
        font-family: "DejaVu Sans", sans-serif;
        line-height: 0.9;
    }

    .qr {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 215px;
        height: 215px;
    }

    .domain {
        position: absolute;
        bottom: 20px;
        left: 20px;
        width: 335px;
        max-width: 335px;
        font-family: "DejaVu Sans", sans-serif;
        font-size: 30px;
        line-height: 0.8;
        word-wrap: break-word;
    }

    .id {
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 215px;
        text-align: center;
        font-family: "DejaVu Sans", sans-serif;
        font-size: 30px;
        line-height: 0.8;
        font-weight: 600;
    }
</style>

@foreach($pages as $page)
    <div class="page">
        <div class="wrapper">
            <div class="logo">
                <img src="{{ $logoImage }}" width="100%" height="100%" alt=""/>
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


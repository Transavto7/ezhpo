<style>
    @page {
        margin: 4px;
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
    .id {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        text-align: center;
        font-family: "DejaVu Sans", sans-serif;
        font-size: 31px;
        line-height: 1;
        font-weight: 600;
    }

    .qr {
        position: absolute;
        top: 46px;
        left: 0;
        width: 199px;
        height: 215px;
    }

    .intro {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        font-size: 16px;
        text-align: center;
        font-weight: 700;
        text-transform: uppercase;
        font-family: "DejaVu Sans", sans-serif;
        line-height: 1;
    }
</style>

@foreach($pages as $page)
    <div class="page">
        <div class="wrapper">
            <div class="id">
                {{ $page['id'] }}
            </div>
            <div class="qr">
                <img src="{{ $page['qrCode'] }}" width="100%" height="100%" alt="QR Code"/>
            </div>
            <div class="intro">
                контроль осмотра
            </div>
        </div>
    </div>
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach


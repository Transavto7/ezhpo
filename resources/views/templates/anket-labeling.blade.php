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
        position: relative;
        width: 100%;
        height: 100%;
    }

    .bg {
        z-index: 0;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .qr {
        z-index: 1;
        position: absolute;
        bottom: 70px;
        right: 70px;
        width: 180px;
        height: 180px;
    }
</style>

@foreach($pages as $page)
    <div class="page">
        <div class="wrapper">
            <div class="bg">
                <img src="{{ $page['imageBg'] }}" width="100%" height="100%" alt=""/>
            </div>
            <div class="qr">
                <img src="{{ $page['qrCode'] }}" width="100%" height="100%" alt="QR Code"/>
            </div>
        </div>
    </div>
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach


<style>
    @page { margin: 8px; }
    body { margin: 8px; }
</style>
<div>
    <img src="{{ $qrCode }}" alt="QR Code" style="text-align:center;"/>
    <p style="margin: 10px auto 0; font-weight: bold; font-size: 18pt; text-align:center;">{{ $domain }}</p>
    <p style="margin: 0 auto 0; font-weight: bold; font-size: 26pt; text-align:center;">{{ $id.' | '.$type }}</p>
</div>

@extends('pages.anketas.base')

@section('title', 'Путевой лист не найден')

@push('custom_styles')
    <style>
        .page-content {
            height: 100vh;
        }

        .status-icon {
            font-size: 70px;
        }

        .status-icon-wrong {
            color: #a70912;
        }

        .status-title {
            margin-top: 10px;
            font-size: 18px;
        }

        .status-phone {
            display: inline-block;
            text-align: center;
            margin-top: 15px;
            font-size: 23px;
            color: inherit!important;
        }
    </style>
@endpush

@section('content')
    <main class="page-content d-flex align-items-stretch">
        <div class="container text-center">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-12">
                    <div class="flex justify-content-center align-items-center">
                        <div>
                            <div>
                                <i class="fa fa-times-circle status-icon status-icon-wrong" aria-hidden="true"></i>
                            </div>
                            <div class="status-title">Путевой лист не найден</div>
                            <a id="phone" href="" class="d-none status-phone"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('custom_scripts')
    <script>
        const phoneNumber = '{{ config('form_verification.phone') }}'
        const phoneLink = document.getElementById('phone')

        if (phoneNumber && phoneLink) {
            let hrefAttr = phoneNumber
                .replaceAll('(', '')
                .replaceAll(')', '')
                .replaceAll('-', '')
                .replaceAll(' ', '')

            hrefAttr = 'tel:+7' + hrefAttr.slice(1)

            phoneLink.innerHTML = phoneNumber
            phoneLink.href = hrefAttr
            phoneLink.classList.remove('d-none')
        }
    </script>
@endpush

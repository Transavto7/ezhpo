<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="type_anketa" value="{{ $type_anketa }}"/>

        @include('profile.ankets.components.camera')

        @include('profile.ankets.components.pvs')

        @include('profile.ankets.components.is_dop')

        @if($is_dop)
            @include('profile.ankets.tech.is_dop')
        @else
            @include('profile.ankets.tech.base')
        @endif
    </div>
</div>

@section('custom-styles')
    <style>
        .camera-btn {
            line-height: 0.5 !important;
            border-radius: 0 0.25rem 0.25rem 0;
        }
        #html5-qrcode-button-camera-stop {
            visibility: hidden;
            display: none;
        }
    </style>
@endsection


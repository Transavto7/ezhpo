<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="type_anketa" value="{{ $type_anketa }}"/>

        @include('profile.ankets.components.pvs')

        @include('profile.ankets.components.is_dop')

        @if($is_dop)
            @include('profile.ankets.medic.is_dop')
        @else
            @include('profile.ankets.medic.base')
        @endif
    </div>

    @include('profile.ankets.components.media')
</div>


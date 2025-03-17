@php
use App\Enums\FlagPakEnum;
@endphp
<div class="form-group">
    <label>Флаг СДПО</label>
    @include('templates.elements_field', [
        'v' => [
            'type' => 'select',
            'values' => [
                FlagPakEnum::INTERNAL => FlagPakEnum::INTERNAL,
                FlagPakEnum::SDPO_A => FlagPakEnum::SDPO_A,
                FlagPakEnum::SDPO_R => FlagPakEnum::SDPO_R,
            ],
        ],
        'model' => 'workdays',
        'k' => 'flag_pak',
        'is_required' => '',
        'default_value' => request()->get('flag_pak', null)
    ])
</div>

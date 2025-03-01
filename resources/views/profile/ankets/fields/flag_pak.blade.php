@php use \App\Enums\FlagPakEnum; @endphp
@include('templates.elements_field', [
    'v' => [
        'type' => 'select',
        'values' => [
            FlagPakEnum::INTERNAL => FlagPakEnum::INTERNAL,
            FlagPakEnum::SDPO_A => FlagPakEnum::SDPO_A,
            FlagPakEnum::SDPO_R => FlagPakEnum::SDPO_R,
        ],
        'multiple' => 1
    ],
    'model' => '',
    'k' => 'flag_pak',
    'is_required' => '',
    'default_value' => $field_default_value
])

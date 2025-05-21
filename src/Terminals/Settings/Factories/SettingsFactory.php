<?php

declare(strict_types=1);

namespace Src\Terminals\Settings\Factories;

use Src\Terminals\Settings\ValueObjects\SettingsContainer;

final class SettingsFactory
{
    /** @var array */
    private const DEFAULT_SETTINGS = [
        'main' => [
            'password' => '0000',
            'medic_password' => '0000',
            'selected_medic' => null,
        ],
        'system' => [
            'driver_info' => false,
            'delay_before_retry_inspection' => 5000,
            'delay_before_redirect_to_main_page' => 10000,
            'driver_photo' => false,
            'tonometer_logs_visible' => false,
            'camera_video' => true,
            'camera_photo' => true,
            'printer_write' => true,
            'print_qr_check' => false,
            'print_count' => 1,
            'auto_start' => true,
        ],
    ];

    public static function makeMain(?array $settings = null): SettingsContainer
    {
        return new SettingsContainer(array_merge(self::DEFAULT_SETTINGS['main'], $settings ?? []));
    }

    public static function makeSystem(?array $settings = null): SettingsContainer
    {
        return new SettingsContainer(array_merge(self::DEFAULT_SETTINGS['system'], $settings ?? []));
    }
}

<?php

namespace App\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class SidebarMenuItemData extends DataTransferObject
{
    public string $title;
    public ?string $tooltip_prompt;
    public ?array $access_permissions;
    public ?int $parent_id;
}
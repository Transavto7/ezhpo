<?php

namespace App\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class SidebarMenuItemData extends DataTransferObject
{
    public string $title;
    public ?string $tooltip_prompt;
    public ?string $access_permissions;
    public ?string $route_name;
    public ?int $parent_id;
}
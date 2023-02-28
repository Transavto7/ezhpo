<?php
namespace App\Services;

use App\Services\Contracts\ServiceInterface;
use App\SideBarMenuItem;
use Illuminate\Database\Eloquent\Collection;

class SidebarService implements ServiceInterface
{
    /**
     * @param  array  $filter
     * @return Collection
     */
    public function getAllItems(array $filter = []): Collection
    {
        return SideBarMenuItem::all();
    }
}
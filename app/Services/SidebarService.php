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
        return SideBarMenuItem::with('children')->get();
    }


    /**
     * @return array
     */
    public static function renderItems(): array
    {
        $pakQueueCnt = \App\Anketa::where('type_anketa', 'pak_queue')->count();
        $pakErrorsCnt = \App\Anketa::where('type_anketa', 'pak')->count();
        $sidebarItems = (new static())->getAllItems();

        return [
            $pakQueueCnt,
            $pakErrorsCnt,
            $sidebarItems
        ];
    }
}
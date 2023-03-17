<?php
namespace App\Services;

use App\Anketa;
use App\Dtos\SidebarMenuItemData;
use App\Services\Contracts\ServiceInterface;
use App\SideBarMenuItem;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SidebarService implements ServiceInterface
{
    /**
     * @param  array  $filters
     * @return Builder
     */
    public function getAllItems(array $filters = []): Builder
    {
        return SideBarMenuItem::with(['children', 'parent'])
            ->when(isset($filters['slug']), function (Builder $builder) use ($filters) {
                return $builder->where('slug', '=', $filters['slug']);
            })
            ->when(isset($filters['title']), function (Builder $builder) use ($filters) {
                return $builder->where('title', '=', $filters['title']);
            })
            ;
    }

    /**
     * @return array
     */
    public static function renderItems(): array
    {
        $pakQueueCnt = Anketa::where('type_anketa', 'pak_queue')->count();
        $pakErrorsCnt = Anketa::where('type_anketa', 'pak')->count();
        $sidebarItems = (new static())->getAllItems()->get();

        return [
            $pakQueueCnt,
            $pakErrorsCnt,
            $sidebarItems
        ];
    }

    /**
     * @param  \App\Dtos\SidebarMenuItemData  $data
     * @param  int  $id
     * @return array
     */
    public function updateItem(SidebarMenuItemData $data, int $id): array
    {
        $item = SideBarMenuItem::find($id);

        if ($item) {
            return [
                'result' => $item->update($data->all()),
                'model' => $item->refresh()
            ];
        } else {
            throw new NotFoundHttpException("Item not found!");
        }
    }
}
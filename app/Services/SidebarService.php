<?php
namespace App\Services;

use App\Anketa;
use App\Dtos\SidebarMenuItemData;
use App\Services\Contracts\ServiceInterface;
use App\SideBarMenuItem;
use Illuminate\Database\Eloquent\Builder;
use App\Point;
use App\Role;
use Auth;
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
        $pakQueueCnt = Anketa::where('type_anketa', 'pak_queue');

        if(Auth::user()->load('roles')->roles->where('id', 2)->count()){
            $point_name = Point::where('id', Auth::user()->pv_id)->first()->name;
            $pakQueueCnt = $pakQueueCnt->where('pv_id', $point_name);
        }

        $pakQueueCnt = $pakQueueCnt->count();
        
        //$pakQueueCnt = Anketa::where('type_anketa', 'pak_queue')->where('pv_id', Point::where('id', Auth::user()->pv_id)->first()->name)->count();
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
     * @return bool
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
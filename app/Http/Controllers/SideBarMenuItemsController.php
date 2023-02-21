<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveSidebarItem;
use App\Services\Contracts\ServiceInterface;
use App\Services\SidebarService;
use App\SideBarMenuItem;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SideBarMenuItemsController extends Controller
{
    /**
     * @var ServiceInterface|null
     */
    private ?ServiceInterface $sidebarMenuService = null;

    /**
     * @param  SidebarService  $sidebarMenuService
     */
    public function __construct(ServiceInterface $sidebarMenuService)
    {
        $this->sidebarMenuService = $sidebarMenuService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view(
            'admin.sidebar.index',
            $this->sidebarMenuService->getAllItems(),
        );
    }

    /**
     * @param  SaveSidebarItem  $request
     * @return array
     */
    public function store(SaveSidebarItem $request): array
    {
        return [];
    }

    /**
     * @param  SaveSidebarItem  $request
     * @param  SideBarMenuItem  $sidebarItem
     * @return void
     */
    public function update(SaveSidebarItem $request, SidebarMenuItem $sidebarItem)
    {

    }

    /**
     * @throws Exception
     */
    public function destroy(SidebarMenuItem $sideBarMenuItem)
    {
        $sideBarMenuItem->delete();

        return response('', Response::HTTP_NO_CONTENT);
    }
}

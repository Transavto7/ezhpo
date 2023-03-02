<?php

namespace App\Http\Controllers;

use App\Services\Contracts\ServiceInterface;
use App\Services\SidebarService;
use App\SideBarMenuItem;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SideBarMenuItemsController extends Controller
{
    /**
     * @var ServiceInterface|null
     */
    private ServiceInterface $sidebarMenuService;

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
        $items = $this->sidebarMenuService->getAllItems();
        return view(
            'admin.sidebar.index', [
                'items' => $items->get()->toArray()
            ]
        );
    }

    /*
    * Axios get all rows in table
    */
    public function filter(Request $request) {
        $items = $this->sidebarMenuService->getAllItems($request->all());

        return $items->paginate(100);
    }
}

<?php

namespace App\Http\Controllers;

use App\Dtos\SidebarMenuItemData;
use App\Http\Requests\SaveSidebarMenuItem;
use App\Services\Contracts\ServiceInterface;
use App\Services\SidebarService;
use App\SideBarMenuItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SidebarMenuItemsController extends Controller
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
                'sidebarItems' => $items->get(),
                'headers' => $items->get()
                    ->where('is_header', '=', true)
                    ->pluck('title', 'id')
            ]
        );
    }

    /*
    * Axios get all rows in table
    */
    public function filter(Request $request): LengthAwarePaginator
    {
        $items = $this->sidebarMenuService->getAllItems($request->all());

        return $items->paginate(100);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(SaveSidebarMenuItem $request, $id) : array
    {
        return $this->sidebarMenuService->updateItem(
            new SidebarMenuItemData($request->validated()),
            $id
        );
    }
}

<?php

namespace Modules\Manage\Http\Controllers;


use Modules\Manage\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuViewController extends Controller
{
    public function __construct()
    {
        $this->menu = new Menu();
    }
    
    public function index(Request $request)
    {
        $this->actionLog("查看菜单");
        $menuLists = $this->menu->getMenuList();
        $menuInfo = getMenuFromPath($request->path());
        return view("manage::menu.index")
            ->with("thisAction", $menuInfo->url)
            ->with("title", $menuInfo->title)
            ->with("menuLists", $menuLists)
            ->with("firstMenuList", $menuLists);
    }
    
    public function action(Request $request, $menuId)
    {
        $this->actionLog("查看菜单操作");
        $menuAction = DB::table("admin_menu_actions")->where("menu_id", $menuId)->get();
        return view("manage::menu.action")
            ->with("menuId", $menuId)
            ->with("menuActionLists", $menuAction)
            ->with("thisAction", "/goods")
            ->with("title", "管理");
    }
}

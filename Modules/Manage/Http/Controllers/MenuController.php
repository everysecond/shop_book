<?php

namespace Modules\Manage\Http\Controllers;


use Modules\Manage\Models\Menu;
use App\Classlib\FormCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->menu = new Menu();
        $this->formCheck = new FormCheck();
    }
    
    //添加菜单
    public function store(Request $request)
    {
        $formCheck = new FormCheck();
        $checkTitle = $formCheck->isEmpty($request->title, "菜单名称");
        if (!$checkTitle->code) {
            return result($checkTitle->msg);
        }
        $checkUrl = $formCheck->isEmpty($request->url, "菜单URL");
        if (!$checkUrl->code) {
            return result($checkUrl->msg);
        }
        $checkIconClass = $formCheck->isEmpty($request->icon_class, "菜单图标");
        if (!$checkIconClass->code) {
            return result($checkIconClass->msg);
        }
        $this->actionLog("添加菜单");
        $result = $this->menu->add($request);
        return result($result->msg, $result->code);
    }
    
    //更新菜单状态
    public function menuStatus(Request $request)
    {
        $checkId = $this->formCheck->isEmpty(intval($request->id), "所选菜单");
        if (!$checkId->code) {
            return result($checkId->msg);
        }
        $menuStatus = $request->is_show;
        if ($menuStatus != 0 && $menuStatus != 1) {
            return result("所选择状态不存在！");
        }
        $this->actionLog("更新菜单状态");
        $result = $this->menu->upMenuStatus(intval($request->id), intval($menuStatus));
        return result($result->msg, $result->code);
    }
    
    //编辑菜单
    public function menuUpdate(Request $request)
    {
        $this->actionLog("编辑菜单");
        $formCheck = new FormCheck();
        $checkMenuId = $formCheck->isEmpty($request->id, "菜单不存在");
        if (!$checkMenuId->code) {
            return result("菜单不存在！");
        }
        $checkTitle = $formCheck->isEmpty($request->title, "菜单名称");
        if (!$checkTitle->code) {
            return result($checkTitle->msg);
        }
        $checkUrl = $formCheck->isEmpty($request->url, "菜单URL");
        if (!$checkUrl->code) {
            return result($checkUrl->msg);
        }
        $checkIconClass = $formCheck->isEmpty($request->icon_class, "菜单图标");
        if (!$checkIconClass->code) {
            return result($checkIconClass->msg);
        }
        $result = $this->menu->menuUpdate($request);
        return result($result->msg, $result->code);
    }
    
    //删除菜单
    public function menuDelete(Request $request)
    {
        $this->actionLog("删除菜单");
        $formCheck = new FormCheck();
        $checkMenuId = $formCheck->isEmpty($request->id, "菜单不存在");
        if (!$checkMenuId->code) {
            return result("菜单不存在！" . $request->id);
        }
        
        //判断删除菜单下是否有子菜单
        $checkIsChild = $this->menu->checkIsChild(intval($request->id));
        if (!$checkIsChild->code) {
            return result("请删除子菜单后再进行删除！");
        }
        
        $result = $this->menu->menuDelete($request->id);
        return result($result->msg, $result->code);
    }
    
    //添加菜单操作
    public function addAction(Request $request)
    {
        $this->actionLog("添加菜单操作");
        $name = $request->name;
        $menuId = $request->menu_id;
        $formCheck = new FormCheck();
        $checkName = $formCheck->isEmpty($name, "操作名称");
        if (!$checkName->code) {
            return result($checkName->msg);
        }
        
        $checkKey = $formCheck->isEmpty($request->key, "操作索引");
        if (!$checkKey) {
            return result($checkKey->msg);
        }
        
        $checkNameLength = $formCheck->strLenthCheck($name, "操作名称", 2, 8, 2);
        if (!$checkNameLength->code) {
            return result($checkNameLength->msg);
        }
        //判断操作名称是否存在
        $menu = new Menu();
        $res = $menu->checkMenuActionIsExists($menuId, $name);
        if ($res) {
            return result("操作名称已存在！");
        }
        
        //添加操作
        $result = $menu->addAction($request);
        return result($result->msg, $result->code, $result->data);
    }
    
    //删除菜单操作
    public function deleteAction(Request $request)
    {
        $this->actionLog("删除菜单操作");
        $actionIds = $request->action_ids;
        $menuId = $request->menu_id;
        if (!count($actionIds)) {
            return result("请选择要删除的操作！");
        }
        $menu = new Menu();
        //删除菜单操作
        $result = $menu->deleteAction($menuId, $actionIds);
        if ($result->code) {
            return result($result->msg, $result->code, $actionIds);
        } else {
            return result($result->msg, $result->code);
        }
    }
    
    //更新菜单排序
    public function sortUpdate(Request $request)
    {
        $sort = $request->sort;
        if (empty($sort) || !is_array($sort)) {
            return result('参数错误');
        }
        $menu = new Menu();
        $result = $menu->updateMenuSort($sort);
        return result($result->msg, $result->code, $result->data);
    }
    
}

<?php

namespace Modules\Manage\Http\Controllers\Mobile;

use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Manage\Http\Controllers\Controller;
use Modules\Manage\Repositories\ManageMenuRepository;

class ManageController extends Controller
{
    protected $menuRepository;

    protected $menuType = [
        "1" => "租点",
        "2" => "快点"
    ];

    public function __construct(ManageMenuRepository $menuRepository)
    {

        $this->menuRepository = $menuRepository;

    }

    public function menus(Request $request)
    {
        try {
            $type = $request->get('type', '1');
            $menuList = $this->menuRepository->scopeQuery(function ($query) {
                return $query->whereStatus(1)->where('terminal', 'app')->orderBy('sort');
            })->get();

            //去除没有权限的菜单
            $menuList = $this->unsetNotPermission($menuList, $request)->toArray();

            $menuTree = listToTree($menuList, 'id', 'pid', 'children');

            //去除空菜单
//            $this->unsetEmptyMenu($menuTree, $request);
            $resultMenu = array();
            foreach ($menuTree as $value) {
                if ($value['name'] == Arr::get($this->menuType, $type, '租点')) {
                    $resultMenu = Arr::get($value, 'children');
                }
            }
            return result('', 1, $resultMenu);
        } catch (\Exception $exception) {
            return result($exception->getMessage(), -1);
        }
    }

    protected function unsetNotPermission($menuList, $request)
    {
        /* @var Manager $manager */
        $manager = $request->manager;
        if ($manager->isSuper()) {
            return $menuList;
        }

        return $menuList->filter(function ($menu) use ($manager) {
            if ($menu->route) {
                return $manager->can($menu->route);
            } else {
                return $menu;
            }
        });
    }

    protected function unsetEmptyMenu(&$menuTree = null, $request)
    {
        /* @var Manager $manager */
        $manager = $request->manager;

        if ($manager->isSuper()) {
            return;
        }
        foreach ($menuTree as $k => &$menu) {
            if (!empty($menu['children'])) {
                $this->unsetEmptyMenu($menu['children'], $request);
            }
            if (empty($menu['children']) && empty($menu['route'])) {
                unset($menuTree[$k]);
            }
        }
    }

    public function timeList()
    {
        $data = [
            'basic'    => [
                1 => '今日',
                2 => '本月',
                3 => '本年',
                4 => '全部'
            ],
            'business' => [
                1 => '近一周',
                2 => '近一月',
                3 => '近半年',
                4 => '近一年'
            ]
        ];
        return result('', 1, $data);
    }
}

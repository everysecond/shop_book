<?php

namespace Modules\Manage\Http\Controllers;

use App\Common\Helpers\Tree;
use App\Models\ManageMenu;
use App\Models\Manager;
use Illuminate\Support\Facades\Auth;
use Modules\Manage\Models\LeaseInsurance;
use Modules\Manage\Models\LeaseRenewal;
use Modules\Manage\Models\LeaseRentChange;
use Modules\Manage\Models\LeaseRentRebate;
use Modules\Manage\Models\Report\LeaseContractDateInfo;
use Modules\Manage\Models\Report\LeaseRegisterLog;
use Modules\Manage\Repositories\ManageMenuRepository;

class ManageController extends Controller
{
    protected $menuRepository;
    public function __construct(ManageMenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function index()
    {
        $menuList = $this->menuRepository->scopeQuery(function ($query) {
            return $query->whereStatus(1)->where('terminal','web')->orderBy('sort');
        })->get();

        //todo 去除没有权限的菜单
        $menuList = $this->unsetNotPermission($menuList)->toArray();

        $menuTree = listToTree($menuList, 'id', 'pid', 'children');

        //todo 去除空菜单
        $this->unsetEmptyMenu($menuTree);

        $manager = getUser();
        $home = '';
        if ($manager->cans('lease.reports.view.dashboard.today')) {
            $home = 'lease.reports.view.dashboard.today';
        }
        if ($manager->cans('console.index')) {
            $home = 'console.index';
        }

        return $this->view('index', [
            'home'     => $home,
            'manager'  => $manager,
            'menuTree' => $menuTree,
        ]);
    }

    public function console()
    {
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $today = date('Y-m-d', time());

        $regis_num = LeaseRegisterLog::where("type", '=', 1)->where("province_id", '=', 0)
            ->where("date", '=', $today)->first("total");
        $regis_num = empty($regis_num) ? 0 : $regis_num->total;

        $rent_num = LeaseContractDateInfo::where("province_id", '=', 0)->whereIn("type", array('1', '2'))
            ->where("date", '=', $today)
            ->sum('today_num');

        $renewal_num = LeaseRenewal::where("type", '=', 1)->where("province_id", '=', 0)
            ->where("renewal_date", '=', $yesterday)->first("renewal_num");
        $renewal_num = empty($renewal_num) ? 0 : $renewal_num->renewal_num;

        $rebate_num = LeaseRentRebate::where("type", '=', 1)->where("province_id", '=', 0)
            ->where("rent_release_date", '=', $yesterday)->first("rent_release_num");
        $rebate_num = empty($rebate_num) ? 0 : $rebate_num->rent_release_num;

        $exchange_num = LeaseRentChange::where("type", '=', 1)->where("province_id", '=', 0)
            ->where("rent_change_date", '=', $yesterday)->first("rent_change_num");
        $exchange_num = empty($exchange_num) ? 0 : $exchange_num->rent_change_num;

        $insurance_num = LeaseInsurance::where("type", '=', 1)->where("province_id", '=', 0)
            ->where("rent_date", '=', $yesterday)->first("insure_num");
        $insurance_num = empty($insurance_num) ? 0 : $insurance_num->insure_num;

        $provinces = allUserProvinces();
        $timeType = timeType();
        return $this->view('console', [
            'regis_num'     => $regis_num,
            'rent_num'      => $rent_num,
            'renewal_num'   => $renewal_num,
            'rebate_num'    => $rebate_num,
            'exchange_num'  => $exchange_num,
            'insurance_num' => $insurance_num,
        ], compact("provinces", "timeType"));
    }

    protected function unsetNotPermission($menuList)
    {
        /* @var Manager $manager */
        $manager = Auth::user();
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

    protected function unsetEmptyMenu(&$menuTree = null)
    {
        /* @var Manager $manager */
        $manager = Auth::user();

        if ($manager->isSuper()) {
            return;
        }
        foreach ($menuTree as $k => &$menu) {
            if (!empty($menu['children'])) {
                $this->unsetEmptyMenu($menu['children']);
            }
            if (empty($menu['children']) && empty($menu['route'])) {
                unset($menuTree[$k]);
            }
        }
    }
}

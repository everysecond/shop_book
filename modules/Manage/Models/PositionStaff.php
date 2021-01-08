<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/19 19:46
 */

namespace Modules\Manage\Models;

use App\Models\Manager;

class PositionStaff extends Model
{
    protected $fillable = [
        "position_id",
        "staff_id",
        "see_level"
    ];

    public function staff()
    {
        return $this->hasOne(Manager::class, 'id', 'staff_id')->withTrashed();
    }

    /**
     * 查询所有下属id
     * @param $userId :当前登录人id
     * @param bool $hasSelf :是否包含当前登录人
     * @param bool $sameLevel :是否包含当前登录人同级
     * @return array
     */
    public function allUnderStaffIds($userId, $hasSelf = false, $sameLevel = false)
    {
        //下属职位ids
        $underPositionIds = app(Position::class)->allUnderPositionIds($userId, $sameLevel);
        //下属职员ids
        $underStaffIds = [];
        if (!empty($underPositionIds)) {
            $underStaffIds = PositionStaff::query()
                ->whereIn('position_id', $underPositionIds)
                ->pluck('staff_id')
                ->toArray();
        }
        if ($hasSelf) {
            $underStaffIds[] = $userId;
        } else {
            $underStaffIds = array_merge(array_diff($underStaffIds, array($userId)));
        }
        return array_unique($underStaffIds);
    }
}
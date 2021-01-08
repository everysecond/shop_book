<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/15 15:15
 */

namespace Modules\Manage\Models;

class Position extends Model
{
    protected $fillable = [
        "pid",
        "sort",
        "title",
        "level"
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'pid', 'id')->with('children')->orderBy("sort", "desc");
    }

    public function allUnderPositionIds($userId, $sameLevel = false)
    {
        $positionIds = PositionStaff::query()
            ->where('staff_id', $userId)
            ->whereNull('deleted_at')
            ->pluck('position_id');
        $underPositionIds = [];
        $underPositions = self::query()->whereIn('id', $positionIds)->with('children')->get();
        foreach ($underPositions as $underPosition) {
            if ($children = $underPosition->children) {
                $underPositionIds = array_merge($underPositionIds, $this->nextPositionsId($children, $underPositionIds));
            }
        }
        if ($sameLevel) $underPositionIds = array_merge($underPositionIds, $positionIds->toArray());
        return array_unique($underPositionIds);
    }

    public function nextPositionsId($positions, &$underPositionIds)
    {
        foreach ($positions as $position) {
            $underPositionIds[] = $position->id;
            if ($children = $position->children) {
                $underPositionIds = $this->nextPositionsId($children, $underPositionIds);
            }
        }
        return $underPositionIds;
    }
}
<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/30 10:25
 */

namespace Modules\Manage\Models\Crm;

use App\Models\Manager;
use Modules\Manage\Models\Model;

class CrmOperateLog extends Model
{
    protected $fillable = [
        'table',
        'type',
        'content',
        'target_user_id',
        'created_by'
    ];

    public function createdUser()
    {
        return $this->hasOne(Manager::class, 'id', 'created_by')
            ->select('id', 'name');
    }

    public function targetUser()
    {
        return $this->hasOne(Manager::class, 'id', 'target_user_id')
            ->select('id', 'name');
    }

    /**
     * @param $table
     * @param $type
     * @param array $logs 二位数组
     * @return bool
     */
    public function batchInsert($table, $type, array $logs)
    {
        $userId = getUserId();
        $time = time();
        foreach ($logs as &$log) {
            if (is_array($log)) {
                $log['table_name'] = $table;
                $log['created_by'] = $userId;
                $log['created_at'] = $time;
                if ($type) $log['type'] = $type;
                if (!isset($log['content'])) $log['content'] = '';
            } else {
                return false;
            }
        }
        return self::query()->insert($logs);
    }
}
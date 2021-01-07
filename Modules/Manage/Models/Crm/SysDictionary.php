<?php
/**
 * Created by : cps
 * User: lidz
 * DateTime: 2019/8/21 16:25
 */

namespace Modules\Manage\Models\Crm;

use App\Models\Manager;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Manage\Models\Model;

class SysDictionary extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "dict_type",
        "type_means",
        "code",
        "means",
        "memo",
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function scopeGetList($query, $page, $request, Callable $callback = null)
    {
        $search = $request->get('searchStr');
        if (!is_null($search) && trim($search)) {
            $search = trim($search);
            $query->where(function ($query) use ($search) {
                $query->where("dict_type", "like", "%$search%")
                    ->orWhere("type_means", "like", "%$search%")
                    ->orWhere("code", "like", "%$search%")
                    ->orWhere("means", "like", "%$search%");
            });
        }

        $callback && $callback($query);

        return $query->orderBy("sort", "desc")->orderBy("created_at", "desc")->paginate($page);
    }

    public function createdUser()
    {
        return $this->hasOne(Manager::class, 'id', 'created_by');
    }
}
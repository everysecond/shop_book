<?php

namespace Modules\Manage\Repositories;

use App\Models\ManagerPermission;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ManagerPermissionRepository.
 *
 * @package namespace App\Repositories;
 */
class ManagerPermissionRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id',
    ];

    //权限level 1
    const LEVEL_ONE = 0;
    //权限level 2
    const LEVEL_TWO = 1;
    //权限level 3
    const LEVEL_THREE = 2;


    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ManagerPermission::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function add(array $attributes, Array $actions = null)
    {
        \DB::beginTransaction();
        $permission = $this->create($attributes);

        if ($permission->level == 2) {
            $data = [];
            foreach ($actions as $action) {
                $data[] = [
                    'permission_id' => $permission->id,
                    'action' => $action
                ];
            }

            $permission->actions()->insert($data);
        }

        \DB::commit();
    }

    public function modify($id, Array $attributes, Array $actions = null)
    {
        \DB::beginTransaction();
        $permission = $this->update($attributes, $id);

        $permission->actions()->delete();
        if ($permission->level == 2) {
            $data = [];
            foreach ($actions as $action) {
                $data[] = [
                    'permission_id' => $permission->id,
                    'action' => $action
                ];
            }

            $permission->actions()->insert($data);
        }

        \DB::commit();
    }

    public function getAllParentPermissions()
    {
        return $this->model->where("level","<",self::LEVEL_THREE)->pluck("id")->toArray();
    }
}

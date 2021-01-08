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
class ManagerPermissionGroupRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'id',
    ];

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
}

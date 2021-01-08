<?php

namespace Modules\Manage\Repositories;

use App\Models\ManagerRole;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ManagerRoleRepository.
 *
 * @package namespace App\Repositories;
 */
class ManagerRoleRepository extends BaseRepository
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
        return ManagerRole::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}

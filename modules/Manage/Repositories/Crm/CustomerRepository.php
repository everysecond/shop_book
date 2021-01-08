<?php

namespace Modules\Manage\Repositories\Crm;

use Modules\Manage\Models\Crm\CrmContact;
use Modules\Manage\Models\Crm\CrmUser;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PostRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CustomerRepository extends BaseRepository
{
    protected $fieldSearchable = [

    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CrmUser::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}

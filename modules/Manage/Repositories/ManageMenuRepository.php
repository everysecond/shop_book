<?php

namespace Modules\Manage\Repositories;

use App\Models\ManageMenu;
use Illuminate\Support\Collection;
use Modules\Manage\Repositories\Criterias\ManageMenuCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ManageMenuRepository.
 *
 * @package namespace App\Repositories;
 */
class ManageMenuRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'pid',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ManageMenu::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getParents($pid)
    {
        $parents = new Collection();

        if ($model = $this->model->where('id', $pid)->first()) {
            $parents->push($model);
            if ($model->pid > 0) {
                $parents = $this->getParents($model->pid)->merge($parents);
            }
        }

        return $parents;
    }
}

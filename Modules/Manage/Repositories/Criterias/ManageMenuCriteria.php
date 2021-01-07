<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2019/7/24
 * Time: 15:55
 */

namespace Modules\Manage\Repositories\Criterias;


use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class ManageMenuCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($model, RepositoryInterface $repository)
    {
        $fieldsSearchable = $repository->getFieldsSearchable();

        foreach ($fieldsSearchable as $field) {
            $value = $this->request->input($field);

            if (!is_null($value)) {
                $model = $model->where($field, $value);
            }

        }

        return $model;
    }

}
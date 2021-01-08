<?php

namespace Modules\Manage\Repositories;

use App\Models\Manager;
use App\Models\ManagerPermission;
use App\Models\ManagerPermissionAction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Validator\Contracts\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class PostRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ManagerRepository extends BaseRepository
{
    protected $fieldSearchable = [
//        'username',
        'mobile' => 'like',
        'name'   => 'like'
    ];
    /**
     * Specify Validator Rules
     * @var array
     */
    protected $rules;

    protected $messages;


    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Manager::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function makePassword($password)
    {
        return $password ? Hash::make($password) : null;
    }

    public function loginCheck($mobile, $password)
    {
        /* @var Manager $manager */
        $manager = $this->makeModel()->where('mobile', $mobile)->first();

        if (empty($manager)) {
            throw new BadRequestHttpException('用户帐号不存在！');
        }

        if (!$manager->checkPassword($password)) {
            throw new BadRequestHttpException('帐号密码错误！');
        }

        if (!$manager->isNormal()) {
            throw new BadRequestHttpException('帐号已冻结！');
        }

        return $manager;
    }

    public function deleteBatch(Array $id)
    {
        $models = $this->findWhereIn('id', $id);

        return $models->delete();
    }

    public function getRolesPermission(Manager $manager)
    {
        $permissions = [];
        $roles = $manager->roles()->with('permissions')->get();

        foreach ($roles as $role) {
            $ids = $role->permissions->pluck('id')->toArray();
            $permissions = array_merge($permissions, $ids);
        }

        return array_unique($permissions);
    }

    public function getAllPermission(Manager $manager)
    {
        $permissions = $this->getRolesPermission($manager);

        $managerPermissions = $manager->permissions()->pluck('id')->toArray();

        return array_unique(array_merge($permissions, $managerPermissions));
    }

    public function getPermissionActions(Manager $manager)
    {
        $permissions = $this->getAllPermission($manager);

        return ManagerPermissionAction::whereIn('permission_id', $permissions)
            ->distinct('action')->pluck('action')->toArray();
    }



    public function login($mobile) {
        $manager = $this->makeModel()->where('mobile', $mobile)->first();

        if (empty($manager)) {

            return result('用户帐号不存在！', 0, []);

        }

        return $manager;
    }


    public function updateInfo($model) {
        $time = time();
        $access_token = Str::random(32);
        $model->access_token = $access_token;      //token
        $model->access_at = $time;
        $model->save();

        return $access_token;
    }
}

<?php

namespace Modules\Manage\Transformers;

use App\Http\Resources\Resource;

class ManagerResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->only('id', 'mobile', 'name', 'username', 'status','access_at');
        $data['is_super'] = $this->isSuper();
        $data['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : '-';
        $data['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : '-';
        $data['last_login_at'] = $this->last_login_at ? $this->last_login_at->created_at->format('Y-m-d H:i') : '-';
        $data['roles'] = $this->roles->pluck('name');
        $data['access_at'] = $this->access_at ? date('Y-m-d H:i',$this->access_at) : '-';
        return $data;
    }
}

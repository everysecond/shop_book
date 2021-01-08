<?php

namespace Modules\Manage\Transformers;

use App\Http\Resources\Resource;

class CrmContactResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->only('id', 'mobile', 'name', 'position','sex','address','memo','wechat');
        $data['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : '-';
        $data['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : '-';
        $data['createdUser'] = $this->createdUser;
        $data['cus'] = isset($this->cus)?$this->cus:'';
        $data['is_key'] = $this->is_key == 1?"是":"否";
        return $data;
    }
}

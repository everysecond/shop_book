<?php

namespace Modules\Manage\Transformers;

use App\Http\Resources\Resource;

class SysDictionaryResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->only('id', 'dict_type', 'type_means', 'code','means','sort','memo');
        $data['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : '-';
        $data['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i') : '-';
        $data['createdUser'] = $this->createdUser;
        return $data;
    }
}

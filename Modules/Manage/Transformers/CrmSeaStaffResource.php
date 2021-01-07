<?php

namespace Modules\Manage\Transformers;

use App\Http\Resources\Resource;

class CrmSeaStaffResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->only('id', 'sea_id', 'staff_id', 'can_assign','can_get');
        $data['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : '-';
        $data['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : '-';
        $data['staff'] = $this->staff;
        return $data;
    }
}

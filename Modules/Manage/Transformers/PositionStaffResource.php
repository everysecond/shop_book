<?php

namespace Modules\Manage\Transformers;

use App\Http\Resources\Resource;

class PositionStaffResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->only('id', 'position_id', 'staff_id', 'see_level');
        $data['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : '-';
        $data['updated_at'] = $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : '-';
        $data['staff'] = $this->staff;
        return $data;
    }
}

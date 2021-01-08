<?php

namespace Modules\Manage\Transformers;

use App\Http\Resources\Resource;

class CrmPlanRecordResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->only('id', 'follow_mode', 'follow_at', 'follow_user_ids', 'content','cus_id');
        $data['follow_at'] = date('Y-m-d H:i:s', $this->follow_at);
        $data['createdUser'] = $this->createdUser;
        $data['contact'] = $this->contact;
        $data['contact_name'] = $this->contact?$this->contact->name:'';
        $data['images'] = $this->images;
        $data['cus'] = isset($this->cus) ? $this->cus : '';
        $mode = dictArrAll('crm_follow_mode');
        $managers = allUsersArr();
        $data['charger'] = $this->cus->charger_id ? $managers[$this->cus->charger_id]['name'] : '';
        $data['mode'] = $mode[$this->follow_mode] ? $mode[$this->follow_mode] : '';
        if ($ids = $this->follow_user_ids) {
            $ids = explode(',', $ids);
            $data['follow_users'] = '';
            foreach ($ids as $id) {
                $name = $managers[$id]['name'];
                $data['follow_users'] .= $data['follow_users'] ? ',' . $name : $name;
            }
        } else {
            $data['follow_users'] = '';
        }
        return $data;
    }
}

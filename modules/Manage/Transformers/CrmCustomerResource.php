<?php

namespace Modules\Manage\Transformers;

use App\Http\Resources\Resource;

class CrmCustomerResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->only(
            'id',
            'is_mark',
            'is_top',
            'mobile',
            'name',
            'area',
            'charger_name',
            'history_deal',
            'constract_end_at',
            'created_at'
        );
        $data['created_at'] = $this->created_at ? $this->created_at->format('Y-m-d H:i') : '-';
        $data['first_contact'] = $this->contact ? $this->contact->name : '';
        $data['first_contact_mobile'] = $this->contact ? $this->contact->mobile : '';
        $data['contract'] = $this->contract ? $this->contract : '';
        $data['preFollowAt'] = $this->preFollow ? date('Y-m-d H:i:s', $this->preFollow->follow_at) : '';
        $cusType = $this->cus_type;
        if ($cusType == 1) {
            $data['cus_type'] = '租点用户';
        } elseif ($cusType == 2) {
            $data['cus_type'] = '租点网点';
        } elseif ($cusType == 3) {
            $data['cus_type'] = '快点用户';
        } elseif ($cusType == 4) {
            $data['cus_type'] = '快点用户';
        }

        $cusLevel = $this->cus_level;
        if ($cusLevel == 1) {
            $data['cus_level'] = '重点客户';
        } elseif ($cusLevel == 2) {
            $data['cus_level'] = '普通客户';
        } elseif ($cusLevel == 3) {
            $data['cus_level'] = '非优先客户';
        } else {
            $data['cus_level'] = '';
        }

        $cusSource = $this->cus_source;
        if ($cusSource == 1) {
            $data['cus_source'] = 'APP录入';
        } elseif ($cusSource == 2) {
            $data['cus_source'] = '租点系统';
        } elseif ($cusSource == 3) {
            $data['cus_source'] = '中台录入';
        } else {
            $data['cus_source'] = '';
        }
        return $data;
    }

    public static function transformers($item)
    {
        $cusType = $item->cus_type;
        if ($cusType == 1) {
            $item['cus_type'] = '租点用户';
        } elseif ($cusType == 2) {
            $item['cus_type'] = '租点网点';
        } elseif ($cusType == 3) {
            $item['cus_type'] = '快点用户';
        } elseif ($cusType == 4) {
            $item['cus_type'] = '快点用户';
        }

        $cusLevel = $item->cus_level;
        if ($cusLevel == 1) {
            $item['cus_level'] = '重点客户';
        } elseif ($cusLevel == 2) {
            $item['cus_level'] = '普通客户';
        } elseif ($cusLevel == 3) {
            $item['cus_level'] = '非优先客户';
        } else {
            $item['cus_level'] = '';
        }

        $cusSource = $item->cus_source;
        if ($cusSource == 1) {
            $item['cus_source'] = 'APP录入';
        } elseif ($cusSource == 2) {
            $item['cus_source'] = '租点系统';
        } elseif ($cusSource == 3) {
            $item['cus_source'] = '中台录入';
        } else {
            $item['cus_source'] = '';
        }
        return $item;
    }
}

<?php

namespace Modules\Kood\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Kood\Models\BaseModel as Model;

class Site extends Model
{
    use SoftDeletes;

    public static function sites($noTest = true)
    {
        $data = self::query()->when($noTest, function ($query) {
            $query->whereNotIn('id', [4, 11]);
        })->pluck('name', 'id')->toArray();
        foreach ($data as $id => &$datum) {
            $datum = str_replace('区', '', $datum);
        }
        return $data;
    }
}

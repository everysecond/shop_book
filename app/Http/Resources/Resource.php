<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2019/7/11
 * Time: 16:19
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    public $with = [
        'code' => 0
    ];

    /**
     * Create new anonymous resource collection.
     *
     * @param  mixed $resource
     * @return ResourceCollection
     */
    public static function collection($resource)
    {
        return tap(new ResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }
}
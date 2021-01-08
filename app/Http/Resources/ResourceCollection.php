<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2019/7/11
 * Time: 16:21
 */

namespace App\Http\Resources;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResourceCollection extends AnonymousResourceCollection
{
    public $with = [
        'code' => 0
    ];

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
            ? (new PaginatedResourceResponse($this))->toResponse($request)
            : parent::toResponse($request);
    }
}
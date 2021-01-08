<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2019/7/11
 * Time: 16:46
 */

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\ResourceResponse;

class PaginatedResourceResponse extends ResourceResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return tap(response()->json(
            $this->wrap(
                $this->resource->resolve($request),
                array_merge_recursive(
                    $this->paginationInformation($request),
                    $this->resource->with($request),
                    $this->resource->additional
                )
            ),
            $this->calculateStatus()
        ), function ($response) use ($request) {
            $response->original = $this->resource->resource->pluck('resource');

            $this->resource->withResponse($request, $response);
        });
    }

    /**
     * Add the pagination information to the response.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function paginationInformation($request)
    {
        return [
            'count' => $this->resource->resource->total(),
        ];
    }
}
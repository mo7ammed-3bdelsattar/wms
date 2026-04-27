<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    use \App\Traits\ApiResponse;

    protected function resourceCollection($paginator, $resource)
    {
        if ($paginator->total() > $paginator->perPage()) {
            $data = [
                'records' => $resource::collection($paginator),
                'paginationLinks' => [
                    'currentPage' => $paginator->currentPage(),
                    'lastPage'    => $paginator->lastPage(),
                    'perPage'     => $paginator->perPage(),
                    'total'       => $paginator->total(),
                    'links'       => [
                        'first'       => $paginator->url(1),
                        'last'        => $paginator->url($paginator->lastPage()),
                        'next'        => $paginator->nextPageUrl(),
                        'previous'    => $paginator->previousPageUrl(),
                    ]
                ]
            ];
        } elseif ($paginator->total() == 0) {
            $data = [
                'records' => [],
                'paginationLinks' => null
            ];
        } else {
            $data = [
                'records' => $resource::collection($paginator),
                'paginationLinks' => null
            ];
        }
        return $data;
    }
}

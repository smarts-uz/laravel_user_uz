<?php

namespace App\Utils;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class PaginateCollection
{

    public static function paginate($items, $perPage, $page = null): LengthAwarePaginator
    {
        if (Paginator::resolveCurrentPage()) {
            $page = $page ?: (Paginator::resolveCurrentPage());
        } else {
            $page = $page ?: (1);
        }
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage ;
        $itemstoshow = array_slice($items , $offset , $perPage);


        return new LengthAwarePaginator($itemstoshow, $total, $perPage, $page,
            ['path'=> Request::fullUrl()]);
    }
}

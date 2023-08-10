<?php

namespace App\Services;

use App\Models\Product;

/**
 * Class ProductService.
 */
class ProductService
{
    public function getList($per_page, $search, $user_id)
    {
        $result = Product::join('stores', 'stores.id', '=', 'products.store_id')
                        ->join('users', 'users.id', '=', 'stores.user_id')
                        ->select('products.*')
                        ->where('users.id', $user_id);

        if (isset($search['name']) && !blank($search['name'])) {
            $name = $search['name'];
            $result = $result->where('products.name', 'LIKE', "%$name%");
        }

        if (isset($search['store_id']) && !blank($search['store_id'])) {
            $store_id = $search['store_id'];
            $result = $result->where('products.store_id', $store_id);
        }

        if (
            isset($search['start_price']) &&
            !blank($search['start_price']) &&
            isset($search['end_price']) &&
            !blank($search['end_price'])
        ) {
            $result = $result->where('products.price', '>=', $search['start_price']);
            $result = $result->where('products.price', '<=', $search['end_price']);
        }

        $result = $result->paginate($per_page);

        return $result;

    }
}

<?php

namespace App\Services;

use App\Models\Store;

class StoreService
{
    public function getList($per_page, $search, $user_id)
    {
        $result = Store::where('user_id',  $user_id);

        if (isset($search['name']) && !blank($search['name'])) {
            $name = $search['name'];
            $result = $result->where('name', 'LIKE', "%$name%");
        }

        if (isset($search['address']) && !blank($search['address'])) {
            $address = $search['address'];
            $result = $result->where('address', 'LIKE', "%$address%");
        }

        $result = $result->paginate($per_page);

        return $result;
    }
}

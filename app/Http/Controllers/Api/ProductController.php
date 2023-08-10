<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getList(Request $request)
    {
        //
        $user_id = auth()->user()->id;
        $per_page = $request->get('per_page') ?? 5;

        $search = [
            'name' => $request->get('name'),
            'store_id' => $request->get('store_id'),
            'start_price' => $request->get('start_price'),
            'end_price' => $request->get('end_price'),
        ];

        $products = $this->productService->getList((int)$per_page, $search, $user_id);

        return response()->json([
            'status' => true,
            'messages' => '',
            'result' => $products
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                'quantity' => 'required|numeric|min:0|digits_between:1,9999999999',
                'store_id' => 'required|integer|exists:stores,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            $user_id = auth()->user()->id;
            $dataStoreProd = $validator->validated();

            $store = Store::find($dataStoreProd['store_id']);
            if ($store->user_id != $user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Store not found',
                    'result' => []
                ]);
            }

            $product = Product::create(array_merge(
                $dataStoreProd,
                [ 'store_id' => $store->id ]
            ));

            return response()->json([
                'status' => true,
                'message' => 'Create product successfully',
                'result' => $product
            ]);

        } catch (\Throwable $th) {
            logger('ProductController.store: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user_id = auth()->user()->id;

        $product = Product::join('stores', 'stores.id', '=', 'products.store_id')
                ->join('users', 'users.id', '=', 'stores.user_id')
                ->where([
                    'users.id' => $user_id,
                    'products.id' => $id,
                ])
                ->select('products.*')
                ->first();

        if (blank($product)) {
            return response()->json([
                'status' => false,
                'messages' => 'Product not found',
                'result' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'messages' => 'Get info product',
            'result' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                'quantity' => 'required|numeric|min:0|digits_between:1,9999999999',
                'store_id' => 'required|integer|exists:stores,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            $user_id = auth()->user()->id;
            $dataStoreProd = $validator->validated();

            $store = Store::find($dataStoreProd['store_id']);
            if ($store->user_id != $user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Store not found',
                    'result' => []
                ]);
            }

            $productExist = Product::join('stores', 'stores.id', '=', 'products.store_id')
                                ->join('users', 'users.id', '=', 'stores.user_id')
                                ->where([
                                    'users.id' => $user_id,
                                    'products.id' => $id,
                                ])
                                ->first();

            if (blank($productExist)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                    'result' => []
                ]);
            }

            $product = Product::find($id);
            $product->name = $dataStoreProd['name'];
            $product->price = $dataStoreProd['price'];
            $product->quantity = $dataStoreProd['quantity'];
            $product->store_id = $dataStoreProd['store_id'];
            $product->save();

            return response()->json([
                'status' => true,
                'message' => 'Update product successfully',
                'result' => $product
            ]);

        } catch (\Throwable $th) {
            logger('ProductController.update: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user_id = auth()->user()->id;

            $productExist = Product::join('stores', 'stores.id', '=', 'products.store_id')
                                ->join('users', 'users.id', '=', 'stores.user_id')
                                ->where([
                                    'users.id' => $user_id,
                                    'products.id' => $id,
                                ])
                                ->first();

            if (blank($productExist)) {
                return response()->json([
                    'status' => false,
                    'messages' => 'Product not found',
                    'result' => []
                ]);
            }

            Product::destroy($id);

            return response()->json([
                'status' => true,
                'messages' => 'Delete product successfully',
                'result' => []
            ]);
        } catch (\Throwable $th) {
            logger('ProductController.destroy: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }
}

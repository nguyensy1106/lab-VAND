<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\StoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    protected $storeService;
    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
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
        $user_id = auth()->user()->id;
        $per_page = $request->get('per_page') ?? 5;

        $search = [
            'name' => $request->get('name'),
            'address' => $request->get('address'),
        ];

        $stores =  $this->storeService->getList((int)$per_page, $search, $user_id);

        return response()->json([
            'status' => true,
            'messages' => '',
            'result' => $stores
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
        //
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'address' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }
            $user_id = auth()->user()->id;
            $dataCreateStore = $validator->validated();

            $store = Store::create(array_merge(
                $dataCreateStore,
                [ 'user_id' => $user_id ]
            ));

            return response()->json([
                'status' => true,
                'message' => 'Create store successfully',
                'result' => $store
            ]);

        } catch (\Throwable $th) {
            logger('StoreController.store: ' . $th->getMessage());
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
        $user_id = auth()->user()->id;

        $store = Store::where([
            'id' => $id,
            'user_id' =>  $user_id
        ])->first();

        if (blank($store)) {
            return response()->json([
                'status' => false,
                'messages' => 'Store not found',
                'result' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'messages' => 'View info store',
            'result' => $store
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
                'address' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            $user_id = auth()->user()->id;
            $dataUpdateStore = $validator->validated();

            $store = Store::where([
                'id' => $id,
                'user_id' => $user_id
            ])->first();


            if (blank($store)) {
                return response()->json([
                    'status' => false,
                    'messages' => 'Store not found',
                    'result' => []
                ]);
            }

            $store->name = $dataUpdateStore['name'];
            $store->address = $dataUpdateStore['address'];
            $store->save();

            return response()->json([
                'status' => true,
                'messages' => 'Update store successfully',
                'result' => $store
            ]);
        } catch (\Throwable $th) {
            logger('StoreController.update: ' . $th->getMessage());
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

            $store = Store::where([
                'id' => $id,
                'user_id' => $user_id
            ])->first();

            if (blank($store)) {
                return response()->json([
                    'status' => false,
                    'messages' => 'Store not found',
                    'result' => []
                ]);
            }

            $store->delete();

            return response()->json([
                'status' => true,
                'messages' => 'Delete store successfully',
                'result' => []
            ]);
        } catch (\Throwable $th) {
            logger('StoreController.destroy: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }
}

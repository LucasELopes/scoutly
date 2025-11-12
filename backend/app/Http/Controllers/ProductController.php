<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{

    private $product;

    public function __construct(Product $product) {
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $product = $this->product->query()
            ->when($request->has('name'), fn ($query) => $query->orWhere('name', 'like', "%{$request['name']}%"))
            ->when($request->has('url'), fn ($query) => $query->orWhere('url', 'like', "%{$request['url']}%"))
            ->when($request->has('desired_price'), fn ($query) => $query->orWhere('desired_price', 'like', "%{$request['desired_price']}%"))
            ->when($request->has('active_until'), fn ($query) => $query->orWhere('active_until', 'like', "%{$request['active_until']}%"))
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->paginate((int) $request->per_page);

            return response()->json($product, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}

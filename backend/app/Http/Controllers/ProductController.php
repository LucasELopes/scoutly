<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{

    private $product;

    public function __construct(Product $product) {
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Product::class);

        $product = $this->product->query()
            ->when($request->has('name'), fn ($query) => $query->orWhere('name', 'like', "%{$request['name']}%"))
            ->when($request->has('url'), fn ($query) => $query->orWhere('url', 'like', "%{$request['url']}%"))
            ->when($request->has('desired_price'), fn ($query) => $query->orWhere('desired_price', 'like', "%{$request['desired_price']}%"))
            ->when($request->has('active_until'), fn ($query) => $query->orWhere('active_until', 'like', "%{$request['active_until']}%"))
            ->orderBy('created_at', 'desc')
            ->with(['user', 'price_histories'])
            ->paginate((int) $request->per_page);

        return response()->json($product, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $product = $this->product->create($data);

        return response()->json(['message' => 'Produto adicionado com sucesso', 'product' => $product], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id): JsonResponse
    {
        $product = $this->product->with(['user', 'price_histories'])->findOrFail($id);

        Gate::authorize('view', $product);

        return response()->json(['message' => 'Produto encontrado', 'product' => $product], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, String $id): JsonResponse
    {
        $data = $request->validated();
        $product = $this->product->findOrFail($id);

        Gate::authorize('update', $product);

        $product->update($data);
        return response()->json(['message' => 'Produto atualizado com sucesso', 'product' => $product], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id): JsonResponse
    {
        $product = $this->product->findOrFail($id);

        Gate::authorize('delete', $product);

        $product->delete();
        return response()->json(['message' => 'Produto removido com sucesso', 'product' => $product], Response::HTTP_OK);
    }
}

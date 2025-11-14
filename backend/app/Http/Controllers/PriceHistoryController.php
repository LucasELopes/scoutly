<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceHistoryRequest;
use App\Models\PriceHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class PriceHistoryController extends Controller
{

    private $priceHistory;
    private $loggedUser;

    public function __construct(PriceHistory $priceHistory) {
        $this->priceHistory = $priceHistory;
        $this->loggedUser = auth()->user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {

        Gate::authorize('viewAny', PriceHistory::class);

        $priceHistory = $this->priceHistory->query()
            ->when($request->has('product_id'), fn ($query) => $query->orWhere('product_id', 'like', "%{$request['product_id']}%"))
            ->when($request->has('price'), fn ($query) => $query->orWhere('price', 'like', "%{$request['price']}%"))
            ->when($request->has('checked_at'), fn ($query) => $query->orWhere('checked_at', 'like', "%{$request['checked_at']}%"))
            ->orderBy('checked_at', 'desc')
            ->with('product')
            ->paginate((int) $request->per_page);

        return response()->json($priceHistory, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PriceHistoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $priceHistory = $this->priceHistory->create($data);

        return response()->json(['message' => 'Preço adicionado com sucesso', 'priceHistory' => $priceHistory], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id): JsonResponse
    {
        $priceHistory = $this->priceHistory->with('product')->findOrFail($id);

        Gate::authorize('view', $priceHistory);

        return response()->json(['message' => 'Preço encontrado', 'priceHistory' => $priceHistory], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PriceHistoryRequest $request, String $id): JsonResponse
    {
        $data = $request->validated();
        $priceHistory = $this->priceHistory->with('product')->findOrFail($id);

        Gate::authorize('update', $priceHistory);

        $priceHistory->update($data);
        return response()->json(['message' => 'Preço atualizado com sucesso', 'priceHistory' => $priceHistory], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id): JsonResponse
    {
        $priceHistory = $this->priceHistory->with('product')->findOrFail($id);

        Gate::authorize('delete', $priceHistory);

        $priceHistory->delete();
        return response()->json(['message' => 'Preço removido com sucesso', 'priceHistory' => $priceHistory], Response::HTTP_OK);
    }
}

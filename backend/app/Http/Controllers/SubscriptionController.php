<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{

    private $subscription;
    private $loggedUser;

    public function __construct(Subscription $subscription) {
        $this->subscription = $subscription;
        $this->loggedUser = auth()->user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $subscription = $this->subscription->query()
        ->when($request->has('name'), fn ($query) => $query->orWhere('name', 'like', "%{$request['name']}%"))
        ->when($request->has('email'), fn ($query) => $query->orWhere('email', 'like', "%{$request['email']}%"))
        ->when($request->has('plan'), fn ($query) => $query->orWhere('plan', 'like', "%{$request['plan']}%"))
        ->when($request->has('active_until'), fn ($query) => $query->orWhere('active_until', 'like', "%{$request['active_until']}%"))
        ->orderBy('created_at', 'desc')
        ->with('user')
        ->paginate((int) $request->per_page);

        return response()->json($subscription, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubscriptionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $subscription = $this->subscription->create($data);

        return response()->json(['message' => 'Assinatura criada com sucesso', 'subscription' => $subscription], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id): JsonResponse
    {
        $subscription = $this->subscription->findOrFail($id);

        if($this->loggedUser->id === $subscription->user_id || $this->loggedUser->role === User::USER_ADMIN) {
            return response()->json(['message' => 'Inscrição encontrada.', 'subscription' => $subscription], Response::HTTP_OK);
        }

        return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_FORBIDDEN);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubscriptionRequest $request, String $id): JsonResponse
    {
        $data = $request->validated();
        $subscription = $this->subscription->findOrFail($id);

        if($this->loggedUser->id === $subscription->user_id || $this->loggedUser->role === User::USER_ADMIN) {
            $subscription->update($data);
            return response()->json(['message' => 'Assinatura atualizada com sucesso', 'subscription' => $subscription], Response::HTTP_OK);
        }

        return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_FORBIDDEN);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id): JsonResponse
    {
        $subscription = $this->subscription->findOrFail($id);

        if($this->loggedUser->id === $subscription->user_id || $this->loggedUser->role === User::USER_ADMIN) {
            $subscription->delete();
            return response()->json(['message' => 'Assinatura cancelada com sucesso', 'subscription' => $subscription], Response::HTTP_OK);
        }

        return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_FORBIDDEN);
    }
}

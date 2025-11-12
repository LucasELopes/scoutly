<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
/**
* @OA\Schema(
*     schema="User",
*     type="object",
*     required={"name", "email", "password"},
*     @OA\Property(property="name", type="string", example="Teste"),
*     @OA\Property(property="email", type="string", example="teste@sec-esportes.com"),
*     @OA\Property(property="cpf", type="string", example="123.456.111-00"),
*     @OA\Property(property="password", type="string", nullable=true, example="password"),
*     @OA\Property(property="phone", type="string", example="(11) 99999-1111"),
*     @OA\Property(property="role", type="string", example="user"),
*     @OA\Property(property="image", type="string", format="binary", description="Arquivo de imagem do usuário")
* )
*/
class UserController extends Controller
{

    private User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
    *  @OA\Get(
    *      path="/api/users",
    *      summary="Lista de usuários",
    *      description="Ver todos os usuários",
    *      tags={"Usuários"},
    *      security={{"bearerAuth": {}}},
    *      @OA\Response(
    *          response=200,
    *          description="OK",
    *          @OA\JsonContent(
    *              type="array",
    *              @OA\Items(
    *                    type="object",
    *                    @OA\Property(property="id", type="string", format="uuid"),
    *                    @OA\Property(property="name", type="string"),
    *                    @OA\Property(property="email", type="string"),
    *                    @OA\Property(property="role", type="string"),
    *                    @OA\Property(property="email_verified_at", type="string", format="date"),
    *                    @OA\Property(property="image", type="string"),
    *                    @OA\Property(property="last_updated_by", type="string", format="uuid"),
    *                    @OA\Property(property="created_at", type="string", format="date"),
    *                    @OA\Property(property="updated_at", type="string", format="date"),
    *                    @OA\Property(property="deleted_at", type="string", format="date")
    *              ),
    *          )
    *      )
    *  )
    */
    public function index(Request $request)
    {
        $users = $this->user->query()
        ->when($request->has('name'), fn ($query) => $query->orWhere('name', 'like', "%{$request['name']}%"))
        ->when($request->has('email'), fn ($query) => $query->orWhere('email', 'like', "%{$request['email']}%"))
        ->orderBy('created_at', 'desc')
        ->paginate((int) $request->per_page);

        return response()->json($users, Response::HTTP_OK);
    }

    /**
    * @OA\Post(
    *      path="/api/users",
    *      summary="Criação de usuário",
    *      description="Cria um novo usuário",
    *      tags={"Usuários"},
    *      security={{"bearerAuth": {}}},
    *      @OA\RequestBody(
    *          required=true,
    *          @OA\MediaType(
    *               mediaType="multipart/form-data",
    *               @OA\Schema(
    *                    required={"name", "email", "password", "password_confirmation"},
    *                    @OA\Property(property="name", type="string", example="Shrek"),
    *                    @OA\Property(property="email", type="string", example="Shrek@gmail.com"),
    *                    @OA\Property(property="password", type="string", example="@Ss12345678"),
    *                    @OA\Property(property="password_confirmation", type="string", example="@Ss12345678"),
    *                    @OA\Property(property="role", type="string", example="admin"),
    *                    @OA\Property(property="image", type="string", format="binary", nullable=true),
    *               ),
    *           ),
    *          @OA\MediaType(
    *              mediaType="application/json",
    *              @OA\Schema(
    *                  schema="User",
    *                  type="object",
    *                  required={"name", "email", "password", "password_confirmation"},
    *                  @OA\Property(property="name", type="string", example="Shrek"),
    *                  @OA\Property(property="email", type="string", example="Shrek@gmail.com"),
    *                  @OA\Property(property="password", type="string", example="@Ss12345678"),
    *                  @OA\Property(property="password_confirmation", type="string", example="@Ss12345678"),
    *                  @OA\Property(property="role", type="string", example="admin"),
    *                  @OA\Property(property="image", type="string", format="binary", nullable=true),
    *              ),
    *          ),
    *      ),
    *      @OA\Response(
    *          response=201,
    *          description="Usuário criado com sucesso",
    *      @OA\JsonContent(
    *              @OA\Property(property="message", type="string", example="Usuário criado com sucesso"),
    *              @OA\Property(property="space", type="object",
    *                  @OA\Property(property="id", type="string", format="uuid"),
    *                  @OA\Property(property="name", type="string", example="Shrek"),
    *                  @OA\Property(property="email", type="string", example="Shrek@gmail.com"),
    *                  @OA\Property(property="imuserage", type="string", format="binary", nullable=true),
    *                  @OA\Property(property="created_at", type="string"),
    *                  @OA\Property(property="updated_at", type="string"),
    *              )
    *          )
    *      ),
    *      @OA\Response(
    *           response=422,
    *           description="Erro de validação",
    *       )
    *  )
    */
    public function store(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        if($request->hasFile('image')) {
            $path = $request->file('image')->store('image', 'public');
            $data['image'] = url('storage/'.$path);
        }

        $user = $this->user->create($data);
        return response()->json(['message' => 'Usuário criado com sucesso', 'user' => $user], Response::HTTP_CREATED);
    }

    /**
    * @OA\Get(
    *      path="/api/users/{user}",
    *      summary="Obter detalhes do usuário",
    *      description="Exibir um usuário específico",
    *      tags={"Usuários"},
    *      security={{"bearerAuth": {}}},
    *      @OA\Parameter(
    *          name="user",
    *          in="path",
    *          required=true,
    *          @OA\Schema(type="string", format="uuid")
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="OK",
    *          @OA\JsonContent(
    *               type="array",
    *               @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="string"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="email", type="string"),
    *                     @OA\Property(property="role", type="string"),
    *                     @OA\Property(property="email_verified_at", type="string"),
    *                     @OA\Property(property="image", type="string"),
    *                     @OA\Property(property="last_updated_by", type="string"),
    *                     @OA\Property(property="created_at", type="string"),
    *                     @OA\Property(property="updated_at", type="string"),
    *                     @OA\Property(property="deleted_at", type="string"),
    *              ),
    *          )
    *      )
    *  )
    */
    public function show(User $user): JsonResponse
    {
        $loggedUser = auth()->user();

        if (Auth::user()->role !== 'admin' && $loggedUser['email'] !== $user['email']) {
            return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json($user, Response::HTTP_OK);
    }

    /**
    * @OA\Put(
    *      path="/api/users/{user}",
    *      summary="Atualizar usuário",
    *      description="Atualiza os dados de um usuário",
    *      tags={"Usuários"},
    *      security={{"bearerAuth": {}}},
    *      @OA\Parameter(
    *          name="user",
    *          in="path",
    *          required=true,
    *          @OA\Schema(type="string", format="uuid")
    *      ),
    *      @OA\RequestBody(
    *          required=true,
    *          @OA\MediaType(
    *              mediaType="application/json",
    *              @OA\Schema(
    *                  schema="User",
    *                  type="object",
    *                  @OA\Property(property="name", type="string", example="Shrek2"),
    *                  @OA\Property(property="password", type="string", example="@Ss12345678"),
    *                  @OA\Property(property="password_confirmation", type="string", example="@Ss12345678"),
    *                  @OA\Property(property="role", type="string", example="admin"),
    *              ),
    *          ),
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Usuário atualizado com sucesso",
    *      @OA\JsonContent(
    *               type="array",
    *               @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="id", type="string"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="email", type="string"),
    *                     @OA\Property(property="role", type="string"),
    *                     @OA\Property(property="email_verified_at", type="string"),
    *                     @OA\Property(property="image", type="string"),
    *                     @OA\Property(property="last_updated_by", type="string"),
    *                     @OA\Property(property="created_at", type="string"),
    *                     @OA\Property(property="updated_at", type="string"),
    *                     @OA\Property(property="deleted_at", type="string"),
    *              ),
    *          )
    *      )
    *  )
    */
    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        $loggedUser = auth()->user();

        if (Auth::user()->role !== 'admin' && $loggedUser['email'] !== $user['email']) {
            return response()->json(['error' => 'Acesso não autorizado'], Response::HTTP_UNAUTHORIZED);
        }

        if ($loggedUser->role !== 'admin') {
            unset($data['role']);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('image')) {
            try {
                if($user['image']) {
                    $image_name = explode('image/', $user['image']);
                    Storage::disk('public')->delete('image/'.$image_name[1]);
                }
            } catch (\Throwable) {
            } finally {
                $path = $request->file('image')->store('image', 'public');
                $data['image'] = url('storage/'.$path);
            }
        }

        $user->update($data);

        return response()->json(['message' => 'Usuário atualizado com sucesso', 'user' => $user], Response::HTTP_OK);
    }

    /**
    * @OA\Delete(
    *      path="/api/users/{user}",
    *      summary="Excluir usuário logicamente",
    *      description="Exclui um usuário de forma lógica",
    *      tags={"Usuários"},
    *      security={{"bearerAuth": {}}},
    *      @OA\Parameter(
    *          name="user",
    *          in="path",
    *          required=true,
    *          @OA\Schema(type="string", format="uuid")
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Usuário excluído com sucesso",
    *      )
    *  )
    */
    public function destroy(User $user)
    {
        $loggedUser = auth()->user();

        if ($loggedUser->email === $user->email) {
            return response()->json(['message' => 'Usuários não podem se auto-deletar.'], Response::HTTP_FORBIDDEN);
        }

        $user->delete();

        return response()->json(['message' => 'Usuário deletado com sucesso', 'user' => $user], Response::HTTP_OK);
    }

    /**
    * @OA\Delete(
    *      path="/api/users/hard-delete/{user}",
    *      summary="Excluir usuário físicamente",
    *      description="Exclui um usuário de forma física",
    *      tags={"Usuários"},
    *      security={{"bearerAuth": {}}},
    *      @OA\Parameter(
    *          name="user",
    *          in="path",
    *          required=true,
    *          @OA\Schema(type="string", format="uuid")
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Usuário removido com sucesso",
    *            @OA\JsonContent(
    *               type="array",
    *               @OA\Items(
    *                     type="object",
    *                     @OA\Property(propertyMiss Serenity Bins="id", type="string"),
    *                     @OA\Property(property="name", type="string"),
    *                     @OA\Property(property="email", type="string"),
    *                     @OA\Property(property="role", type="string"),
    *                     @OA\Property(property="email_verified_at", type="string"),
    *                     @OA\Property(property="image", type="string"),
    *                     @OA\Property(property="last_updated_by", type="string"),
    *                     @OA\Property(property="created_at", type="string"),
    *                     @OA\Property(property="updated_at", type="string"),
    *                     @OA\Property(property="deleted_at", type="string"),
    *              ),
    *          )
    *      )
    *  )
    */
    public function hardDelete(String $id): JsonResponse
    {
        $loggedUser = auth()->user();
        $user = $this->user->withTrashed()->findOrFail($id);

        if ($loggedUser['email'] === $user->email) {
            return response()->json(['message' => 'Usuários não podem se auto-deletar.'], Response::HTTP_FORBIDDEN);
        }

        $user->forceDelete();
        return response()->json(['message' => 'Usuários deletado com sucesso', 'user' => $user], Response::HTTP_OK);
    }

    /**
    * @OA\Put(
    *      path="/api/users/reset-password/{email}",
    *      summary="Reseta a senha do usuário e define uma padrão",
    *      description="Atualiza as senha do usuário",
    *      tags={"Usuários"},
    *      security={{"bearerAuth": {}}},
    *      @OA\Parameter(
    *          name="Email",
    *          in="path",
    *          required=true,
    *          @OA\Schema(type="string", format="email")
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Senha resetada com sucesso",
    *      @OA\JsonContent(
    *               type="array",
    *               @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="message", type="string"),
    *              ),
    *          )
    *      )
    *  )
    */
    // public function resetPassword(String $email): JsonResponse
    // {
    //     $validator = Validator::make(
    //         ['email' => $email],
    //         [
    //             'email' => ['required', 'email', 'exists:users,email'],
    //         ],
    //     [
    //             'email.required' => 'O campo e-mail é obrigatório.',
    //             'email.email' => 'O e-mail informado não é válido.',
    //             'email.exists' => 'Nenhum usuário encontrado com esse e-mail.',
    //         ]
    //     );

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
    //     }

    //     $user = $this->user->where('email', $email)->firstOrFail();
    //     $user->password = Hash::make("Pmsm2020");
    //     $user->save();

    //     return response()->json(['message' => 'Senha resetada com sucesso'], Response::HTTP_OK);
    // }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Usuário autenticado atual
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Se o usuário é admin
     *
     * @var bool
     */
    protected $isAdmin;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->isAdmin = $this->user && $this->user->user_type === 'admin';
            
            view()->share('currentUser', $this->user);
            view()->share('isAdmin', $this->isAdmin);

            return $next($request);
        });
    }

    /**
     * Resposta de sucesso padrão
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Resposta de erro padrão
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message = null, $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    /**
     * Resposta de não autorizado
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorizedResponse($message = 'Acesso não autorizado')
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Resposta de recurso não encontrado
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notFoundResponse($message = 'Recurso não encontrado')
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Verifica se o usuário tem permissão
     *
     * @param string $permission
     * @return bool
     */
    protected function userHasPermission($permission)
    {
        if (!$this->user) {
            return false;
        }

        // Implemente sua lógica de permissões aqui
        // Exemplo: return $this->user->hasPermission($permission);
        return true;
    }
}
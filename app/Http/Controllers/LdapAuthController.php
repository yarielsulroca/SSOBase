<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\LdapUser;
use App\Services\LdapService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LdapAuthController extends Controller
{
    private LdapService $ldapService;
    private const TOKEN_EXPIRATION = 3600; // 1 hora en segundos

    public function __construct(LdapService $ldapService)
    {
        $this->ldapService = $ldapService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $userData = $this->ldapService->authenticate(
                $request->input('username'),
                $request->input('password')
            );

            if (!$userData) {
                return response()->json([
                    'message' => 'Credenciales inválidas'
                ], 401);
            }

            // Crear usuario LDAP
            $user = new LdapUser($userData);
            
            // Generar token JWT
            $token = JWTAuth::fromUser($user, [
                'exp' => time() + self::TOKEN_EXPIRATION,
                'username' => $userData['username'],
                'email' => $userData['email']
            ]);

            Log::info('Token JWT generado', [
                'username' => $userData['username']
            ]);

            return response()->json([
                'message' => 'Usuario autenticado exitosamente',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => self::TOKEN_EXPIRATION,
                'user' => $userData
            ]);

        } catch (\Exception $e) {
            Log::error('Error en autenticación LDAP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error en el servidor'
            ], 500);
        }
    }

    public function check(Request $request): JsonResponse
    {
        try {
            $token = $request->header('Authorization');
            if (!$token) {
                Log::warning('No se proporcionó token en la petición');
                return response()->json([
                    'status' => 'error',
                    'authenticated' => false,
                    'user' => null
                ]);
            }

            $token = str_replace('Bearer ', '', $token);
            Log::info('Validando token JWT', ['token' => $token]);

            try {
                $payload = JWTAuth::setToken($token)->getPayload();
                $userData = [
                    'username' => $payload->get('username'),
                    'email' => $payload->get('email')
                ];

                Log::info('Token JWT válido', [
                    'username' => $userData['username']
                ]);

                return response()->json([
                    'status' => 'success',
                    'authenticated' => true,
                    'user' => $userData
                ]);

            } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $e) {
                Log::warning('Token JWT expirado');
                return response()->json([
                    'status' => 'error',
                    'authenticated' => false,
                    'message' => 'Token expirado',
                    'user' => null
                ]);
            } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e) {
                Log::warning('Token JWT inválido');
                return response()->json([
                    'status' => 'error',
                    'authenticated' => false,
                    'message' => 'Token inválido',
                    'user' => null
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al validar token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'authenticated' => false,
                'message' => 'Error al validar token',
                'user' => null
            ]);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->header('Authorization');
            if ($token) {
                $token = str_replace('Bearer ', '', $token);
                JWTAuth::setToken($token)->invalidate();
                
                Log::info('Token JWT invalidado exitosamente');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Sesión cerrada exitosamente'
            ]);

        } catch (Exception $e) {
            Log::error('Error al cerrar sesión', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error al cerrar sesión'
            ], 500);
        }
    }

    private function successResponse($userInfo): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Usuario autenticado exitosamente',
            'data' => [
                'token' => $userInfo['token'],
                'user' => [
                    'username' => $userInfo['user']->username,
                    'email' => $userInfo['user']->email,
                    'domain' => $userInfo['user']->domain,
                    'name' => $userInfo['user']->name,
                    'displayName' => $userInfo['user']->displayName,
                    'givenName' => $userInfo['user']->givenName,
                    'surname' => $userInfo['user']->surname,
                    'department' => $userInfo['user']->department,
                    'title' => $userInfo['user']->title,
                    'company' => $userInfo['user']->company,
                    'manager' => $userInfo['user']->manager,
                    'employeeID' => $userInfo['user']->employeeID,
                    'employeeNumber' => $userInfo['user']->employeeNumber,
                    'employeeType' => $userInfo['user']->employeeType,
                    'division' => $userInfo['user']->division,
                    'office' => $userInfo['user']->office,
                    'telephoneNumber' => $userInfo['user']->telephoneNumber,
                    'mobile' => $userInfo['user']->mobile,
                    'pager' => $userInfo['user']->pager,
                    'street' => $userInfo['user']->street,
                    'city' => $userInfo['user']->city,
                    'state' => $userInfo['user']->state,
                    'postalCode' => $userInfo['user']->postalCode,
                    'country' => $userInfo['user']->country,
                    'description' => $userInfo['user']->description,
                    'whenCreated' => $userInfo['user']->whenCreated,
                    'whenChanged' => $userInfo['user']->whenChanged,
                    'lastLogon' => $userInfo['user']->lastLogon,
                    'accountExpires' => $userInfo['user']->accountExpires,
                    'userAccountControl' => $userInfo['user']->userAccountControl,
                    'groups' => $userInfo['user']->groups
                ]
            ]
        ]);
    }

    private function errorResponse(string $message, string $details, int $status): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'details' => $details
        ], $status);
    }
}

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

class LdapAuthController extends Controller
{
    private LdapService $ldapService;

    public function __construct(LdapService $ldapService)
    {
        $this->ldapService = $ldapService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $userInfo = $this->ldapService->authenticate(
                $credentials['username'],
                $credentials['password']
            );

            if ($userInfo) {
                // Generar token
                $token = bin2hex(random_bytes(32));
                $userInfo->setAttribute('api_token', $token);
                $userInfo->save();
                return $this->successResponse(['token' => $token, 'user' => $userInfo]);
            }

            return $this->errorResponse(
                'Invalid credentials',
                'Authentication failed for all available domains',
                401
            );

        } catch (Exception $e) {
            Log::error('LDAP Authentication Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Authentication error',
                $e->getMessage(),
                500
            );
        }
    }

    private function successResponse($userInfo): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'User authenticated successfully',
            'user' => $userInfo
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

    public function check(Request $request): JsonResponse
    {
        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'authenticated' => false,
                'user' => null
            ]);
        }

        $token = str_replace('Bearer ', '', $token);
        $user = LdapUser::findByToken($token);

        return response()->json([
            'status' => $user ? 'success' : 'error',
            'authenticated' => (bool) $user,
            'user' => $user
        ]);
    }
}

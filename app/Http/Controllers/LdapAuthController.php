<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LdapRecord\Container;
use Illuminate\Support\Facades\Auth;

class LdapAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        try {
            $connection = Container::getConnection('default');
            
            // Search for user
            $user = $connection->query()
                ->where('samaccountname', '=', $credentials['username'])
                ->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                    'redirect' => route('login')
                ], 404);
            }
            
            // Attempt to authenticate user
            if ($connection->auth()->attempt($user->getDn(), $credentials['password'])) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User authenticated successfully',
                    'user' => [
                        'username' => $user->getFirstAttribute('samaccountname'),
                        'name' => $user->getFirstAttribute('cn'),
                        'email' => $user->getFirstAttribute('mail'),
                    ]
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'redirect' => route('login')
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication error: ' . $e->getMessage(),
                'redirect' => route('login')
            ], 500);
        }
    }

    public function check()
    {
        if (Auth::check()) {
            return response()->json([
                'status' => 'success',
                'authenticated' => true,
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'status' => 'error',
            'authenticated' => false,
            'redirect' => route('login')
        ]);
    }
}

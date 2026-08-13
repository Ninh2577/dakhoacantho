<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WpBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $validUsername = ''admin_dakhoa'';
        $validPassword = ''mat_khau_cua_ban_123'';

        if ($request->getUser() !== $validUsername || $request->getPassword() !== $validPassword) {
            return response()->json([
                ''code'' => ''rest_not_logged_in'',
                ''message'' => ''Lỗi xác thực (401). Sai Username hoặc Password.'',
                ''data'' => [''status'' => 401]
            ], 401);
        }

        return $next($request);
    }
}

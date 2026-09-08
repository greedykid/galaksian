<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceMiddleware
{
    /**
     * Batasi aksi keuangan hanya untuk ADMIN / SUPER_ADMIN.
     * CS boleh melihat data (via middleware admin), tapi tidak boleh
     * menyetujui refund, membuat invoice, atau mengubah status order
     * yang berdampak keuangan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, [UserRole::ADMIN, UserRole::SUPER_ADMIN], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Aksi ini hanya untuk admin keuangan.',
            ], 403);
        }

        return $next($request);
    }
}

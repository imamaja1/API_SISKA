<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnsureRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $allowedRoles = $this->normalizeRoles($roles);
        if ($allowedRoles === []) {
            return $next($request);
        }

        $userRole = $this->resolveUserRoleName($user);
        if ($userRole === null) {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $userRole = mb_strtolower(trim($userRole));

        foreach ($allowedRoles as $allowedRole) {
            if ($userRole === $allowedRole) {
                return $next($request);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Forbidden',
        ], 403);
    }

    /**
     * @param  array<int,string>  $roles
     * @return array<int,string>
     */
    private function normalizeRoles(array $roles): array
    {
        $normalized = [];

        foreach ($roles as $roleArg) {
            if ($roleArg === '') {
                continue;
            }

            // Supports: role:akademik|keuangan (as single arg)
            $parts = preg_split('/[|]/', $roleArg) ?: [];

            foreach ($parts as $part) {
                $part = mb_strtolower(trim($part));
                if ($part !== '') {
                    $normalized[] = $part;
                }
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * Resolve role name from the authenticated user.
     */
    private function resolveUserRoleName(object $user): ?string
    {
        // If the user model exposes a role name directly.
        if (isset($user->role) && is_string($user->role) && $user->role !== '') {
            return $user->role;
        }

        // If the user model has id_role, resolve it from the 'role' table.
        if (isset($user->id_role) && is_numeric($user->id_role)) {
            $roleName = DB::table('role')
                ->where('id_role', (int) $user->id_role)
                ->value('nama_role');

            return is_string($roleName) ? $roleName : null;
        }

        return null;
    }
}

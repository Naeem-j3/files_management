<?php

namespace App\Http\Middleware;

use App\Models\GroupUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckGroupMembership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::id();
        $groupId = $request->route('groupId');

        // Check if the authenticated user is a member of the specified group
        $isMember = GroupUser::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'accepted') // Ensure the user is an accepted member
            ->exists();

        if (!$isMember) {
            return response()->json([
                'status' => false,
                'message' => 'You do not belong to this group.',
            ], 403);
        }

        return $next($request);
    }
}

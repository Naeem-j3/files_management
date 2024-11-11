<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGroupOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $groupId = $request->route('groupId'); // Assuming groupId is a route parameter
        $userId = $request->user()->id; // Authenticated user's ID
        // Check if the authenticated user is the owner of the group
        $group = Group::find($groupId);
        if (!$group || $group->owner_id !== $userId) {
            return response()->json([
                'status' => false,
                'message' => 'Only the group owner can perform this action.'
            ], 403);
        }

        return $next($request); // Allow request to proceed if the user is the owner
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserBan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AdminActivity;

class AdminBanController extends Controller
{
    public function store(Request $request, User $user)
    {
        // Prevent banning admins or super admins
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot ban admin or super admin users.',
            ], 403);
        }

        $request->validate([
            'type'     => 'required|in:permanent,temporary',
            'duration' => 'required_if:type,temporary|in:1_day,1_week,1_month',
            'reason'   => 'required|string|max:500',
        ]);

        // Deactivate any existing bans
        UserBan::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $bannedUntil = null;

        if ($request->type === 'temporary') {
            $bannedUntil = match($request->duration) {
                '1_day'   => Carbon::now()->addDay(),
                '1_week'  => Carbon::now()->addWeek(),
                '1_month' => Carbon::now()->addMonth(),
                default   => null,
            };
        }

        $ban = UserBan::create([
            'user_id'      => $user->id,
            'banned_by'    => auth()->id(),
            'type'         => $request->type,
            'banned_until' => $bannedUntil,
            'reason'       => $request->reason,
            'is_active'    => true,
        ]);

            AdminActivity::log(
                    'ban_user',
                    auth()->user()->first_name . ' banned ' . $user->first_name . ' ' . $user->last_name,
                    [
                        'user_id'      => $user->id,
                        'user_name'    => $user->first_name . ' ' . $user->last_name,
                        'ban_type'     => $request->type,
                        'ban_duration' => $request->duration ?? 'permanent',
                        'reason'       => $request->reason,
                    ]
                );
    

        return response()->json([
            'success' => true,
            'message' => 'User has been banned successfully.',
            'ban'     => $ban,
        ]);
    }

    public function destroy(User $user)
    {
        UserBan::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);


            AdminActivity::log(
        'unban_user',
        auth()->user()->first_name . ' unbanned ' . $user->first_name . ' ' . $user->last_name,
        [
            'user_id'   => $user->id,
            'user_name' => $user->first_name . ' ' . $user->last_name,
        ]
    );

        return response()->json([
            'success' => true,
            'message' => 'Ban has been lifted.',
        ]);
    }
}
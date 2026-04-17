<?php

namespace App\Policies;

use App\Models\User;

class LeaveRequestPolicy
{
    /**
     * Create a new policy instance.
     */
    public function view(User $user, LeaveRequest $leave): bool
    {
        // User hanya boleh lihat miliknya sendiri
        if ($user->id === $leave->user_id) {
            return true;
        }

        // Admin boleh semua
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }
}

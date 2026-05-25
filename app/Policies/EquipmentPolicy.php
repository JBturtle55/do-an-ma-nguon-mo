<?php

namespace App\Policies;

use App\Models\Equipment;
use App\Models\User;

class EquipmentPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function manage(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Equipment $equipment): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Equipment $equipment): bool
    {
        return $user->hasRole('admin');
    }
}

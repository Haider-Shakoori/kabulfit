<?php

namespace App\Policies;

use App\Models\MeasurementDefinition;
use App\Models\User;

class MeasurementDefinitionPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('measurements.manage'); }
    public function view(User $user, MeasurementDefinition $definition): bool { return $this->viewAny($user); }
    public function update(User $user, MeasurementDefinition $definition): bool { return $this->viewAny($user); }
}

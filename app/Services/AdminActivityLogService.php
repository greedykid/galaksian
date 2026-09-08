<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLogService
{
    public function log(
        User $admin,
        string $action,
        string $description,
        ?Model $subject = null,
        ?array $properties = null,
        ?string $ipAddress = null
    ): AdminActivityLog {
        return AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'properties' => $properties,
            'ip_address' => $ipAddress,
        ]);
    }
}

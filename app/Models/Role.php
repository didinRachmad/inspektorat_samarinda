<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory, SoftDeletes;

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_has_permissions')
            ->withPivot('permission_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'route', 'parent_id', 'icon', 'order'];
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order')->with('children');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions')
            ->withPivot('permission_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'role_key',
        'role_name',
        'role_type',
        'description',
        'is_system',
        'is_active',
    ];

    public $timestamps = false;

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}


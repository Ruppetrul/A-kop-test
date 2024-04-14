<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCompany extends Pivot
{
    protected $table = 'users_companies';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'company_id',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_companies_roles', 'user_company_id', 'role_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRoleCompany extends Pivot
{
    protected $table = 'users_companies_roles';

    protected $fillable = [
        'user',
        'company',
        'role',
    ];
}

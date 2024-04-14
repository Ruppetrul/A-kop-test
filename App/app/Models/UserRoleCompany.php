<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRoleCompany extends Pivot
{
    protected $table = 'users_companies_roles';

    public $timestamps = false;

    protected $fillable = [
        'user_company_id',
        'role_id',
    ];

    public function companiesRoles()
    {
        return $this->belongsTo(UserCompany::class);
    }
}

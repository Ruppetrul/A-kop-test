<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_companies_roles')->using(UserRoleCompany::class)->withPivot('role_id');
    }
}

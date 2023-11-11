<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Model
{
    use HasFactory;
    use Authorizable;
    use HasRoles;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin($super = false)
    {
        /**
         * Is this user admin or super admin?
         */

        // Check this user actually has a role
        if ($this->role()) {
            return $super ? $this->role()->name == 'Super Admin' : in_array($this->role()->name, ['Admin', 'Super Admin']);
        }

        return false;
    }

}

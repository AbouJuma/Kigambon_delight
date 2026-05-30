<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    protected $guarded = ['id'];
    protected $fillable = array('name','status', 'label', 'description');

    protected static function boot()
    {
        parent::boot();

        static::created(function ($role) {
            // Automatically assign Sales_pos permission to new roles
            $salesPosPermission = DB::table('permissions')->where('name', 'Sales_pos')->first();
            
            if ($salesPosPermission) {
                DB::table('permission_role')->insert([
                    'permission_id' => $salesPosPermission->id,
                    'role_id' => $role->id,
                ]);
            }
        });

        static::deleted(function ($role) {
            // Clean up permission assignments when role is deleted
            DB::table('permission_role')->where('role_id', $role->id)->delete();
        });
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermissionTo(Permission $permission)
    {
        return $this->permissions()->save($permission);
    }
    /**
     * Determine if the user may perform the given permission.
     *
     * @param  Permission $permission
     * @return boolean
     */
    public function hasPermission(Permission $permission, User $user)
    {
        return $this->hasRole($permission->roles);
    }
    /**
     * Determine if the role has the given permission.
     *
     * @param  mixed $permission
     * @return boolean
     */
    public function inRole($permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }
        return !!$permission->intersect($this->permissions)->count();
    }
}

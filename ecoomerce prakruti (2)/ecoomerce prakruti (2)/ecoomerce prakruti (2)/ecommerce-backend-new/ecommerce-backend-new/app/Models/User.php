<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'role_id', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, \App\Traits\HasApiTokens;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function familyPacks(): HasMany
    {
        return $this->hasMany(FamilyPack::class);
    }

    public function getRoleNameAttribute(): ?string
    {
        return $this->role?->name ?? Role::where('id', $this->role_id)->value('name');
    }

    public function isSuperAdmin(): bool
    {
        if (strtolower(trim((string) $this->status)) !== 'active') {
            return false;
        }

        $roleName = strtolower((string) $this->role_name);

        return in_array($roleName, ['super admin', 'super_admin', 'superadmin'], true);
    }

    public function isAdmin(): bool
    {
        if (strtolower(trim((string) $this->status)) !== 'active') {
            return false;
        }

        $roleName = strtolower((string) $this->role_name);

        return in_array($roleName, ['super admin', 'super_admin', 'superadmin', 'admin'], true);
    }

    public function isCustomer(): bool
    {
        if (strtolower(trim((string) $this->status)) !== 'active') {
            return false;
        }

        $roleName = strtolower((string) $this->role_name);

        return $roleName === 'customer';
    }

    public function hasRole(string|array $roles): bool
    {
        if (strtolower(trim((string) $this->status)) !== 'active') {
            return false;
        }

        $roleName = $this->role?->name;
        if (! $roleName) {
            return false;
        }

        if (is_string($roles)) {
            return strcasecmp($roleName, $roles) === 0;
        }

        foreach ((array) $roles as $r) {
            if (strcasecmp($roleName, (string) $r) === 0) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function hasPermission(string $permission): bool
    {
        if (strtolower(trim((string) $this->status)) !== 'active') {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->role) {
            return false;
        }

        return $this->role->permissions->contains('name', $permission);
    }
}

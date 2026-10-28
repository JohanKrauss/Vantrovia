<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPasswordNotification;

#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(
            new ResetPasswordNotification($token)
        );
    }
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_platform_admin' => 'boolean',
        ];
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class)
            ->withPivot([
                'id',
                'role_id',
                'is_active',
            ])
            ->withTimestamps();
    }

    public function isPlatformAdmin(): bool
    {
        return $this->is_active
            && $this->is_platform_admin;
    }

    public function hasBusinessPermission(
        Business|int $business,
        string $permission
    ): bool {
        if ($this->isPlatformAdmin()) {
            return true;
        }

        if (! $this->is_active) {
            return false;
        }

        $businessId = $business instanceof Business
            ? $business->id
            : $business;

        if ($business instanceof Business) {
            if ($business->status !== 'active') {
                return false;
            }
        }

        $membership = DB::table('business_user')
            ->where('business_id', $businessId)
            ->where('user_id', $this->id)
            ->where('is_active', true)
            ->first([
                'role_id',
            ]);

        if (
            ! $membership ||
            ! $membership->role_id
        ) {
            return false;
        }

        return Role::query()
            ->where('id', $membership->role_id)
            ->where('business_id', $businessId)
            ->whereHas(
                'permissions',
                function ($query) use ($permission) {
                    $query->where(
                        'permissions.slug',
                        $permission
                    );
                }
            )
            ->exists();
    }
}

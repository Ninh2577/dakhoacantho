<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Setting;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        $customRoles = Setting::get('custom_roles', []);
        if (empty($customRoles)) {
            return in_array($this->role, ['doctor', 'editor']);
        }

        foreach ($customRoles as $role) {
            if (($role['slug'] ?? '') === $this->role) {
                return true;
            }
        }

        return in_array($this->role, ['doctor', 'editor']);
    }

    public function hasPermission(string $class): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        $permissions = Setting::get('role_permissions', []);
        if (isset($permissions[$this->role]) && is_array($permissions[$this->role])) {
            return in_array($class, $permissions[$this->role]);
        }

        // Fallback default permissions for legacy editor/doctor
        $defaultPermissions = [
            'doctor' => [
                \App\Filament\Resources\PatientResource::class,
                \App\Filament\Resources\ConsultationResource::class,
            ],
            'editor' => [
                \App\Filament\Resources\ArticleResource::class,
                \App\Filament\Resources\CategoryResource::class,
                \App\Filament\Resources\ArticleCommentResource::class,
                \App\Filament\Resources\MediaFileResource::class,
            ],
        ];

        if (isset($defaultPermissions[$this->role])) {
            return in_array($class, $defaultPermissions[$this->role]);
        }

        return false;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

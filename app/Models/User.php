<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'role_id',
        'active',
        'phone',
        'avatar',
        'created_by',
        'updated_by',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
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

    /**
     * Relasi ke model Kendaraan
     */
    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class);
    }

    /**
     * Relasi ke model Role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',   // <-- tambahkan ini
        'locked_until'      => 'datetime',   // <-- opsional
    ];
}

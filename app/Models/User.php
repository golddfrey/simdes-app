<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use App\Models\KepalaKeluarga;
use App\Models\Anggota;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * We allow meta as array/json so it can hold small profile bits.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kepala_keluarga_id',
        'meta',
    ];

    /**
     * The attributes that should be hidden for arrays / JSON.
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes casts.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Mutator: always hash password when set.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value): void
    {
        // If value already looks hashed (starts with $2y$) we avoid double-hashing.
        if ($value && (str_starts_with($value, '$2y$') || str_starts_with($value, '$argon2i$') || str_starts_with($value, '$argon2id$'))) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * Relationship: optional link to kepala_keluargas table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kepalaKeluarga()
    {
        return $this->belongsTo(KepalaKeluarga::class, 'kepala_keluarga_id');
    }

    /**
     * Helper: does this user "own" the provided anggota?
     * We define ownership as anggota.kepala_keluarga_id === user's kepala_keluarga_id.
     *
     * @param \App\Models\Anggota|int|null $anggota
     * @return bool
     */
    public function ownsAnggota($anggota): bool
    {
        if (is_null($anggota)) {
            return false;
        }

        $kkId = $this->kepala_keluarga_id;
        if (!$kkId) {
            return false;
        }

        $anggotaKkId = $anggota instanceof Anggota ? $anggota->kepala_keluarga_id : (int) $anggota;

        return (int) $kkId === (int) $anggotaKkId;
    }

    /**
     * Short helper to check whether the user can perform admin-level actions.
     * You can expand this later to permissions table or policy classes.
     *
     * @return bool
     */
    public function canAdmin(): bool
    {
        return $this->isAdmin();
    }
    public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function isKepalaKeluarga(): bool
{
    return $this->role === 'kepala_keluarga';
}
}

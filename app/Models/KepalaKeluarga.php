<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KepalaKeluarga extends Model
{
    protected $fillable = [
        'nik',
        'nama',
        'phone',
        'alamat',
        'desa_id',
        'rt',     // <-- pastikan ada
        'rw',     // <-- pastikan ada
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function anggotas()
    {
        return $this->hasMany(\App\Models\Anggota::class, 'kepala_keluarga_id');
    }

    public function desa()
    {
        return $this->belongsTo(\App\Models\Desa::class, 'desa_id');
    }
}

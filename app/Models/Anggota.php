<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $fillable = [
        'kepala_keluarga_id',
        'nik',
        'nama',
        'jenis_kelamin',
        'status_keluarga',
        'tempat_lahir',
        'tanggal_lahir',
        'hubungan',
    ];

    // Pastikan field tanggal_lahir selalu dikonversi jadi Carbon instance
    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function kepalaKeluarga()
    {
        return $this->belongsTo(KepalaKeluarga::class, 'kepala_keluarga_id');
    }
}

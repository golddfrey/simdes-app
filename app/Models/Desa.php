<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $fillable = ['nama','kecamatan_id','luas_m2','koordinat','potensi','jumlah_penduduk_cached'];

    protected $casts = [
        'potensi' => 'array',
    ];

    public function kepalaKeluargas()
    {
        return $this->hasMany(KepalaKeluarga::class);
    }
}

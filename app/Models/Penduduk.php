<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    use HasFactory;

    protected $table = 'penduduks'; // sesuaikan dengan nama tabel di DB
    protected $fillable = [
        'nama',
        'nik',
        'alamat',
        'tanggal_lahir',
        'jenis_kelamin',
        // tambah kolom lain jika ada
    ];
}

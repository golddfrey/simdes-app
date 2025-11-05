<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingAnggota extends Model
{
    use HasFactory;

    protected $table = 'pending_anggotas';

    // izinkan mass-assignment untuk kolom yang diperlukan
    protected $fillable = [
        'nik',
        'nama',
        'jenis_kelamin',
        'status_keluarga',
        'tempat_lahir',
        'tanggal_lahir',
        'kepala_keluarga_id',
        'status',
        'reviewed_by',
        'approved_at',
        'rejected_at',
        'alasan',
        'submitted_by',
        'data_json',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'approved_at'   => 'datetime',
        'rejected_at'   => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'data_json'     => 'array',
    ];

    // Relasi ke KepalaKeluarga
    public function kepalaKeluarga()
    {
        return $this->belongsTo(KepalaKeluarga::class);
    }

    // Relasi admin (user) yang memutuskan
    public function admin()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** Helper */
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
}

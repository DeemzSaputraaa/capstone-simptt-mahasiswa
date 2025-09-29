<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLegalisasi extends Model
{
    protected $table = 'form_legalisasi';

    protected $fillable = [
        // Tambahkan kolom-kolom yang bisa diisi mass assignment
        // Sesuaikan dengan struktur tabel di database
    ];

    public $timestamps = false; // Jika tabel tidak memiliki created_at dan updated_at

    // Relasi dengan tabel lain
    public function history()
    {
        return $this->hasMany(FormLegalisasiHistory::class);
    }

    public function status()
    {
        return $this->belongsTo(FormLegalisasiStatus::class);
    }
}

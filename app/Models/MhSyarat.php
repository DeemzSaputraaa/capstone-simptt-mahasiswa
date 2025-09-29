<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MhSyarat extends Model
{
    protected $table = 'mh_syarat';

    protected $fillable = [
        // Tambahkan kolom-kolom yang bisa diisi mass assignment
        // Sesuaikan dengan struktur tabel di database
    ];

    public $timestamps = false; // Jika tabel tidak memiliki created_at dan updated_at

    // Relasi dengan tabel lain
    public function isian()
    {
        return $this->hasMany(MhSyaratIsian::class);
    }

    public function jenis()
    {
        return $this->belongsTo(MhSyaratJenis::class);
    }

    public function paket()
    {
        return $this->belongsTo(MhSyaratPaket::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkValidasiIjazahMahasiswa extends Model
{
    protected $table = 'ak_validasi_ijazah_mahasiswa';

    protected $fillable = [
        // Tambahkan kolom-kolom yang bisa diisi mass assignment
    ];

    public $timestamps = false;

    // Relasi dengan comment
    public function comments()
    {
        return $this->hasMany(AkValidasiIjazahMahasiswaComment::class);
    }
}

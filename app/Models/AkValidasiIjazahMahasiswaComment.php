<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkValidasiIjazahMahasiswaComment extends Model
{
    protected $table = 'ak_validasi_ijazah_mahasiswa_comment';

    protected $fillable = [
        // Tambahkan kolom-kolom yang bisa diisi mass assignment
    ];

    public $timestamps = false;

    // Relasi dengan validasi ijazah
    public function validasiIjazah()
    {
        return $this->belongsTo(AkValidasiIjazahMahasiswa::class);
    }
}

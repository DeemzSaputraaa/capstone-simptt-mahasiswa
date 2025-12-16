<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkValidasiIjazahMahasiswa extends Model
{
    protected $table = 'ak_validasi_ijazah_mahasiswa';

    protected $primaryKey = 'kdvalidasiijazahmahasiswa';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'kdmahasiswa',
        'is_ijazah_validate',
        'is_transkrip_validate',
        'coment_transkrip',
        'coment_ijazah',
        'diambil_sendiri',
        'surat_kuasa',
        'tgl_diambil_ijazah',
    ];

    public $timestamps = false;

    // Relasi dengan comment
    public function comments()
    {
        return $this->hasMany(AkValidasiIjazahMahasiswaComment::class);
    }
}

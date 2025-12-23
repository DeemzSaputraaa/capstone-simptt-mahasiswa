<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLegalisasi extends Model
{
    protected $table = 'form_legalisasi';
    protected $primaryKey = 'kdlegalisasi';
    protected $keyType = 'int';
    public $incrementing = true;

    // Sesuaikan kolom dengan struktur tabel Anda
    protected $fillable = [
        'kdmahasiswa',
        'jumlah_legalisasi',
        'biaya_legalisasi',
        'alamat_kirim',
        'nama_penerima_legalisasi',
        'noresi',
        'idtagihan',
        'tgl_dikirim',
        'kdlegalisasi_sebelum',
        'telp_penerima',
        'comment',
    ];

    public $timestamps = true;
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
    const DELETED_AT = 'delete_at';

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

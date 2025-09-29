<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MhSyaratPaket extends Model
{
    protected $table = 'mh_syarat_paket';

    protected $fillable = [
        // Tambahkan kolom-kolom yang bisa diisi mass assignment
    ];

    public $timestamps = false;

    // Relasi dengan syarat
    public function syarat()
    {
        return $this->hasMany(MhSyarat::class);
    }
}

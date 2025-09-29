<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MhSyaratJenis extends Model
{
    protected $table = 'mh_syarat_jenis';

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

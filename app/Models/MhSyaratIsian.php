<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MhSyaratIsian extends Model
{
    protected $table = 'mh_syarat_isian';

    protected $fillable = [
        // Tambahkan kolom-kolom yang bisa diisi mass assignment
    ];

    public $timestamps = false;

    // Relasi dengan syarat
    public function syarat()
    {
        return $this->belongsTo(MhSyarat::class);
    }
}

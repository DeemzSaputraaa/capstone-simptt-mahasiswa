<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkPraYudisium extends Model
{
    protected $table = 'ak_pra_yudisium';

    protected $fillable = [
        // Tambahkan kolom-kolom yang bisa diisi mass assignment
        // Sesuaikan dengan struktur tabel di database
    ];

    public $timestamps = false; // Jika tabel tidak memiliki created_at dan updated_at
}

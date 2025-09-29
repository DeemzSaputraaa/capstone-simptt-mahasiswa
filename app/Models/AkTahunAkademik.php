<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkTahunAkademik extends Model
{
    protected $table = 'ak_tahunakademik';

    protected $fillable = [
        // Tambahkan kolom-kolom yang bisa diisi mass assignment
    ];

    public $timestamps = false;
}

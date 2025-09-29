<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLegalisasiHistory extends Model
{
    protected $table = 'form_legalisasi_history';

    protected $fillable = [
        // Tambahkan kolom-kolom yang bisa diisi mass assignment
    ];

    public $timestamps = false;

    // Relasi dengan form legalisasi
    public function formLegalisasi()
    {
        return $this->belongsTo(FormLegalisasi::class);
    }
}

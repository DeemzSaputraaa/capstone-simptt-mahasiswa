<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLegalisasiStatus extends Model
{
    protected $table = 'form_legalisasi_status';

    protected $fillable = [
        'nama',
        'keterangan',
    ];

    public $timestamps = false;

    // Relasi dengan form legalisasi
    public function formLegalisasi()
    {
        return $this->hasMany(FormLegalisasi::class);
    }
}

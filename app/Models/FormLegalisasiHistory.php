<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormLegalisasiHistory extends Model
{
    protected $table = 'form_legalisasi_history';

    protected $fillable = [
        'form_legalisasi_id',
        'status',
        'catatan',
        'created_at',
    ];

    public $timestamps = false;

    // Relasi dengan form legalisasi
    public function formLegalisasi()
    {
        return $this->belongsTo(FormLegalisasi::class);
    }
}

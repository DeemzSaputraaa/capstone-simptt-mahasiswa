<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkValidasiIjazahMahasiswaComment extends Model
{
    protected $table = 'comment';

    protected $fillable = [
        'id',
        'user_id',
        'content',
        'created_at',
        'updated_at',
        'parent_id',
        'validasi_ijazah_id',
    ];

    public $timestamps = true;

    // Relasi dengan validasi ijazah
    public function validasiIjazah()
    {
        return $this->belongsTo(AkValidasiIjazahMahasiswa::class, 'validasi_ijazah_id');
    }

    // Relasi dengan parent comment (untuk reply)
    public function parent()
    {
        return $this->belongsTo(AkValidasiIjazahMahasiswaComment::class, 'parent_id');
    }

    // Relasi dengan replies
    public function replies()
    {
        return $this->hasMany(AkValidasiIjazahMahasiswaComment::class, 'parent_id');
    }

    // Relasi dengan user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

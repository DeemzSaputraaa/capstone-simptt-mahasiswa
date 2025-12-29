<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkValidasiIjazahMahasiswaComment extends Model
{
    protected $table = 'ak_validasi_ijazah_mahasiswa_comment';

    protected $primaryKey = 'kdcomment';

    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $timestamps = true;

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
    const DELETED_AT = 'delete_at';

    protected $fillable = [
        'kdvalidasiijazahmahasiswa',
        'parent_id',
        'user_id',
        'user_type',
        'comment',
        'is_edited',
        'create_at',
        'update_at',
        'delete_at',
    ];

    protected $dates = [
        'create_at',
        'update_at',
        'delete_at',
    ];

    // Relasi dengan validasi ijazah
    public function validasiIjazah()
    {
        return $this->belongsTo(AkValidasiIjazahMahasiswa::class, 'kdvalidasiijazahmahasiswa');
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

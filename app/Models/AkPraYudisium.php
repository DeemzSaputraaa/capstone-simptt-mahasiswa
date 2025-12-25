<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AkPraYudisium extends Model
{
    protected $table = 'ak_pra_yudisium';

    use SoftDeletes;

    protected $primaryKey = 'kdprayudisium';

    public $timestamps = true;

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
    const DELETED_AT = 'delete_at';

    protected $fillable = [
        'berkas_foto_ijazah',
        'berkas_ijazah_terakhir',
        'berkas_kk_ktp',
        'kdmahasiswa',
        'is_validate',
        'tgl_validate',
        'comment',
        'status_foto',
        'status_ijazah',
        'status_ktp',
    ];

    protected $dates = [
        'tgl_validate',
        'create_at',
        'update_at',
        'delete_at',
    ];
}

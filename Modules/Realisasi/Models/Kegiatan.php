<?php

namespace Modules\Realisasi\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\MsSubKegiatan;

class Kegiatan extends Model
{
    protected $guarded = [''];
    protected $table = 't_realisasi_kegiatan';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;

    public function subKegiatan()
    {
        return $this->hasOne(MsSubKegiatan::class, 'id', 'fk_kegiatan_id');
    }
}

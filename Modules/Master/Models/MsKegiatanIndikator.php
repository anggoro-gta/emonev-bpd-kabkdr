<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class MsKegiatanIndikator extends Model
{
    protected $guarded = [''];
    protected $table = 'ms_kegiatan_indikator';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
}

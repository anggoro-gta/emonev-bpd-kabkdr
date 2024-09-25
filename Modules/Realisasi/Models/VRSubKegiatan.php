<?php

namespace Modules\Realisasi\Models;

use Illuminate\Database\Eloquent\Model;

class VRSubKegiatan extends Model
{
    protected $guarded = [''];
    protected $table = 'v_realisasi_sub_kegiatan';
    public $incrementing = false;
    public $timestamps = false;
}

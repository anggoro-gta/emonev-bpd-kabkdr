<?php

namespace Modules\Realisasi\Models;

use Illuminate\Database\Eloquent\Model;

class VRKegiatan extends Model
{
    protected $guarded = [''];
    protected $table = 'v_realisasi_kegiatan';
    public $incrementing = false;
    public $timestamps = false;
}

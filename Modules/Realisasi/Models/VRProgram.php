<?php

namespace Modules\Realisasi\Models;

use Illuminate\Database\Eloquent\Model;

class VRProgram extends Model
{
    protected $guarded = [''];
    protected $table = 'v_realisasi_program';
    public $incrementing = false;
    public $timestamps = false;
    protected $appends = ['keterangan'];
    public function getKeteranganAttribute()
    {
        return number_format($this->volume_realisasi) . ' ' . $this->satuan_prog;
    }
}

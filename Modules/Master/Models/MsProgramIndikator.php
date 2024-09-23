<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class MsProgramIndikator extends Model
{
    protected $guarded = [''];
    protected $table = 'ms_program_indikator';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $appends = ['keterangan','keterangan_rpjmd'];
    public function getKeteranganAttribute()
    {
        return number_format($this->volume_prog) . ' ' . $this->satuan_prog;
    }
    public function getKeteranganRpjmdAttribute()
    {
        return number_format($this->volume_prog_rpjmd) . ' ' . $this->satuan_prog_rpjmd;
    }
}

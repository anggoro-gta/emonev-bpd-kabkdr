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
    protected $appends = ['keterangan','keterangan_rpjmd'];
    public function getKeteranganAttribute()
    {
        return number_format($this->volume_keg) . ' ' . $this->satuan_keg;
    }
    public function getKeteranganRpjmdAttribute()
    {
        return number_format($this->volume_keg_rpjmd) . ' ' . $this->satuan_keg_rpjmd;
    }
}

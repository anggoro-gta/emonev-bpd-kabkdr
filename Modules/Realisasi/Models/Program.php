<?php

namespace Modules\Realisasi\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $guarded = [''];
    protected $table = 't_realisasi_program';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $appends = ['keterangan'];
    public function getKeteranganAttribute()
    {
        return number_format($this->volume_prog) . ' ' . $this->satuan_prog;
    }
}

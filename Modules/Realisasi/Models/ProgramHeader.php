<?php

namespace Modules\Realisasi\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\MsSKPD;

class ProgramHeader extends Model
{
    protected $guarded = [''];
    protected $table = 't_realisasi_program_header';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;

    function scopeFilter($query)
    {
        if (auth()->user()->hasRole("OPD") || request()->fk_skpd_id) {
            $query->where('fk_skpd_id',auth()->user()->unit->skpd->id ?? request()->fk_skpd_id);
        }
    }
    public function skpd()
    {
        return $this->hasOne(MsSKPD::class, 'id', 'fk_skpd_id');
    }
    public function detail()
    {
        return $this->hasMany(Program::class, 'fk_t_realisasi_program_header_id', 'id');
    }
}

<?php

namespace Modules\Realisasi\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\MsSKPD;
use Modules\Master\Models\MsSubKegiatan;

class FaktorTL extends Model
{
    protected $guarded = [''];
    protected $table = 't_faktor_tl';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
    public function skpd()
    {
        return $this->hasOne(MsSKPD::class, 'id', 'fk_skpd_id');
    }
    function scopeFilter($query)
    {
        if (auth()->user()->hasRole("OPD") || request()->fk_skpd_id) {
            $query->where('fk_skpd_id',auth()->user()->unit->skpd->id ?? request()->fk_skpd_id);
        }
    }
}

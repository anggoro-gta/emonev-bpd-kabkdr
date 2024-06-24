<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class MsSKPDUnit extends Model
{
    protected $guarded = [''];
    protected $table = 'ms_skpd_unit';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
    function scopeFilter($query)
    {
        $query->when(request('q'), function ($query) {
            $query->where(function ($query) {
                $param = '%' . request('q') . '%';
                $query->where('nama_unit', 'like', $param)->orWhere('nama_unit', 'like', $param);
            });
        });
    }
    public function skpd()
    {
        return $this->hasOne(MsSKPD::class, 'id', 'fk_skpd_id');
    }
}

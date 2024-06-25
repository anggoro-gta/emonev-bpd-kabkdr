<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class MsProgram extends Model
{
    protected $guarded = [''];
    protected $table = 'ms_program';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
    function scopeFilter($query)
    {
        $query->when(request('q'), function ($query) {
            $query->where(function ($query) {
                $param = '%' . request('q') . '%';
                $query->where('nama_program', 'like', $param)->orWhere('nama_program', 'like', $param);
            });
        });
    }
    public function unit()
    {
        return $this->hasOne(MsSKPDUnit::class, 'kode_unit', 'kode_sub_unit_skpd');
    }
    public function bidang()
    {
        return $this->hasOne(MsBidangUrusan::class, 'id', 'fk_bidang_urusan_id');
    }
}

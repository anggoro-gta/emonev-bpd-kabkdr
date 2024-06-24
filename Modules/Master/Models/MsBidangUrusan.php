<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class MsBidangUrusan extends Model
{
    protected $guarded = [''];
    protected $table = 'ms_bidang_urusan';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
    function scopeFilter($query)
    {
        $query->when(request('q'), function ($query) {
            $query->where(function ($query) {
                $param = '%' . request('q') . '%';
                $query->where('nama_bidang_urusan', 'like', $param)->orWhere('nama_bidang_urusan', 'like', $param);
            });
        });
    }
    public function urusan()
    {
        return $this->hasOne(MsUrusan::class, 'id', 'fk_urusan_id');
    }
}

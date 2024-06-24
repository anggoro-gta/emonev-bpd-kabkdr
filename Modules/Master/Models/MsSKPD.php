<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class MsSKPD extends Model
{
    protected $guarded = [''];
    protected $table = 'ms_skpd';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
    function scopeFilter($query)
    {
        $query->when(request('q'), function ($query) {
            $query->where(function ($query) {
                $param = '%' . request('q') . '%';
                $query->where('nama_skpd', 'like', $param)->orWhere('nama_skpd', 'like', $param);
            });
        });
    }
}

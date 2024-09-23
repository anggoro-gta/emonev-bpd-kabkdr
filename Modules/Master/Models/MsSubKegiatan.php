<?php

namespace Modules\Master\Models;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use Illuminate\Database\Eloquent\Model;

class MsSubKegiatan extends Model
{
    use LaravelSubQueryTrait;
    protected $guarded = [''];
    protected $table = 'ms_sub_kegiatan';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
    protected $appends = ['keterangan','keterangan_rpjmd'];
    public function getKeteranganAttribute()
    {
        return number_format($this->volume_sub) . ' ' . $this->satuan_sub;
    }
    public function getKeteranganRpjmdAttribute()
    {
        return number_format($this->volume_sub_rpjmd) . ' ' . $this->satuan_sub_rpjmd;
    }
    function scopeFilter($query)
    {
        $query->when(request('q'), function ($query) {
            $query->where(function ($query) {
                $param = '%' . request('q') . '%';
                $query->where('nama_sub_kegiatan', 'like', $param)->orWhere('nama_sub_kegiatan', 'like', $param);
                $query->orwhere('nama_kegiatan', 'like', $param)->orWhere('nama_kegiatan', 'like', $param);
            });
        });
    }
    public function kegiatan()
    {
        return $this->hasOne(MsKegiatan::class, 'id', 'fk_kegiatan_id');
    }
}

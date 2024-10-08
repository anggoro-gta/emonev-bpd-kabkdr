<?php

namespace Modules\Master\Models;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use Illuminate\Database\Eloquent\Model;

class MsKegiatan extends Model
{
    use LaravelSubQueryTrait;
    protected $guarded = [''];
    protected $table = 'ms_kegiatan';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
    function scopeFilter($query)
    {
        $query->when(request('q'), function ($query) {
            $query->where(function ($query) {
                $param = '%' . request('q') . '%';
                $query->where('kode_kegiatan', 'like', $param)->orWhere('nama_kegiatan', 'like', $param);
                $query->orWhere('kode_program', 'like', $param)->orWhere('nama_program', 'like', $param);
            });
        });
    }
    public function program()
    {
        return $this->hasOne(MsProgram::class, 'id', 'fk_program_id');
    }
    public function indikator()
    {
        return $this->hasMany(MsKegiatanIndikator::class, 'fk_kegiatan_id', 'id');
    }
    public function sub_kegiatan()
    {
        return $this->hasMany(MsSubKegiatan::class, 'fk_kegiatan_id', 'id');
    }
    public function kegiatanTahunLalu()
    {
        return $this->hasOne(MsProgram::class, 'kode_program', 'kode_program')->where('tahun', session('tahunSession') - 1);
    }
}

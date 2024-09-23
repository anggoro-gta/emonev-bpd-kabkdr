<?php

namespace Modules\Master\Models;

use Alexmg86\LaravelSubQuery\Traits\LaravelSubQueryTrait;
use Illuminate\Database\Eloquent\Model;

class MsProgram extends Model
{
    use LaravelSubQueryTrait;
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
                $query->where('kode_program', 'like', $param)->orWhere('nama_program', 'like', $param);
                $query->orwhere('kode_unit', 'like', $param)->orWhere('nama_unit', 'like', $param);
            });
        });
        if (auth()->user()->hasRole("OPD")) {
            $query->where('kode_sub_unit_skpd', auth()->user()->unit->kode_unit);
        }
    }
    public function unit()
    {
        return $this->hasOne(MsSKPDUnit::class, 'kode_unit', 'kode_sub_unit_skpd');
    }
    public function bidang()
    {
        return $this->hasOne(MsBidangUrusan::class, 'id', 'fk_bidang_urusan_id');
    }
    public function indikator()
    {
        return $this->hasMany(MsProgramIndikator::class, 'fk_program_id', 'id');
    }
    public function kegiatan()
    {
        return $this->hasMany(MsKegiatan::class, 'fk_program_id', 'id');
    }
    public function programTahunLalu()
    {
        return $this->hasOne(MsProgram::class, 'kode_program', 'kode_program')->where('tahun', session('tahunSession') - 1);
    }

    public function sub_kegiatan()
    {
        return $this->hasManyThrough(MsSubKegiatan::class, MsKegiatan::class, 'fk_program_id', 'fk_kegiatan_id', 'id');
    }
}

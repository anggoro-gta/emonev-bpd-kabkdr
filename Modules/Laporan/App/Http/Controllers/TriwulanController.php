<?php

namespace Modules\Laporan\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Models\MsProgram;
use Modules\Master\Models\MsSKPDUnit;
use Modules\Realisasi\Models\ProgramHeader;
use Modules\Realisasi\Models\SubKegiatan;
use Modules\Realisasi\Models\VRKegiatan;
use Modules\Realisasi\Models\VRProgram;
use Modules\Realisasi\Models\VRSubKegiatan;

class TriwulanController extends Controller
{

    private $type_menu;
    public function __construct()
    {
        $this->type_menu = 'laporan';
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $data =  (object)[
            'type_menu' => $this->type_menu
        ];
        if (auth()->user()->hasRole("Admin")) {
            $data->skpd_unit = MsSKPDUnit::all();
        }
        return view('laporan::triwulan.index', compact('data'));
    }
    function cetak(Request $request) {
        $skpd = MsSKPDUnit::where('kode_unit',$request->kode_sub_unit_skpd ?? auth()->user()->unit->kode_unit)->first();
        $triwulanArray = [
            1 => [1],
            2 => [1,2],
            3 => [1,2,3],
            4 => [1,2,3,4],
        ];
        $r_program = VRProgram::where('fk_skpd_id',$skpd->fk_skpd_id)->whereIn('triwulan',$triwulanArray[request()->triwulan])->where('tahun',session('tahunSession'))->get();
        // dd(VRProgram::all());
        $r_kegiatan = VRKegiatan::where('fk_skpd_id',$skpd->fk_skpd_id)->whereIn('triwulan',$triwulanArray[request()->triwulan])->where('tahun',session('tahunSession'))->get();
        $r_sub_kegiatan = SubKegiatan::selectRaw("	t_realisasi_sub_kegiatan.id,
        fk_skpd_id,
        fk_sub_kegiatan_id,
        triwulan,
        tahun,fk_kegiatan_id,fk_program_id,
        TRIM(TRAILING '.' FROM TRIM(TRAILING '0' from volume_realisasi )) as volume_realisasi  ,
        satuan_sub_kegiatan ,
        anggaran_realisasi")->join('t_realisasi_sub_kegiatan_header','t_realisasi_sub_kegiatan_header.id','=','t_realisasi_sub_kegiatan.fk_t_realisasi_sub_kegiatan_header_id')
        ->where('fk_skpd_id',$skpd->fk_skpd_id)->whereIn('triwulan',$triwulanArray[request()->triwulan])->where('tahun',session('tahunSession'))
        ->where('status_posting',1)->orderBy('fk_sub_kegiatan_id')->get();
        $program = MsProgram::with([
            'kegiatan.sub_kegiatan',
            'kegiatan.indikator',
            'indikator',
            'sub_kegiatan',
            'programTahunLalu.sub_kegiatan',
            'programTahunLalu.indikator',
            'programTahunLalu.kegiatan.indikator',
            'programTahunLalu.kegiatan.sub_kegiatan',
        ])->where('tahun',session('tahunSession'))
        ->where('kode_sub_unit_skpd',$request->kode_sub_unit_skpd ?? auth()->user()->unit->kode_unit)->get();
        $data = (object)[
            'program' => $program,
            'r_program' => $r_program,
            'r_kegiatan' => $r_kegiatan,
            'r_kegiatan' => $r_kegiatan,
            'r_sub_kegiatan' => $r_sub_kegiatan,
        ];
        return view('laporan::triwulan.cetak', compact('data'));
    }
}

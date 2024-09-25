<?php

namespace Modules\Laporan\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Models\MsProgram;
use Modules\Master\Models\MsSKPDUnit;
use Modules\Realisasi\Models\ProgramHeader;

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
        $r_program = ProgramHeader::with('detail')->where('fk_skpd_id',$skpd->fk_skpd_id)->where('status_posting',1)->whereIn('triwulan',$triwulanArray[request()->triwulan])->where('tahun',session('tahunSession'))->get();
        // dd($r_program->where('triwulan',1));
        $data = (object)[
            'program' => MsProgram::with([
                'kegiatan.sub_kegiatan',
                'kegiatan.indikator',
                'indikator',
                'sub_kegiatan',
                'programTahunLalu.sub_kegiatan',
                'programTahunLalu.indikator',
                'programTahunLalu.kegiatan.indikator',
                'programTahunLalu.kegiatan.sub_kegiatan',
            ])->where('tahun',session('tahunSession'))
            ->where('kode_sub_unit_skpd',$request->kode_sub_unit_skpd ?? auth()->user()->unit->kode_unit)->get(),
        ];
        return view('laporan::triwulan.cetak', compact('data'));
    }
}

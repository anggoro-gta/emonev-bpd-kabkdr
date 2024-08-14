<?php

namespace Modules\Laporan\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MonitoringOpdController extends Controller
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
        // $this->authorize('master.bidang_urusan.read');
        // dd($request->all());
        $data =  (object)[
            'type_menu' => $this->type_menu,
        ];
        if ($request->type) {
           $table = [
            'Program' => 'program',
            'Kegiatan' => 'kegiatan',
            'Sub Kegitan' => 'sub_kegiatan',
           ];
           $data->realisasi = DB::select("select
               ms.*,
               trph.status_posting
           from
               ms_skpd ms
           left join t_realisasi_{$table[$request->realisasi]}_header trph on
               ms.id = trph.fk_skpd_id and triwulan =? and tahun = ?",[$request->triwulan,session('tahunSession')]);
        }
        return view('laporan::monitoring_opd.index', compact('data'));
    }
}

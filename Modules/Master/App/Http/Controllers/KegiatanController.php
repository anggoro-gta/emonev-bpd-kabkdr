<?php

namespace Modules\Master\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\MsProgram;
use Modules\Master\Models\MsKegiatan;
use Modules\Master\Models\MsKegiatanIndikator;
use Modules\Realisasi\Models\Kegiatan;

class KegiatanController extends Controller
{

    private $type_menu;
    public function __construct()
    {
        $this->type_menu = 'master';
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        // $this->authorize('master.kegiatan.read');
        $kegiatan = MsKegiatan::select('ms_kegiatan.*')->filter()->with('program')->join('ms_program','ms_program.id','=','ms_kegiatan.fk_program_id')->where('ms_program.tahun', '=', session('tahunSession'));
        if (auth()->user()->hasRole("OPD")) {
            $kegiatan->where('kode_sub_unit_skpd',auth()->user()->unit->kode_unit);
        }
        $kegiatan = $kegiatan->paginate(10);
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'kegiatan' => $kegiatan
        ];
        return view('master::kegiatan.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('master.kegiatan.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('master.kegiatan.store'),
            'method' => 'POST',
            'program' => MsProgram::where('tahun', '=', session('tahunSession'))->get()
        ];
        return view('master::kegiatan.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        MsKegiatan::create([
            'fk_program_id' => $request->fk_program_id,
            'kode_kegiatan' => $request->kode_kegiatan,
            'nama_kegiatan' => $request->nama_kegiatan,
        ]);
        $id = DB::getPdo()->lastInsertId();
        $this->insertOrUpdateIndikator($request,$id);
        return redirect(route('master.kegiatan.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
    }

    function destroyIndikator($id) {
        MsKegiatanIndikator::find($id)->delete();
    }
    function insertOrUpdateIndikator($request,$id) {
        for ($i=0; $i < count($request->kegiatan_indikator_id) ; $i++) {
            MsKegiatanIndikator::updateOrCreate(
                ['id' => $request->kegiatan_indikator_id[$i]],
                [
                    'fk_kegiatan_id' => $id,
                    'indikator_keg' => $request->indikator_keg[$i],
                    'volume_keg_rpjmd' => $request->volume_keg_rpjmd[$i] ? str_replace(',', '', $request->volume_keg_rpjmd[$i]):null,
                    'satuan_keg_rpjmd' => $request->satuan_keg_rpjmd[$i],
                    'volume_keg' => $request->volume_keg[$i] ? str_replace(',', '', $request->volume_keg[$i]):null,
                    'satuan_keg' => $request->satuan_keg[$i],
                ]
            );
        }
    }
    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('setting::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorize('master.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'kegiatan' => MsKegiatan::find($id),
            'action' => route('master.kegiatan.update', $id),
            'method' => 'PUT',
            'program' => MsProgram::where('tahun', '=', session('tahunSession'))->get()
        ];
        return view('master::kegiatan.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kegiatan = MsKegiatan::find($id);
        $kegiatan->kode_kegiatan = $request->kode_kegiatan;
        $kegiatan->nama_kegiatan = $request->nama_kegiatan;
        $kegiatan->fk_program_id = $request->fk_program_id;
        $kegiatan->save();
        $this->insertOrUpdateIndikator($request,$id);
        return redirect(route('master.kegiatan.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('master.kegiatan.destroy');
        MsKegiatan::find($id)->delete();
        return redirect(route('master.kegiatan.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
}

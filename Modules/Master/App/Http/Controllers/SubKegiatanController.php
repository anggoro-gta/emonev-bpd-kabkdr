<?php

namespace Modules\Master\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Models\MsKegiatan;
use Modules\Master\Models\MsSubKegiatan;

class SubKegiatanController extends Controller
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
        // $this->authorize('master.sub_kegiatan.read');
        $sub_kegiatan = MsSubKegiatan::select('ms_sub_kegiatan.*')->with('kegiatan.program')->join('ms_kegiatan','ms_kegiatan.id','=','fk_kegiatan_id')->join('ms_program','ms_program.id','=','ms_kegiatan.fk_program_id')->filter();
        if (auth()->user()->hasRole("OPD")) {
            $sub_kegiatan->where('kode_sub_unit_skpd',auth()->user()->unit->kode_unit);
        }
        $sub_kegiatan = $sub_kegiatan->where('tahun', '=', session('tahunSession'))->paginate(10);
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'sub_kegiatan' => $sub_kegiatan
        ];
        return view('master::sub_kegiatan.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('master.sub_kegiatan.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('master.sub_kegiatan.store'),
            'method' => 'POST',
            'kegiatan' => MsKegiatan::with('program')->whereHas('program', function($q){
                $q->where('tahun', '=', session('tahunSession'));
            })->get()
        ];
        return view('master::sub_kegiatan.form', compact('data'));
    }
    function inputData($request) {
        return [
            'fk_kegiatan_id' => $request->fk_kegiatan_id,
            'kode_sub_kegiatan' => $request->kode_sub_kegiatan,
            'nama_sub_kegiatan' => $request->nama_sub_kegiatan,
            'anggaran_murni' => $request->anggaran_murni ? str_replace(',', '', $request->anggaran_murni):null,
            'anggaran_rpjmd' => $request->anggaran_rpjmd ? str_replace(',', '', $request->anggaran_rpjmd):null,
            'perubahan_perbup1' => $request->perubahan_perbup1 ? str_replace(',', '', $request->perubahan_perbup1):null,
            'perubahan_perbup2' => $request->perubahan_perbup2 ? str_replace(',', '', $request->perubahan_perbup2):null,
            'perubahan_perbup3' => $request->perubahan_perbup3 ? str_replace(',', '', $request->perubahan_perbup3):null,
            'perubahan_perbup4' => $request->perubahan_perbup4 ? str_replace(',', '', $request->perubahan_perbup4):null,
            'perubahan_anggaran' => $request->perubahan_anggaran ? str_replace(',', '', $request->perubahan_anggaran):null,
            'indikator_sub' => $request->indikator_sub,
            'volume_sub' => $request->volume_sub ? str_replace(',', '', $request->volume_sub):null,
            'satuan_sub' => $request->satuan_sub,
            'volume_sub_rpjmd' => $request->volume_sub_rpjmd ? str_replace(',', '', $request->volume_sub_rpjmd):null,
            'satuan_sub_rpjmd' => $request->satuan_sub_rpjmd,
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        MsSubKegiatan::create($this->inputData($request));
        return redirect(route('master.sub_kegiatan.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
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
            'sub_kegiatan' => MsSubKegiatan::find($id),
            'action' => route('master.sub_kegiatan.update', $id),
            'method' => 'PUT',
            'kegiatan' => MsKegiatan::with('program')->whereHas('program', function($q){
                $q->where('tahun', '=', session('tahunSession'));
            })->get()
        ];
        return view('master::sub_kegiatan.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        MsSubKegiatan::where('Id', $id)->update($this->inputData($request));
        return redirect(route('master.sub_kegiatan.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('master.sub_kegiatan.destroy');
        MsSubKegiatan::find($id)->delete();
        return redirect(route('master.sub_kegiatan.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
}

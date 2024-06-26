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
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'sub_kegiatan' => MsSubKegiatan::with('kegiatan.program')->whereHas('kegiatan.program', function($q){
                $q->where('tahun', '=', session('tahunSession'));
            })->filter()->paginate(10)
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        MsSubKegiatan::create([
            'fk_kegiatan_id' => $request->fk_kegiatan_id,
            'kode_sub_kegiatan' => $request->kode_sub_kegiatan,
            'nama_sub_kegiatan' => $request->nama_sub_kegiatan,
            'anggaran_murni' => str_replace(',', '', $request->anggaran_murni),
            'perubahan_perbup1' => str_replace(',', '', $request->perubahan_perbup1),
            'perubahan_perbup2' => str_replace(',', '', $request->perubahan_perbup2),
            'perubahan_perbup3' => str_replace(',', '', $request->perubahan_perbup3),
            'perubahan_perbup4' => str_replace(',', '', $request->perubahan_perbup4),
            'perubahan_anggaran' => str_replace(',', '', $request->perubahan_anggaran),
        ]);
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
        $this->authorize('master.sub_kegiatan.update');
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
        $sub_kegiatan = MsSubKegiatan::find($id);
        $sub_kegiatan->fk_kegiatan_id = $request->fk_kegiatan_id;
        $sub_kegiatan->kode_sub_kegiatan = $request->kode_sub_kegiatan;
        $sub_kegiatan->nama_sub_kegiatan = $request->nama_sub_kegiatan;
        $sub_kegiatan->anggaran_murni = str_replace(',', '', $request->anggaran_murni);
        $sub_kegiatan->perubahan_perbup1 = str_replace(',', '', $request->perubahan_perbup1);
        $sub_kegiatan->perubahan_perbup2 = str_replace(',', '', $request->perubahan_perbup2);
        $sub_kegiatan->perubahan_perbup3 = str_replace(',', '', $request->perubahan_perbup3);
        $sub_kegiatan->perubahan_perbup4 = str_replace(',', '', $request->perubahan_perbup4);
        $sub_kegiatan->perubahan_anggaran = str_replace(',', '', $request->perubahan_anggaran);
        $sub_kegiatan->save();
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

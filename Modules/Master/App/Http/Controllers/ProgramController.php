<?php

namespace Modules\Master\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Models\MsBidangUrusan;
use Modules\Master\Models\MsSKPD;
use Modules\Master\Models\MsProgram;
use Modules\Master\Models\MsSKPDUnit;

class ProgramController extends Controller
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
        // $this->authorize('master.program.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'program' => MsProgram::with('unit','bidang')->where('tahun',session('tahunSession'))->filter()->paginate(10)
        ];
        return view('master::program.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('master.program.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('master.program.store'),
            'method' => 'POST',
            'unit' => MsSKPDUnit::get(),
            'bidang' => MsBidangUrusan::get()
        ];
        return view('master::program.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        MsProgram::create([
            'kode_sub_unit_skpd' => $request->kode_sub_unit_skpd,
            'tahun' => session('tahunSession'),
            'fk_bidang_urusan_id' => $request->fk_bidang_urusan_id,
            'kode_program' => $request->kode_program,
            'nama_program' => $request->nama_program,
        ]);
        return redirect(route('master.program.index'))
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
        // $this->authorize('master.program.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'program' => MsProgram::find($id),
            'action' => route('master.program.update', $id),
            'method' => 'PUT',
            'unit' => MsSKPDUnit::get(),
            'bidang' => MsBidangUrusan::get()
        ];
        return view('master::program.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $program = MsProgram::find($id);
        $program->kode_sub_unit_skpd = $request->kode_sub_unit_skpd;
        $program->tahun = session('tahunSession');
        $program->fk_bidang_urusan_id = $request->fk_bidang_urusan_id;
        $program->kode_program = $request->kode_program;
        $program->nama_program = $request->nama_program;
        $program->save();
        return redirect(route('master.program.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('master.program.destroy');
        MsProgram::find($id)->delete();
        return redirect(route('master.program.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
}

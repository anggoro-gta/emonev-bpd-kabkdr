<?php

namespace Modules\Master\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Models\MsSKPD;
use Modules\Master\Models\MsSKPDUnit;

class SkpdUnitController extends Controller
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
        // $this->authorize('master.skpd_unit.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'skpd_unit' => MsSKPDUnit::with('skpd')->filter()->paginate(10)
        ];
        return view('master::skpd_unit.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('master.skpd_unit.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('master.skpd_unit.store'),
            'method' => 'POST',
            'skpd' => MsSKPD::get()
        ];
        return view('master::skpd_unit.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        MsSKPDUnit::create([
            'fk_skpd_id' => $request->fk_skpd_id,
            'kode_unit' => $request->kode_unit,
            'nama_unit' => $request->nama_unit,
        ]);
        return redirect(route('master.skpd_unit.index'))
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
        // $this->authorize('master.skpd_unit.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'skpd_unit' => MsSKPDUnit::find($id),
            'action' => route('master.skpd_unit.update', $id),
            'method' => 'PUT',
            'skpd' => MsSKPD::get()
        ];
        return view('master::skpd_unit.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $skpd_unit = MsSKPDUnit::find($id);
        $skpd_unit->kode_unit = $request->kode_unit;
        $skpd_unit->nama_unit = $request->nama_unit;
        $skpd_unit->fk_skpd_id = $request->fk_skpd_id;
        $skpd_unit->save();
        return redirect(route('master.skpd_unit.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('master.skpd_unit.destroy');
        MsSKPDUnit::find($id)->delete();
        return redirect(route('master.skpd_unit.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
}

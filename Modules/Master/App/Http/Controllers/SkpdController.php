<?php

namespace Modules\Master\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Models\MsSKPD;

class SkpdController extends Controller
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
        // $this->authorize('master.skpd.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'skpd' => MsSKPD::filter()->paginate(10)
        ];
    
        return view('master::skpd.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('master.skpd.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('master.skpd.store'),
            'method' => 'POST',
        ];
        return view('master::skpd.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        MsSKPD::create([
            'kode_skpd' => $request->kode_skpd,
            'nama_skpd' => $request->nama_skpd,
        ]);
        return redirect(route('master.skpd.index'))
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
        // $this->authorize('master.skpd.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'skpd' => MsSKPD::find($id),
            'action' => route('master.skpd.update', $id),
            'method' => 'PUT',
        ];
        return view('master::skpd.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $skpd = MsSKPD::find($id);
        $skpd->kode_skpd = $request->kode_skpd;
        $skpd->nama_skpd = $request->nama_skpd;
        $skpd->save();
        return redirect(route('master.skpd.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('master.skpd.destroy');
        MsSKPD::find($id)->delete();
        return redirect(route('master.skpd.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
}

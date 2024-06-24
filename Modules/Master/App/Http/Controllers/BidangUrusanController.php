<?php

namespace Modules\Master\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Models\MsBidangUrusan;
use Modules\Master\Models\MsUrusan;

class BidangUrusanController extends Controller
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
        // $this->authorize('master.bidang_urusan.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'bidang_urusan' => MsBidangUrusan::with('urusan')->filter()->paginate(10)
        ];
        return view('master::bidang_urusan.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('master.bidang_urusan.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('master.bidang_urusan.store'),
            'method' => 'POST',
            'urusan' => MsUrusan::get()
        ];
        return view('master::bidang_urusan.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        MsBidangUrusan::create([
            'fk_urusan_id' => $request->fk_urusan_id,
            'kode_bidang_urusan' => $request->kode_bidang_urusan,
            'nama_bidang_urusan' => $request->nama_bidang_urusan,
        ]);
        return redirect(route('master.bidang_urusan.index'))
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
        // $this->authorize('master.bidang_urusan.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'bidang_urusan' => MsBidangUrusan::find($id),
            'action' => route('master.bidang_urusan.update', $id),
            'method' => 'PUT',
            'urusan' => MsUrusan::get()
        ];
        return view('master::bidang_urusan.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bidang_urusan = MsBidangUrusan::find($id);
        $bidang_urusan->kode_bidang_urusan = $request->kode_bidang_urusan;
        $bidang_urusan->nama_bidang_urusan = $request->nama_bidang_urusan;
        $bidang_urusan->fk_urusan_id = $request->fk_urusan_id;
        $bidang_urusan->save();
        return redirect(route('master.bidang_urusan.index'))
            ->with('flash_message', "Data berhasil disimpan")
            ->with('flash_type', 'primary');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('master.bidang_urusan.destroy');
        MsBidangUrusan::find($id)->delete();
        return redirect(route('master.bidang_urusan.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
}

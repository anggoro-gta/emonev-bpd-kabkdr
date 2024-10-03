<?php

namespace Modules\Realisasi\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\MsSKPD;
use Modules\Master\Models\MsSKPDUnit;
use Modules\Realisasi\Models\FaktorTL;
use Modules\Realisasi\Models\Program;
use Modules\Realisasi\Models\ProgramHeader;

class FaktorTLController extends Controller
{

    private $type_menu;
    public function __construct()
    {
        $this->type_menu = 'realisasi';
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        // $this->authorize('realisasi.faktortl.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'realisasi' => FaktorTL::where('tahun',session('tahunSession'))->filter()->get()
        ];
        if (auth()->user()->hasRole("Admin")) {
            $data->skpd_unit = MsSKPDUnit::all();
        }
        return view('realisasi::faktortl.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('realisasi.faktortl.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('realisasi.faktortl.store'),
            'method' => 'POST',
            'skpd' => MsSKPD::get()
        ];
        return view('realisasi::faktortl.form', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $input['fk_skpd_id'] = $request->fk_skpd_id;
            $input['tahun'] = session('tahunSession');
            $input['triwulan'] = $request->triwulan;
            $input['faktor_pendorong'] = $request->faktor_pendorong;
            $input['faktor_penghambat'] = $request->faktor_penghambat;
            $input['tindaklanjut_tw_berikutnya'] = $request->tindaklanjut_tw_berikutnya;
            $input['tindaklanjut_rkpd_berikutnya'] = $request->tindaklanjut_rkpd_berikutnya;
            $input['time_act'] = now();
            $input['user_act'] = auth()->user()->id;
            FaktorTL::create($input);
            DB::commit();
            $msg = 'Data berhasil disimpan';
            $type = "success";
        } catch (\Exception $e) {
            DB::rollback();
            $type = "danger";
            $msg = $e->getMessage();
            \Log::debug($e);
        }
        return redirect(route('realisasi.faktortl.index'))
            ->with('flash_message', $msg)
            ->with('flash_type', $type);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return $this->edit($id,true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id,$show = false)
    {
        // $this->authorize('realisasi.faktortl.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'realisasi' => FaktorTL::find($id),
            'action' => route('realisasi.faktortl.update', $id),
            'method' => 'PUT',
            'skpd' => MsSKPD::get(),
            'header' => ProgramHeader::find($id),
            'readonly' => $show
        ];
        return view('realisasi::faktortl.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $input['triwulan'] = $request->triwulan;
            $input['faktor_pendorong'] = $request->faktor_pendorong;
            $input['faktor_penghambat'] = $request->faktor_penghambat;
            $input['tindaklanjut_tw_berikutnya'] = $request->tindaklanjut_tw_berikutnya;
            $input['tindaklanjut_rkpd_berikutnya'] = $request->tindaklanjut_rkpd_berikutnya;
            $input['time_act'] = now();
            $input['user_act'] = auth()->user()->id;
            FaktorTL::where('id',$id)->update($input);
            DB::commit();
            $msg = 'Data berhasil disimpan';
            $type = "success";
        } catch (\Exception $e) {
            DB::rollback();
            $type = "danger";
            $msg = $e->getMessage();
            \Log::debug($e);
        }
        return redirect(route('realisasi.faktortl.index'))
            ->with('flash_message', $msg)
            ->with('flash_type', $type);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('realisasi.faktortl.destroy');
        MsSKPDUnit::find($id)->delete();
        return redirect(route('realisasi.faktortl.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
}

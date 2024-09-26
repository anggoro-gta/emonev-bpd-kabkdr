<?php

namespace Modules\Realisasi\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\MsSKPD;
use Modules\Master\Models\MsSKPDUnit;
use Modules\Master\Models\MsSubKegiatan;
use Modules\Realisasi\Models\SubKegiatan;
use Modules\Realisasi\Models\SubKegiatanHeader;

class SubKegiatanController extends Controller
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
        // $this->authorize('realisasi.sub_kegiatan.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'realisasi' => SubKegiatanHeader::where('tahun',session('tahunSession'))->filter()->get()
        ];
        if (auth()->user()->hasRole("Admin")) {
            $data->skpd_unit = MsSKPDUnit::all();
        }
        return view('realisasi::sub_kegiatan.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('realisasi.sub_kegiatan.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('realisasi.sub_kegiatan.store'),
            'method' => 'POST',
            'skpd' => MsSKPD::get()
        ];
        return view('realisasi::sub_kegiatan.form', compact('data'));
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
            $input['time_act'] = now();
            $input['user_act'] = auth()->user()->id;
            SubKegiatanHeader::create($input);
            $idHeader = DB::getPdo()->lastInsertId();
            for ($i = 0; $i < count($request->fk_sub_kegiatan_id); $i++) {
                $inputDetail[] = [
                    'fk_t_realisasi_sub_kegiatan_header_id' => $idHeader,
                    'fk_sub_kegiatan_id' => $request->fk_sub_kegiatan_id[$i],
                    'fk_kegiatan_id' => $request->fk_kegiatan_id[$i],
                    'fk_program_id' => $request->fk_program_id[$i],
                    'indikator_sub_kegiatan' => $request->indikator_sub_kegiatan[$i] ?? null,
                    'anggaran_sub_kegiatan' => $request->anggaran_sub_kegiatan[$i] ?? null,
                    'volume_sub_kegiatan' => $request->volume_sub_kegiatan[$i]  ?? null,
                    'satuan_sub_kegiatan' => $request->satuan_sub_kegiatan[$i]  ?? null,
                    'volume_realisasi' => isset($request->volume_realisasi[$i]) ? str_replace(',', '', $request->volume_realisasi[$i]) : null,
                    'anggaran_realisasi' => isset($request->anggaran_realisasi[$i]) ? str_replace(',', '', $request->anggaran_realisasi[$i]) : null,
                ];
            }
            if (count($inputDetail) > 0) {
                SubKegiatan::insert($inputDetail);
            }
            DB::commit();
            $msg = 'Data berhasil disimpan';
            $type = "success";
        } catch (\Exception $e) {
            DB::rollback();
            $type = "danger";
            $msg = $e->getMessage();
            \Log::debug($e);
        }
        return redirect(route('realisasi.sub_kegiatan.index'))
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
        // $this->authorize('realisasi.sub_kegiatan.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'realisasi' => SubKegiatanHeader::find($id),
            'action' => route('realisasi.sub_kegiatan.update', $id),
            'method' => 'PUT',
            'skpd' => MsSKPD::get(),
            'readonly' => $show
        ];
        return view('realisasi::sub_kegiatan.form', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $input['time_act'] = now();
            $input['user_act'] = auth()->user()->id;
            SubKegiatanHeader::where('id',$id)->update($input);
            for ($i = 0; $i < count($request->id); $i++) {
                $inputDetail = [
                    'fk_kegiatan_id' => $request->fk_kegiatan_id[$i],
                    'fk_program_id' => $request->fk_program_id[$i],
                    'volume_realisasi' => isset($request->volume_realisasi[$i]) ? str_replace(',', '', $request->volume_realisasi[$i]) : null,
                    'anggaran_realisasi' => isset($request->anggaran_realisasi[$i]) ? str_replace(',', '', $request->anggaran_realisasi[$i]) : null,
                ];
                SubKegiatan::where('id',$request->id[$i])->update($inputDetail);
            }
            DB::commit();
            $msg = 'Data berhasil disimpan';
            $type = "success";
        } catch (\Exception $e) {
            DB::rollback();
            $type = "danger";
            $msg = $e->getMessage();
            \Log::debug($e);
        }
        return redirect(route('realisasi.sub_kegiatan.index'))
            ->with('flash_message', $msg)
            ->with('flash_type', $type);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('realisasi.sub_kegiatan.destroy');
        MsSKPDUnit::find($id)->delete();
        return redirect(route('realisasi.sub_kegiatan.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
    function getSubKegiatan()
    {
        $header = SubKegiatanHeader::where('fk_skpd_id', request()->fk_skpd_id)->where('triwulan', request()->triwulan)->where('tahun', session('tahunSession'))->first();

        if ($header) {
            $header = $header;
            $view= null;
        } else {
            $skpd = MsSKPD::find(request()->fk_skpd_id);
            $sub_kegiatan = MsSubKegiatan::with('kegiatan.program')
                ->whereHas('kegiatan.program', function ($q) use ($skpd) {
                    $q->where('tahun', '=', session('tahunSession'));
                    $q->where('kode_sub_unit_skpd', $skpd->kode_skpd);
                })->orderByRaw('kode_sub_kegiatan asc, nama_sub_kegiatan asc')->get();
            $data =  (object)[
                'sub_kegiatan' => $sub_kegiatan,
            ];
            $view = view('realisasi::sub_kegiatan._sub_kegiatan', compact('data'))->render();
            $header = null;
        }
        return response()->json([
            'header' => $header,
            'view' => $view
        ], 200);
    }
    function tooglePosting($id) {
        $input['status_posting'] = request()->status_posting;
        $input['time_act'] = now();
        $input['user_act'] = auth()->user()->id;
        SubKegiatanHeader::where('id',$id)->update($input);
    }
}

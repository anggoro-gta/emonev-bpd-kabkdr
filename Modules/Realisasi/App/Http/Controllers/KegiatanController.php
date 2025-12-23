<?php

namespace Modules\Realisasi\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\MsSKPD;
use Modules\Master\Models\MsSKPDUnit;
use Modules\Master\Models\MsKegiatan;
use Modules\Realisasi\Models\Kegiatan;
use Modules\Realisasi\Models\SubKegiatan;
use Modules\Realisasi\Models\KegiatanHeader;

class KegiatanController extends Controller
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
        // $this->authorize('realisasi.kegiatan.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'realisasi' => KegiatanHeader::where('tahun',session('tahunSession'))->filter()->get()
        ];
        if (auth()->user()->hasRole("Admin")) {
            $data->skpd_unit = MsSKPDUnit::all();
        }
        return view('realisasi::kegiatan.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('realisasi.kegiatan.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('realisasi.kegiatan.store'),
            'method' => 'POST',
            'skpd' => MsSKPD::get()
        ];
        return view('realisasi::kegiatan.form', compact('data'));
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
            KegiatanHeader::create($input);
            $idHeader = DB::getPdo()->lastInsertId();
            for ($i = 0; $i < count($request->fk_kegiatan_id); $i++) {
                $inputDetail[] = [
                    'fk_t_realisasi_kegiatan_header_id' => $idHeader,
                    'fk_kegiatan_id' => $request->fk_kegiatan_id[$i],
                    'fk_kegiatan_indikator_id' => $request->indikator_kegiatan_id[$i] ?? null,
                    'indikator_kegiatan' => $request->indikator_kegiatan[$i] ?? null,
                    'volume_kegiatan' => $request->volume_kegiatan[$i]  ?? null,
                    'satuan_kegiatan' => $request->satuan_kegiatan[$i]  ?? null,
                    'volume_realisasi' => isset($request->volume_realisasi[$i]) ? str_replace(',', '', $request->volume_realisasi[$i]) : null,
                ];
            }
            if (count($inputDetail) > 0) {
                Kegiatan::insert($inputDetail);
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
        return redirect(route('realisasi.kegiatan.index'))
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
        // $this->authorize('realisasi.kegiatan.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'realisasi' => DB::select("select
                    trk.*,
                    mk.nama_kegiatan
                from
                    t_realisasi_kegiatan trk
                    join ms_kegiatan mk on mk.id =trk.fk_kegiatan_id
                where
                    fk_t_realisasi_kegiatan_header_id = $id"),
            'action' => route('realisasi.kegiatan.update', $id),
            'method' => 'PUT',
            'skpd' => MsSKPD::get(),
            'header' => KegiatanHeader::find($id),
            'readonly' => $show
        ];
        return view('realisasi::kegiatan.form', compact('data'));
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
            KegiatanHeader::where('id',$id)->update($input);
            for ($i = 0; $i < count($request->id); $i++) {
                $inputDetail = [
                    'fk_kegiatan_indikator_id' => $request->indikator_kegiatan_id[$i] ?? null,
                    'volume_realisasi' => isset($request->volume_realisasi[$i]) ? str_replace(',', '', $request->volume_realisasi[$i]) : null,
                ];
                Kegiatan::where('id',$request->id[$i])->update($inputDetail);
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
        return redirect(route('realisasi.kegiatan.index'))
            ->with('flash_message', $msg)
            ->with('flash_type', $type);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('realisasi.kegiatan.destroy');
        MsSKPDUnit::find($id)->delete();
        return redirect(route('realisasi.kegiatan.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
    function getKegiatan()
    {
        $header = KegiatanHeader::where('fk_skpd_id', request()->fk_skpd_id)->where('triwulan', request()->triwulan)->where('tahun', session('tahunSession'))->first();

        if ($header) {
            $header = $header;
            $view= null;
        } else {
            $skpd = MsSKPD::find(request()->fk_skpd_id);
            $tahun = session('tahunSession');
            $kegiatan = DB::select("select
                ms_kegiatan.*,
                mki.indikator_keg,
                mki.volume_keg,
                mki.satuan_keg,
                mki.id as ms_kegiatan_indikator_id
            from
                `ms_kegiatan`
            left join ms_kegiatan_indikator mki on mki.fk_kegiatan_id =ms_kegiatan.id
            where
                exists (
                select
                    *
                from
                    `ms_program`
                where
                    `ms_kegiatan`.`fk_program_id` = `ms_program`.`id`
                    and `tahun` = '$tahun'
                    and `kode_sub_unit_skpd` = '$skpd->kode_skpd')
            order by
                kode_kegiatan asc,
                nama_kegiatan asc");
            $data =  (object)[
                'kegiatan' => $kegiatan,
            ];
            $view = view('realisasi::kegiatan._kegiatan', compact('data'))->render();
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
        KegiatanHeader::where('id',$id)->update($input);
    }
}

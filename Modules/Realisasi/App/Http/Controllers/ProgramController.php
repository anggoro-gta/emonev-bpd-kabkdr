<?php

namespace Modules\Realisasi\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\MsSKPD;
use Modules\Master\Models\MsSKPDUnit;
use Modules\Realisasi\Models\Program;
use Modules\Realisasi\Models\ProgramHeader;

class ProgramController extends Controller
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
        // $this->authorize('realisasi.program.read');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'realisasi' => ProgramHeader::where('tahun',session('tahunSession'))->filter()->get()
        ];
        if (auth()->user()->hasRole("Admin")) {
            $data->skpd_unit = MsSKPDUnit::all();
        }
        return view('realisasi::program.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('realisasi.program.create');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'action' => route('realisasi.program.store'),
            'method' => 'POST',
            'skpd' => MsSKPD::get()
        ];
        return view('realisasi::program.form', compact('data'));
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
            ProgramHeader::create($input);
            $idHeader = DB::getPdo()->lastInsertId();
            for ($i = 0; $i < count($request->fk_program_id); $i++) {
                $inputDetail[] = [
                    'fk_t_realisasi_program_header_id' => $idHeader,
                    'fk_program_id' => $request->fk_program_id[$i],
                    'fk_program_indikator_id' => $request->indikator_prog_id[$i] ?? null,
                    'indikator_prog' => $request->indikator_prog[$i] ?? null,
                    'volume_prog' => $request->volume_prog[$i]  ?? null,
                    'satuan_prog' => $request->satuan_prog[$i]  ?? null,
                    'volume_realisasi' => isset($request->volume_realisasi[$i]) ? str_replace(',', '', $request->volume_realisasi[$i]) : null,
                ];
            }
            if (count($inputDetail) > 0) {
                Program::insert($inputDetail);
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
        return redirect(route('realisasi.program.index'))
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
        // $this->authorize('realisasi.program.update');
        $data =  (object)[
            'type_menu' => $this->type_menu,
            'realisasi' => DB::select("select
                    trk.*,
                    mk.nama_program
                from
                    t_realisasi_program trk
                    join ms_program mk on mk.id =trk.fk_program_id
                where
                    fk_t_realisasi_program_header_id = $id"),
            'action' => route('realisasi.program.update', $id),
            'method' => 'PUT',
            'skpd' => MsSKPD::get(),
            'header' => ProgramHeader::find($id),
            'readonly' => $show
        ];
        // dd($data);
        return view('realisasi::program.form', compact('data'));
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
            ProgramHeader::where('id',$id)->update($input);
            for ($i = 0; $i < count($request->id); $i++) {
                $inputDetail = [
                    'fk_program_indikator_id' => $request->indikator_prog_id[$i] ?? null,
                    'volume_realisasi' => isset($request->volume_realisasi[$i]) ? str_replace(',', '', $request->volume_realisasi[$i]) : null,
                ];
                Program::where('id',$request->id[$i])->update($inputDetail);
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
        return redirect(route('realisasi.program.index'))
            ->with('flash_message', $msg)
            ->with('flash_type', $type);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // $this->authorize('realisasi.program.destroy');
        MsSKPDUnit::find($id)->delete();
        return redirect(route('realisasi.program.index'))
            ->with('flash_message', "Data berhasil dihapus")
            ->with('flash_type', 'primary');
    }
    function getKegiatan()
    {
        $header = ProgramHeader::where('fk_skpd_id', request()->fk_skpd_id)->where('triwulan', request()->triwulan)->where('tahun', session('tahunSession'))->first();

        if ($header) {
            $header = $header;
            $view= null;
        } else {
            $skpd = MsSKPD::find(request()->fk_skpd_id);
            $tahun = session('tahunSession');
            $program = DB::select("select
                ms_program.*,
                mpi.indikator_prog,
                mpi.volume_prog ,
                mpi.satuan_prog,
                mpi.id as ms_program_indikator_id
            from
                `ms_program`
                left join ms_program_indikator mpi  on mpi.fk_program_id = ms_program.id
            where
               `tahun` = '$tahun' and `kode_sub_unit_skpd` = '$skpd->kode_skpd'");
            $data =  (object)[
                'program' => $program,
            ];
            $view = view('realisasi::program._program', compact('data'))->render();
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
        ProgramHeader::where('id',$id)->update($input);
    }
}

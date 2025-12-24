<?php

namespace Modules\Laporan\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Laporan\Exports\TriwulanExport;
use Modules\Laporan\Exports\AllSKPDTriwulanExport;
use Modules\Master\Models\MsProgram;
use Modules\Master\Models\MsSKPDUnit;
use Modules\Realisasi\Models\FaktorTL;
use Modules\Realisasi\Models\ProgramHeader;
use Modules\Realisasi\Models\SubKegiatan;
use Modules\Realisasi\Models\VRKegiatan;
use Modules\Realisasi\Models\VRProgram;
use Modules\Realisasi\Models\VRSubKegiatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

use function Symfony\Component\String\b;

class TriwulanController extends Controller
{

    private $type_menu;
    public function __construct()
    {
        $this->type_menu = 'laporan';
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $data =  (object)[
            'type_menu' => $this->type_menu
        ];
        if (auth()->user()->hasRole("Admin")) {
            $data->skpd_unit = MsSKPDUnit::all();
        }
        return view('laporan::triwulan.index', compact('data'));
    }
    function cetak(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        try {
            $triwulanArray = [
                1 => [1],
                2 => [1, 2],
                3 => [1, 2, 3],
                4 => [1, 2, 3, 4],
            ];

            $kode_sub_unit_skpd = $request->kode_sub_unit_skpd;
            $nama_unit = '';
            $faktortl = [];
            $realisasi = [];
            $no = 1;
            $index = 0;
            $kp_per_t = 0;
            $kinerja_program = [];
            $anggaran_kegiatan = 0;
            $jenis_anggaran = $request->anggaran;

            //jika filter semua skpd
            $anggaranAgg = [];
            $realisasiByUnit = [];
            $r_sub_kegiatan = [];

            //cek jika filter semua skpd / tidak
            if ($kode_sub_unit_skpd=='all') {
                $skpd = MsSKPDUnit::whereRaw("no_urut != '0'")->orderBy('no_urut', 'asc')->get();

                $fkSkpdIds = $skpd->pluck('fk_skpd_id')->unique()->values();
                $kodeUnits = $skpd->pluck('kode_unit')->values();

                $triwulans = $triwulanArray[$request->triwulan];

                $faktortl = FaktorTL::whereIn('fk_skpd_id', $fkSkpdIds)
                ->whereIn('triwulan', $triwulans)
                ->where('tahun', session('tahunSession'))
                ->orderByDesc('triwulan')        // penting: biar first() = triwulan terbesar
                ->get()
                ->groupBy('fk_skpd_id')
                ->map(fn($rows) => $rows->first());   // hasil: [fk_skpd_id => 1 row FaktorTL]

                $r_program_all = VRProgram::whereIn('fk_skpd_id', $fkSkpdIds)
                ->whereIn('triwulan', $triwulans)
                ->where('tahun', session('tahunSession'))
                ->get()
                ->groupBy('fk_skpd_id');

                $r_kegiatan_all = VRKegiatan::whereIn('fk_skpd_id', $fkSkpdIds)
                ->whereIn('triwulan', $triwulans)
                ->where('tahun', session('tahunSession'))
                ->get()
                ->groupBy('fk_skpd_id');

                $r_sub_all = SubKegiatan::selectRaw("t_realisasi_sub_kegiatan.id,
                    fk_skpd_id,
                    fk_sub_kegiatan_id,
                    triwulan,
                    tahun,fk_kegiatan_id,fk_program_id,
                    TRIM(TRAILING '.' FROM TRIM(TRAILING '0' from volume_realisasi )) as volume_realisasi  ,
                    satuan_sub_kegiatan ,
                    anggaran_realisasi,anggaran_sub_kegiatan")
                ->join('t_realisasi_sub_kegiatan_header', 't_realisasi_sub_kegiatan_header.id', '=', 't_realisasi_sub_kegiatan.fk_t_realisasi_sub_kegiatan_header_id')
                ->whereIn('fk_skpd_id', $fkSkpdIds)
                ->whereIn('triwulan', $triwulans)
                ->where('tahun', session('tahunSession'))
                ->where('status_posting', 1)
                ->get()
                ->groupBy('fk_skpd_id');
            
                $r_sub_index_all = $r_sub_all->map(function ($rowsPerSkpd) {
                    // rowsPerSkpd = Collection baris2 untuk 1 skpd
                    return $rowsPerSkpd
                        ->groupBy('fk_sub_kegiatan_id')           // level 1: sub_kegiatan
                        ->map(fn($rows) => $rows->keyBy('triwulan')); // level 2: triwulan
                });

                $programsByUnit = MsProgram::with([
                    'kegiatan.sub_kegiatan',
                    'kegiatan.indikator',
                    'indikator',
                    'sub_kegiatan',
                    'programTahunLalu.sub_kegiatan',
                    'programTahunLalu.indikator',
                    'programTahunLalu.kegiatan.indikator',
                    'programTahunLalu.kegiatan.sub_kegiatan',
                ])->where('tahun', session('tahunSession'))
                ->whereIn('kode_sub_unit_skpd', $kodeUnits)
                ->get()
                ->groupBy('kode_sub_unit_skpd');

                $anggaranAgg = DB::table('t_realisasi_sub_kegiatan as r')
                    ->join('t_realisasi_sub_kegiatan_header as h', 'h.id', '=', 'r.fk_t_realisasi_sub_kegiatan_header_id')
                    ->select(
                        'h.fk_skpd_id',
                        'h.triwulan',
                        'h.tahun',
                        DB::raw('SUM(COALESCE(r.anggaran_realisasi,0)) as realisasi'),
                        DB::raw('SUM(COALESCE(r.anggaran_sub_kegiatan,0)) as target')
                    )
                    ->whereIn('h.fk_skpd_id', $fkSkpdIds)
                    ->whereIn('h.triwulan', $triwulans)
                    ->where('h.tahun', session('tahunSession'))
                    ->where('h.status_posting', 1)
                    ->groupBy('h.fk_skpd_id', 'h.triwulan', 'h.tahun')
                    ->get();

                $anggaranAgg = collect($anggaranAgg)
                    ->groupBy('fk_skpd_id')              // <-- sekarang $rows = Collection per skpd
                    ->map(fn ($rows) => $rows->keyBy('triwulan'));

                foreach ($skpd as $item) {
                    $kode_unit = $item->kode_unit;

                    $r_program_temp = $r_program_all->get($item->fk_skpd_id, collect());

                    $r_kegiatan_temp = $r_kegiatan_all->get($item->fk_skpd_id, collect());

                    $r_sub_kegiatan_temp = $r_sub_all->get($item->fk_skpd_id, collect());

                    $subIndex = $r_sub_index_all->get($item->fk_skpd_id, collect());

                    $progAngIndex = [];
                    $kegAngIndex  = [];
                    
                    foreach ($r_sub_kegiatan_temp as $row) {
                        $t = (int) $row->triwulan;
                        $ang = (float) ($row->anggaran_realisasi ?? 0);

                        $progAngIndex[$row->fk_program_id][$t] = ($progAngIndex[$row->fk_program_id][$t] ?? 0) + $ang;
                        $kegAngIndex[$row->fk_kegiatan_id][$t] = ($kegAngIndex[$row->fk_kegiatan_id][$t] ?? 0) + $ang;
                    }

                    $programs = $programsByUnit->get($item->kode_unit, collect());
                    $nomor = 1;
                    foreach ($programs as  $program) {
                        $sumAnggaranProgram = (float) $program->sub_kegiatan->sum($jenis_anggaran);
                        $programTahunLalu = $program->programTahunLalu;
                        $anggaranProgramTahunLalu = isset($programTahunLalu) ?  $program->programTahunLalu->sub_kegiatan->sum($jenis_anggaran) : 0;
                        $col = 0;
                        $realisasi[$index] = [
                            'kode_unit' => $kode_unit,
                            'background-color' => '#87d1eb;',
                            'type' => 'program',
                            'col' . ++$col => ['type' => 'string', 'value' => $nomor++],
                            'col' . ++$col => ['type' => 'string', 'value' => $program->kode_program],
                            'col' . ++$col => ['type' => 'string', 'value' => $program->nama_program],
                            'col' . ++$col => ['type' => 'string', 'value' => $program->indikator->pluck('indikator_prog')->implode(';')],
                            'col' . ++$col => ['type' => 'string', 'value' => $program->indikator->pluck('keterangan_rpjmd')->implode(';')],
                            'col' . ++$col => ['type' => 'int', 'value' => $program->sub_kegiatan->sum('anggaran_rpjmd')],
                            'col' . ++$col => ['type' => 'string', 'value' => isset($programTahunLalu) ?  $program->programTahunLalu->indikator->pluck('keterangan_rpjmd')->implode(';') : ''],
                            'col' . ++$col => ['type' => 'int', 'value' => $anggaranProgramTahunLalu],
                            'col' . ++$col => ['type' => 'string', 'value' => $program->indikator->pluck('keterangan')->implode(';')],
                            'col' . ++$col => ['type' => 'int', 'value' => $program->sub_kegiatan->sum($jenis_anggaran)],
                        ];
                        $anggaran_kegiatan += $program->sub_kegiatan->sum($jenis_anggaran);
                        $totalRealisasi = 0;
                        $totalVolume = 0;
                        $satuanVolume = null;
                        $volume_prog = (float) optional($program->indikator->first())->volume_prog ?? 0;
                        $kinerja_program_triwulan = [];
                        $volProg = [];
                        $volRealisasiProg = [];
                        for ($i = 1; $i < $request->triwulan + 1; $i++) {
                            $rp = realisasiProgram($r_program_temp, $program->id, $i);
                            $volProg[] = $rp['vArray'];
                            $volRealisasiProg[] = $rp['rArray'];
                            $kinerja_program_triwulan[] = $volume_prog > 0 ? $rp['volume_realisasi'] / $volume_prog * 100:0;
                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $rp['k']];
                            $realisasi[$index]['kinerja_program' . $i] =$volume_prog > 0?  $rp['volume_realisasi'] / $volume_prog * 100:0;
                            $angTw = $progAngIndex[$program->id][$i] ?? 0;
                            $realisasi[$index]['col' . ++$col] = ['type'=>'int','value'=>$angTw];
                            $totalRealisasi += $angTw;
                            $totalVolume += $rp['volume_realisasi'];
                            if ($i == 1) {
                                $satuanVolume = $rp['satuan_prog'];
                            }
                        }

                        $kinerja_program[] = $kinerja_program_triwulan;
                        $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' =>  $this->getkinerjaRealisasi($volRealisasiProg, $satuanVolume)]; #12k
                        $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => $totalRealisasi];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $program->indikator->sum('volume_prog') > 0 ? $totalVolume / $program->indikator->sum('volume_prog') * 100 : 0];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $program->sub_kegiatan->sum($jenis_anggaran) > 0 ?  ($totalRealisasi / $program->sub_kegiatan->sum($jenis_anggaran) * 100) : 0];

                        $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => ($anggaranProgramTahunLalu + $totalRealisasi)];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                        $realisasi[$index]['col' . ++$col] = [
                            'type' => 'persentase',
                            'value' => $sumAnggaranProgram > 0
                                ? ($totalRealisasi / $sumAnggaranProgram) * 100
                                : 0
                        ];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => ''];

                        foreach ($program->kegiatan as  $kegiatan) {
                            $sumAnggaranKegiatan = (float) $kegiatan->sub_kegiatan->sum($jenis_anggaran);
                            $kegiatanTahunLalu = $program->programTahunLalu?->kegiatan->where('kode_kegiatan',$kegiatan->kode_kegiatan)->first() ?? null;
                            $anggaranKegiatanTahunLalu = isset($kegiatanTahunLalu->indikator) ? $kegiatanTahunLalu->sub_kegiatan->sum('anggaran_rpjmd'): 0;
                            $col = 0;
                            $realisasi[++$index] = [
                                'kode_unit' => $kode_unit,
                                'background-color' => '#e7b763',
                                'type' => 'kegiatan',
                                'col' . ++$col => ['type' => 'string', 'value' => null],
                                'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->kode_kegiatan],
                                'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->nama_kegiatan],
                                'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->indikator->pluck('indikator_keg')->implode(';')],
                                'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->indikator->pluck('keterangan_rpjmd')->implode(';')],
                                'col' . ++$col => ['type' => 'int', 'value' => $kegiatan->sub_kegiatan->sum('anggaran_rpjmd')],
                                'col' . ++$col => ['type' => 'string', 'value' => isset($kegiatanTahunLalu->programTahunLalu->indikator) ?  $kegiatan->programTahunLalu->indikator->pluck('keterangan')->implode(';') : ''],
                                'col' . ++$col => ['type' => 'int', 'value' => $anggaranProgramTahunLalu],
                                'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->indikator->pluck('keterangan')->implode(';')],
                                'col' . ++$col => ['type' => 'int', 'value' => $kegiatan->sub_kegiatan->sum($jenis_anggaran)],
                            ];
                            $totalRealisasi = 0;
                            $totalVolume = 0;
                            $satuanVolume = null;
                            $volume_keg  = (float) optional($kegiatan->indikator->first())->volume_keg ?? 0;
                            $kinerja_kegiatan_triwulan = [];
                            $volKeg = [];
                            $volRealisasiKeg = [];
                            for ($i = 1; $i < $request->triwulan + 1; $i++) {
                                $rp = realisasiKegiatan($r_kegiatan_temp, $kegiatan->id, $i);

                                $volKeg[] = $rp['vArray'];
                                $volRealisasiKeg[] = $rp['rArray'];
                                $kinerja_kegiatan_triwulan[] = $volume_keg > 0 ? $rp['volume_realisasi'] / $volume_keg * 100 :0;
                                $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $rp['k']];
                                $realisasi[$index]['kinerja_kegiatan' . $i] = $volume_keg > 0 ? $rp['volume_realisasi'] / $volume_keg * 100 :0;
                                $angTw = $kegAngIndex[$kegiatan->id][$i] ?? 0;
                                $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => $angTw];
                                $totalRealisasi += $angTw;
                                $totalVolume += $rp['volume_realisasi'];
                                if ($i == 1) {
                                    $satuanVolume = $rp['satuan_kegiatan'];
                                }
                            }
                            
                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' =>  $this->getkinerjaRealisasiK($volRealisasiKeg, $satuanVolume)]; #12k
                            // $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => $totalRealisasi];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $kegiatan->indikator->sum('volume_keg') > 0 ? $totalVolume/$kegiatan->indikator->sum('volume_keg')*100:0];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $kegiatan->sub_kegiatan->sum($jenis_anggaran) > 0 ? ($totalRealisasi / $kegiatan->sub_kegiatan->sum($jenis_anggaran) * 100):0];

                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => ($anggaranKegiatanTahunLalu + $totalRealisasi)];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                            $realisasi[$index]['col' . ++$col] = [
                                'type' => 'persentase',
                                'value' => ($anggaranKegiatanTahunLalu > 0 && $sumAnggaranProgram > 0)
                                    ? (($sumAnggaranKegiatan + $totalRealisasi) / $sumAnggaranProgram) * 100
                                    : 0
                            ];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => ''];


                            foreach ($kegiatan->sub_kegiatan as $sub_kegiatan) {
                                $subKegiatanTahunLalu = isset($kegiatanTahunLalu->sub_kegiatan) ?  $kegiatanTahunLalu->sub_kegiatan->where('kode_sub_kegiatan',$sub_kegiatan->kode_sub_kegiatan)->first():null;;
                                $anggaranSubKegiatanTahunLalu = $subKegiatanTahunLalu->anggaran_rpjmd ?? 0;
                                $col = 0;
                                $realisasi[++$index] = [
                                    'kode_unit' => $kode_unit,
                                    'background-color' => 'none',
                                    'type' => 'sub_kegiatan',
                                    'col' . ++$col => ['type' => 'string', 'value' => null],
                                    'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->kode_sub_kegiatan],
                                    'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->nama_sub_kegiatan],
                                    'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->indikator_sub],
                                    'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->keterangan_rpjmd],
                                    'col' . ++$col => ['type' => 'int', 'value' => $sub_kegiatan->anggaran_rpjmd],
                                    'col' . ++$col => ['type' => 'string', 'value' =>$subKegiatanTahunLalu->keterangan_rpjmd ?? null],
                                    'col' . ++$col => ['type' => 'int', 'value' => $anggaranSubKegiatanTahunLalu],
                                    'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->keterangan],
                                    'col' . ++$col => ['type' => 'int', 'value' => $sub_kegiatan->anggaran_murni],
                                ];
                                $totalRealisasi = 0;
                                $totalVolume = 0;
                                $satuanVolume = null;
                                for ($i = 1; $i < $request->triwulan + 1; $i++) {
                                    $rp = $subIndex->get($sub_kegiatan->id)?->get($i);

                                    $vol = (float) ($rp->volume_realisasi ?? 0);
                                    $sat = $rp->satuan_sub_kegiatan ?? '';
                                    $ang = (float) ($rp->anggaran_realisasi ?? 0);

                                    // dd($rp->volume_realisasi);
                                    $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $vol.' '.$sat];
                                    $realisasi[$index]['kinerja_sub_kegiatan'.$i] = ($sub_kegiatan && $sub_kegiatan->volume_sub > 0) ? ($vol / $sub_kegiatan->volume_sub * 100) : 0;
                                    $realisasi[$index]['target_anggaran_sub_kegiatan' . $i] = $rp->anggaran_sub_kegiatan  ?? 0;
                                    $realisasi[$index]['realisasi_anggaran_sub_kegiatan' . $i] = $ang ?? 0;
                                    $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => $ang];
                                    $totalRealisasi += $ang ?? 0;
                                    $totalVolume += $vol ?? 0;
                                    if ($i == 1) {
                                        $satuanVolume = $sat ?? null;
                                    }
                                }
                                $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                                $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => $totalRealisasi];
                                $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $sub_kegiatan->volume_sub >  0 ? $totalVolume/$sub_kegiatan->volume_sub*100:0];
                                $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $sub_kegiatan->anggaran_murni > 0 ? ($totalRealisasi / $sub_kegiatan->anggaran_murni * 100):0];

                                $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                                $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => ($anggaranSubKegiatanTahunLalu + $totalRealisasi)];
                                $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                                $xx = $sub_kegiatan->anggaran_murni > 0 ? ($sub_kegiatan->anggaran_murni + $totalRealisasi) / $sub_kegiatan->anggaran_murni * 100 :0;
                                $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $anggaranSubKegiatanTahunLalu > 0 ? $xx : 0];
                                $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => ''];
                            }
                        }
                        ++$index;
                    }
                }

                $realisasiByUnit = collect($realisasi)->groupBy('kode_unit');
            }else{
                $skpd = MsSKPDUnit::where('kode_unit', $request->kode_sub_unit_skpd ?? auth()->user()->unit->kode_unit)->first();
                
                $nama_unit = $skpd->nama_unit;

                $faktortl = FaktorTL::where('fk_skpd_id', $skpd->fk_skpd_id)->where('triwulan', request()->triwulan)->where('tahun', session('tahunSession'))->first();
                $r_program = VRProgram::where('fk_skpd_id', $skpd->fk_skpd_id)->whereIn('triwulan', $triwulanArray[request()->triwulan])->where('tahun', session('tahunSession'))->get();
                // dd(VRProgram::all());
                $r_kegiatan = VRKegiatan::where('fk_skpd_id', $skpd->fk_skpd_id)->whereIn('triwulan', $triwulanArray[request()->triwulan])->where('tahun', session('tahunSession'))->get();

                $r_sub_kegiatan = SubKegiatan::selectRaw("	t_realisasi_sub_kegiatan.id,
                fk_skpd_id,
                fk_sub_kegiatan_id,
                triwulan,
                tahun,fk_kegiatan_id,fk_program_id,
                TRIM(TRAILING '.' FROM TRIM(TRAILING '0' from volume_realisasi )) as volume_realisasi  ,
                satuan_sub_kegiatan ,
                anggaran_realisasi,anggaran_sub_kegiatan")->join('t_realisasi_sub_kegiatan_header', 't_realisasi_sub_kegiatan_header.id', '=', 't_realisasi_sub_kegiatan.fk_t_realisasi_sub_kegiatan_header_id')
                    ->where('fk_skpd_id', $skpd->fk_skpd_id)->whereIn('triwulan', $triwulanArray[request()->triwulan])->where('tahun', session('tahunSession'))
                    ->where('status_posting', 1)->orderBy('fk_sub_kegiatan_id')->get();
                $programs = MsProgram::with([
                    'kegiatan.sub_kegiatan',
                    'kegiatan.indikator',
                    'indikator',
                    'sub_kegiatan',
                    'programTahunLalu.sub_kegiatan',
                    'programTahunLalu.indikator',
                    'programTahunLalu.kegiatan.indikator',
                    'programTahunLalu.kegiatan.sub_kegiatan',
                ])->where('tahun', session('tahunSession'))
                    ->where('kode_sub_unit_skpd', $request->kode_sub_unit_skpd ?? auth()->user()->unit->kode_unit)->get();
                    
                foreach ($programs as  $program) {
                    $programTahunLalu = $program->programTahunLalu;
                    $anggaranProgramTahunLalu = isset($programTahunLalu) ?  $program->programTahunLalu->sub_kegiatan->sum($jenis_anggaran) : 0;
                    $col = 0;
                    $realisasi[$index] = [
                        'background-color' => '#87d1eb',
                        'type' => 'program',
                        'col' . ++$col => ['type' => 'string', 'value' => $no++],
                        'col' . ++$col => ['type' => 'string', 'value' => $program->kode_program],
                        'col' . ++$col => ['type' => 'string', 'value' => $program->nama_program],
                        'col' . ++$col => ['type' => 'string', 'value' => $program->indikator->pluck('indikator_prog')->implode(';')],
                        'col' . ++$col => ['type' => 'string', 'value' => $program->indikator->pluck('keterangan_rpjmd')->implode(';')],
                        'col' . ++$col => ['type' => 'int', 'value' => $program->sub_kegiatan->sum('anggaran_rpjmd')],
                        'col' . ++$col => ['type' => 'string', 'value' => isset($programTahunLalu) ?  $program->programTahunLalu->indikator->pluck('keterangan_rpjmd')->implode(';') : ''],
                        'col' . ++$col => ['type' => 'int', 'value' => $anggaranProgramTahunLalu],
                        'col' . ++$col => ['type' => 'string', 'value' => $program->indikator->pluck('keterangan')->implode(';')],
                        'col' . ++$col => ['type' => 'int', 'value' => $program->sub_kegiatan->sum($jenis_anggaran)],
                    ];
                    $anggaran_kegiatan += $program->sub_kegiatan->sum($jenis_anggaran);
                    $totalRealisasi = 0;
                    $totalVolume = 0;
                    $satuanVolume = null;
                    $volume_prog = $program->indikator->first()->volume_prog;
                    $kinerja_program_triwulan = [];
                    $volProg = [];
                    $volRealisasiProg = [];
                    for ($i = 1; $i < $request->triwulan + 1; $i++) {
                        $rp = realisasiProgram($r_program, $program->id, $i);
                        $volProg[] = $rp['vArray'];
                        $volRealisasiProg[] = $rp['rArray'];
                        $kinerja_program_triwulan[] = $volume_prog > 0 ? $rp['volume_realisasi'] / $volume_prog * 100:0;
                        $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $rp['k']];
                        $realisasi[$index]['kinerja_program' . $i] =$volume_prog > 0?  $rp['volume_realisasi'] / $volume_prog * 100:0;
                        $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => realisasiAnggaranProgram($r_sub_kegiatan, $program->id, $i)];
                        $totalRealisasi += realisasiAnggaranProgram($r_sub_kegiatan, $program->id, $i);
                        $totalVolume += $rp['volume_realisasi'];
                        if ($i == 1) {
                            $satuanVolume = $rp['satuan_prog'];
                        }
                    }

                    $kinerja_program[] = $kinerja_program_triwulan;
                    $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' =>  $this->getkinerjaRealisasi($volRealisasiProg, $satuanVolume)]; #12k
                    $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => $totalRealisasi];
                    $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $program->indikator->sum('volume_prog') > 0 ? $totalVolume / $program->indikator->sum('volume_prog') * 100 : 0];
                    $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $program->sub_kegiatan->sum($jenis_anggaran) > 0 ?  ($totalRealisasi / $program->sub_kegiatan->sum($jenis_anggaran) * 100) : 0];

                    $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                    $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => ($anggaranProgramTahunLalu + $totalRealisasi)];
                    $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                    $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $anggaranProgramTahunLalu > 0 && ($program->sub_kegiatan->sum($jenis_anggaran) * 100) > 0 ? ($program->sub_kegiatan->sum($jenis_anggaran) + $totalRealisasi) / $program->sub_kegiatan->sum($jenis_anggaran) * 100 : 0];
                    $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => ''];

                    foreach ($program->kegiatan as  $kegiatan) {

                        $kegiatanTahunLalu = $program->programTahunLalu?->kegiatan->where('kode_kegiatan',$kegiatan->kode_kegiatan)->first() ?? null;
                        $anggaranKegiatanTahunLalu = isset($kegiatanTahunLalu->indikator) ? $kegiatanTahunLalu->sub_kegiatan->sum('anggaran_rpjmd'): 0;
                        $col = 0;
                        $realisasi[++$index] = [
                            'background-color' => '#e7b763',
                            'type' => 'kegiatan',
                            'col' . ++$col => ['type' => 'string', 'value' => null],
                            'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->kode_kegiatan],
                            'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->nama_kegiatan],
                            'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->indikator->pluck('indikator_keg')->implode(';')],
                            'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->indikator->pluck('keterangan_rpjmd')->implode(';')],
                            'col' . ++$col => ['type' => 'int', 'value' => $kegiatan->sub_kegiatan->sum('anggaran_rpjmd')],
                            'col' . ++$col => ['type' => 'string', 'value' => isset($kegiatanTahunLalu->programTahunLalu->indikator) ?  $kegiatan->programTahunLalu->indikator->pluck('keterangan')->implode(';') : ''],
                            'col' . ++$col => ['type' => 'int', 'value' => $anggaranProgramTahunLalu],
                            'col' . ++$col => ['type' => 'string', 'value' => $kegiatan->indikator->pluck('keterangan')->implode(';')],
                            'col' . ++$col => ['type' => 'int', 'value' => $kegiatan->sub_kegiatan->sum($jenis_anggaran)],
                        ];
                        $totalRealisasi = 0;
                        $totalVolume = 0;
                        $satuanVolume = null;
                        $volume_keg = $kegiatan->indikator->first()->volume_keg;
                        $kinerja_kegiatan_triwulan = [];
                        $volKeg = [];
                        $volRealisasiKeg = [];
                        for ($i = 1; $i < $request->triwulan + 1; $i++) {
                            $rp = realisasiKegiatan($r_kegiatan, $kegiatan->id, $i);
                            // if ($kegiatan->id==1254) {
                            //     dd($kegiatan->indikator);
                            // }
                            $volKeg[] = $rp['vArray'];
                            $volRealisasiKeg[] = $rp['rArray'];
                            $kinerja_program_triwulan[] = $volume_keg > 0 ? $rp['volume_realisasi'] / $volume_keg * 100 :0;
                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $rp['k']];
                            $realisasi[$index]['kinerja_kegiatan' . $i] = $volume_keg > 0 ? $rp['volume_realisasi'] / $volume_keg * 100 :0;
                            $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => realisasiAnggaranKegiatan($r_sub_kegiatan, $kegiatan->id, $i)];
                            $totalRealisasi += realisasiAnggaranKegiatan($r_sub_kegiatan, $kegiatan->id, $i);
                            $totalVolume += $rp['volume_realisasi'];
                            if ($i == 1) {
                                $satuanVolume = $rp['satuan_kegiatan'];
                            }
                        }
                        $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' =>  $this->getkinerjaRealisasiK($volRealisasiKeg, $satuanVolume)]; #12k
                        $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => $totalRealisasi];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $kegiatan->indikator->sum('volume_keg') > 0 ? $totalVolume/$kegiatan->indikator->sum('volume_keg')*100:0];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $kegiatan->sub_kegiatan->sum($jenis_anggaran) > 0 ? ($totalRealisasi / $kegiatan->sub_kegiatan->sum($jenis_anggaran) * 100):0];

                        $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => ($anggaranKegiatanTahunLalu + $totalRealisasi)];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $anggaranKegiatanTahunLalu > 0 ? ($kegiatan->sub_kegiatan->sum($jenis_anggaran) + $totalRealisasi) / $program->sub_kegiatan->sum($jenis_anggaran) * 100 : 0];
                        $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => ''];


                        foreach ($kegiatan->sub_kegiatan as $sub_kegiatan) {
                            $subKegiatanTahunLalu = isset($kegiatanTahunLalu->sub_kegiatan) ?  $kegiatanTahunLalu->sub_kegiatan->where('kode_sub_kegiatan',$sub_kegiatan->kode_sub_kegiatan)->first():null;;
                            $anggaranSubKegiatanTahunLalu = $subKegiatanTahunLalu->anggaran_rpjmd ?? 0;
                            $col = 0;
                            $realisasi[++$index] = [
                                'background-color' => 'none',
                                'type' => 'sub_kegiatan',
                                'col' . ++$col => ['type' => 'string', 'value' => null],
                                'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->kode_sub_kegiatan],
                                'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->nama_sub_kegiatan],
                                'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->indikator_sub],
                                'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->keterangan_rpjmd],
                                'col' . ++$col => ['type' => 'int', 'value' => $sub_kegiatan->anggaran_rpjmd],
                                'col' . ++$col => ['type' => 'string', 'value' =>$subKegiatanTahunLalu->keterangan_rpjmd ?? null],
                                'col' . ++$col => ['type' => 'int', 'value' => $anggaranSubKegiatanTahunLalu],
                                'col' . ++$col => ['type' => 'string', 'value' => $sub_kegiatan->keterangan],
                                'col' . ++$col => ['type' => 'int', 'value' => $sub_kegiatan->anggaran_murni],
                            ];
                            $totalRealisasi = 0;
                            $totalVolume = 0;
                            $satuanVolume = null;
                            for ($i = 1; $i < $request->triwulan + 1; $i++) {
                                $rp = $r_sub_kegiatan->where('fk_sub_kegiatan_id',$sub_kegiatan->id)->where('triwulan',$i)->first();
                                $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => ($rp->volume_realisasi ?? 0) . ' ' . ($rp->satuan_sub_kegiatan ?? '')];
                                $realisasi[$index]['kinerja_sub_kegiatan' . $i] = $sub_kegiatan!=null && $sub_kegiatan->volume_sub>0 ? ($rp->volume_realisasi??0)/$sub_kegiatan->volume_sub*100:0;
                                $realisasi[$index]['target_anggaran_sub_kegiatan' . $i] = $rp->anggaran_murni ?? 0;
                                $realisasi[$index]['realisasi_anggaran_sub_kegiatan' . $i] = $rp->anggaran_realisasi ?? 0;
                                $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => $rp->anggaran_realisasi ?? 0];
                                $totalRealisasi += $rp->anggaran_realisasi ?? 0;
                                $totalVolume += $rp->volume_realisasi ?? 0;
                                if ($i == 1) {
                                    $satuanVolume = $rp->satuan_sub_kegiatan;
                                }
                            }
                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => $totalRealisasi];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $sub_kegiatan->volume_sub >  0 ? $totalVolume/$sub_kegiatan->volume_sub*100:0];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $sub_kegiatan->anggaran_murni > 0 ? ($totalRealisasi / $sub_kegiatan->anggaran_murni * 100):0];

                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'int', 'value' => ($anggaranSubKegiatanTahunLalu + $totalRealisasi)];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => $totalVolume . ' ' . $satuanVolume];
                            $xx = $sub_kegiatan->anggaran_murni > 0 ? ($sub_kegiatan->anggaran_murni + $totalRealisasi) / $sub_kegiatan->anggaran_murni * 100 :0;
                            $realisasi[$index]['col' . ++$col] = ['type' => 'persentase', 'value' => $anggaranSubKegiatanTahunLalu > 0 ? $xx : 0];
                            $realisasi[$index]['col' . ++$col] = ['type' => 'string', 'value' => ''];
                        }
                    }
                    ++$index;
                }
            }

            $data = (object)[
                'triwulan'          => $request->triwulan,
                'realisasi'         => $realisasi,
                'dinas'             => $nama_unit,
                'r_sub_kegiatan'    => $r_sub_kegiatan,
                'faktortl'          => $faktortl,
                'anggaran_kegiatan' => $anggaran_kegiatan,
                'jenis_anggaran'    => $jenis_anggaran,
                'skpd'              => $skpd,
                'realisasiByUnit'   => $realisasiByUnit,
                'anggaranAgg'       => $anggaranAgg
            ];
            // dd($data);
            // return view('laporan::triwulan.cetakAll', compact('data'));
            $tahun = session('tahunSession');
            $title = "Monitoring OPD Realisasi Triwulan {$request->triwulan} Tahun {$tahun} ";
            if ($request->type == 'PDF') {
                if($kode_sub_unit_skpd=='all'){
                    $chunkSize = 5; // coba 5 dulu, kalau masih berat => 4
                    $triwulan = $request->triwulan;
                    $tmpDir = 'tmp';
                    Storage::makeDirectory($tmpDir);

                    // Biar unik (tidak tabrakan kalau banyak user)
                    $batchId = now()->format('Ymd_His') . '_' . uniqid();
                    $batchDir = "$tmpDir/$batchId";
                    Storage::makeDirectory($batchDir);

                    $pdfPaths = [];
                    $chunks = $skpd->chunk($chunkSize);

                    foreach ($chunks as $idx => $skpdChunk) {
                        $payload = [
                            'skpd'              => $skpdChunk,
                            'realisasiByUnit'    => $realisasiByUnit,
                            'anggaranAgg'        => $anggaranAgg,
                            'faktortl'           => $faktortl,
                            'dinas'              => $nama_unit,
                            'jenis_anggaran'     => $jenis_anggaran,
                            'anggaran_kegiatan'  => $anggaran_kegiatan,
                            'triwulan'           => $triwulan, // gunakan ini di blade (hindari request() berulang)
                            'type'               => 'PDF',
                        ];

                        $pdf = Pdf::loadView('laporan::triwulan.cetakAll', $payload)
                            ->setPaper('a4', 'landscape');

                        $partName = "laporan_part_" . str_pad(($idx+1), 2, '0', STR_PAD_LEFT) . ".pdf";
                        $partPath = "$batchDir/$partName";

                        Storage::put($partPath, $pdf->output());
                        $pdfPaths[] = $partPath;
                    }

                    // ===== buat ZIP =====
                    $zipName = "monitoring_opd_realisasi_triwulan_{$triwulan}_tahun_{$tahun}_$batchId.zip";
                    $zipStoragePath = "$tmpDir/$zipName";
                    $zipFullPath = storage_path("app/$zipStoragePath");

                    $zip = new ZipArchive();
                    if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                        // bersihkan jika gagal
                        Storage::deleteDirectory($batchDir);
                        abort(500, "Gagal membuat ZIP");
                    }

                    foreach ($pdfPaths as $p) {
                        $full = storage_path("app/$p");
                        $zip->addFile($full, basename($p)); // nama file di dalam zip
                    }
                    $zip->close();

                    // ===== optional: hapus part PDF setelah zip jadi =====
                    Storage::deleteDirectory($batchDir);

                    // download ZIP, lalu hapus file zip setelah dikirim
                    return response()->download($zipFullPath, $zipName)->deleteFileAfterSend(true);
                }else{
                    $pdf = Pdf::loadView('laporan::triwulan.cetak2', (array)$data)->setPaper('a4', 'landscape');
                }
                return $pdf->download("$title.pdf");
            } else {
                
                if($kode_sub_unit_skpd=='all'){
                    $title = "Monitoring Semua OPD Realisasi Triwulan {$request->triwulan} Tahun {$tahun} ";
                    return Excel::download(new AllSKPDTriwulanExport($data), "$title.xlsx");
                }else{
                    return Excel::download(new TriwulanExport($data), "$title.xlsx");
                }
            }
            // return view('laporan::triwulan.cetak', compact('data'));
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
    function cekFaktor(Request $request)
    {
        if ($request->kode_sub_unit_skpd!='all') {
            $skpd = MsSKPDUnit::where('kode_unit', $request->kode_sub_unit_skpd ?? auth()->user()->unit->kode_unit)->first();
            $faktor_tl = FaktorTL::where('tahun', session('tahunSession'))->where('fk_skpd_id', $skpd->fk_skpd_id)->where('triwulan', $request->triwulan)->count('Id');
        }else{
            $faktor_tl = FaktorTL::where('tahun', session('tahunSession'))->where('triwulan', $request->triwulan)->count('Id');
        }
        $data = (object)[
            'faktor_tl' => $faktor_tl,
            'kode_sub_unit_skpd' => $request->kode_sub_unit_skpd
        ];
        return view('laporan::triwulan._form', compact('data'));
    }
    function getkinerjaRealisasi($data, $satuanVolume)
    {

        // Initialize an array to store the sums by index
        $sums = [];

        // Loop through the array to sum by index
        foreach ($data as $subArray) {
            foreach ($subArray as $index => $value) {
                if (!isset($sums[$index])) {
                    $sums[$index] = 0; // Initialize if not set
                }
                $sums[$index] += $value;
            }
        }

        // // Print the results
        $totalKinerjaProgram = "";
        foreach ($sums as $index => $sum) {
            $totalKinerjaProgram .= "$sum $satuanVolume;";
        }
        return $totalKinerjaProgram;
    }
    function getkinerjaRealisasiK($data, $satuanVolume)
    {

        // Initialize an array to store the sums by index
        $sums = [];

        // Loop through the array to sum by index
        foreach ($data as $subArray) {
            foreach ($subArray as $index => $value) {
                if (!isset($sums[$index])) {
                    $sums[$index] = 0; // Initialize if not set
                }
                $sums[$index] += $value;
            }
        }

        // // Print the results
        $totalKinerjaKegiatan = "";
        foreach ($sums as $index => $sum) {
            $totalKinerjaKegiatan .= "$sum $satuanVolume;";
        }
        return $totalKinerjaKegiatan;
    }
}

<style>
    table.bordered {
        border-collapse: collapse;
    }

    table.bordered td,
    table.bordered th {
        border: 1px solid black;
    }
    th, td {
    padding: 5px;
    }
</style>
@php
    $triwulanString = [
        1 => 'I',
        2 => 'II',
        3 => 'III',
        4 => 'IV',
    ];
    $row3Value = [
        1 => 8,
        2 => 9,
        3 => 10,
        4 => 11,
    ];
    $triwulan = request()->triwulan;
    $row1 = 2 * $triwulan;
@endphp
<table class="table-striped table bordered" id="table-userx">
    <thead>
        <tr>
            <th rowspan="2" width="5%">No</th>
            <th rowspan="2">Kode</th>
            <th rowspan="2">Urusan /Bidang Urusan Pemerintahan Daerah Dan Program / Kegiatan </th>
            <th rowspan="2">Indikator Kinerja Program (Outcome) / Kegiatan (Output)</th>
            <th rowspan="2" colspan="2">Target RPJMD Kabupaten / kota pada Tahun {{ session('tahunSession') }} (Akhir Periode RPJMD)</th>
            <th rowspan="2" colspan="2">Realisasi Capaian Kerja RPJMD Kabupaten/ kota Tahun Lalu (n-2)</th>
            <th rowspan="2" colspan="2">Target dan Anggaran RKPD Kabupaten/ kota (Tahun -1 ) yang di evaluasi</th>
            <th colspan="{{ $row1 }}">Realisasi Kinerja Pada Triwulan</th> {{--  --}}
            <th rowspan="2" colspan="2">Realisasi Capaian Kinerja dan Anggaran RKPD yang dievaluasi {{ session('tahunSession') }}</th>
            <th rowspan="2" colspan="2">Tingkat Capaian Kinerja dan Realisasi Anggaran RKPD (%)	</th>
            <th rowspan="2" colspan="2">Realisasi Kinerja dan Anggaran RKPD s/d Tahun {{ session('tahunSession') }}</th>
            <th rowspan="2" colspan="2">Tingkat Capaian Kinerja & Realisasi Anggaran RPJMD s/d Tahun  {{ session('tahunSession') }} (%)</th>
            <th rowspan="2">Perangkat Daerah Penanggung jawab</th>
            <th rowspan="2">Ket</th>
        </tr>
        <tr>
            @for ($i = 1; $i < $triwulan+1; $i++)
                <td colspan="2" align="center">{{ $triwulanString[$i] }} </td>
            @endfor
        </tr>
        <tr>
            <td rowspan="2" align="center">1</td>
            <td rowspan="2" align="center">2</td>
            <td rowspan="2" align="center">3</td>
            <td rowspan="2" align="center">4</td>
            <td colspan="2" align="center">5</td>
            <td colspan="2" align="center">6</td>
            <td colspan="2" align="center">7</td>
            @for ($i = 8; $i < $triwulan +8; $i++)
            <td colspan="2" align="center">{{ $i }}</td>
            @endfor
            <td colspan="2" align="center">{{ 8+$triwulan }}</td>
            <td colspan="2" align="center">{{ 9+$triwulan }}</td>
            <td colspan="2" align="center">{{ 10+$triwulan }}</td>
            <td colspan="2" align="center">{{ 11+$triwulan }}</td>
            <td rowspan="2" align="center">{{ 12+$triwulan }}</td>
            <td rowspan="2" align="center">{{ 13+$triwulan }}</td>
        </tr>
        <tr>
            @for ($i = 1; $i < $triwulan+1; $i++)
                <td>K</td>
                <td>Rp</td>
            @endfor
            <td>K</td>
            <td>Rp</td>
            <td>K</td>
            <td>Rp</td>
            <td>K</td>
            <td>Rp</td>
            <td>K</td>
            <td>Rp</td>
            <td>K</td>
            <td>Rp</td>
            <td>K</td>
            <td>Rp</td>
            <td>K</td>
            <td>Rp</td>
        </tr>
    </thead>
    <tbody>

        @foreach ($data->program as $program)
            <tr style="background-color: #87d1eb;">
                <td align="center">{{ $loop->iteration }}</td>
                <td>{{ $program->kode_program }}</td>
                <td>{{ $program->nama_program }}</td>
                <td>{{ $program->indikator->pluck('indikator_prog')->implode(';') }}</td>
                <td>{{ $program->indikator->pluck('keterangan_rpjmd')->implode(';') }}</td>
                <td align="right">{{ number_format($program->sub_kegiatan->sum('anggaran_rpjmd'))}}</td>

                @php
                    $programTahunLalu = $program->programTahunLalu;
                @endphp
                <td>{{ isset($programTahunLalu )?  $program->programTahunLalu->indikator->pluck('keterangan_rpjmd')->implode(';') :''}}</td>
                <td align="right">{{ isset($programTahunLalu )?  number_format($program->programTahunLalu->sub_kegiatan->sum('anggaran_murni')):0}}</td>
                <td>{{ $program->indikator->pluck('keterangan')->implode(';') }}</td>
                @php
                    $col7 = $program->sub_kegiatan->sum('anggaran_murni');
                @endphp
                <td align="right">{{ number_format($col7)}}</td>
                {{-- start Realisasi Program--}}
                @php
                    $totalRealisasi = 0;
                    $totalVolumeRealiasi = 0;
                @endphp
                @if (in_array($triwulan,[1,2,3,4]))
                    @php
                        $rp = realisasiProgram($data->r_program,$program->id,1);
                        $satuan_prog =$rp['satuan_prog'];
                        $totalVolumeRealiasi += $rp['volume_realisasi'];
                        $r_anggaran_program = realisasiAnggaranProgram($data->r_sub_kegiatan,$program->id,1);
                        $totalRealisasi +=$r_anggaran_program??0;
                    @endphp
                    <td>
                        {{ $rp['k'] ?? null }}
                    </td>
                    <td align="right">
                        {{ number_format($r_anggaran_program??0) }}
                    </td>
                @endif
                @if (in_array($triwulan,[2,3,4]))
                    @php
                        $rp = realisasiProgram($data->r_program,$program->id,2);
                        $totalVolumeRealiasi += $rp['volume_realisasi'];
                        $r_anggaran_program = realisasiAnggaranProgram($data->r_sub_kegiatan,$program->id,2);
                        $totalRealisasi +=$r_anggaran_program??0;
                    @endphp
                    <td>
                        {{ $rp['k'] ?? null }}
                    </td>
                    <td align="right">
                        {{ number_format($r_anggaran_program??0) }}
                    </td>
                @endif
                @if (in_array($triwulan,[3,4]))
                    @php
                        $rp = realisasiProgram($data->r_program,$program->id,3);
                        $totalVolumeRealiasi += $rp['volume_realisasi'];
                        $r_anggaran_program = realisasiAnggaranProgram($data->r_sub_kegiatan,$program->id,3);
                        $totalRealisasi +=$r_anggaran_program??0;
                    @endphp
                    <td>
                        {{ $rp['k'] ?? null }}
                    </td>
                    <td align="right">
                        {{ number_format($r_anggaran_program??0) }}
                    </td>
                @endif
                @if (in_array($triwulan,[4]))
                    @php
                        $rp = realisasiProgram($data->r_program,$program->id,4);
                        $totalVolumeRealiasi += $rp['volume_realisasi'];
                        $r_anggaran_program = realisasiAnggaranProgram($data->r_sub_kegiatan,$program->id,4);
                        $totalRealisasi +=$r_anggaran_program??0;
                    @endphp
                    <td>
                        {{ $rp['k'] ?? null }}
                    </td>
                    <td align="right">
                        {{ number_format($r_anggaran_program??0) }}
                    </td>
                @endif
                <td> {{ number_format($totalVolumeRealiasi??0) }} {{  $satuan_prog  }}</td>
                <td align="right">{{ number_format($totalRealisasi??0) }}</td>
                <td> {{ number_format($totalVolumeRealiasi??0) }} {{  $satuan_prog  }}</td>
                <td>{{ number_format($totalRealisasi/$col7*100, 2, '.', '') }} %</td>
                {{-- end Realisasi Program--}}
            </tr>
            @foreach ($program->kegiatan as $kegiatan)
            <tr style="background-color: #e7b763">
                <td></td>
                <td>{{ $kegiatan->kode_kegiatan }}</td>
                <td>{{ $kegiatan->nama_kegiatan }}</td>
                <td>{{ $kegiatan->indikator->pluck('indikator_keg')->implode(';') }}</td>
                <td>{{ $kegiatan->indikator->pluck('keterangan_rpjmd')->implode(';') }}</td>
                <td align="right">{{ isset($kegiatan->sub_kegiatan) ? number_format($kegiatan->sub_kegiatan->sum('anggaran_rpjmd') ) : null}}</td>
                @php
                    $kegiatanTahunLalu = $program->programTahunLalu?->kegiatan->where('kode_kegiatan',$kegiatan->kode_kegiatan)->first() ?? null;
                @endphp
                <td>{{ isset($kegiatanTahunLalu->indikator) ?  $kegiatanTahunLalu->indikator->pluck('keterangan')->implode(';') : ''}}</td>
                <td align="right">{{ isset($kegiatanTahunLalu->indikator) ? number_format($kegiatanTahunLalu->sub_kegiatan->sum('anggaran_rpjmd')): ''}}</td>
                <td>{{ $kegiatan->indikator->pluck('keterangan')->implode(';') }}</td>
                <td align="right">{{ number_format($kegiatan->sub_kegiatan->sum('anggaran_murni'))}}</td>

                {{-- start Realisasi Kegiatan --}}

                @php
                    $totalRealisasi = 0;
                    $totalVolumeRealiasi = 0;
                @endphp
                @if (in_array($triwulan,[1,2,3,4]))
                    @php
                        $rk = realisasiKegiatan($data->r_kegiatan,$kegiatan->id,1);
                        $satuanKegiatan =$rk['satuan_kegiatan'];
                        $totalVolumeRealiasi += $rk['volume_realisasi'];
                        $r_anggaran_kegiatan = realisasiAnggaranKegiatan($data->r_sub_kegiatan,$kegiatan->id,1);
                        $totalRealisasi +=$r_anggaran_kegiatan??0;
                    @endphp
                    <td>
                        {{ $rk['k'] ?? null }}
                    </td>
                    <td align="right">
                        {{ number_format($r_anggaran_kegiatan??0) }}
                    </td>
                @endif
                @if (in_array($triwulan,[2,3,4]))
                    @php
                        $rk = realisasiKegiatan($data->r_kegiatan,$kegiatan->id,2);
                        $totalVolumeRealiasi += $rk['volume_realisasi'];
                        $r_anggaran_kegiatan = realisasiAnggaranKegiatan($data->r_sub_kegiatan,$kegiatan->id,2);
                        $totalRealisasi +=$r_anggaran_kegiatan??0;
                    @endphp
                    <td>
                        {{ $rk['k'] ?? null }}
                    </td>
                    <td align="right">
                        {{ number_format($r_anggaran_kegiatan??0) }}
                    </td>
                @endif
                @if (in_array($triwulan,[3,4]))
                    @php
                        $rk = realisasiKegiatan($data->r_kegiatan,$kegiatan->id,3);
                        $totalVolumeRealiasi += $rk['volume_realisasi'];
                        $r_anggaran_kegiatan = realisasiAnggaranKegiatan($data->r_sub_kegiatan,$kegiatan->id,3);
                        $totalRealisasi +=$r_anggaran_kegiatan??0;
                    @endphp
                    <td>
                        {{ $rk['k'] ?? null }}
                    </td>
                    <td align="right">
                        {{ number_format($r_anggaran_kegiatan??0) }}
                    </td>
                @endif
                @if (in_array($triwulan,[4]))
                    @php
                        $rk = realisasiKegiatan($data->r_kegiatan,$kegiatan->id,4);
                        $totalVolumeRealiasi += $rk['volume_realisasi'];
                        $r_anggaran_kegiatan = realisasiAnggaranKegiatan($data->r_sub_kegiatan,$kegiatan->id,4);
                        $totalRealisasi +=$r_anggaran_kegiatan??0;
                    @endphp
                    <td>
                        {{ $rk['k'] ?? null }}
                    </td>
                    <td align="right">
                        {{ number_format($r_anggaran_kegiatan??0) }}
                    </td>
                @endif
                <td> {{ number_format($totalVolumeRealiasi??0) }} {{  $satuanKegiatan  }}</td>
                <td align="right">{{ number_format($totalRealisasi??0) }}</td>
                <td> {{ number_format($totalVolumeRealiasi??0) }} {{  $satuanKegiatan  }}</td>
                <td>{{ number_format($totalRealisasi/$col7*100, 2, '.', '') }} %</td>
                {{-- end Realisasi Kegiatan --}}
            </tr>
                @foreach ($kegiatan->sub_kegiatan as $sub_kegiatan)
                    <tr>
                        <td></td>
                        <td>{{ $sub_kegiatan->kode_sub_kegiatan }} {{ $sub_kegiatan->id }}</td>
                        <td>{{ $sub_kegiatan->nama_sub_kegiatan }}</td>
                        <td>{{ $sub_kegiatan->indikator_sub }}</td>
                        <td>{{ $sub_kegiatan->keterangan_rpjmd }}</td>
                        <td align="right">{{ number_format($sub_kegiatan->anggaran_rpjmd)}}</td>
                        @php
                            $subKegiatanTahunLalu = isset($kegiatanTahunLalu->sub_kegiatan) ?  $kegiatanTahunLalu->sub_kegiatan->where('kode_sub_kegiatan',$sub_kegiatan->kode_sub_kegiatan)->first():null;
                        @endphp
                        <td>{{ $subKegiatanTahunLalu->keterangan_rpjmd ?? null }}</td>
                        <td align="right">{{ number_format($subKegiatanTahunLalu->anggaran_rpjmd ?? null)}}</td>
                        <td>{{ $sub_kegiatan->keterangan }}</td>
                        <td align="right">{{ number_format($sub_kegiatan->anggaran_murni)}}</td>
                        {{-- start Realisasi Kegiatan --}}

                        @php
                            $totalRealisasi = 0;
                            $totalVolumeRealiasi = 0;
                        @endphp
                        @if (in_array($triwulan,[1,2,3,4]))
                            @php
                                $r = $data->r_sub_kegiatan->where('fk_sub_kegiatan_id',$sub_kegiatan->id)->where('triwulan',1)->first();
                                $totalRealisasi += $r->anggaran_realisasi ?? 0;
                                $totalVolumeRealiasi += $r->volume_realisasi ?? 0;
                                $satuan_sub_kegiatan = $r->satuan_sub_kegiatan;
                            @endphp
                            <td>{{ $r->volume_realisasi ?? null }} {{ $r->satuan_sub_kegiatan ?? null }}</td>
                            <td align="right">{{ number_format($r->anggaran_realisasi ?? 0) }} </td>
                        @endif
                        @if (in_array($triwulan,[2,3,4]))
                            @php
                                $r = $data->r_sub_kegiatan->where('fk_sub_kegiatan_id',$sub_kegiatan->id)->where('triwulan',2)->first();
                                $totalRealisasi += $r->anggaran_realisasi ?? 0;
                                $totalVolumeRealiasi += $r->volume_realisasi ?? 0;
                            @endphp
                            <td>{{ $r->volume_realisasi ?? null }} {{ $r->satuan_sub_kegiatan ?? null }}</td>
                            <td align="right">{{ number_format($r->anggaran_realisasi ?? 0) }}</td>
                        @endif
                        @if (in_array($triwulan,[3,4]))
                            @php
                                $r = $data->r_sub_kegiatan->where('fk_sub_kegiatan_id',$sub_kegiatan->id)->where('triwulan',3)->first();
                                $totalRealisasi += $r->anggaran_realisasi ?? 0;
                                $totalVolumeRealiasi += $r->volume_realisasi ?? 0;
                            @endphp
                            <td>{{ $r->volume_realisasi ?? null }} {{ $r->satuan_sub_kegiatan ?? null }}</td>
                            <td align="right">{{ number_format($r->anggaran_realisasi ?? 0) }}</td>
                        @endif
                        @if (in_array($triwulan,[4]))
                            @php
                                $r = $data->r_sub_kegiatan->where('fk_sub_kegiatan_id',$sub_kegiatan->id)->where('triwulan',4)->first();
                                $totalRealisasi += $r->anggaran_realisasi ?? 0;
                                $totalVolumeRealiasi += $r->volume_realisasi ?? 0;
                            @endphp
                            <td>{{ $r->volume_realisasi ?? null }} {{ $r->satuan_sub_kegiatan ?? null }}</td>
                            <td align="right">{{ number_format($r->anggaran_realisasi ?? 0) }}</td>
                        @endif
                        {{-- end Realisasi Kegiatan --}}
                            <td>{{ $totalVolumeRealiasi ?? null }} {{ $satuan_sub_kegiatan ?? null }}</td>
                        <td align="right">{{ number_format($totalRealisasi??0) }}</td>
                        <td>{{ $totalVolumeRealiasi ?? null }} {{ $satuan_sub_kegiatan ?? null }}</td>
                        <td>{{ number_format($totalRealisasi/$col7*100, 2, '.', '') }} %</td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    </tbody>
</table>
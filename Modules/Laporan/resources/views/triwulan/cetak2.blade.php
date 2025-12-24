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
    vertical-align: top;
    }
    .text-right{
        text-align: right;
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
    $triwulan = $data->triwulan ?? $triwulan;
    $row1 = 2 * $triwulan;
@endphp
@php
    $realisasi = $data->realisasi ?? $realisasi;
    $dinas = $data->dinas ?? $dinas;
    $r_sub_kegiatan = $data->r_sub_kegiatan ?? $r_sub_kegiatan;
    $faktortl = $data->faktortl ?? $faktortl ?? null;
    $anggaran_kegiatan = $data->anggaran_kegiatan ?? $anggaran_kegiatan ?? null;
    $jenis_anggaran = $data->jenis_anggaran ?? $jenis_anggaran ?? null;

    $total_target = 0
@endphp
<table width="100%">
    <tr>
        <td align="center" colspan="{{ 22+$triwulan }}">Evaluasi Terhadap Hasil RKPD</td>
    </tr>
    <tr>
        <td align="center" colspan="{{ 22+$triwulan }}">{{ $dinas }}</td>
    </tr>
    <tr>
        <td align="center" colspan="{{ 22+$triwulan }}">Tahun {{ session('tahunSession') }}</td>
    </tr>
</table>
<table class="table-striped table bordered" id="table-userx" style="{{ request()->type == 'PDF' ? 'font-size:6px':'' }}">

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
            <th rowspan="2" colspan="2">Tingkat Capaian Kinerja dan Realisasi Anggaran RPJMD s/d Tahun  {{ session('tahunSession') }} (%)</th>
            <th rowspan="2">Perangkat Daerah Penanggung jawab</th>
            {{-- <th rowspan="2">Ket</th> --}}
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
        @php
            $jumlahProgram = 0;
            $jumlahKegiatan = 0;
            $jumlahSubKegiatan= 0;
            $kinerjaProgram1= 0;
            $kinerjaProgram2= 0;
            $kinerjaProgram3= 0;
            $kinerjaProgram4= 0;
            $kinerjaKegiatan1= 0;
            $kinerjaKegiatan2= 0;
            $kinerjaKegiatan3= 0;
            $kinerjaKegiatan4= 0;
            $kinerjaSubKegiatan1= 0;
            $kinerjaSubKegiatan2= 0;
            $kinerjaSubKegiatan3= 0;
            $kinerjaSubKegiatan4= 0;
        @endphp
        @foreach ($realisasi as $item)
            @php
                if ($item['type']=='program') {
                    $jumlahProgram +=1;
                    $kinerjaProgram1 +=$item['kinerja_program1'] ?? 0;
                    $kinerjaProgram2 +=$item['kinerja_program2'] ?? 0;
                    $kinerjaProgram3 +=$item['kinerja_program3'] ?? 0;
                    $kinerjaProgram4 +=$item['kinerja_program4'] ?? 0;
                }
                if ($item['type']=='kegiatan') {
                    $jumlahKegiatan +=1;
                    $kinerjaKegiatan1 +=$item['kinerja_kegiatan1'] ?? 0;
                    $kinerjaKegiatan2 +=$item['kinerja_kegiatan2'] ?? 0;
                    $kinerjaKegiatan3 +=$item['kinerja_kegiatan3'] ?? 0;
                    $kinerjaKegiatan4 +=$item['kinerja_kegiatan4'] ?? 0;
                }
                if ($item['type']=='sub_kegiatan') {
                    $jumlahSubKegiatan +=1;
                    $kinerjaSubKegiatan1 +=$item['kinerja_sub_kegiatan1'] ?? 0;
                    $kinerjaSubKegiatan2 +=$item['kinerja_sub_kegiatan2'] ?? 0;
                    $kinerjaSubKegiatan3 +=$item['kinerja_sub_kegiatan3'] ?? 0;
                    $kinerjaSubKegiatan4 +=$item['kinerja_sub_kegiatan4'] ?? 0;
                    $total_target+=$item['col10']['value'] ?? 0;
                }
            @endphp
            <tr style="background-color: {{ $item['background-color'] }}">
                <td class="{{ isNumberData($item['col1']) ? 'text-right':'' }}">{{ getRowData($item['col1']) }}</td>
                <td class="{{ isNumberData($item['col2']) ? 'text-right':'' }}">{{ getRowData($item['col2']) }}</td>
                <td class="{{ isNumberData($item['col3']) ? 'text-right':'' }}">{{ getRowData($item['col3']) }}</td>
                <td class="{{ isNumberData($item['col4']) ? 'text-right':'' }}">{{ getRowData($item['col4']) }}</td>
                <td class="{{ isNumberData($item['col5']) ? 'text-right':'' }}">{{ getRowData($item['col5']) }}</td>
                <td class="{{ isNumberData($item['col6']) ? 'text-right':'' }}">{{ getRowData($item['col6']) }}</td>
                <td class="{{ isNumberData($item['col7']) ? 'text-right':'' }}">{{ getRowData($item['col7']) }}</td>
                <td class="{{ isNumberData($item['col8']) ? 'text-right':'' }}">{{ getRowData($item['col8']) }}</td>
                <td class="{{ isNumberData($item['col9']) ? 'text-right':'' }}">{{ getRowData($item['col9']) }}</td>
                <td class="{{ isNumberData($item['col10']) ? 'text-right':'' }}">{{ getRowData($item['col10']) }}</td>
                <td class="{{ isNumberData($item['col11']) ? 'text-right':'' }}">{{ getRowData($item['col11']) }}</td>
                <td class="{{ isNumberData($item['col12']) ? 'text-right':'' }}">{{ getRowData($item['col12']) }}</td>
                <td class="{{ isNumberData($item['col13']) ? 'text-right':'' }}">{{ getRowData($item['col13']) }}</td>
                <td class="{{ isNumberData($item['col14']) ? 'text-right':'' }}">{{ getRowData($item['col14']) }}</td>
                <td class="{{ isNumberData($item['col15']) ? 'text-right':'' }}">{{ getRowData($item['col15']) }}</td>
                <td class="{{ isNumberData($item['col16']) ? 'text-right':'' }}">{{ getRowData($item['col16']) }}</td>
                <td class="{{ isNumberData($item['col17']) ? 'text-right':'' }}">{{ getRowData($item['col17']) }}</td>
                <td class="{{ isNumberData($item['col18']) ? 'text-right':'' }}">{{ getRowData($item['col18']) }}</td>
                <td class="{{ isNumberData($item['col19']) ? 'text-right':'' }}">{!! getRowData($item['col19']) !!}</td>
                <td class="{{ isNumberData($item['col20']) ? 'text-right':'' }}">{{ getRowData($item['col20']) }}</td>
                <td class="{{ isNumberData($item['col21']) ? 'text-right':'' }}">{{ getRowData($item['col21']) }}</td>

                @if (isset($item['col22']))
                <td class="{{ isNumberData($item['col22']) ? 'text-right':'' }}">{{ getRowData($item['col22']) }}</td>
                @endif
                @if (isset($item['col23']))
                <td class="{{ isNumberData($item['col23']) ? 'text-right':'' }}">{{ getRowData($item['col23']) }}</td>
                @endif
                @if (isset($item['col24']))
                <td class="{{ isNumberData($item['col24']) ? 'text-right':'' }}">{{ getRowData($item['col24']) }}</td>
                @endif
                @if (isset($item['col25']))
                <td class="{{ isNumberData($item['col25']) ? 'text-right':'' }}">{{ getRowData($item['col25']) }}</td>
                @endif
                @if (isset($item['col26']))
                <td class="{{ isNumberData($item['col26']) ? 'text-right':'' }}">{{ getRowData($item['col26']) }}</td>
                @endif
                @if (isset($item['col27']))
                <td class="{{ isNumberData($item['col27']) ? 'text-right':'' }}">{{ getRowData($item['col27']) }}</td>
                @endif
            </tr>
        @endforeach
        @php
            $kinerja = 0;
            $anggaran = 0;
            $r_anggaran = 0;
            $total_target_realisasi=0;
            $t_pesentase = 0;
        @endphp
        <tr>
            <td align="right" colspan="10">Rata- rata Capaian Kinerja (%)</td>
            @for ($i = 1; $i < $triwulan+1; $i++)
                @php
                    $kinerjaTriwulan =(${'kinerjaProgram' . $i}/$jumlahProgram + ${'kinerjaKegiatan' . $i}/$jumlahKegiatan + ${'kinerjaSubKegiatan' . $i}/$jumlahSubKegiatan )/3;
                    $kinerja +=$kinerjaTriwulan;
                    $realisasi = $r_sub_kegiatan->where('triwulan',$i)->sum('anggaran_realisasi');
                    $r_anggaran += $r_sub_kegiatan->where('triwulan',$i)->sum('anggaran_realisasi');
                    $target = $r_sub_kegiatan->where('triwulan',$i)->sum('anggaran_sub_kegiatan');
                    $target_reallisasi = $target > 0 ? $realisasi/$target*100 : 0;

                    $total_target_realisasi =+$target_reallisasi;
                    $persentase = $realisasi /$total_target*100;
                    $t_pesentase+=$persentase;
                @endphp
                <td align="right">{{  number_format($kinerjaTriwulan, 2, '.', ''). ' %' }} </td>
                <td>
                    {{-- {{  number_format($target_reallisasi , 2, '.', '') }} %  --}}
                    {{  number_format($persentase, 2, '.', '') }} %
                </td>
            @endfor
            <td align="right">{{  number_format($kinerja, 2, '.', ''). ' %' }}</td>
                <td align="right">
                    {{-- {{  number_format($total_target_realisasi, 2, '.', '') }} % --}}
                    {{  number_format($t_pesentase, 2, '.', '') }} %
                </td>
                <td colspan="7"></td>
        </tr>
        <tr>
            <td align="right" colspan="10">Predikat Kinerja</td>
            @for ($i = 1; $i < $triwulan+1; $i++)
                @php
                    $kinerjaTriwulan =(${'kinerjaProgram' . $i}/$jumlahProgram + ${'kinerjaKegiatan' . $i}/$jumlahKegiatan + ${'kinerjaSubKegiatan' . $i}/$jumlahSubKegiatan )/3;
                @endphp
                <td align="right">{{  getPredikat($kinerjaTriwulan) }} </td>
                <td>{{  getPredikat($anggaranTriwulan ?? 0) }} </td>
            @endfor
            <td>{{ getPredikat($kinerja) }}</td>
            <td align="right">{{ getPredikat($target > 0 && $anggaran_kegiatan>0 ? $r_anggaran/$anggaran_kegiatan*100 : 0) }}</td>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td colspan="{{ 19+$triwulan*2 }}">Faktor Pendorong Keberhasilan Kinerja: {{ $faktortl->faktor_pendorong ?? null }}</td>
        </tr>
        <tr>
            <td colspan="{{ 19+$triwulan*2 }}">Faktor Penghambat pencapaian Kinerja: {{ $faktortl->faktor_penghambat ?? null }}</td>
        </tr>
        <tr>
            <td colspan="{{ 19+$triwulan*2 }}">Tindak Lanjut yang diperlukan dalam triwulan berikutnya: {{ $faktortl->tindaklanjut_tw_berikutnya ?? null }}</td>
        </tr>
        <tr>
            <td colspan="{{ 19+$triwulan*2 }}">Tindak Lanjut yang diperlukan dalam RKPD berikutnya: {{ $faktortl->tindaklanjut_rkpd_berikutnya ?? null }}</td>
        </tr>
        @if (request()->type == 'PDF')
            <tr>
                <td style="border: 0px" colspan="{{ 16+$triwulan*2 }}"></td>
                <td style="border: 0px;padding-bottom: 0px" align="center" colspan="3">Kediri, {{ TglIndo(request()->tanggal) }}</td>
            </tr>
            <tr>
                <td style="border: 0px"colspan="{{ 16+$triwulan*2 }}"></td>
                <td style="border: 0px;padding-top: 0px" align="center" colspan="3">{{ request()->jabatan }}</td>
            </tr>
            <tr>
                <td style="border: 0px"colspan="{{ 16+$triwulan*2 }}"></td>
                <td style="border: 0px" align="center" colspan="3"></td>
            </tr>
            <tr>
                <td style="border: 0px"colspan="{{ 16+$triwulan*2 }}"></td>
                <td style="border: 0px" align="center" colspan="3"></td>
            </tr>
            <tr>
                <td style="border: 0px"colspan="{{ 16+$triwulan*2 }}"></td>
                <td style="border: 0px" align="center" colspan="3"></td>
            </tr>
            <tr>
                <td style="border: 0px"colspan="{{ 16+$triwulan*2 }}"></td>
                <td style="border: 0px;padding-bottom: 0px" align="center" colspan="3"><b><u>{{ request()->nama }}</u></b></td>
            </tr>
            <tr>
                <td style="border: 0px"colspan="{{ 16+$triwulan*2 }}"></td>
                <td style="border: 0px;padding-top: 0px" align="center" colspan="3">NIP. {{ request()->nip }}</td>
            </tr>
        @endif
    </tbody>
</table>
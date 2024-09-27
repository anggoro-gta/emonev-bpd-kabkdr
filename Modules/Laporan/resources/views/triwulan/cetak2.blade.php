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
    $triwulan = request()->triwulan;
    $row1 = 2 * $triwulan;
@endphp
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
    @php
        $realisasi = $data->realisasi ?? $realisasi;

    @endphp
    <tbody>
        @foreach ($realisasi as $item)
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
                <td class="{{ isNumberData($item['col19']) ? 'text-right':'' }}">{{ getRowData($item['col19']) }}</td>
                <td class="{{ isNumberData($item['col20']) ? 'text-right':'' }}">{{ getRowData($item['col20']) }}</td>
                <td class="{{ isNumberData($item['col21']) ? 'text-right':'' }}">{{ getRowData($item['col21']) }}</td>
                <td class="{{ isNumberData($item['col22']) ? 'text-right':'' }}">{{ getRowData($item['col22']) }}</td>

                @if (isset($item['col23']))
                <td class="{{ isNumberData($item['col23']) ? 'text-right':'' }}">{{ getRowData($item['col23']) }}</td>
                @endif
                @if (isset($item['col23']))
                <td class="{{ isNumberData($item['col24']) ? 'text-right':'' }}">{{ getRowData($item['col24']) }}</td>
                @endif
                @if (isset($item['col23']))
                <td class="{{ isNumberData($item['col25']) ? 'text-right':'' }}">{{ getRowData($item['col25']) }}</td>
                @endif
                @if (isset($item['col23']))
                <td class="{{ isNumberData($item['col26']) ? 'text-right':'' }}">{{ getRowData($item['col26']) }}</td>
                @endif
                @if (isset($item['col23']))
                <td class="{{ isNumberData($item['col27']) ? 'text-right':'' }}">{{ getRowData($item['col27']) }}</td>
                @endif
                @if (isset($item['col23']))
                <td class="{{ isNumberData($item['col28']) ? 'text-right':'' }}">{{ getRowData($item['col28']) }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
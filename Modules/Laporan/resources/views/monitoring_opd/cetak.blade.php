<style>
    table.bordered {
        border-collapse: collapse;
    }

    table.bordered td,
    table.bordered th {
        border: 1px solid black;
    }
</style>
Monitoring OPD Realisasi {{request()->realisasi}} Triwulan {{request()->triwulan}} Tahun {{ session('tahunSession') }}
<table class="table-striped table bordered" id="table-userx">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th>Nama OPD</th>
            <th>Status Input</th>
        </tr>
    </thead>
    <tbody>
        @php
        $bgColor = [
        null => '#fc544b',
        0 => 'orange',
        1 => '#47c363'
        ];
        $label = [
        null => 'Belum Input',
        0 => 'Belum Posting',
        1 => 'Sudah Posting'
        ];
        @endphp
        @foreach ($realisasi as $key => $item)
        <tr>
            <td align="center">{{ ++$key }}</td>
            <td>{{ $item->nama_skpd }}</td>
            <td style="background-color: {!! $bgColor[$item->status_posting] !!}">
                {{ $label[$item->status_posting] }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
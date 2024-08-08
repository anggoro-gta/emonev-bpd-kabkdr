<div class="table-responsive">
    <table class="table table-bordered table-md">
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Kegiatan</th>
            <th class="text-center">Indikator</th>
            <th class="text-center">Volume</th>
            <th class="text-center">Satuan</th>
            <th class="text-center">Realisasi Volume</th>
        </tr>
        @php
            $indikatorKosong = 0;
        @endphp
        @foreach ($data->kegiatan as $item)
        <input type="hidden" name="fk_kegiatan_id[]" value="{{ $item->id ?? ''}}">
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->nama_kegiatan }}</td>
            <input type="hidden" name="indikator_kegiatan[]" value="{{$item->indikator_keg }}">
            <input type="hidden" name="satuan_kegiatan[]" value="{{ $item->satuan_keg }}">
            <input type="hidden" name="volume_kegiatan[]" value="{{ $item->volume_keg }}">
            @if ($item->indikator_keg)
                <td>{{ $item->indikator_keg }}</td>
                <td>{{ number_format($item->satuan_keg) }}</td>
                <td>{{ number_format($item->volume_keg) }}</td>
                <td>
                    <input type="text" name="volume_realisasi[]" value="" class="form-control mb-2 mr-sm-2 dec" placeholder="Volume">
                </td>
            @else
            <input type="hidden" name="volume_realisasi[]" value="" class="form-control mb-2 mr-sm-2 dec" placeholder="Volume">
            <td colspan="4" class="text-danger">Indikator Belum Ada</td>
            @endif
        </tr>

        @endforeach
    </table>
    {{-- @if ($indikatorKosong==0) --}}
        <button class="btn btn-primary">Submit </button>
    {{-- @endif --}}
</div>
<script>
    $(".dec").autoNumeric("init", {
        vMax: 9999999999999,
        vMin: -9999999999999,
        mDec: 2
    });
</script>
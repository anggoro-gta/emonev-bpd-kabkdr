<div class="table-responsive">
    <table class="table table-bordered table-md" width='100%'>
        <tr>
            <th rowspan="2" class="text-center">No</th>
            <th rowspan="2" class="text-center">Sub Kegiatan</th>
            <th rowspan="2" class="text-center">Anggaran</th>
            <th rowspan="2" class="text-center">Indikator</th>
            <th rowspan="2" class="text-center">Volume</th>
            <th rowspan="2" class="text-center">Satuan</th>
            <th colspan="2" class="text-center">Realisasi</th>
        </tr>
        <tr>
            <th class="text-center" width='16%'>Anggaran</th>
            <th class="text-center">Volume</th>
        </tr>
        @php
            $indikatorKosong = 0;
        @endphp
        @foreach ($data->sub_kegiatan as $item)
        <input type="hidden" name="fk_sub_kegiatan_id[]" value="{{ $item->fk_sub_kegiatan_id ?? $item->id }}" class="form-control mb-2 mr-sm-2">
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->nama_sub_kegiatan }} </td>
            <td class="text-right">{{ number_format($item->anggaran_murni) }}</td>

            <input type="hidden" name="fk_kegiatan_id[]" value="{{ $item->kegiatan->id }}">
            <input type="hidden" name="fk_program_id[]" value="{{ $item->kegiatan->fk_program_id }}">
            @if ($item->indikator_sub ==null)
                @php
                    $indikatorKosong ++;
                @endphp
                <td class="text-danger" colspan="5">Indikator Belum Ada</td>
                <input type="hidden" name="indikator_sub_kegiatan[]" value="">
                <input type="hidden" name="anggaran_sub_kegiatan[]" value="">
                <input type="hidden" name="volume_sub_kegiatan[]" value="">
                <input type="hidden" name="satuan_sub_kegiatan[]" value="">
                <input type="hidden" name="anggaran_realisasi[]" value="">
                <input type="hidden" name="volume_realisasi[]" value="">
            @else
                <input type="hidden" name="indikator_sub_kegiatan[]" value="{{ $item->indikator_sub_kegiatan ?? $item->indikator_sub }}">
                <input type="hidden" name="anggaran_sub_kegiatan[]" value="{{ $item->anggaran_sub_kegiatan ?? $item->anggaran_murni }}">
                <input type="hidden" name="volume_sub_kegiatan[]" value="{{ $item->volume_sub_kegiatan ?? $item->volume_sub }}">
                <input type="hidden" name="satuan_sub_kegiatan[]" value="{{ $item->satuan_sub_kegiatan ?? $item->satuan_sub }}">
                <td>{{ $item->indikator_sub_kegiatan ?? $item->indikator_sub }}</td>
                <td class="text-right">{{ $item->volume_sub_kegiatan ?  number_format($item->volume_sub_kegiatan):number_format($item->volume_sub) }}</td>
                <td>{{ $item->satuan_sub_kegiatan ?? $item->satuan_sub }}</td>
                <td>

                    <input required type="text" name="anggaran_realisasi[]" value="{{ $item->anggaran_realisasi ?? '' }}" class="form-control mb-2 mr-sm-2 nominal text-right" placeholder="Anggaran">
                </td>
                <td>
                    <input required type="text" name="volume_realisasi[]" value="{{ $item->volume_realisasi ?? '' }}" class="form-control mb-2 mr-sm-2 nominal" placeholder="Volume">
                </td>
            @endif
        </tr>

        @endforeach
    </table>
    @if ($indikatorKosong==0)
        <button class="btn btn-primary">Submit </button>
    @endif
</div>
<script>
    $(".nominal").autoNumeric("init", {
        vMax: 9999999999999,
        vMin: -9999999999999,
    });
</script>
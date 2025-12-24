@if ($data->faktor_tl>0)

@if ($data->kode_sub_unit_skpd!='all')
<div class="form-group col-md-3 mt-3" data-select2-id="70">
    <label for="inputPassword4">
        Tanggal
    </label>
    <input required type="text" name="tanggal" class="form-control datepicker" id="tanggal">
</div>
<div class="form-group col-md-3 mt-3" data-select2-id="70">
    <label for="inputPassword4">
        Jabatan
    </label>
    <input required type="text" name="jabatan" id="" class="form-control">
</div>
<div class="form-group col-md-3 mt-3" data-select2-id="70">
    <label for="inputPassword4">
        Nama
    </label>
    <input required type="text" name="nama" id="" class="form-control">
</div>
<div class="form-group col-md-3 mt-3" data-select2-id="70">
    <label for="inputPassword4">
        NIP
    </label>
    <input required type="text" name="nip" id="" class="form-control">
</div>
@endif
<div class="form-group col-md-3 {{$data->kode_sub_unit_skpd=='all' ? 'mt-2' : ''}}" data-select2-id="70">
    <label for="inputPassword4">
        Jenis Anggaran
    </label>
    <select required class="form-control select2" name="anggaran" required>
        <option value="anggaran_murni">Anggaran Murni</option>
        <option value="perubahan_perbup1">Perubahan Perbup 1</option>
        <option value="perubahan_perbup2">Perubahan Perbup 2</option>
        <option value="perubahan_perbup3">Perubahan Perbup 3</option>
        <option value="perubahan_perbup4">Perubahan Perbup 4</option>
        <option value="perubahan_anggaran">Perubahan Anggaran 5</option>
    </select>
</div>
<div class="form-group col-md-3 {{$data->kode_sub_unit_skpd=='all' ? 'mt-3' : 'mt-2'}}">
    <button class="btn btn-danger mt-4" name="type" value="PDF">Pdf</button>
    <button class="btn btn-success mt-4" name="type" value="Excel">Excel</button>
</div>

@else

<span class="text-danger mt-4">Faktor Pendorong dan Tindak Lanjut Belum diisi</span>
@endif
<script>
    $('#tanggal').daterangepicker({
        locale: {format: 'YYYY-MM-DD'},
        singleDatePicker: true,
        autoApply: true
      });
</script>
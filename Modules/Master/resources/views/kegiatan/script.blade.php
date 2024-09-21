<script>
    $(document).on('click', '.tambah', function () {
        numrow = $('#indikator tbody tr').length+1;
        row = `<tr>
                    <input type="hidden" name="kegiatan_indikator_id[]" id="">
                    <td valign="top" class="text-center">${numrow}</td>
                    <td valign="top" class="text-center"><textarea style="height: 150px;" name="indikator_keg[]"
                            class="form-control" data-height="150" rows="5"></textarea></td>
                    <td valign="top" class="text-center"><input type="text"
                            name="volume_keg_rpjmd[]" class="form-control nominal"></td>
                    <td valign="top" class="text-center"><input type="text" name="satuan_keg_rpjmd[]"
                            class="form-control"></td>
                    <td valign="top" class="text-center"><input type="text"
                            name="volume_keg[]" class="form-control nominal"></td>
                    <td valign="top" class="text-center"><input type="text" name="satuan_keg[]"
                            class="form-control"></td>
                    <td class="text-center">
                        <a href="#" data-url="" class="text-danger delete" title="Hapus"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>`
        $("#indikator tbody").append(row)
        $(".nominal").autoNumeric("init", {
            vMax: 9999999999999,
            vMin: -9999999999999,
        });
    })
</script>
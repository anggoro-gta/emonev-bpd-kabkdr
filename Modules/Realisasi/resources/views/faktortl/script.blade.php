<script>
    var loadingHtml = `<div style="position: absolute;left: 47%;top: 55%;border-top: none;" id="loadingHtml">
                            <span class="spinner-border spinner-border-sm text-warning" role="status" aria-hidden="true"></span>
                            <span class="ml-25 align-middle text-warning"> Loading...</span>
                        </div>`;
    $(document).on('change', '.getData', function(e) {
        triwulan = $('#triwulan').val()
        fk_skpd_id = $('#fk_skpd_id').val()
        if (triwulan!=''&&fk_skpd_id!='') {

            $.ajax({
                url: '/realisasi/get_program',
                type: "GET",
                data: {
                    fk_skpd_id,triwulan
                },
                dataType: "json",
                beforeSend: function(data) {
                    $('#wrapDetail').append(loadingHtml);
                },
                success: function(data) {
                    if (data.header) {
                        console.log(data);
                        if (data.header.status_posting==0) {
                            window.location.replace(`/realisasi/program/${data.header.id}/edit`)
                        } else {
                            window.location.replace(`/realisasi/program/${data.header.id}/edit`)
                        }
                    } else {
                        $('#wrapDetail').html(data.view);
                    }
                    console.log(data);
                    // feather.replace()
                },
                error: function() {
                    alert('Terjadi kesalahan, silakan reload');
                }
            })
        }
    })
</script>
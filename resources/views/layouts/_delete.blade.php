<script>
    $(document).on("click", ".delete", function() {
        let urlDelete = $(this).data('url');
        $that = $(this);
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: "Anda Yakin menghapus data?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (urlDelete=='') {
                    $that.closest('tr').remove();
                } else{

                    let csrf_token = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: urlDelete,
                        type: "POST",
                        data: {
                            '_token': csrf_token,
                            '_method': 'DELETE',
                        },
                        beforeSend: function() {
                            $('.swal2-confirm').prop('disabled', true);
                            $('.swal2-confirm').html('Loading...');
                            $that.closest('tr').css('background', '#f7f1f1')
                        },
                        success: function(data) {
                            $that.hide('slow', function(){
                                $that.closest('tr').remove();
                                // $that.remove();
                            });
                        },
                        error: function(data) {
                            swalError(data.msg);
                        }
                    });
                }
            }
        })
    })
</script>

<style>
    .table > :not(caption) > * > * {
        padding: 0.72rem 1rem !important;
    }

    .col-form-label, .col-form-label-lg, .col-form-label-sm {
        align-self: start;
    }
</style>

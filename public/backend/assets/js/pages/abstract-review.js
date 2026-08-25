$(document).ready(function () {
    $(document).on('click', 'button[data-abstract="true"]', function () {
        var title = $(this).data('title');
        var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');
        var url = $(this).data('route');
        var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
        };
        $("#commanModel .modal-title").html(title);
        $("#commanModel .modal-dialog").addClass('modal-' + size);
        $.ajax({
            url: url,
            type: 'get',
            data: data,
            success: function (data) {
                $('#commanModel .render-data').html(data.form);
                $("#commanModel").modal('show');
            },
            error: function (data) {
                data = data.responseJSON;
            }
        });
    });
    
    

    /*Assign abstract to user */
    $(document).off('change', '.assign-reviewer-select').on('change', '.assign-reviewer-select', function (event) {
        event.preventDefault();
        let select = $(this);
        let route = select.data('route');
        let reviewerId = select.val();
        select.prop('disabled', true);
        $.ajax({
            url: route,
            type: 'POST',
            data: {
                reviewer_id: reviewerId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                select.prop('disabled', false);
                if (response.status === 'success') {
                    $('.abstract-submission-list-table-render').html(response.html);
                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-success"
                    }).showToast();
                }
            },
            error: function (xhr) {
                select.prop('disabled', false);
                if (xhr.status === 422) {
                    Toastify({
                        text: xhr.responseJSON?.message || "Validation error",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger"
                    }).showToast();
                } else {
                    Toastify({
                        text: xhr.responseJSON?.message || "Something went wrong",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger"
                    }).showToast();
                }
            }
        });
    });
    /*Assign abstract to user */
});

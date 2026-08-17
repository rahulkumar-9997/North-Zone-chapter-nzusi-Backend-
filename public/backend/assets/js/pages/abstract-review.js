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
    
    $(document).off('click', '#abstractReviewSave').on('click', '#abstractReviewSave', function (event) {
        event.preventDefault();
        let form = $('#abstractReviewForm');
        let submitButton = $(this);
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        submitButton
            .prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        let formData = new FormData(form[0]);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                submitButton.prop('disabled', false).html('Save Category');
                if (response.status === 'success') {
                    $('.abstract-submission-list-table-render').html(response.html);
                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-success"
                    }).showToast();
                    form[0].reset();
                    $('#commanModel').modal('hide');
                }
            },
            error: function (xhr) {
                submitButton.prop('disabled', false).html('Save Category');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        let input = $('#' + key);
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback">' + value[0] + '</div>');
                    });
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

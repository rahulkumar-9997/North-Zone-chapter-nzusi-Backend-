$(document).ready(function () {
    $(document).on('click', 'a[data-blog-subcategory-add="true"]', function () {
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
    
    $(document).off('click', '#saveBlogSubcategoryBtn').on('click', '#saveBlogSubcategoryBtn', function (event) {
        event.preventDefault();
        let form = $('#addBlogSubcategoryForm');
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
                if (response.status =='success') {
                    $('.blog-subcategory-list-table-render').html(response.blogSubcategoryContent);
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

    $(document).on('click', 'a[data-blog-subcategory-edit="true"]', function () {
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

    $(document).off('click', '#updateBlogSubcategoryBtn').on('click', '#updateBlogSubcategoryBtn', function (event) {
        event.preventDefault();
        let form = $('#editBlogSubcategoryForm');
        let submitButton = $(this);
        if (!form.length) {
            console.error('Edit blog subcategory form not found');
            return;
        }
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        submitButton
            .prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm"></span> Updating...');
        let formData = new FormData(form[0]);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                submitButton.prop('disabled', false).html('Update Category');
                if (response.status == 'success') {
                    form[0].reset();
                    $('#commanModel').modal('hide');
                    $('.blog-subcategory-list-table-render').html(response.blogSubcategoryContent);
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
                submitButton.prop('disabled', false).html('Update Category');
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        let inputField = $('#' + key);
                        if (inputField.length) {
                            inputField.addClass('is-invalid');
                            inputField.after('<div class="invalid-feedback">' + value[0] + '</div>');
                        }
                    });

                } else {
                    Toastify({
                        text: xhr.responseJSON?.message || "Error updating blog subcategory",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger"
                    }).showToast();
                }
            }
        });
    });

    $(document).on('click', '.show_confirm', function (event) {
    event.preventDefault();
        let form = $(this).closest("form");
        let url = form.attr('action');
        let token = form.find('input[name="_token"]').val();
        Swal.fire({
            title: "Are you sure?",
            text: "This will delete the category permanently!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: 'DELETE',
                        _token: token
                    },
                    success: function (response) {
                        if (response.status ==='success') {
                            $('.blog-subcategory-list-table-render').html(response.blogSubcategoryContent);
                            Swal.fire("Deleted!", response.message, "success");
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            "Error!",
                            xhr.responseJSON?.message || "Something went wrong",
                            "error"
                        );
                    }
                });
            }
        });
    });
});

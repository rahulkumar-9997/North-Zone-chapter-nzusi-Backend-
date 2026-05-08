$(document).off("click", "#submitbtnstep1").on("click", "#submitbtnstep1", function (event) {
        event.preventDefault();
        let form = $("#member-add-fm-step1");
        let submitButton = $(this);
        $(".form-control").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        submitButton.prop("disabled", true)
        .html(
            '<span class="spinner-border spinner-border-sm"></span> Saving...',
        );
        let formData = new FormData(form[0]);
        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                submitButton.prop('disabled', false).html('Save and Next');
                if (response.status === 'success') {
                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-success"
                    }).showToast();
                    setTimeout(function() {
                        window.location.href = response.redirect_url;
                    }, 1000);
                }
            },
            error: function (xhr) {
                submitButton.prop("disabled", false).html("Save Category");
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    let input = $("#" + key);
                    input.addClass("is-invalid");
                    input.after(
                        '<div class="invalid-feedback">' +
                            value[0] +
                            "</div>",
                    );
                });
            } else {
                Toastify({
                    text:
                        xhr.responseJSON?.message || "Something went wrong",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    className: "bg-danger",
                }).showToast();
            }
        },
    });
});

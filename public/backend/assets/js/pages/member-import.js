$(document)
    .off("submit", "#import-member-form")
    .on("submit", "#import-member-form", function (e) {
        e.preventDefault();
        let form = $(this);
        let button = $("#import-btn");
        $(".form-control").removeClass("is-invalid");
        $(".invalid-feedback").text("");
        $("#import-error-box").html("");
        button
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm"></span> Importing...',
            );
        let formData = new FormData(this);
        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                button.prop("disabled", false).html("Import");
                if (response.status === "success") {
                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-success",
                    }).showToast();

                    form[0].reset();
                }
                if (response.import_errors) {
                    let errorHtml = '<div class="alert alert-danger"><ul>';
                    response.import_errors.forEach(function (err) {
                        errorHtml += `<li>${err}</li>`;
                    });
                    errorHtml += "</ul></div>";

                    $("#import-error-box").html(errorHtml);
                }
            },
            error: function (xhr) {
                button.prop("disabled", false).html("Import");
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        let input = $('[name="' + key + '"]');
                        input.addClass("is-invalid");
                        input.next(".invalid-feedback").text(value[0]);
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

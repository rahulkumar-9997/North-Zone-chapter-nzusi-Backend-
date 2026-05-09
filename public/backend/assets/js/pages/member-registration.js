$(document).ready(function () {
    $(document)
        .off("click", "#submitbtnstep1")
        .on("click", "#submitbtnstep1", function (event) {
            event.preventDefault();
            let form = $("#member-add-fm-step1");
            let submitButton = $(this);
            $(".form-control").removeClass("is-invalid");
            $(".invalid-feedback").remove();

            let isEdit = form.find('input[name="_method"]').length > 0;
            let buttonText = isEdit ? "Updating..." : "Saving...";            
            submitButton.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> ' + buttonText);
            let formData = new FormData(form[0]);
            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    submitButton.prop("disabled", false).html("Save and Next");
                    if (response.status === "success") {
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            className: "bg-success",
                        }).showToast();
                        setTimeout(function () {
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
                                xhr.responseJSON?.message ||
                                "Something went wrong",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            className: "bg-danger",
                        }).showToast();
                    }
                },
            });
        });

    $(document)
        .off("click", "#submitbtnstep2")
        .on("click", "#submitbtnstep2", function (event) {
            event.preventDefault();
            let form = $("#member-add-fm-step2");
            let submitButton = $(this);
            $(".form-control").removeClass("is-invalid");
            $(".invalid-feedback").remove();
            submitButton
                .prop("disabled", true)
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
                    submitButton.prop("disabled", false).html("Save and Next");
                    if (response.status === "success") {
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            className: "bg-success",
                        }).showToast();
                        setTimeout(function () {
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
                                xhr.responseJSON?.message ||
                                "Something went wrong",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            className: "bg-danger",
                        }).showToast();
                    }
                },
            });
        });

    $(document)
        .off("click", "#submitbtnstep3")
        .on("click", "#submitbtnstep3", function (event) {
            event.preventDefault();
            let form = $("#member-add-fm-step3");
            let submitButton = $(this);
            $(".form-control").removeClass("is-invalid");
            $(".invalid-feedback").empty();

            submitButton
                .prop("disabled", true)
                .html(
                    '<span class="spinner-border spinner-border-sm"></span> Saving...',
                );
            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: new FormData(form[0]),
                processData: false,
                contentType: false,
                success: function (response) {
                    submitButton.prop("disabled", false).html("Save and Next");
                    if (response.status === "success") {
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            className: "bg-success",
                        }).showToast();
                        setTimeout(function () {
                            window.location.href = response.redirect_url;
                        }, 1000);
                    }
                },
                error: function (xhr) {
                    submitButton.prop("disabled", false).html("Save and Next");
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        console.log("Validation Errors:", errors);
                        for (let key in errors) {
                            let match = key.match(
                                /qualifications\.(\d+)\.(\w+)/,
                            );

                            if (match) {
                                let index = match[1];
                                let field = match[2];
                                let nameAttr = `qualifications[${index}][${field}]`;

                                let input = $(`[name="${nameAttr}"]`);
                                if (input.length) {
                                    input.addClass("is-invalid");
                                    input
                                    .closest(".mb-3")
                                    .find(".invalid-feedback")
                                    .html(errors[key][0]);
                                }
                            }
                        }
                        $("html, body").animate(
                            {
                                scrollTop:
                                $(".is-invalid:first").offset().top - 100,
                            },
                            500,
                        );
                    } else {
                        Toastify({
                            text:
                            xhr.responseJSON?.message ||
                            "Something went wrong",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            className: "bg-danger",
                        }).showToast();
                    }
                },
            });
        });

    $(document)
    .off("click", "#submitbtnstep4")
    .on("click", "#submitbtnstep4", function (event) {
        event.preventDefault();
        let form = $("#member-add-fm-step4");
        let submitButton = $(this);
        $(".form-control").removeClass("is-invalid");
        $(".invalid-feedback").empty();
        submitButton
            .prop("disabled", true)
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
                submitButton.prop("disabled", false).html("Save and Next");
                if (response.status === "success") {
                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-success",
                    }).showToast();
                    setTimeout(function () {
                        window.location.href = response.redirect_url;
                    }, 1000);
                }
            },
            error: function (xhr) {
                submitButton.prop("disabled", false).html("Save and Next");
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        let match = key.match(/trainings\.(\d+)\.(\w+)/);
                        if (match) {
                            let index = match[1];
                            let field = match[2];
                            let nameAttr = `trainings[${index}][${field}]`;
                            let input = $(`[name="${nameAttr}"]`);
                            if (input.length) {
                                input.addClass("is-invalid");
                                input
                                    .closest(".mb-3")
                                    .find(".invalid-feedback")
                                    .html(value[0]);
                            }
                        } else {
                            let input = $(`[name="${key}"]`);
                            if (input.length) {
                                input.addClass("is-invalid");
                                input
                                .closest(".mb-3")
                                .find(".invalid-feedback")
                                .html(value[0]);
                            }
                        }
                    });
                    Toastify({
                        text: "Please check the form for errors",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger",
                    }).showToast();
                } else {
                    Toastify({
                        text:
                        xhr.responseJSON?.message ||
                        "Something went wrong",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger",
                    }).showToast();
                }
            },
        });
    });
});

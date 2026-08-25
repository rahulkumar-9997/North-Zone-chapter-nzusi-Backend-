$(document).ready(function () {
    $("#guidelines-continue-btn").on("click", function () {
        $("#review-guidelines-block").addClass("d-none");
        $("#review-score-form-block").removeClass("d-none");
    });
    /*Tab code js */
    var hash = window.location.hash;
    if (hash) {
        var tabTriggerEl = document.querySelector(
            '#reviewTab button[data-bs-target="' + hash + '"]'
        );
        if (tabTriggerEl) {
            new bootstrap.Tab(tabTriggerEl).show();
        }
    }    
    $('#reviewTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = e.target.getAttribute('data-bs-target');
        if (history.replaceState) {
            history.replaceState(null, null, target);
        } else {
            window.location.hash = target;
        }
    });
    /*Tab code js */

    function recalcTotal() {
        let total = 0;
        $(".criterion-score").each(function () {
            let val = parseInt($(this).val(), 10);
            let max = parseInt($(this).data("max"), 10);
            if (isNaN(val)) val = 0;
            if (val < 0) val = 0;
            if (val > max) val = max;
            $(this).val(val);
            total += val;
        });
        $("#total-score").text(total);
    }
    $(document).on("input change", ".criterion-score", recalcTotal);

    function toggleCOI() {
        let coi = $('input[name="conflict_of_interest"]:checked').val();
        if (coi === "1") {
            $("#scoring-section").addClass("d-none");
            $("#coi-warning").removeClass("d-none");
        } else {
            $("#scoring-section").removeClass("d-none");
            $("#coi-warning").addClass("d-none");
        }
    }

    $(document).on("change", "#category-select", function () {
        let text = $(this).find("option:selected").text().trim().toLowerCase();
        if (text.startsWith("other")) {
            $("#other-category-text").removeClass("d-none");
        } else {
            $("#other-category-text").addClass("d-none").val("");
        }
        $(this).removeClass("is-invalid");
        $("#category-error").removeClass("d-block");
    });

    $(document).on("change", 'input[name="presentation_type_id"]', function () {
        $("#presentation-type-group .form-check-input").removeClass(
            "is-invalid",
        );
        $("#presentation-type-error").removeClass("d-block");
    });

    $(document).on("input", "#other-category-text", function () {
        $(this).removeClass("is-invalid");
        $("#other-category-error").removeClass("d-block");
    });

    recalcTotal();
    // toggleCOI();

    function clearErrors($form) {
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find(".invalid-feedback").removeClass("d-block");
    }

    function showFieldError($el, $feedback) {
        $el.addClass("is-invalid");
        $feedback.addClass("d-block");
    }

    function validateForm($form) {
        clearErrors($form);
        let isValid = true;
        let coi = $form
            .find('input[name="conflict_of_interest"]:checked')
            .val();
        if (!coi) {
            $form
                .find('input[name="conflict_of_interest"]')
                .addClass("is-invalid");
            $("#coi-error").addClass("d-block");
            isValid = false;
        }
        if (
            !$form.find(
                'input[name="presentation_type_id"]:checked'
            ).length
        ) {
            $("#presentation-type-group .form-check-input").addClass(
                "is-invalid"
            );
            $("#presentation-type-error").addClass("d-block");
            isValid = false;
        }
        let $category = $("#category-select");
        if (!$category.val()) {
            showFieldError(
                $category,
                $("#category-error")
            );
            isValid = false;
        } else {
            let categoryText = $category
                .find("option:selected")
                .text()
                .trim()
                .toLowerCase();
            if (categoryText.startsWith("other")) {
                let $otherText = $("#other-category-text");
                if (!$otherText.val().trim()) {
                    showFieldError(
                        $otherText,
                        $("#other-category-error")
                    );
                    isValid = false;
                }
            }
        }
        return isValid;
    }

    function applyServerErrors($form, errors) {
        clearErrors($form);
        $.each(errors, function (field, messages) {
            let message = messages[0];
            if (field === "presentation_type_id") {
                $("#presentation-type-group .form-check-input").addClass(
                    "is-invalid",
                );
                $("#presentation-type-error").text(message).addClass("d-block");
            } else if (field === "presentation_category_id") {
                $("#category-error").text(message);
                showFieldError($("#category-select"), $("#category-error"));
            } else if (field === "other_category_text") {
                $("#other-category-error").text(message);
                showFieldError(
                    $("#other-category-text"),
                    $("#other-category-error"),
                );
            } else if (field.indexOf("scores.") === 0) {
                let match = field.match(/scores\.(\d+)/);
                if (match) {
                    $('input[name="scores[' + match[1] + ']"]').addClass(
                        "is-invalid",
                    );
                }
            } else {
                $form.find('[name="' + field + '"]').addClass("is-invalid");
            }
        });
    }

    $(document).on("submit", "#review-score-form", function (e) {
        e.preventDefault();
        let $form = $(this);

        if (!validateForm($form)) {
            let $firstError = $form.find(".is-invalid").first();
            if ($firstError.length) {
                $("html, body").animate(
                    { scrollTop: $firstError.offset().top - 100 },
                    300,
                );
            }
            return;
        }

        Swal.fire({
            icon: "warning",
            title: "Submit Final Review?",
            text: "Please ensure your abstract review is final before submitting. Once submitted, it cannot be edited or modified.",
            showCancelButton: true,
            confirmButtonText: "Yes, Submit",
            cancelButtonText: "Go Back",
        }).then(function (result) {
            if (!result.isConfirmed) return;

            let $btn = $form.find('button[type="submit"]');
            let originalBtnHtml = $btn.html();
            $btn.prop("disabled", true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...',
            );

            $.ajax({
                url: $form.data("route"),
                type: "POST",
                data: $form.serialize(),
                success: function (response) {
                    if (!response.success) {
                        Swal.fire({
                            icon: "error",
                            title: "Submission Failed",
                            text: response.message,
                        });
                        $btn.prop("disabled", false).html(originalBtnHtml);
                        return;
                    }
                    Swal.fire({
                        icon: "success",
                        title: "Review Submitted",
                        text: response.message,
                        confirmButtonText: "OK",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then(function () {
                        window.location.href = $form.data("redirect");
                    });
                },
                error: function (xhr) {
                    $btn.prop("disabled", false).html(originalBtnHtml);
                    let data = xhr.responseJSON || {};

                    if (data.errors) {
                        applyServerErrors($form, data.errors);
                        Swal.fire({
                            icon: "error",
                            title: "Please Fix the Highlighted Fields",
                            text: "Some information is missing or invalid.",
                        });
                        let $firstError = $form.find(".is-invalid").first();
                        if ($firstError.length) {
                            $("html, body").animate(
                                { scrollTop: $firstError.offset().top - 100 },
                                300,
                            );
                        }
                        return;
                    }

                    if (data.success === false) {
                        Swal.fire({
                            icon: "error",
                            title: "Cannot Submit",
                            text:
                                data.message ||
                                "This review can no longer be submitted.",
                        }).then(function () {
                            location.reload();
                        });
                        return;
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text:
                            data.message ||
                            "Something went wrong. Please try again.",
                    });
                },
            });
        });
    });
});

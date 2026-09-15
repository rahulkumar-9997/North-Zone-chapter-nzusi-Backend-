function initReviewerSelect2(context) {
    let $scope = context ? $(context) : $(document);
    $scope.find(".assign-reviewer-select").each(function () {
        let $select = $(this);
        // agar pehle se select2 laga hai to phir se init mat karo
        if ($select.hasClass("select2-hidden-accessible")) {
            $select.select2("destroy");
        }
        $select.select2({
            width: "resolve",
            placeholder: "Assign reviewer(s)",
            allowClear: true,
            closeOnSelect: false,
            maximumSelectionLength: 5,
            dropdownParent: $("body"),
        });
    });
}

$(document).ready(function () {
    initReviewerSelect2();
    let currentPage = 1;
    function fetchAbstractSubmissions(page = 1) {
        currentPage = page;
        let presentation_type = $("#member_type").val();
        let topic_category = $("#topic_category").val();
        let name = $("#filter_name").val();
        let date_from = $("#date_from").val();
        let date_to = $("#date_to").val();
        let url = $(".abstract-submission-list-table-render").data("url");
        $("#loader").show();
        $.ajax({
            url: url,
            type: "GET",
            data: {
                presentation_type: presentation_type,
                topic_category: topic_category,
                name: name,
                date_from: date_from,
                date_to: date_to,
                page: page,
            },
            success: function (response) {
                $(".abstract-submission-list-table-render").html(response);
                initReviewerSelect2(".abstract-submission-list-table-render");
                toggleResetButton();
            },
            error: function () {
                alert("Something went wrong.");
            },
            complete: function () {
                $("#loader").hide();
            },
        });
    }

    function toggleResetButton() {
        let presentation_type = $("#member_type").val();
        let topic_category = $("#topic_category").val();
        let name = $("#filter_name").val();
        let date_from = $("#date_from").val();
        let date_to = $("#date_to").val();
        if (
            presentation_type !== "" ||
            topic_category !== "" ||
            name !== "" ||
            date_from !== "" ||
            date_to !== ""
        ) {
            $("#reset-button").show();
        } else {
            $("#reset-button").hide();
        }
    }

    $(document).on("click", 'button[data-abstract="true"]', function () {
        var title = $(this).data("title");
        var size = $(this).data("size") == "" ? "md" : $(this).data("size");
        var url = $(this).data("route");
        var data = {
            _token: $('meta[name="csrf-token"]').attr("content"),
        };
        $("#commanModel .modal-title").html(title);
        $("#commanModel .modal-dialog").addClass("modal-" + size);
        $.ajax({
            url: url,
            type: "get",
            data: data,
            success: function (data) {
                $("#commanModel .render-data").html(data.form);
                $("#commanModel").modal("show");
            },
            error: function (data) {
                data = data.responseJSON;
            },
        });
    });
    /* Assign abstract to reviewer */
    $(document)
        .off("change", ".assign-reviewer-select")
        .on("change", ".assign-reviewer-select", function (event) {
            event.preventDefault();
            let select = $(this);
            let route = select.data("route");
            let reviewerId = select.val() || [];
            select.prop("disabled", true);
            $.ajax({
                url: route,
                type: "POST",
                data: {
                    reviewer_id: reviewerId,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    select.prop("disabled", false);
                    if (response.status === "success") {
                        fetchAbstractSubmissions(currentPage);
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            className: "bg-success",
                        }).showToast();
                    }
                },
                error: function (xhr) {
                    select.prop("disabled", false);
                    let message = "Something went wrong";
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        let errors = xhr.responseJSON.errors;
                        let firstKey = Object.keys(errors)[0];
                        message = errors[firstKey][0];
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }
                    Toastify({
                        text: message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger",
                    }).showToast();
                },
            });
        });

    /* Filters */
    $("#member_type, #topic_category").on("change", function () {
        fetchAbstractSubmissions();
    });

    let nameSearchTimer;
    $("#filter_name").on("keyup", function () {
        clearTimeout(nameSearchTimer);
        nameSearchTimer = setTimeout(function () {
            fetchAbstractSubmissions();
        }, 400);
    });

    $("#date_from, #date_to").on("change", function () {
        fetchAbstractSubmissions();
    });

    $(document).on("click", ".pagination a", function (e) {
        e.preventDefault();
        let href = $(this).attr("href");
        let urlParams = new URLSearchParams(href.split("?")[1]);
        let page = urlParams.get("page") || 1;
        fetchAbstractSubmissions(page);
    });

    $("#reset-button").on("click", function () {
        $("#member_type").val("");
        $("#topic_category").val("");
        $("#filter_name").val("");
        $("#date_from").val("");
        $("#date_to").val("");
        fetchAbstractSubmissions(1);
    });
    toggleResetButton();
});

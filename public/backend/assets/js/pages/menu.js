$(document).ready(function () {
    
    $(document).on("click", ".order-btn", function (e) {
        e.preventDefault();
        let button = $(this);
        let id = button.data("id");
        let direction = button.data("direction");
        let url = $(".user-list-table-render")
            .data("order-url")
            .replace(":id", id);
        button.prop("disabled", true);
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                direction: direction,
            },
            success: function (response) {
                if (response.status) {
                    $(".user-list-table-render").html(response.menuContent);
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            className: "bg-success"
                        }).showToast();
                } else {
                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger"
                    }).showToast();
                }
            },
            error: function (xhr) {
                let message = "Something went wrong.";
                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    className: "bg-danger"
                }).showToast();
            },
            complete: function () {
                button.prop("disabled", false);
            },
        });
    });
    
    $(document).on("change", ".status-toggle", function () {
        let checkbox = $(this);
        let id = checkbox.data("id");
        let url = $(".user-list-table-render")
            .data("status-url")
            .replace(":id", id);
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.status) {
                    $(".user-list-table-render").html(response.menuContent);
                    toastr.success(response.message);
                } else {
                    checkbox.prop("checked", !checkbox.prop("checked"));

                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger"
                    }).showToast();
                }
            },
            error: function (xhr) {
                checkbox.prop("checked", !checkbox.prop("checked"));
                let message = "Something went wrong.";
                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    className: "bg-danger"
                }).showToast(); 
            },
        });
    });

    $(document).on("change", ".sidebar-toggle", function () {
        let checkbox = $(this);
        let id = checkbox.data("id");
        let url = $(".user-list-table-render")
            .data("sidebar-url")
            .replace(":id", id);
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.status) {
                    $(".user-list-table-render").html(response.menuContent);
                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-success"
                    }).showToast();
                } else {
                    checkbox.prop("checked", !checkbox.prop("checked"));

                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger"
                    }).showToast();
                }
            },
            error: function (xhr) {
                checkbox.prop("checked", !checkbox.prop("checked"));
                let message = "Something went wrong.";
                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    className: "bg-danger"
                }).showToast();
            },
        });
    });
});

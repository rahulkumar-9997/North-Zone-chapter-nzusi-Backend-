$(document).ready(function () {
    $("#member_type, #member_status").on("change", updateFilters);
    let typingTimer;
    $("#member_key").on("keyup", function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(updateFilters, 400);
    });
    $("#reset-button").on("click", function () {
        $("#member_type, #member_status, #member_key").val("");
        $("#reset-button").hide();
        fetchMembers();
    });
    $(document).on("click", ".pagination a", function (e) {
        e.preventDefault();

        const memberType = $("#member_type").val();
        const status = $("#member_status").val();
        const search = $("#member_key").val();
        const page = $(this).attr("href").split("page=")[1];

        fetchMembers(memberType, search, page, status);
    });
});

function updateFilters() {
    const memberType = $("#member_type").val();
    const status = $("#member_status").val();
    const search = $("#member_key").val();

    if (memberType || status || search) {
        $("#reset-button").show();
    } else {
        $("#reset-button").hide();
    }

    fetchMembers(memberType, search, 1, status);
}

function fetchMembers(memberType = "", search = "", page = 1, status = "") {
    $("#loader").show();

    $.ajax({
        url: window.routes.memberIndex,
        type: "GET",
        data: {
            member_type: memberType,
            search: search,
            page: page,
            status: status,
        },
        success: function (data) {
            $(".member-lists-table-render").html(data);
            $("#loader").hide();
        },
        error: function () {
            Toastify({
                text: 'Error loading members.',
                duration: 3000,
                gravity: "top",
                position: "right",
                className: "bg-info"
            }).showToast();
            $("#loader").hide();
        },
    });
}

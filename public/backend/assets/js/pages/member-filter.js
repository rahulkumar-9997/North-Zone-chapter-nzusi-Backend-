$(document).ready(function () {
    $("#member_type, #member_status").on("change", updateFilters);

    let typingTimer;
    $("#member_key").on("keyup", function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(updateFilters, 400);
    });
    
    $(document).on("click", ".sort-btn", function(e) {
        e.preventDefault();
        let sortBy = $(this).data("sort");
        let sortOrder = $(this).data("order");
        // console.log("Sort clicked - sortBy:", sortBy, "sortOrder:", sortOrder); 
        $(".sort-btn").removeClass("text-primary");
        $(this).addClass("text-primary");
        $("#current_sort_by").val(sortBy);
        $("#current_sort_order").val(sortOrder);
        
        updateFilters();
    });

    $("#reset-button").on("click", function () {
        $("#member_type, #member_status, #member_key").val("");
        $("#current_sort_by, #current_sort_order").val("");
        $(".sort-btn").removeClass("text-primary");
        $("#reset-button").hide();
        fetchMembers("", "", 1, "", "", "");
    });

    $(document).on("click", ".pagination a", function (e) {
        e.preventDefault();
        const memberType = $("#member_type").val();
        const status = $("#member_status").val();
        const search = $("#member_key").val();
        const sortBy = $("#current_sort_by").val();
        const sortOrder = $("#current_sort_order").val();
        const page = $(this).attr("href").split("page=")[1];
        fetchMembers(memberType, search, page, status, sortBy, sortOrder);
    });
});

function updateFilters() {
    const memberType = $("#member_type").val();
    const status = $("#member_status").val();
    const search = $("#member_key").val();
    const sortBy = $("#current_sort_by").val();
    const sortOrder = $("#current_sort_order").val();

    if (memberType || status || search || sortBy) {
        $("#reset-button").show();
    } else {
        $("#reset-button").hide();
    }
    fetchMembers(memberType, search, 1, status, sortBy, sortOrder);
}

function fetchMembers(memberType = "", search = "", page = 1, status = "", sortBy = "", sortOrder = "") {
    $("#loader").show();
    $.ajax({
        url: window.routes.memberIndex,
        type: "GET",
        data: {
            member_type: memberType,
            search: search,
            page: page,
            status: status,
            sort_by: sortBy,
            sort_order: sortOrder
        },
        success: function (data) {
            $(".member-lists-table-render").html(data);
            $("#loader").hide();
            highlightActiveSort();
        },
        error: function () {
            Toastify({
                text: 'Error loading members.',
                duration: 3000,
                gravity: "top",
                position: "right",
                className: "bg-danger"
            }).showToast();
            $("#loader").hide();
        },
    });
}

function highlightActiveSort() {
    let sortBy = $("#current_sort_by").val();
    let sortOrder = $("#current_sort_order").val();
    
    if (sortBy && sortOrder) {
        $(`.sort-btn[data-sort="${sortBy}"][data-order="${sortOrder}"]`).addClass("text-primary");
    }
}
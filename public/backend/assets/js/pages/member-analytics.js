let chart = null;

function loadAnalytics(year) {
    $.ajax({
        url: window.memberAnalyticsUrl,
        type: "GET",
        data: { year: year },
        success: function (res) {
            let months = [];
            let total = [];
            let approved = [];
            let pending = [];
            let rejected = [];
            res.forEach((item) => {
                months.push(item.month);
                total.push(item.total);
                approved.push(item.approved);
                pending.push(item.pending);
                rejected.push(item.rejected);
            });
            renderChart(months, total, approved, pending, rejected);
        },
        error: function () {
            console.error("Failed to load analytics");
        },
    });
}

function renderChart(months, total, approved, pending, rejected) {

    if (chart !== null) {
        chart.destroy();
    }

    var options = {
        series: [
            {
                name: "Total",
                data: total
            },
            {
                name: "Approved",
                data: approved
            },
            {
                name: "Pending",
                data: pending
            },
            {
                name: "Rejected",
                data: rejected
            }
        ],
        chart: {
            height: 310,
            type: 'area',
            zoom: {
                enabled: false
            }
        },
        colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth', // 🔥 smoother than straight
            width: 2
        },
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        title: {
            text: '',
            align: 'left'
        },
        xaxis: {
            categories: months
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return val; // no 'K' since members count
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " Members";
                }
            }
        }
    };

    chart = new ApexCharts(document.querySelector("#member_analysis"), options);
    chart.render();
}

function renderChart_old(months, total, approved, pending, rejected) {
    if (chart !== null) {
        chart.destroy();
    }
    chart = new ApexCharts(document.querySelector("#sales-analysis"), {
        chart: {
            type: "bar",
            height: 350,
        },
        series: [
            { name: "Total", data: total },
            { name: "Approved", data: approved },
            { name: "Pending", data: pending },
            { name: "Rejected", data: rejected },
        ],
        xaxis: {
            categories: months,
        },
        colors: ["#008FFB", "#00E396", "#FEB019", "#FF4560"],
    });

    chart.render();
}
$(document).on("click", ".year-option", function () {
    let year = $(this).data("year");
    $("#selectedYear").text(year);
    loadAnalytics(year);
});

$(document).ready(function () {
    let currentYear = new Date().getFullYear();
    loadAnalytics(currentYear);
});

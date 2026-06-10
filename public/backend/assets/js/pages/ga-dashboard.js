$(document).ready(function () {
    var ROUTES = window.GA_ROUTES || {};
    var CSRF   = window.GA_CSRF || $('meta[name="csrf-token"]').attr('content') || '';
    var startDate = moment().subtract(29, 'days').format('YYYY-MM-DD');
    var endDate   = moment().format('YYYY-MM-DD');
    var trendChart = null;
    var donutChart = null;
    var activeXHR = [];
    if ($('#daterange').length) {
        var drpOptions = {
            opens               : 'left',
            drops               : 'down',
            autoUpdateInput     : true,
            startDate           : moment().subtract(29, 'days'),
            endDate             : moment(),
            maxDate             : moment(),
            showCustomRangeLabel: true,
            alwaysShowCalendars : true,
            locale: {
                format      : 'DD MMM YYYY',
                separator   : '  →  ',
                cancelLabel : 'Clear',
                applyLabel  : 'Apply',
            },
            ranges: {
                'Today'        : [moment(), moment()],
                'Yesterday'    : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days'  : [moment().subtract(6, 'days'), moment()],
                'Last 15 Days' : [moment().subtract(14, 'days'), moment()],
                'Last 30 Days' : [moment().subtract(29, 'days'), moment()],
                'Last 60 Days' : [moment().subtract(59, 'days'), moment()],
                'Last 90 Days' : [moment().subtract(89, 'days'), moment()],
                'Last 6 Months': [moment().subtract(6, 'months'), moment()],
                'Last 1 Year'  : [moment().subtract(1, 'year'), moment()],
                'This Month'   : [moment().startOf('month'), moment()],
                'Last Month'   : [
                    moment().subtract(1, 'month').startOf('month'),
                    moment().subtract(1, 'month').endOf('month')
                ],
            }
        };
        $('#daterange').daterangepicker(drpOptions);
        $('#daterange').on('apply.daterangepicker', function (ev, picker) {
            startDate = picker.startDate.format('YYYY-MM-DD');
            endDate   = picker.endDate.format('YYYY-MM-DD');
            var today = moment().format('YYYY-MM-DD');
            if (endDate > today) {
                endDate = today;
            }
            /* Display label */
            var isSameDay = (startDate === endDate);
            var displayLabel = isSameDay
                ? picker.startDate.format('DD MMM YYYY')
                : picker.startDate.format('DD MMM YYYY') + '  →  ' + picker.endDate.format('DD MMM YYYY');
            $('#gaPeriodLabel').text(displayLabel);
            $('#gaPeriodBadge').text(displayLabel);

            /* Abort any in-flight requests, then reload */
            abortAll();
            loadAll();
        });

        /* ── Cancel / Clear ── */
        $('#daterange').on('cancel.daterangepicker', function () {
            startDate = moment().subtract(29, 'days').format('YYYY-MM-DD');
            endDate   = moment().format('YYYY-MM-DD');
            $(this).val('');
            $('#gaPeriodLabel').text('Last 30 Days');
            $('#gaPeriodBadge').text('Last 30 Days');
            abortAll();
            loadAll();
        });
    }
    function params() {
        return {
            start_date : startDate,
            end_date   : endDate
        };
    }

    function injectHTML(selector, html) {
        $(selector).html('<div class="ga-fade-in">' + html + '</div>');
    }

    function errHtml(msg) {
        return '<div class="ga-error-block">'
            + '<i class="ti ti-alert-circle"></i>'
            + '<span>' + msg + '</span>'
            + '</div>';
    }

    function startLoad(wrapId) {
        $('#' + wrapId).addClass('ga-loading');
    }

    function stopLoad(wrapId) {
        $('#' + wrapId).removeClass('ga-loading');
    }
    var activeRequests = 0;
    function bumpSpinner(delta) {
        activeRequests += delta;
        if (activeRequests <= 0) {
            activeRequests = 0;
            $('#gaBarSpinner').removeClass('show');
        } else {
            $('#gaBarSpinner').addClass('show');
        }
    }
    function resetSpinner() {
        activeRequests = 0;
        $('#gaBarSpinner').removeClass('show');
    }
    function abortAll() {
        $.each(activeXHR, function (i, xhr) {
            if (xhr && xhr.readyState !== 4) {
                xhr.abort();
            }
        });
        activeXHR = [];
        resetSpinner();
    }
    function gaAjax(opts) {
        bumpSpinner(+1);

        var xhr = $.ajax({
            url     : opts.url,
            method  : 'GET',
            data    : params(),
            headers : { 'X-CSRF-TOKEN': CSRF },
            success : opts.success,
            error   : function (xhr, status) {
                if (status !== 'abort') {
                    opts.error && opts.error(xhr);
                }
            },
            complete: function (xhr, status) {
                activeXHR = activeXHR.filter(function (x) { return x !== xhr; });
                bumpSpinner(-1);
                opts.complete && opts.complete();
            }
        });

        activeXHR.push(xhr);
        return xhr;
    }

    function loadSummary() {
        startLoad('wrap-summary');

        gaAjax({
            url    : ROUTES.summary,
            success: function (html) { injectHTML('#ga-summary', html); },
            error  : function (xhr) {
                $('#ga-summary').html(errHtml('Summary could not load. (' + xhr.status + ')'));
            },
            complete: function () { stopLoad('wrap-summary'); }
        });
    }

    function loadTrend() {
        startLoad('wrap-trend');
        gaAjax({
            url: ROUTES.trend,
            success: function (html) {
                injectHTML('#ga-trend', html);
                var dataEl = document.getElementById('gaChartData');
                if (!dataEl) return;
                var d = JSON.parse(dataEl.textContent);
                var series = [
                    { name: 'Visitors', data: d.visitors || [] },
                    { name: 'Page Views', data: d.pageviews || [] }
                ];
                if (trendChart) {
                    trendChart.destroy();
                }
                trendChart = new ApexCharts(
                    document.querySelector("#gaApexTrend"),
                    buildTrendOptions(d.dates || [], series)
                );
                trendChart.render();
            },
            error: function (xhr) {
                $('#ga-trend').html(
                    errHtml(
                        'Traffic trend could not load. (' + xhr.status + ')'
                    )
                );
            },
            complete: function () {
                stopLoad('wrap-trend');
            }
        });
    }

    function buildTrendOptions(dates, series) {
        return {
            series,
            chart: {
                type      : 'area',
                height    : 280,
                toolbar   : { show: true, tools: { download: true, zoom: true, reset: true, zoomin: true, zoomout: true } },
                animations: { enabled: true, easing: 'easeinout', speed: 500,
                              animateGradually: { enabled: true, delay: 60 } },
                fontFamily: 'inherit',
            },
            colors  : ['#667eea', '#06b6d4'],
            stroke  : { curve: 'smooth', width: [2.5, 2], dashArray: [0, 6] },
            fill    : {
                type    : 'gradient',
                gradient: { type: 'vertical', shadeIntensity: 0.5,
                            opacityFrom: [0.22, 0.12], opacityTo: [0.01, 0.01] }
            },
            xaxis: {
                categories: dates,
                labels    : { style: { fontSize: '11px', colors: '#9ca3af' }, rotate: -30 },
                axisBorder: { show: false },
                axisTicks : { show: false },
                tickAmount: Math.min(dates.length, 14),
            },
            yaxis: [
                {
                    title : { text: 'Visitors',   style: { fontSize: '11px', color: '#667eea' } },
                    labels: { style: { colors: '#9ca3af' } }
                },
                {
                    opposite: true,
                    title   : { text: 'Page Views', style: { fontSize: '11px', color: '#06b6d4' } },
                    labels  : { style: { colors: '#9ca3af' } }
                },
            ],
            dataLabels: { enabled: false },
            grid      : { borderColor: '#f3f4f6', strokeDashArray: 4, padding: { left: 0, right: 0 } },
            legend    : { show: false },
            tooltip   : {
                shared   : true, intersect: false,
                y: [
                    { formatter: function (v) { return Number(v || 0).toLocaleString('en-IN') + ' visitors';  } },
                    { formatter: function (v) { return Number(v || 0).toLocaleString('en-IN') + ' pageviews'; } },
                ]
            },
            markers: { size: [3, 2.5], strokeWidth: 2, fillOpacity: 1 },
            noData : { text: 'No data for this period', style: { color: '#9ca3af', fontSize: '13px' } },
        };
    }

    function loadSources() {
        startLoad('wrap-sources');

        gaAjax({
            url    : ROUTES.sources,
            success: function (html) { injectHTML('#ga-sources', html); },
            error  : function (xhr) {
                $('#ga-sources').html(errHtml('Sources could not load. (' + xhr.status + ')'));
            },
            complete: function () { stopLoad('wrap-sources'); }
        });
    }

    function loadEngagement() {
        if (!ROUTES.engagement || !$('#wrap-engagement').length) return;
        startLoad('wrap-engagement');

        gaAjax({
            url    : ROUTES.engagement,
            success: function (html) { injectHTML('#ga-engagement', html); },
            error  : function (xhr) {
                $('#ga-engagement').html(errHtml('Engagement could not load. (' + xhr.status + ')'));
            },
            complete: function () { stopLoad('wrap-engagement'); }
        });
    }

    function loadDevices() {
        startLoad('wrap-devices');

        gaAjax({
            url    : ROUTES.devices,
            success: function (html) {
                injectHTML('#ga-devices', html);

                var dataEl = document.getElementById('gaDonutData');
                if (!dataEl) return;

                var d = JSON.parse(dataEl.textContent);
                var mob = d.mobile  || 0;
                var dsk = d.desktop || 0;
                var tab = d.tablet  || 0;

                if (donutChart) {
                    donutChart.updateSeries([mob, dsk, tab]);
                } else {
                    donutChart = new ApexCharts(
                        document.getElementById('gaApexDonut'),
                        {
                            series     : [mob, dsk, tab],
                            labels     : ['Mobile', 'Desktop', 'Tablet'],
                            chart      : {
                                type      : 'donut',
                                height    : 160,
                                animations: { enabled: true, easing: 'easeinout', speed: 500 }
                            },
                            colors     : ['#667eea', '#10b981', '#f59e0b'],
                            legend     : { show: true, position: 'bottom', fontSize: '11px',
                                           itemMargin: { horizontal: 8 } },
                            dataLabels : { enabled: false },
                            plotOptions: { pie: { donut: { size: '68%',
                                labels: { show: true, total: {
                                    show: true, label: 'Sessions',
                                    fontSize: '11px', color: '#9ca3af'
                                }}
                            }}},
                            tooltip    : { y: { formatter: function (v) { return v + '%'; } } },
                            noData     : { text: 'No data' },
                        }
                    );
                    donutChart.render();
                }
            },
            error  : function (xhr) {
                $('#ga-devices').html(errHtml('Devices could not load. (' + xhr.status + ')'));
            },
            complete: function () { stopLoad('wrap-devices'); }
        });
    }

    function loadTopPages() {
        startLoad('wrap-toppages');

        gaAjax({
            url    : ROUTES.toppages,
            success: function (html) { injectHTML('#ga-toppages', html); },
            error  : function (xhr) {
                $('#ga-toppages').html(errHtml('Top pages could not load. (' + xhr.status + ')'));
            },
            complete: function () { stopLoad('wrap-toppages'); }
        });
    }

    function loadReferrers() {
        if (!ROUTES.referrers || !$('#wrap-referrers').length) return;
        startLoad('wrap-referrers');

        gaAjax({
            url    : ROUTES.referrers,
            success: function (html) { injectHTML('#ga-referrers', html); },
            error  : function (xhr) {
                $('#ga-referrers').html(errHtml('Referrers could not load. (' + xhr.status + ')'));
            },
            complete: function () { stopLoad('wrap-referrers'); }
        });
    }

    function loadCountries() {
        startLoad('wrap-countries');

        gaAjax({
            url    : ROUTES.countries,
            success: function (html) { injectHTML('#ga-countries', html); },
            error  : function (xhr) {
                $('#ga-countries').html(errHtml('Countries could not load. (' + xhr.status + ')'));
            },
            complete: function () { stopLoad('wrap-countries'); }
        });
    }
    function loadAll() {
        loadSummary();
        loadTrend();
        loadSources();
        loadEngagement();
        loadDevices();
        loadTopPages();
        loadReferrers();
        loadCountries();
    }
    loadAll();
});
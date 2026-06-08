/**
 * ga-dashboard.js
 * Sirf AJAX + innerHTML — koi JS rendering nahi.
 * Controller HTML banata hai, yahan sirf inject hota hai.
 * Place: public/backend/assets/js/pages/ga-dashboard.js
 */
$(document).ready(function () {

    var ROUTES = window.GA_ROUTES || {};
    var CSRF   = window.GA_CSRF || $('meta[name="csrf-token"]').attr('content') || '';

    var startDate = moment().subtract(29, 'days').format('YYYY-MM-DD');
    var endDate   = moment().format('YYYY-MM-DD');

    /* Listen to existing header #daterange picker */
    $('#daterange').on('apply.daterangepicker', function (ev, picker) {
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate   = picker.endDate.format('YYYY-MM-DD');
        $('#gaPeriodLabel').text(picker.startDate.format('DD MMM YYYY') + ' – ' + picker.endDate.format('DD MMM YYYY'));
        loadAll();
    });

    $('#daterange').on('cancel.daterangepicker', function () {
        startDate = moment().subtract(29, 'days').format('YYYY-MM-DD');
        endDate   = moment().format('YYYY-MM-DD');
        $('#gaPeriodLabel').text('Last 30 Days');
        loadAll();
    });

    function params() { return { start_date: startDate, end_date: endDate }; }
    function startLoad(id) { $('#' + id).addClass('ga-loading'); }
    function stopLoad(id)  { $('#' + id).removeClass('ga-loading'); }
    function errHtml(msg)  {
        return '<div class="ga-error-block"><i class="ti ti-alert-circle"></i>' + msg + '</div>';
    }

    /* chart instances */
    var trendChart = null;
    var donutChart = null;

    /* ─ SECTION 1: Summary KPI ─ */
    function loadSummary() {
        startLoad('wrap-summary');
        $.ajax({ url: ROUTES.summary, method: 'GET', data: params(), headers: { 'X-CSRF-TOKEN': CSRF },
            success  : function (html) { $('#ga-summary').html(html); },
            error    : function (xhr) { $('#ga-summary').html(errHtml('Summary error: ' + xhr.status)); },
            complete : function () { stopLoad('wrap-summary'); }
        });
    }

    /* ─ SECTION 2: Traffic Trend (HTML + embedded chart JSON) ─ */
    function loadTrend() {
        startLoad('wrap-trend');
        $.ajax({ url: ROUTES.trend, method: 'GET', data: params(), headers: { 'X-CSRF-TOKEN': CSRF },
            success  : function (html) {
                $('#ga-trend').html(html);
                var el = document.getElementById('gaChartData');
                if (!el) return;
                var d = JSON.parse(el.textContent);
                var series = [{ name:'Visitors', data: d.visitors }, { name:'Page Views', data: d.pageviews }];
                if (trendChart) {
                    trendChart.updateOptions({ xaxis: { categories: d.dates } }, false, false);
                    trendChart.updateSeries(series, true);
                } else {
                    trendChart = new ApexCharts(document.getElementById('gaApexTrend'), {
                        series, chart: { type:'area', height:280, toolbar:{ show:true, tools:{download:true,zoom:true,reset:true} }, animations:{enabled:true,speed:600}, fontFamily:'inherit' },
                        colors: ['#667eea','#06b6d4'], stroke: { curve:'smooth', width:[2.5,2], dashArray:[0,6] },
                        fill: { type:'gradient', gradient:{ type:'vertical', shadeIntensity:0.4, opacityFrom:[0.2,0.1], opacityTo:[0.01,0.01] } },
                        xaxis: { categories:d.dates, labels:{style:{fontSize:'11px',colors:'#9ca3af'},rotate:-30}, axisBorder:{show:false}, axisTicks:{show:false}, tickAmount:Math.min(d.dates.length,14) },
                        yaxis: [{ title:{text:'Visitors',style:{fontSize:'11px',color:'#667eea'}}, labels:{style:{colors:'#9ca3af'}} },
                                { opposite:true, title:{text:'Page Views',style:{fontSize:'11px',color:'#06b6d4'}}, labels:{style:{colors:'#9ca3af'}} }],
                        dataLabels: { enabled:false }, grid: { borderColor:'#f3f4f6', strokeDashArray:4 }, legend: { show:false },
                        tooltip: { shared:true, intersect:false, y:[
                            { formatter: function(v){ return Number(v).toLocaleString('en-IN')+' visitors'; } },
                            { formatter: function(v){ return Number(v).toLocaleString('en-IN')+' pageviews'; } }
                        ]},
                        markers: { size:[3,2.5], strokeWidth:2, fillOpacity:1 }
                    });
                    trendChart.render();
                }
            },
            error    : function (xhr) { $('#ga-trend').html(errHtml('Trend error: ' + xhr.status)); },
            complete : function () { stopLoad('wrap-trend'); }
        });
    }

    /* ─ SECTION 3A: Sources ─ */
    function loadSources() {
        startLoad('wrap-sources');
        $.ajax({ url: ROUTES.sources, method: 'GET', data: params(), headers: { 'X-CSRF-TOKEN': CSRF },
            success  : function (html) { $('#ga-sources').html(html); },
            error    : function (xhr) { $('#ga-sources').html(errHtml('Sources error: ' + xhr.status)); },
            complete : function () { stopLoad('wrap-sources'); }
        });
    }

    /* ─ SECTION 3B: Engagement ─ */
    function loadEngagement() {
        startLoad('wrap-engagement');
        $.ajax({ url: ROUTES.engagement, method: 'GET', data: params(), headers: { 'X-CSRF-TOKEN': CSRF },
            success  : function (html) { $('#ga-engagement').html(html); },
            error    : function (xhr) { $('#ga-engagement').html(errHtml('Engagement error: ' + xhr.status)); },
            complete : function () { stopLoad('wrap-engagement'); }
        });
    }

    /* ─ SECTION 3C: Devices (HTML + embedded donut JSON) ─ */
    function loadDevices() {
        startLoad('wrap-devices');
        $.ajax({ url: ROUTES.devices, method: 'GET', data: params(), headers: { 'X-CSRF-TOKEN': CSRF },
            success  : function (html) {
                $('#ga-devices').html(html);
                var el = document.getElementById('gaDonutData');
                if (!el) return;
                var d = JSON.parse(el.textContent);
                if (donutChart) {
                    donutChart.updateSeries([d.mobile, d.desktop, d.tablet]);
                } else {
                    donutChart = new ApexCharts(document.getElementById('gaApexDonut'), {
                        series:[d.mobile,d.desktop,d.tablet], labels:['Mobile','Desktop','Tablet'],
                        chart:{ type:'donut', height:140 }, colors:['#667eea','#10b981','#f59e0b'],
                        legend:{ show:true, position:'bottom', fontSize:'11px', itemMargin:{horizontal:6} },
                        dataLabels:{ enabled:false }, plotOptions:{ pie:{ donut:{ size:'65%' } } },
                        tooltip:{ y:{ formatter: function(v){ return v+'%'; } } }
                    });
                    donutChart.render();
                }
            },
            error    : function (xhr) { $('#ga-devices').html(errHtml('Devices error: ' + xhr.status)); },
            complete : function () { stopLoad('wrap-devices'); }
        });
    }

    /* ─ SECTION 4: Top Pages ─ */
    function loadTopPages() {
        startLoad('wrap-toppages');
        $.ajax({ url: ROUTES.toppages, method: 'GET', data: params(), headers: { 'X-CSRF-TOKEN': CSRF },
            success  : function (html) { $('#ga-toppages').html(html); },
            error    : function (xhr) { $('#ga-toppages').html(errHtml('Top Pages error: ' + xhr.status)); },
            complete : function () { stopLoad('wrap-toppages'); }
        });
    }

    /* ─ SECTION 5: Referrers ─ */
    function loadReferrers() {
        startLoad('wrap-referrers');
        $.ajax({ url: ROUTES.referrers, method: 'GET', data: params(), headers: { 'X-CSRF-TOKEN': CSRF },
            success  : function (html) { $('#ga-referrers').html(html); },
            error    : function (xhr) { $('#ga-referrers').html(errHtml('Referrers error: ' + xhr.status)); },
            complete : function () { stopLoad('wrap-referrers'); }
        });
    }

    function loadAll() { loadSummary(); loadTrend(); loadSources(); loadEngagement(); loadDevices(); loadTopPages(); loadReferrers(); }

    // Initial load on page ready
    loadAll();
});

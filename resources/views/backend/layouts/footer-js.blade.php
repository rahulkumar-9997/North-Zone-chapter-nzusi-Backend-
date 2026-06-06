<script src="{{asset('backend/assets/js/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('backend/assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('backend/assets/js/feather.min.js')}}"></script>
<script src="{{asset('backend/assets/js/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('backend/assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/assets/js/dataTables.bootstrap5.min.js')}}"></script>
<!-- <script src="{{asset('backend/assets/plugins/summernote/summernote-bs4.min.js')}}"></script> -->
<script src="{{asset('backend/assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{asset('backend/assets/js/custom-select2.js')}}" type="text/javascript"></script>
<script src="{{asset('backend/assets/js/moment.min.js')}}"></script>
<script src="{{asset('backend/assets/plugins/flatpickr/flatpickr.js')}}" type="text/javascript"></script>
<script src="{{asset('backend/assets/js/daterangepicker/daterangepicker.min.js')}}" type="text/javascript"></script>
<!-- <script src="{{asset('backend/assets/js/bootstrap-datetimepicker.min.js')}}"></script> -->
<script src="{{asset('backend/assets/js/script.js')}}?v={{ config('app.assets_version') }}"></script>
<script src="{{asset('backend/assets/plugins/toastr/toastify-js.js')}}"></script>
@stack('scripts')
<script>
$(document).ready(function () {
    if ($('#daterange').length) {
        $('#daterange').daterangepicker({
            opens: 'right',
            autoUpdateInput: true,
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'Last 60 Days': [moment().subtract(59, 'days'), moment()],
                'Last 90 Days': [moment().subtract(89, 'days'), moment()],
                'Last 6 Months': [moment().subtract(6, 'months'), moment()],
                'Last 1 Year': [moment().subtract(1, 'year'), moment()]
            }
        });
        $('#daterange').on('cancel.daterangepicker', function () {
            $(this).val('');
        });
    }
});
</script>
@if(session()->has('success'))
<script>
    Toastify({
        text: "{{ session()->get('success') }}",
        duration: 5000,
        gravity: "top",
        position: "right",
        className: "bg-success",
        close: true,
        onClick: function() {}
    }).showToast();
</script>
@endif
@if(session()->has('error'))
<script>
    Toastify({
        text: "{{ session()->get('error') }}",
        duration: 5000,
        gravity: "top",
        position: "right",
        className: "bg-danger",
        close: true,
        onClick: function() {}
    }).showToast();
</script>
@endif


@if($errors->any())
<script>
    @foreach($errors->all() as $error)
    Toastify({
        text: "{{ $error }}",
        duration: 4000,
        gravity: "top",
        position: "right",
        className: "bg-danger",
        close: true,
        onClick: function() {}
    }).showToast();
    @endforeach
</script>
@endif
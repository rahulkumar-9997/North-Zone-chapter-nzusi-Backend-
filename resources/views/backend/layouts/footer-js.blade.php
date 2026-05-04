<script src="{{asset('backend/assets/js/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('backend/assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('backend/assets/js/feather.min.js')}}"></script>
<script src="{{asset('backend/assets/js/jquery.slimscroll.min.js')}}"></script>
<script src="{{asset('backend/assets/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/assets/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('backend/assets/plugins/summernote/summernote-bs4.min.js')}}"></script>
<script src="{{asset('backend/assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{asset('backend/assets/js/moment.min.js')}}"></script>
<script src="{{asset('backend/assets/plugins/flatpickr/flatpickr.js')}}" type="text/javascript"></script>
<script src="{{asset('backend/assets/js/bootstrap-datetimepicker.min.js')}}"></script>
<!-- <script src="{{asset('backend/assets/plugins/daterangepicker/daterangepicker.js')}}" type="text/javascript"></script> -->
<script src="{{asset('backend/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js')}}"></script>
<script src="{{asset('backend/assets/js/script.js')}}?v={{ config('app.assets_version') }}"></script>
<script src="{{asset('backend/assets/plugins/toastr/toastify-js.js')}}"></script>
<script src="{{asset('backend/assets/js/rocket-loader.min.js')}}" defer></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js"></script>
@stack('scripts')

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
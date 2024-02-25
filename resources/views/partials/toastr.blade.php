<style type="text/css">
    .toast {
        opacity: 1 !important;
        border-radius: 20px !important;
        max-width: 250px !important;

    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
<script>
    toastr.options.fadeOut = 6000;
    @isset ($info)
        toastr.info('{{ $info }}');
    @endisset
    @isset ($success)
        toastr.success('{{ $success }}');
    @endisset
    @isset ($warning)
        toastr.warning('{{ $warning }}');
    @endisset
    @if($errors->any())
        @foreach ($errors->all() as $err)
            toastr.error('{{ $err }}');
        @endforeach
    @else
        @isset($error)
            toastr.error('{{ $error }}');
        @endisset
    @endif

    @if(Session::has('info'))
        toastr.info('{{ Session::pull('info') }}');
    @endif
    @if(Session::has('success'))
        toastr.success('{{ Session::pull('success') }}');
    @endif
    @if(Session::has('warning'))
        toastr.warning('{{ Session::pull('warning') }}');
    @endif
    @if(Session::has('error'))
        toastr.error('{{ Session::pull('error') }}');
    @endif
</script>

<script type="text/javascript">
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "opacity" : 1,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "5000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
</script>

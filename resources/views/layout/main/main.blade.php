<!-- master.blade.php -->
<html>

<head>
    @include('layout.guest.header') <!-- Include header -->
</head>

<body>

    @include('layout.main.sidebar') <!-- Include header -->
    @yield('content') <!-- Content goes here -->

    @include('layout.guest.footer') <!-- Include footer -->

<script>
        @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif
</script>
</body>


</html>
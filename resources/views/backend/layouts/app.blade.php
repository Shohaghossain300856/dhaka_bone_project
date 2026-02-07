@php
    $baseurl = url('/');
@endphp
<!doctype html>
<html
      lang="en"
      class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
      dir="ltr"
      data-theme="theme-default"
      data-assets-path="{{ $baseurl . '/backend/' }}"
      data-template="vertical-menu-template"
      data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $webSetting['title'] }} @yield('title', '')</title>
    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $baseurl . '/backend/img/favicon/favicon.ico' }}" />


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
          href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
          rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ $baseurl . '/backend/vendor/fonts/fontawesome.css' }}" />
    <link rel="stylesheet" href="{{ $baseurl . '/backend/vendor/fonts/tabler-icons.css' }}" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ $baseurl . '/backend/vendor/css/rtl/core.css' }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ $baseurl . '/backend/css/demo.css' }}" />
    @stack('vendor_style')
    <!-- alert css -->
    <link rel="stylesheet" href="{{ $baseurl . '/backend/vendor/libs/sweetalert2/sweetalert2.css' }}" />

    <!-- Page css -->
    @stack('style')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ $baseurl . '/backend/vendor/libs/perfect-scrollbar/perfect-scrollbar.css' }}" />
    <!-- Helpers -->
    <script src="{{ $baseurl . '/backend/vendor/js/helpers.js' }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ $baseurl . '/backend/vendor/js/template-customizer.js' }}"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ $baseurl . '/backend/js/config.js' }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="{{ $baseurl . '/modalStyle.css' }}">

</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('backend.layouts.partial.sidebarlogo')

                <div class="menu-inner-shadow"></div>
                <!-- sidebar -->
                @include('backend.layouts.partial.sidebar')
            </aside>
            <!-- / Menu -->
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('backend.layouts.partial.topnavbar')
                <!-- / Navbar -->
                <!-- Content wrapper -->
                <div class="content-wrapper" id="app">

                    <!-- Content -->
                    @yield('content')
                    <!-- / Content -->
                    <!-- Footer -->
                    @include('backend.layouts.partial.footer')
                    <!-- / Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    @php
        $version = '1.0.2'; // change this on every update
    @endphp
    <script>
    </script>
    <script src="{{ $baseurl . '/js/app.js' }}?v={{ $version }}"></script>
    <script src="{{ $baseurl . '/backend/vendor/libs/jquery/jquery.js' }}?v={{ $version }}"></script>
    <script src="{{ $baseurl . '/backend/vendor/libs/popper/popper.js' }}?v={{ $version }}"></script>
    <script src="{{ $baseurl . '/backend/vendor/js/bootstrap.js' }}?v={{ $version }}"></script>
    <script src="{{ $baseurl . '/backend/vendor/libs/perfect-scrollbar/perfect-scrollbar.js' }}?v={{ $version }}"></script>
    <script src="{{ $baseurl . '/backend/vendor/js/menu.js' }}?v={{ $version }}"></script>
    @stack('vendor_js')
    <script src="{{ $baseurl . '/backend/js/main.js' }}?v={{ $version }}"></script>
    <script src="{{ $baseurl . '/backend/vendor/libs/sweetalert2/sweetalert2.js' }}?v={{ $version }}"></script>
    <script src="{{ $baseurl . '/backend/js/extended-ui-sweetalert2.js' }}?v={{ $version }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>



    <!-- alert fire -->
    @if (session()->has('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false // Hides the "OK" button

                });
            });
        </script>
    @endif
    @if (session()->has('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false // Hides the "OK" button

                });
            });
        </script>
    @endif
    @if (session()->has('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'warning!',
                    text: '{{ session('warning') }}',
                    icon: 'warning',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false // Hides the "OK" button

                });
            });
        </script>
    @endif

    <!-- Page JS -->
    @stack('script')
</body>

</html>

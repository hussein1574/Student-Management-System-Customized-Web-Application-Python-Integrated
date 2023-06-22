<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('backpack.base.html_direction') }}">
<head>
    @include(backpack_view('inc.head'))
    <script src="{{ asset('sweetalert2.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('sweetalert2.min.css') }}">
</head>
<body class="app flex-row align-items-center" >

  @yield('header')

  <div class="container">
  @yield('content')
  </div>

  <footer class="app-footer sticky-footer">
    @include('backpack::inc.footer')
  </footer>

  @yield('before_scripts')
  @stack('before_scripts')

  @include(backpack_view('inc.scripts'))

  @yield('after_scripts')
  @stack('after_scripts')

</body>
</html>

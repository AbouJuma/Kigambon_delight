\<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel=icon href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/master.css') }}">

    <title>Stocky | Ultimate Inventory With POS</title>
  </head>

  <body class="text-left">
    <noscript>
      <strong>
        We're sorry but Stocky doesn't work properly without JavaScript
        enabled. Please enable it to continue.</strong
      >
    </noscript>

    <!-- built files will be auto injected -->
    <div class="loading_wrap" id="loading_wrap">
      <div class="loader_logo">
      <img src="{{ asset('images/logo.png') }}" class="" alt="logo" />

      </div>

      <div class="loading"></div>
    </div>
    <div id="login">
        @if(session('license_expired'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px;">
            {{ session('license_expired') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <login-component></login-component>
      </div>

      <script>
        window.config = {
          "ModulesEnabled" : @json($ModulesEnabled),
          "ModulesInstalled" : @json($ModulesInstalled),
        };
      </script>

      <script src="{{ asset('js/login.min.js') }}?v=4.0.8"></script>
  </body>
</html>

    
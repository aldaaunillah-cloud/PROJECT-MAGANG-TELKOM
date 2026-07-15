<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Billing Telkom</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

<div class="wrapper">

    @include('partials.sidebar')

    <div class="main">

        @include('partials.navbar')

        <div class="container-fluid p-4">

            @yield('content')

        </div>

    </div>

</div>

</body>
</html>
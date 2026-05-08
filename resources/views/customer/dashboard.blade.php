<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Customer</title>

    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/dist/css/bootstrap.min.css') }}">
</head>
<body>

<div class="container mt-5">

    <h1>Dashboard Customer</h1>

    <p>Selamat datang Customer</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit" class="btn btn-danger">
            Logout
        </button>
    </form>

</div>

</body>
</html>

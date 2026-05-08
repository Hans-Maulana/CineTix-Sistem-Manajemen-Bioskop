<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Studiova</title>

  <link rel="shortcut icon" type="image/png" href="{{ asset("assets/images/logos/favicon.svg") }}" />
  <link rel="stylesheet" href="{{ asset("assets/libs/owl.carousel/dist/assets/owl.carousel.min.css") }}">
  <link rel="stylesheet" href="{{ asset("assets/libs/aos-master/dist/aos.css") }}">
  <link rel="stylesheet" href="{{ asset("assets/css/styles.css") }}" />
</head>

<body>

  <div class="page-wrapper overflow-hidden">

    <section class="bg-light-gray border-top border-primary border-4 d-flex align-items-center justify-content-center min-vh-100">

      <div class="container py-3">

        <div class="sign-in card mx-auto shadow-lg">

          <div class="card-body py-8 px-lg-5">

            <!-- Logo -->
            <a href="index.html" class="mb-8 hstack justify-content-center">
              <img src="../assets/images/logos/logo-dark.svg" alt="logo-dark" class="img-fluid">
            </a>


            <!-- Google Login -->
            <div class="hstack gap-3">
              <a href="{{ route('login.google') }}"
                class="btn btn-outline-light bg-white px-3 py-2 fs-4 text-dark w-100 fw-medium hstack gap-2 lh-lg justify-content-center">
                Sign In with
                <img src="{{ asset('assets/images/svgs/icon-google.svg') }}" alt="google" class="img-fluid">
              </a>
            </div>

            <!-- Divider -->
            <div class="position-relative hstack justify-content-center">
              <hr class="my-8 w-100 d-block">
              <p class="mb-0 fs-3 bg-body px-3 position-absolute top-50 start-50 translate-middle">OR</p>
            </div>

            <!-- Form -->
            <form class="d-flex flex-column gap-3" method="POST" action="{{ route('login') }}">
              @csrf

              <div>
                <input type="email" name="email" class="form-control border-bottom" placeholder="Email" required autofocus>
              </div>

              <div>
                <input type="password" name="password" class="form-control border-bottom" placeholder="Password" required>
              </div>

              <button type="submit" class="btn btn-dark w-100 justify-content-center py-2 fw-medium my-7 fs-4 lh-lg">
                Sign In
              </button>
            </form>


            <!-- Register -->
            <p class="mb-0 fw-medium text-center">
              Not a member yet?
              <a class="text-dark" href="{{ route('register') }}">Sign Up</a>
            </p>

          </div>

        </div>

      </div>

    </section>

  </div>

  <!-- Scripts -->
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/libs/owl.carousel/dist/owl.carousel.min.js"></script>
  <script src="../assets/libs/aos-master/dist/aos.js"></script>
  <script src="../assets/js/custom.js"></script>

</body>
</html>

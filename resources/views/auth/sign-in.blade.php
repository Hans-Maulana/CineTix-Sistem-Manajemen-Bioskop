<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CineTix - Sign In</title>

  <link rel="shortcut icon" type="image/png" href="{{ asset("assets/images/logos/favicon.svg") }}" />
  <link rel="stylesheet" href="{{ asset("assets/libs/owl.carousel/dist/assets/owl.carousel.min.css") }}">
  <link rel="stylesheet" href="{{ asset("assets/libs/aos-master/dist/aos.css") }}">
  <link rel="stylesheet" href="{{ asset("assets/css/styles.css") }}" />
  
  <style>
    body {
      background: linear-gradient(135deg, #0c0b24 0%, #1A1953 100%) !important;
      font-family: 'Manrope', sans-serif;
      min-height: 100vh;
    }
    
    .bg-light-gray {
      background: transparent !important;
      border-top: none !important;
    }
    
    .sign-in.card {
      background: rgba(255, 255, 255, 0.98);
      border-radius: 24px;
      border: 0;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3) !important;
      max-width: 440px;
      width: 100%;
    }
    
    .form-control.border-bottom {
      border-top: 0 !important;
      border-left: 0 !important;
      border-right: 0 !important;
      border-bottom: 2px solid rgba(26, 25, 83, 0.1) !important;
      border-radius: 0 !important;
      background: transparent !important;
      padding: 12px 4px !important;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }
    
    .form-control.border-bottom:focus {
      box-shadow: none !important;
      border-bottom-color: #1A1953 !important;
    }
    
    .btn-outline-light {
      border: 1px solid rgba(26, 25, 83, 0.15) !important;
      border-radius: 50px !important;
      transition: all 0.3s ease;
    }
    
    .btn-outline-light:hover {
      background-color: #f8f9fa !important;
      border-color: #1A1953 !important;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    
    .btn-dark {
      background-color: #1A1953 !important;
      border: 1px solid #1A1953 !important;
      border-radius: 50px !important;
      transition: all 0.3s ease;
    }
    
    .btn-dark:hover {
      background-color: #162E93 !important;
      border-color: #162E93 !important;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(26, 25, 83, 0.3);
    }
    
    .text-dark {
      color: #1A1953 !important;
    }
    
    .text-dark:hover {
      color: #162E93 !important;
    }
  </style>
</head>

<body>

  <div class="page-wrapper overflow-hidden">

    <section class="bg-light-gray d-flex align-items-center justify-content-center min-vh-100">

      <div class="container py-3">

        <div class="sign-in card mx-auto shadow-lg">

          <div class="card-body py-8 px-lg-5">

            <!-- Logo -->
            <a href="{{ route('landing-page') }}" class="mb-8 hstack justify-content-center">
              <img src="{{ asset('assets/images/logos/logo-dark.svg') }}" alt="logo-dark" class="img-fluid" style="max-height: 55px;">
            </a>


            <!-- Google  -->
            @php
                $authRedirect = request('redirect');
                $googleLoginUrl = $authRedirect ? route('login.google', ['redirect' => $authRedirect]) : route('login.google');
                $registerUrl = $authRedirect ? route('register', ['redirect' => $authRedirect]) : route('register');
            @endphp
            <div class="hstack gap-3">
              <a href="{{ $googleLoginUrl }}"
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
              @if(request('redirect'))
                  <input type="hidden" name="redirect" value="{{ request('redirect') }}">
              @endif

              <div>
                <input type="email" name="email" class="form-control border-bottom @error('email') is-invalid @enderror" placeholder="Email" required autofocus value="{{ old('email') }}">
                @error('email')
                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem; font-weight: 500;">
                        {{ $message }}
                    </div>
                @enderror
              </div>

              <div>
                <input type="password" name="password" class="form-control border-bottom @error('password') is-invalid @enderror" placeholder="Password" required>
                @error('password')
                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem; font-weight: 500;">
                        {{ $message }}
                    </div>
                @enderror
              </div>

              <div class="d-flex justify-content-end mb-2">
                <a href="{{ route('password.request') }}" class="text-dark fs-3 fw-medium text-decoration-none" style="font-size: 0.85rem !important;">Lupa Password?</a>
              </div>

              <button type="submit" class="btn btn-dark w-100 justify-content-center py-2 fw-medium my-7 fs-4 lh-lg">
                Sign In
              </button>
            </form>


            <!-- Register -->
            <p class="mb-0 fw-medium text-center">
              Not a member yet?
              <a class="text-dark" href="{{ $registerUrl }}">Sign Up</a>
            </p>

          </div>

        </div>

      </div>

    </section>

    </div>

  <!-- Scripts -->
  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/libs/owl.carousel/dist/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('assets/libs/aos-master/dist/aos.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

</body>
</html>

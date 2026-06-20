<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CineTix - Reset Password</title>

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
            <a href="{{ route('landing-page') }}" class="mb-4 hstack justify-content-center">
              <img src="{{ asset('assets/images/logos/logo-dark.svg') }}" alt="logo-dark" class="img-fluid" style="max-height: 55px;">
            </a>

            <div class="mb-4 text-center fw-bolder text-dark" style="font-size: 1.25rem;">
                Buat Password Baru
            </div>

            <!-- Form -->
            <form class="d-flex flex-column gap-3" method="POST" action="{{ route('password.store') }}">
              @csrf

              <!-- Password Reset Token -->
              <input type="hidden" name="token" value="{{ $request->route('token') }}">

              <div>
                <input type="email" name="email" class="form-control border-bottom @error('email') is-invalid @enderror" placeholder="Email" required autofocus autocomplete="username" value="{{ old('email', $request->email) }}">
                @error('email')
                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem; font-weight: 500;">
                        {{ $message }}
                    </div>
                @enderror
              </div>

              <div>
                <input type="password" name="password" class="form-control border-bottom @error('password') is-invalid @enderror" placeholder="Password Baru" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem; font-weight: 500;">
                        {{ $message }}
                    </div>
                @enderror
              </div>

              <div>
                <input type="password" name="password_confirmation" class="form-control border-bottom @error('password_confirmation') is-invalid @enderror" placeholder="Konfirmasi Password Baru" required autocomplete="new-password">
                @error('password_confirmation')
                    <div class="invalid-feedback d-block mt-1" style="font-size: 0.85rem; font-weight: 500;">
                        {{ $message }}
                    </div>
                @enderror
              </div>

              <button type="submit" class="btn btn-dark w-100 justify-content-center py-2 fw-medium my-4 fs-4 lh-lg">
                Simpan Password Baru
              </button>
            </form>

            <!-- Back to Login -->
            <p class="mb-0 fw-medium text-center mt-3">
              <a class="text-dark d-inline-flex align-items-center gap-1" href="{{ route('login') }}">
                <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                Batal & Kembali ke Login
              </a>
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
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

</body>
</html>

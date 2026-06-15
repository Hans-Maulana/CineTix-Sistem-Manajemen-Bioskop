<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Studiova - Sign Up</title>

  <link rel="shortcut icon" href="../assets/images/logos/favicon.svg">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      background: #f5f6fa;
    }

    .auth-card {
      border: 0;
      border-radius: 18px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .form-control {
      border-radius: 10px;
    }

    .form-control:focus {
        border-color: #212529;
        box-shadow: none;
    }

    .btn-dark {
      border-radius: 10px;
    }

    .google-btn {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        color: #444;
        font-weight: 500;
        transition: all 0.2s;
    }

    .google-btn:hover {
        background: #f8f9fa;
        border-color: #ccc;
    }
  </style>
</head>

<body>

  <section class="min-vh-100 d-flex align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-4">

          <div class="card auth-card">
            <div class="card-body p-5">

              <div class="text-center mb-4">
                <img src="../assets/images/logos/logo-dark.svg" style="height:40px;" alt="Logo">
                <h5 class="fw-bold mt-3">Create Account</h5>
                <p class="text-muted small mb-0">Sign up to get started</p>
              </div>

              @php
                $authRedirect = request('redirect');
                $googleLoginUrl = $authRedirect ? route('login.google', ['redirect' => $authRedirect]) : route('login.google');
                $loginUrl = $authRedirect ? route('login', ['redirect' => $authRedirect]) : route('login');
              @endphp

              <a href="{{ $googleLoginUrl }}" class="btn w-100 d-flex align-items-center justify-content-center gap-2 mb-3 google-btn">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="18" height="18">
                Continue with Google
              </a>

              <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1">
                <span class="px-2 text-muted small">OR</span>
                <hr class="flex-grow-1">
              </div>

              <form method="POST" action="{{ route('register') }}">
                @csrf
                @if(request('redirect'))
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif

                <div class="mb-3">
                  <label class="form-label small">Name</label>
                  <input type="text" name="name" class="form-control py-2 @error('name') is-invalid @enderror"
                         placeholder="Your name" value="{{ old('name') }}" required autofocus>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label small">Email</label>
                  <input type="email" name="email" class="form-control py-2 @error('email') is-invalid @enderror"
                         placeholder="you@email.com" value="{{ old('email') }}" required>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label small">Password</label>
                  <input type="password" name="password" class="form-control py-2 @error('password') is-invalid @enderror"
                         placeholder="••••••••" required>
                  @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label small">Confirm Password</label>
                  <input type="password" name="password_confirmation" class="form-control py-2"
                         placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-dark w-100 py-2 mt-2">
                  Sign Up
                </button>
              </form>

              <div class="text-center mt-4">
                <p class="small text-muted mb-2">
                  By signing up you agree to our <a href="#" class="text-decoration-none">Privacy Policy</a>
                </p>

                <p class="small mb-0">
                  Already have an account?
                  <a href="{{ $loginUrl }}" class="fw-semibold text-dark">Sign In</a>
                </p>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

</body>
</html>

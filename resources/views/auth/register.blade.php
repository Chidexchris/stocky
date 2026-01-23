<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Register | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <!-- CoreUI CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body class="c-app flex-row align-items-center">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mx-4">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
        <img class="w-40" src="{{ asset('images/dtrecord.png') }}" alt="Logo">
                    </div>
                    <form method="post" action="{{ url('/register') }}" id="register-form">
                        @csrf
                        <h1 class="mb-2">Create Account</h1>
                        <p class="text-muted mb-4">Provide your details below</p>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                              </span>
                            </div>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   placeholder="Email" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control"
                                           id="first_name" value="{{ old('first_name') }}"
                                           placeholder="First name" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control"
                                           id="last_name" value="{{ old('last_name') }}"
                                           placeholder="Last name" required>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="name" id="full_name">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                              </span>
                            </div>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" placeholder="Password" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                  <i class="bi bi-lock"></i>
                              </span>
                            </div>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Confirm password">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="bi bi-building"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control"
                                   name="company_name" value="{{ old('company_name') }}"
                                   placeholder="Company name">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="bi bi-telephone"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control"
                                   name="phone" value="{{ old('phone') }}"
                                   placeholder="Phone number">
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <select class="form-control" name="company_size">
                                        <option value="">Company Size</option>
                                        <option value="1-10">1-10</option>
                                        <option value="11-50">11-50</option>
                                        <option value="51-200">51-200</option>
                                        <option value="200+">200+</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <select class="form-control" name="help_topic">
                                        <option value="">What can we help you with today?</option>
                                        <option value="sales">Sales</option>
                                        <option value="support">Support</option>
                                        <option value="billing">Billing</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="referral_source">
                                <option value="">How did you hear about us?</option>
                                <option value="search">Search</option>
                                <option value="social">Social</option>
                                <option value="friend">Friend</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-flat mb-3">Register</button>
                        <a href="{{ route('login') }}" class="text-center">I already have a membership.</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CoreUI -->
<script src="{{ mix('js/app.js') }}" defer></script>
<script>
document.getElementById('register-form').addEventListener('submit', function() {
  var fn = document.getElementById('first_name').value || '';
  var ln = document.getElementById('last_name').value || '';
  document.getElementById('full_name').value = (fn + ' ' + ln).trim();
});
</script>

</body>
</html>

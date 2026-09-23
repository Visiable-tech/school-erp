<!DOCTYPE html>
<html>
<head>
    <title>School ERP Login</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container">

    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-md-5 col-lg-4">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <h3 class="text-center mb-4">
                        School ERP
                    </h3>

                    @if($errors->any())

                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>

                    @endif

                    <form method="POST"
                          action="{{ route('login.submit') }}">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="form-control"
                                   required
                                   autofocus>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="form-check mb-3">

                            <input type="checkbox"
                                   name="remember"
                                   value="1"
                                   class="form-check-input"
                                   id="remember">

                            <label class="form-check-label"
                                   for="remember">
                                Remember Me
                            </label>

                        </div>

                        <button type="submit"
                                class="btn btn-primary w-100">

                            Login

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
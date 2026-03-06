<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link href="{{ asset('asset/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('asset/register.css') }}" rel="stylesheet">
</head>
<body>
  <div class="register-page-main">
    <div class="register-container">
      <h1 class="register-main-title">Login</h1>

      @if (session('success'))
        <div class="register-alert register-alert-success">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="register-alert register-alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="process_check_login" method="POST" class="register-form">
        @csrf
        <div class="form-group">
          <label for="name" class="form-label">Full Name:</label>
          <input type="text" id="name" name="name" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email:</label>
          <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password:</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-actions">
          <button type="submit" class="register-btn">Login</button>
        </div>
      </form>

      <div style="text-align:center; margin-top: 1rem;">
        <p>Don't have an account? <a href="register">Register here</a></p>
      </div>
    </div>
  </div>
</body>
</html>
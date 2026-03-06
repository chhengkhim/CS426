<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
 <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }
    
    body {
      background-color: #1c1c32;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    
    .login-container {
      background-color: #2b2b45;
      border-radius: 15px;
      padding: 40px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    h1 {
      color: #ffffff;
      text-align: center;
      margin-bottom: 30px;
      font-size: 32px;
      font-weight: 700;
    }
    
    .form-group {
      margin-bottom: 25px;
    }
    
    label {
      display: block;
      color: #ffffff;
      margin-bottom: 8px;
      font-size: 16px;
    }
    
    input {
      width: 100%;
      padding: 15px;
      border-radius: 10px;
      border: none;
      background-color: #50508e;
      color: #ffffff;
      font-size: 16px;
    }
    
    input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }
    
    input:focus {
      outline: 1px solid #ff9191;
    }
    
    button {
      width: 100%;
      padding: 15px;
      border-radius: 10px;
      border: 1px solid #ff9191;
      background-color: #50508e;
      color: #ffffff;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    button:hover {
      background-color: #3d3d6e;
    }
    
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 10px;
      text-align: center;
    }
    
    .alert-success {
      background-color: #4CAF50;
      color: white;
    }
    
    .alert-error {
      background-color: #f44336;
      color: white;
    }
    
    .error-list {
      list-style-type: none;
      margin-top: 20px;
    }
    
    .error-list li {
      color: #ff9191;
      margin-bottom: 5px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    <h1>Login</h1>

    <form action="process_check_login" method="POST">
      @csrf
      <div class="form-group">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>

      <button type="submit">Login</button>
    </form>

    @if ($errors->any())
      <div class="alert alert-error">
        <ul class="error-list">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>
</body>
</html>
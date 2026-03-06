<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile</title>
  <style>
    /* General Page Layout */
    body {
      background: #f7e7d7; /* Light background matching your site */
      font-family: 'Segoe UI', Arial, sans-serif;
      color: #2d1c0b;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .update-profile-page-main {
      flex-grow: 1;
      padding: 40px 20px;
      max-width: 700px;
      margin: 0 auto;
      width: 100%;
    }

    .update-profile-container {
      background: #fff; /* Main container background */
      border-radius: 24px;
      box-shadow: 0 4px 32px rgba(0,0,0,0.06);
      padding: 32px 32px 40px 32px;
    }

    .update-profile-main-title {
      font-size: 2.5rem;
      font-weight: 700;
      color: #b85c38; /* Main title color */
      text-align: center;
      margin-bottom: 30px;
    }

    /* Alerts */
    .update-profile-alert {
      padding: 12px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 0.95rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .update-profile-alert-danger {
      background: #f8d7da;
      color: #721c24;
    }

    .update-profile-alert-danger ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }

    /* Form Styles */
    .update-profile-form .form-group {
      margin-bottom: 20px;
    }

    .update-profile-form .form-label {
      display: block;
      font-weight: 600;
      color: #2d1c0b;
      margin-bottom: 8px;
      font-size: 1rem;
    }

    .update-profile-form .form-control,
    .update-profile-form .form-control-file {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #f0d6c2;
      border-radius: 8px;
      font-size: 1rem;
      color: #2d1c0b;
      background-color: #fdfaf7;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .update-profile-form .form-control:focus,
    .update-profile-form .form-control-file:focus {
      outline: none;
      border-color: #b85c38;
      box-shadow: 0 0 0 3px rgba(184,92,56,0.2);
    }

    .update-profile-form .form-control-file {
      padding: 10px 0;
    }

    .update-profile-form .form-text {
      font-size: 0.85rem;
      color: #6d4c2b;
      margin-top: 5px;
      display: block;
    }

    .update-profile-form .form-text a {
      color: #b85c38;
      text-decoration: none;
    }

    .update-profile-form .form-text a:hover {
      text-decoration: underline;
    }

    .form-action-group {
      margin-top: 30px;
      text-align: center;
    }

    .update-profile-btn {
      background: #b85c38;
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 15px 30px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, box-shadow 0.2s;
      width: 100%;
      max-width: 300px;
    }

    .update-profile-btn:hover {
      background: #a14d2a;
      box-shadow: 0 6px 16px rgba(184,92,56,0.35);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .update-profile-container {
        padding: 25px;
      }
      .update-profile-main-title {
        font-size: 2rem;
      }
    }

    @media (max-width: 480px) {
      .update-profile-page-main {
        padding: 20px 10px;
      }
      .update-profile-container {
        padding: 20px;
      }
      .update-profile-main-title {
        font-size: 1.8rem;
      }
      .update-profile-btn {
        font-size: 1rem;
        padding: 12px 25px;
      }
    }

    /* Back button */
    .back-btn {
      padding: 10px 20px;
      background: #b85c38;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      margin-bottom: 20px;
      display: inline-block;
      transition: background 0.2s;
    }
    .back-btn:hover {
      background: #a14d2a;
      color: white;
    }
  </style>
</head>
<body>
  <div class="update-profile-page-main">
    <a href="seller_Home" class="back-btn">Back to Profile</a>
    
    <div class="update-profile-container">
      <h1 class="update-profile-main-title">Edit Profile</h1>

      @if ($errors->any())
        <div class="update-profile-alert update-profile-alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form class="update-profile-form" action="process_edit_sellerProfile" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
          <label for="seller_profile_img" class="form-label">Profile Picture</label>
          <input type="file" class="form-control-file" id="seller_profile_img" name="seller_profile_img">
          <small class="form-text">Current: {{ Auth::guard('seller')->user()->seller_profile_img ? 'Image uploaded' : 'No image' }}</small>
        </div>

        <div class="form-group">
          <label for="full_name" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="full_name" name="full_name" value="{{ Auth::guard('seller')->user()->full_name }}" required>
        </div>

        <div class="form-group">
          <label for="seller_email" class="form-label">Email</label>
          <input type="email" class="form-control" id="seller_email" name="seller_email" value="{{ Auth::guard('seller')->user()->seller_email }}" required>
        </div>

        <div class="form-group">
          <label for="store_name" class="form-label">Store Name</label>
          <input type="text" class="form-control" id="store_name" name="store_name" value="{{ Auth::guard('seller')->user()->store_name }}" required>
        </div>

        <div class="form-group">
          <label for="phone_number" class="form-label">Phone Number</label>
          <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ Auth::guard('seller')->user()->phone_number }}" required>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">New Password (leave blank to keep current)</label>
          <input type="password" class="form-control" id="password" name="password">
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label">Confirm New Password</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>

        <div class="form-action-group">
          <button type="submit" class="update-profile-btn">Update Profile</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
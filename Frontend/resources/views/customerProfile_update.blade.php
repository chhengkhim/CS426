<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Customer Profile</title>
  <link rel="stylesheet" href="{{ asset('asset/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('asset/customer_profile_update.css') }}">
</head>
<body>
  

  <div class="update-profile-page-main">
    <a href="/customer_Home" class="modern-back-btn" style="margin-bottom:18px;display:inline-flex;align-items:center;"><span style="margin-right:8px;font-size:1.2em;">&#8962;</span> Back Home</a>
    <div class="update-profile-container">
      <h1 class="update-profile-main-title">Update Profile</h1>

      @if ($errors->any())
        <div class="update-profile-alert update-profile-alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('process_updateCustomerProfile') }}" method="POST" enctype="multipart/form-data" class="update-profile-form">
        @csrf
        
        <div class="form-group">
          <label for="customer_profile_images" class="form-label">Profile Picture:</label>
          <input type="file" id="customer_profile_images" name="customer_profile_images" class="form-control-file">
          @if (Auth::guard('customer')->user()->customer_profile_images)
            <small class="form-text text-muted">Current: <a href="{{ asset(Auth::guard('customer')->user()->customer_profile_images) }}" target="_blank">View Image</a></small>
          @else
            <small class="form-text text-muted">No profile image uploaded.</small>
          @endif
        </div>

        <div class="form-group">
          <label for="full_name" class="form-label">Full Name:</label>
          <input type="text" id="full_name" name="full_name" value="{{Auth::guard('customer')->user()->full_name }}" class="form-control" required>
        </div>
        
        <div class="form-group">
          <label for="age" class="form-label">Age:</label>
          <input type="number" id="age" name="age" value="{{Auth::guard('customer')->user()->age }}" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="customer_email" class="form-label">Email:</label>
          <input type="email" id="customer_email" name="customers_email" value="{{Auth::guard('customer')->user()->customers_email }}" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="phone_number" class="form-label">Phone Number:</label>
          <input type="text" id="phone_number" name="phone_number" value="{{Auth::guard('customer')->user()->phone_number}}" class="form-control" required>
        </div>
        
        <div class="form-group">
          <label for="password" class="form-label">New Password (leave blank to keep current): </label>
          <input type="password" id="password" name="password" class="form-control">
        </div>
        
        <div class="form-group">
          <label for="password_confirmation" class="form-label">Confirm Password:</label>
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Leave blank to keep current password" class="form-control">
        </div>
        
        <div class="form-action-group">
          <button type="submit" class="update-profile-btn">Update Profile</button>
        </div>
      </form>
    </div>
  </div>

<style>
  .modern-back-btn {
    background: #b85c38;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 24px;
    font-size: 1.08rem;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(184,92,56,0.08);
    transition: background 0.18s, box-shadow 0.18s;
    margin-top: 18px;
    margin-left: 18px;
  }
  .modern-back-btn:hover {
    background: #a14d2a;
    color: #fff;
    box-shadow: 0 4px 16px rgba(184,92,56,0.16);
    text-decoration: none;
  }
</style>
</body>
</html>
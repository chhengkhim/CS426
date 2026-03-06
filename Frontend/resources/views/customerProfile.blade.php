<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Profile</title>
  <link rel="stylesheet" href="{{ asset('asset/styles.css') }}"> <!-- Link to main stylesheet -->
  <link rel="stylesheet" href="{{ asset('asset/customer_profile.css') }}"> <!-- Specific stylesheet for profile -->
</head>
<body>
   @if ($errors->any())
          <div> 
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

  <div class="profile-page-main">
    <a href="/customer_Home" class="modern-back-btn" style="margin-bottom:18px;display:inline-flex;align-items:center;"><span style="margin-right:8px;font-size:1.2em;">&#8962;</span> Back Home</a>
    <div class="profile-container">
      <h1 class="profile-main-title">My Profile</h1>
      <div class="profile-image-section">
        @foreach ($customer as $customerImage)
          @if ($customerImage && $customerImage->customer_profile_images)
            <img src="{{ asset($customerImage->customer_profile_images) }}" alt="Customer Profile" class="profile-img">
          @else
            <div class="profile-img-placeholder">
              <span>No Image</span>
            </div>
          @endif
        @endforeach
      </div>

      <div class="profile-info-grid">
        <div class="profile-info-item">
          <span class="info-label">ID:</span>
          <span class="info-value">{{ Auth::guard('customer')->user()->customer_id}}</span>
        </div>
        <div class="profile-info-item">
          <span class="info-label">Name:</span>
          <span class="info-value">{{ Auth::guard('customer')->user()->full_name }}</span>
        </div>
        <div class="profile-info-item">
          <span class="info-label">Email:</span>
          <span class="info-value">{{ Auth::guard('customer')->user()->customers_email}}</span>
        </div>
        <div class="profile-info-item">
          <span class="info-label">Gender:</span>
          <span class="info-value">{{ Auth::guard('customer')->user()->gender}}</span>
        </div>
        <div class="profile-info-item">
          <span class="info-label">Age:</span>
          <span class="info-value">{{ Auth::guard('customer')->user()->age}}</span>
        </div>
        <div class="profile-info-item">
          <span class="info-label">Phone Number:</span>
          <span class="info-value">{{ Auth::guard('customer')->user()->phone_number}}</span>
        </div>
      </div>

      <div class="profile-actions">
        <form action="customer_profile_update" method="get">
          @csrf
          <button type="submit" class="profile-btn profile-btn-edit">Edit Profile</button>
        </form>
        <form action="logout" method="post">
          @csrf
          <button type="submit" class="profile-btn profile-btn-logout">Logout</button>
        </form>
        <form action="{{ route('delete_customerAccount', Auth::guard('customer')->user()->customer_id) }}" method="POST">
          @csrf
          <button type="submit" class="profile-btn profile-btn-delete">Delete Account</button>
        </form>
      </div>
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
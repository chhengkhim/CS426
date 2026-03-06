<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>seller Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="css.css">
</head>
<body>
<!-- Mobile Menu Toggle Button -->
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>
<div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar p-0" id="sidebar">
                <div class="logo">Handcraft Admin</div>
                <div class="px-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/Home">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/customerManagement">
                                <i class="fas fa-users"></i> Customer Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/sellerManagement">
                                <i class="fas fa-store"></i> Seller Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/productManagement">
                                <i class="fas fa-boxes me-2"></i> Product Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/reviewManagement">
                                <i class="fas fa-star"></i> Review Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/viewAllOrders">
                                <i class="fas fa-shopping-cart"></i> Order Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/allAdminMessages">
                                <i class="fa-solid fa-envelope"></i> Message Seller
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Moved Logout Button to Bottom -->
                    <div class="mt-auto" style="position: absolute; bottom: 20px; width: calc(100% - 24px);">
                        <form action="/logout" method="post" class="w-100">
                            @csrf
                            <button type="submit" class="nav-link text-start w-100 bg-transparent border-0">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

    <main class="col-md-9 col-lg-10 main-content">
      <h2 class="section-title">View All seller</h2>
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    @foreach($errors->all() as $error)
        {{ $error }}
    @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

  <div class="seller-table-container">
    <div class="table-responsive">
      <div class="table-wrapper">
      <table class="seller-table">
        <thead>
          <tr>
            <th>Seller ID</th>
            <th>Seller Profile</th>
            <th>Seller Name</th>
            <th>Store Name</th>
            <th>Email</th>
            <th>Phone Numebr</th>
            <th>Seller Address</th>
            <th>Account Status</th>
            <th>Activate/Deactivate User</th>
            <th>View Seller Store</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($seller as $seller)
            <tr>
              <td>{{$seller -> seller_id}}</td>
              <td>
                @if ($seller->seller_profile_img)
                          <img src="{{ asset($seller->seller_profile_img) }}" alt="seller profile" class="seller-avatar">
                      @else
                          <div class="seller-avatar bg-secondary d-flex align-items-center justify-content-center">
                            <i class="fas fa-user"></i>
                          </div>
                      @endif

              </td>
              <td>{{ $seller->full_name }}</td>
              <td>{{$seller->store_name}}</td>
              <td>{{ $seller->seller_email }}</td>
              <td>{{ $seller->phone_number }}</td>
              <td>{{ $seller->seller_address }}</td>
              <td>
                <span class="status-badge {{ $seller->account_status === 'Activate' ? 'status-active' : 'status-inactive' }}">
                        {{ $seller->account_status}}
                      </span>  
              </td>

              <td>
                  @if($seller->account_status === 'Activate')
                      <form method="POST" action="{{ url('deactivateSeller/'. $seller->seller_id) }}">
                          @csrf
                          <button type="submit" class="btn btn-danger"
                                  onclick="return confirm('Deactivate this seller?\n\nThis seller will no longer be able to login.')">
                                  <i class="fas fa-ban"></i>
                              Deactivate seller
                          </button>
                      </form>
                  @else
                      <form method="POST" action="{{ url('activateSeller/'. $seller->seller_id) }}">
                          @csrf
                          <button type="submit" class="btn btn-success"
                                  onclick="return confirm('Activate this seller?\n\nThis seller will now be able to login.')">
                                  <i class="fas fa-check"></i> 
                              Activate seller
                          </button>
                      </form>
                  @endif
              </td>

              <td>
                <form action="{{ url('storePage_fromSellerManagement/' . $seller->seller_id)}}" method="GET">
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i>
                  Store Page</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    </div>
    </main>
    </div>
</div>



    <!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  
  // Toggle sidebar
  menuToggle.addEventListener('click', function(e) {
    e.stopPropagation();
    sidebar.classList.toggle('active');
  });
  
  // Close sidebar when clicking outside
  document.addEventListener('click', function(e) {
    if (window.innerWidth < 992 && 
        !sidebar.contains(e.target) && 
        e.target !== menuToggle) {
      sidebar.classList.remove('active');
    }
  });
  
  // Handle window resize
  window.addEventListener('resize', function() {
    if (window.innerWidth >= 992) {
      sidebar.classList.remove('active');
    }
  });
});
</script>
</body>
</html>
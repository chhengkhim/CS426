<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Handcraft Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <a class="nav-link active" href="/Home">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/customerManagement">
                                <i class="fas fa-users"></i> Customer Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="/sellerManagement">
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

      <!-- Main Content -->
      <main class="col-md-9 col-lg-10 main-content">
        <h1 class="mb-4">Dashboard Overview</h1>
        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-md-6 col-lg-3 mb-4">
            <div class="stat-card card-teal">
              <div class="stat-value">{{ $totalCustomer }}</div>
              <div class="stat-label">Total Customers</div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3 mb-4">
            <div class="stat-card card-blue">
              <div class="stat-value">{{ $totalProducts }}</div>
              <div class="stat-label">Total Products</div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3 mb-4">
            <div class="stat-card card-pink">
              <div class="stat-value">{{ $totalOrder }}</div>
              <div class="stat-label">Total Orders</div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3 mb-4">
            <div class="stat-card card-purple">
              <div class="stat-value">{{ $totalSeller }}</div>
              <div class="stat-label">Total Sellers</div>
            </div>
          </div>
        </div>

        
        <!-- Product Management Section -->
        <section class="mb-5">
          <h2 class="section-title">Product Gallery</h2>
          
          <!-- Category Filter -->
          <div class="category-filter">
            @foreach ($category as $catItem)
              <form action="viewCategoryProduct" method="GET">
                <input type="hidden" name="category_id" value="{{ $catItem->category_id }}">
                <button type="submit" class="category-btn">
                  {{ $catItem->category_name }}
                </button>
              </form>
            @endforeach
          </div>

          <!-- Product Grid -->
          <div class="product-grid">
            @foreach ($product as $p)
              @php
                $image = collect($images)->firstWhere('product_id', $p->product_id);
                $cat = collect($category)->firstWhere('category_id', $p->category_id);
              @endphp
              <a href="{{ url('/viewProductDetail/' . $p->product_id) }}" class="product-card">
                @if ($image)
                  <img src="{{ asset($image->img_url) }}" alt="{{ $p->product_name }}" class="product-image">
                @else
                  <div style="background-color: var(--secondary-color); height: 100%; display: flex; align-items: center; justify-content: center;">
                    <span>No Image Available</span>
                  </div>
                @endif
                <div class="product-info">
                  <div class="product-category">{{ $cat ? $cat->category_name : 'Unknown' }}</div>
                  <div class="product-name">{{ $p->product_name }}</div>
                  <div class="product-price">${{ number_format($p->product_price, 2) }}</div>
                </div>
              </a>
            @endforeach
          </div>
        </section>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  
  // Better touch handling for mobile
  menuToggle.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    sidebar.classList.toggle('active');
  });
  
  // Close sidebar when clicking outside
  document.addEventListener('click', function(e) {
    if (window.innerWidth < 992 && sidebar.classList.contains('active') && 
        !sidebar.contains(e.target) && e.target !== menuToggle) {
      sidebar.classList.remove('active');
    }
  });
  
  // Handle window resize
  function handleResize() {
    if (window.innerWidth >= 992) {
      sidebar.classList.remove('active');
    }
  }
  
  window.addEventListener('resize', handleResize);
  handleResize(); // Run once on load
});
    </script>
</body>
</html>
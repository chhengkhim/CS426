<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products by Category - Handcraft Admin</title>
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
                            <a class="nav-link" href="/sellerManagement">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="section-title">
            @if($selectedCategory)
              Products in Category: {{ $selectedCategory->category_name }}
            @else
              All Products
            @endif
          </h1>
          <a href="/Home" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Home
          </a>
        </div>
        
        @if (session('status'))
          <div class="alert alert-success alert-dismissible fade show">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <!-- Category Filter Form -->
        <div class="category-filter-form">
          <form action="{{ url('/viewCategoryProduct') }}" method="GET" class="row g-3">
            <div class="col-md-6">
              <label for="category_id" class="form-label">Filter by Category:</label>
              <select name="category_id" id="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                @foreach ($category as $cat)
                  <option value="{{ $cat->category_id }}" {{ $selectedCategory && $cat->category_id == $selectedCategory->category_id ? 'selected' : '' }}>
                    {{ $cat->category_name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter me-1"></i> Filter
              </button>
            </div>
          </form>
        </div>

        <!-- Product Grid -->
        <div class="product-grid">
          @forelse ($products as $product)
            @php
              $productImage = $images->firstWhere('product_id', $product->product_id);
              $productCategory = $category->firstWhere('category_id', $product->category_id);
            @endphp
            <a href="{{ url('/viewProductDetail/' . $product->product_id) }}" class="product-card">
              @if ($productImage)
                <img src="{{ asset($productImage->img_url) }}" alt="{{ $product->product_name }}" class="product-image">
              @else
                <div style="background-color: var(--secondary-color); height: 100%; display: flex; align-items: center; justify-content: center;">
                  <span>No Image Available</span>
                </div>
              @endif
              <div class="product-info">
                <div class="product-category">{{ $productCategory ? $productCategory->category_name : 'Unknown' }}</div>
                <div class="product-name">{{ $product->product_name }}</div>
                <div class="product-price">${{ number_format($product->product_price, 2) }}</div>
              </div>
            </a>
          @empty
            <div class="col-12 text-center py-5">
              <h4>No products found in this category</h4>
            </div>
          @endforelse
        </div>
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
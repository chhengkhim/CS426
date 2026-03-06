<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Products - Handcraft Admin</title>
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
                            <a class="nav-link" href="/sellerManagement">
                                <i class="fas fa-store"></i> Seller Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/productManagement">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title">Seller Products Management</h2>
                    <a href="/sellerManagement" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Sellers
                    </a>
                </div>
                
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="products-card">
                    <div class="card-header">
                        <i class="fas fa-boxes me-2"></i> Products List
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>View</th>
                                        <th>Change status</th>
                                        <th>Delete product</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($product as $p)
                                        <tr>
                                            <td>
                                                @php
                                                    $image = collect($images)->firstWhere('product_id', $p->product_id);
                                                @endphp
                                                @if ($image)
                                                    <img src="{{ asset($image->img_url) }}" alt="Product Image" class="product-img">
                                                @else
                                                    <div class="product-img bg-secondary d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $p->product_name }}</td>
                                            <td>
                                                @php
                                                    $cat = collect($category)->firstWhere('category_id', $p->category_id);
                                                @endphp
                                                {{ $cat ? $cat->category_name : 'Unknown' }}
                                            </td>
                                            <td>${{ number_format($p->product_price, 2) }}</td>
                                            <td>
                                                <span class="status-badge {{ $p->product_status == 'available' ? 'status-available' : 'status-unavailable' }}">
                                                    {{ ucfirst($p->product_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <form action="{{ url('/viewProductDetail/' . $p->product_id) }}" method="get">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-eye me-1"></i> View
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                            <td>
                                              <div class="d-flex gap-2">
                                                @if($p->product_status == 'Activate')
                                                    <form action="{{ url('/deactivateProduct_viewAllProduct/' . $p->product_id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Deactivate this product?')">
                                                            <i class="fas fa-ban me-1"></i> Deactivate
                                                        </button>
                                                    </form>
                                                    @else
                                                    <form action="{{ url('/activateProduct_viewAllProduct/' . $p->product_id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success" onclick="return confirm('Activate this product?')">
                                                            <i class="fas fa-check me-1"></i> Activate
                                                        </button>
                                                    </form>
                                                    @endif
                                              </div>
                                            </td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <form action="{{ url('/deleteProduct/' . $p->product_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">No products found for this seller</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Detail - Handcraft Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

   <style>
    :root {
      --primary-color: #2b2b45;
      --secondary-color: #50508e;
      --accent-color: #ff9191;
      --dark-bg: #1c1c32;
      --light-text: #ffffff;
      --muted-text: rgba(255, 255, 255, 0.7);
    }
    
    body {
      background-color: var(--dark-bg);
      color: var(--light-text);
      font-family: 'Poppins', sans-serif;
    }
    
    .sidebar {
      background-color: var(--primary-color);
      min-height: 100vh;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
      position: fixed;
      width: 280px;
    }
    
    .main-content {
      margin-left: 280px;
      padding: 30px;
    }
    
    .nav-link {
      color: var(--muted-text);
      padding: 12px 20px;
      margin: 5px 0;
      border-radius: 8px;
      transition: all 0.3s;
    }
    
    .nav-link:hover, .nav-link.active {
      background-color: var(--secondary-color);
      color: var(--light-text);
    }
    
    .logo {
      padding: 20px;
      font-size: 24px;
      font-weight: bold;
      color: var(--light-text);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 20px;
    }
    
    .product-detail-container {
      background-color: var(--primary-color);
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .product-table {
      width: 100%;
      color: var(--light-text);
      border-collapse: separate;
      border-spacing: 0;
    }
    
    .product-table thead th {
      background-color: var(--secondary-color);
      color: var(--light-text);
      font-weight: 600;
      padding: 15px;
      border: none;
    }
    
    .product-table tbody td {
      padding: 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      vertical-align: middle;
    }
    
    .product-table tbody tr:hover {
      background-color: rgba(80, 80, 142, 0.2);
    }
    
    .product-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid var(--accent-color);
    }
    
    .status-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 500;
    }
    
    .status-active {
      background-color: rgba(40, 167, 69, 0.2);
      color: #28a745;
    }
    
    .status-inactive {
      background-color: rgba(220, 53, 69, 0.2);
      color: #dc3545;
    }
    
    .btn-info {
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
    }
    
    .btn-info:hover {
      background-color: #3d3d6e;
      border-color: #3d3d6e;
    }
    
    .section-title {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--light-text);
    }
    
    .alert {
      border-radius: 8px;
    }
    
      /* Mobile menu toggle button */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--secondary-color);
            border: none;
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-280px);
                position: fixed;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .profile-img {
                width: 100px;
                height: 100px;
            }
            
            .section-title {
                font-size: 20px;
            }
            
            .btn {
                padding: 0.375rem 0.5rem;
                font-size: 0.875rem;
            }
            
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Add these styles to your existing media queries */

@media (max-width: 992px) {
  .seller-table {
    display: block;
    width: 100%;
    overflow-x: auto;
    white-space: nowrap;
  }
  
  .seller-table thead th {
    position: static; /* Remove sticky headers on mobile */
  }
}

@media (max-width: 768px) {
  .seller-table td, 
  .seller-table th {
    padding: 10px 8px; /* Reduce padding */
    font-size: 14px; /* Smaller font */
  }
  
  .action-btn {
    padding: 6px 8px;
    font-size: 12px;
  }
  
  .seller-avatar {
    width: 40px;
    height: 40px;
  }
}

@media (max-width: 576px) {
  .main-content {
    padding: 15px 10px; /* Reduce padding further */
  }
  
  .section-title {
    font-size: 18px;
    margin-bottom: 15px;
  }
  
  /* Stack action buttons vertically */
  .seller-table td:nth-last-child(2),
  .seller-table td:last-child {
    display: block;
    width: 100%;
    text-align: center;
  }
  
  .seller-table td:nth-last-child(2) {
    margin-bottom: 5px;
  }
}

        @media (max-width: 768px) {
            .profile-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .profile-card-header > div {
                width: 100%;
            }
            
            .profile-card-header form {
                width: 100%;
            }
            
            .profile-card-header button {
                width: 100%;
            }
            
            .card-statistics .col-md-4 {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 15px;
            }
            
            .profile-img {
                width: 80px;
                height: 80px;
            }
            
            .info-label, .info-value {
                font-size: 0.9rem;
            }
            
            .btn-action {
                margin-bottom: 5px;
                width: 100%;
            }
            
            .d-flex.gap-2 {
                flex-direction: column;
                gap: 5px !important;
            }
        }
  </style>
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
        <h2 class="section-title">Product Details</h2>
        <a href="/Home" class="btn btn-outline-primary">
          <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
      </div>
      
      @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show">
          {{ session('status') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      
      @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <div class="product-detail-container">
        <div class="table-responsive">
          <table class="product-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Created</th>
                <th>Store</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($product as $p)
                <tr>
                  <td>{{$p->product_id}}</td>
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
                  <td>{{ $p->product_description }}</td>
                  <td>${{ number_format($p->product_price, 2) }}</td>
                  <td>{{ $p->stock_quantity }}</td>
                  <td>
                    <span class="status-badge {{ $p->product_status === 'active' ? 'status-active' : 'status-inactive' }}">
                      {{ ucfirst($p->product_status) }}
                    </span>
                  </td>
                  <td>{{ $p->created_at }}</td>
                  <td>
                    <div class="d-flex flex-column">
                      <span class="mb-2">{{$seller->store_name}}</span>
                      <form action="{{ url('/storePage/' . $p->product_id) }}" method="get" class="d-inline">
                        <button type="submit" class="btn btn-info btn-sm">
                          <i class="fas fa-store me-1"></i> View Store
                        </button>
                      </form>
                    </div>
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